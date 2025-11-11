<?php
// Process Checkout Action - handles backend processing of checkout flow
require_once '../src/settings/core.php';
require_once '../database/database.php';
require_once '../controllers/cart_controller.php';
require_once '../controllers/order_controller.php';
require_once '../controllers/product_controller.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to checkout']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Get customer information
$customer_id = get_user_id();

try {
    // Start database transaction
    getDB()->beginTransaction();
    
    // Get cart items
    $cart_items = get_user_cart_ctr();
    
    if (empty($cart_items)) {
        throw new Exception('Cart is empty');
    }
    
    // Calculate total amount
    $total_amount = 0;
    foreach ($cart_items as $item) {
        // Get current product price from database
        $product = get_product_by_id_ctr($item['product_id']);
        if (!$product) {
            throw new Exception('Product not found: ' . $item['product_id']);
        }
        $total_amount += $product['product_price'] * $item['qty'];
    }
    
    // Get delivery information
    $delivery_address = trim($_POST['delivery_address'] ?? '');
    $delivery_phone = trim($_POST['delivery_phone'] ?? '');
    $special_instructions = trim($_POST['special_instructions'] ?? '');
    
    if (empty($delivery_address) || empty($delivery_phone)) {
        throw new Exception('Delivery address and phone are required');
    }
    
    // Generate invoice number
    require_once '../classes/order_class.php';
    $order = new Order();
    $invoice_no = $order->generateInvoiceNo();
    
    // Create order
    $order_params = [
        'customer_id' => $customer_id,
        'invoice_no' => $invoice_no,
        'total_amount' => $total_amount,
        'delivery_address' => $delivery_address,
        'delivery_phone' => $delivery_phone,
        'special_instructions' => $special_instructions
    ];
    
    $order_id = create_order_ctr($order_params);
    
    if (!$order_id) {
        throw new Exception('Failed to create order');
    }
    
    // Add order details
    foreach ($cart_items as $item) {
        // Get current product price from database
        $product = get_product_by_id_ctr($item['product_id']);
        if (!$product) {
            throw new Exception('Product not found: ' . $item['product_id']);
        }
        
        $order_detail_params = [
            'order_id' => $order_id,
            'product_id' => $item['product_id'],
            'qty' => $item['qty']
        ];
        
        $result = add_order_details_ctr($order_detail_params);
        if (!$result) {
            throw new Exception('Failed to add order details');
        }
    }
    
    // Handle receipt upload for payment methods that require it
    $receipt_image_path = null;
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $payment_reference = trim($_POST['payment_reference'] ?? '');
    
    if (in_array($payment_method, ['mobile_money', 'bank_transfer']) && isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/receipts/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['receipt_image']['name'], PATHINFO_EXTENSION);
        $file_name = 'receipt_' . $order_id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($file_extension), $allowed_types)) {
            throw new Exception('Invalid file type. Only JPG, PNG, and GIF files are allowed.');
        }
        
        // Validate file size (max 5MB)
        if ($_FILES['receipt_image']['size'] > 5 * 1024 * 1024) {
            throw new Exception('File size too large. Maximum size is 5MB.');
        }
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['receipt_image']['tmp_name'], $file_path)) {
            $receipt_image_path = 'uploads/receipts/' . $file_name;
        } else {
            throw new Exception('Failed to upload receipt image');
        }
    }
    
    // Record payment (simulated payment confirmation)
    $payment_params = [
        'customer_id' => $customer_id,
        'order_id' => $order_id,
        'amt' => $total_amount,
        'payment_method' => $payment_method,
        'payment_status' => 'pending', // Will be verified by admin
        'payment_reference' => $payment_reference,
        'receipt_image' => $receipt_image_path
    ];
    
    $payment_id = record_payment_ctr($payment_params);
    
    if (!$payment_id) {
        throw new Exception('Failed to record payment');
    }
    
    // Empty the cart
    $cart_emptied = empty_cart_ctr();
    
    if (!$cart_emptied) {
        // Log warning but don't fail the transaction
        error_log("Warning: Failed to empty cart after order creation. Order ID: " . $order_id);
    }
    
    // Commit transaction
    getDB()->commit();
    
    // Prepare success response
    $response = [
        'status' => 'success',
        'message' => 'Order placed successfully! Your order reference is: ' . $invoice_no,
        'order_id' => $order_id,
        'invoice_no' => $invoice_no,
        'total_amount' => $total_amount,
        'payment_id' => $payment_id
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (getDB()->inTransaction()) {
        getDB()->rollBack();
    }
    
    error_log("Checkout error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>

