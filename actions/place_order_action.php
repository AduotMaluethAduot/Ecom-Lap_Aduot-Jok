<?php
// Place Order Action - handles order placement with payment integration
require_once '../src/settings/core.php';
require_once '../database/database.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to place an order']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Get customer information
$customer_id = get_user_id();
$customer_name = get_user_name();

try {
    // Get POST data
    $action = $_POST['action'] ?? '';
    $cart_data = json_decode($_POST['cart_data'] ?? '[]', true);
    $delivery_phone = trim($_POST['delivery_phone'] ?? '');
    $delivery_address = trim($_POST['delivery_address'] ?? '');
    $special_instructions = trim($_POST['special_instructions'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $payment_reference = trim($_POST['payment_reference'] ?? '');
    $total_amount = floatval($_POST['total_amount'] ?? 0);

    // Validate required fields
    if (empty($cart_data) || empty($delivery_phone) || empty($delivery_address)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required information']);
        exit();
    }

    if ($total_amount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid order amount']);
        exit();
    }

    // Validate payment method
    $valid_payment_methods = ['mobile_money', 'bank_transfer', 'pos', 'cash'];
    if (!in_array($payment_method, $valid_payment_methods)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid payment method']);
        exit();
    }

    // Validate payment reference for mobile money and bank transfer
    if (in_array($payment_method, ['mobile_money', 'bank_transfer']) && empty($payment_reference)) {
        echo json_encode(['status' => 'error', 'message' => 'Payment reference is required for this payment method']);
        exit();
    }

    // Start database transaction
    getDB()->beginTransaction();

    // Generate invoice number
    $invoice_no = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Insert order
    $order_sql = "INSERT INTO orders (customer_id, invoice_no, order_status, total_amount, delivery_address, delivery_phone, special_instructions) VALUES (?, ?, 'pending', ?, ?, ?, ?)";
    $order_result = executeQuery($order_sql, [
        $customer_id,
        $invoice_no,
        $total_amount,
        $delivery_address,
        $delivery_phone,
        $special_instructions
    ]);

    if (!$order_result) {
        throw new Exception('Failed to create order');
    }

    $order_id = getDB()->lastInsertId();

    // Insert order details
    foreach ($cart_data as $item) {
        $order_detail_sql = "INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)";
        $order_detail_result = executeQuery($order_detail_sql, [
            $order_id,
            $item['id'],
            $item['quantity']
        ]);

        if (!$order_detail_result) {
            throw new Exception('Failed to create order details');
        }
    }

    // Handle receipt upload
    $receipt_image_path = null;
    if (isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/receipts/';
        
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

    // Insert payment record
    $payment_sql = "INSERT INTO payment (customer_id, order_id, amt, payment_method, payment_status, payment_reference, receipt_image) VALUES (?, ?, ?, ?, 'pending', ?, ?)";
    $payment_result = executeQuery($payment_sql, [
        $customer_id,
        $order_id,
        $total_amount,
        $payment_method,
        $payment_reference,
        $receipt_image_path
    ]);

    if (!$payment_result) {
        throw new Exception('Failed to create payment record');
    }

    // Commit transaction
    getDB()->commit();

    // Prepare success response
    $response = [
        'status' => 'success',
        'message' => 'Order placed successfully! Your order is pending admin approval.',
        'order_id' => $order_id,
        'invoice_no' => $invoice_no,
        'total_amount' => $total_amount
    ];

    // Add payment-specific message
    switch ($payment_method) {
        case 'mobile_money':
            $response['message'] .= ' Please wait for payment verification.';
            break;
        case 'bank_transfer':
            $response['message'] .= ' Please wait for payment verification.';
            break;
        case 'pos':
            $response['message'] .= ' You can pay with card when your order is ready.';
            break;
        case 'cash':
            $response['message'] .= ' You can pay with cash when your order is delivered.';
            break;
    }

    echo json_encode($response);

} catch (Exception $e) {
    // Rollback transaction on error
    if (getDB()->inTransaction()) {
        getDB()->rollBack();
    }
    
    error_log("Order placement error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to place order: ' . $e->getMessage()
    ]);
}
?>
