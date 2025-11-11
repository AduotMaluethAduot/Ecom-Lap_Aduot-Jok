<?php
// Remove from Cart Action
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

// Get product ID
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if (empty($product_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
    exit();
}

try {
    // Remove from cart
    $result = remove_from_cart_ctr($product_id);
    
    if ($result) {
        // Get updated cart count and total
        $cart_count = get_cart_item_count_ctr();
        $cart_total = get_cart_total_ctr();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Product removed from cart successfully',
            'cart_count' => $cart_count,
            'cart_total' => $cart_total
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to remove product from cart'
        ]);
    }
} catch (Exception $e) {
    error_log("Remove from cart error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while removing product from cart'
    ]);
}
?>

