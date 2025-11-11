<?php
// Get Cart Count Action
require_once '../src/settings/core.php';
require_once '../controllers/cart_controller.php';

try {
    if (is_user_logged_in()) {
        $cart_count = get_cart_item_count_ctr();
        echo json_encode([
            'status' => 'success',
            'cart_count' => $cart_count
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'cart_count' => 0
        ]);
    }
} catch (Exception $e) {
    error_log("Get cart count error: " . $e->getMessage());
    echo json_encode([
        'status' => 'success',
        'cart_count' => 0
    ]);
}
?>

