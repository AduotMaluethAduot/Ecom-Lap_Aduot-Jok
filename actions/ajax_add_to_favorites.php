<?php
// AJAX add to favorites
require_once '../src/settings/core.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to add items to favorites']);
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
    // For demo purposes, we'll simulate adding to favorites
    // In a real application, you would add to the favorites table
    
    $success = true; // Simulate successful addition
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Product added to favorites successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add product to favorites'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to add product to favorites'
    ]);
}
?>
