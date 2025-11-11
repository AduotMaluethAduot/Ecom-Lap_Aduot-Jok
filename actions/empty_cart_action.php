<?php
// Empty Cart Action
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

try {
    // Empty cart
    $result = empty_cart_ctr();
    
    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Cart emptied successfully',
            'cart_count' => 0,
            'cart_total' => 0
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to empty cart'
        ]);
    }
} catch (Exception $e) {
    error_log("Empty cart error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while emptying cart'
    ]);
}
?>

