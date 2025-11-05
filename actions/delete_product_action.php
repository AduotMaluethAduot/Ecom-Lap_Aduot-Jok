<?php
// Delete product action
require_once '../src/settings/core.php';
require_once '../controllers/product_controller.php';

// Check if user is logged in and is admin
if (!is_user_logged_in() || !is_user_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Get product ID
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

// Validate input
if (empty($product_id) || $product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
    exit();
}

// Delete product
$result = delete_product_ctr($product_id);

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete product']);
}
?>
