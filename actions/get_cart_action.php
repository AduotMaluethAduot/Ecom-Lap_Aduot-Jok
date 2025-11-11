<?php
// Get Cart Action
require_once '../src/settings/core.php';
require_once '../controllers/cart_controller.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to view your cart']);
    exit();
}

try {
    // Get cart items
    $cart_items = get_user_cart_ctr();
    $cart_total = get_cart_total_ctr();
    
    if ($cart_items === false) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to retrieve cart items'
        ]);
        exit();
    }
    
    echo json_encode([
        'status' => 'success',
        'cart_items' => $cart_items,
        'cart_total' => $cart_total,
        'item_count' => count($cart_items)
    ]);
} catch (Exception $e) {
    error_log("Get cart error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while retrieving cart'
    ]);
}
?>

