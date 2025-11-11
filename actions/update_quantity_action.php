<?php
// Update Quantity Action
require_once '../src/settings/core.php';
require_once '../controllers/cart_controller.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to manage your cart']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Get product ID and quantity
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if (empty($product_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
    exit();
}

if ($quantity <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Quantity must be greater than 0']);
    exit();
}

try {
    // Update cart item quantity
    $result = update_cart_item_ctr($product_id, $quantity);
    
    if ($result) {
        // Get updated cart count and total
        $cart_count = get_cart_item_count_ctr();
        $cart_total = get_cart_total_ctr();
        
        // Get cart items to calculate item subtotal
        $cart_items = get_user_cart_ctr();
        $item_subtotal = 0;
        foreach ($cart_items as $item) {
            if ($item['product_id'] == $product_id) {
                $item_subtotal = $item['product_price'] * $item['qty'];
                break;
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Cart quantity updated successfully',
            'cart_count' => $cart_count,
            'cart_total' => $cart_total,
            'item_subtotal' => $item_subtotal
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update cart quantity'
        ]);
    }
} catch (Exception $e) {
    error_log("Update quantity error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while updating cart quantity'
    ]);
}
?>

