<?php
// Update a product
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

// Get POST data
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$product_data = [
    'product_title' => isset($_POST['product_title']) ? trim($_POST['product_title']) : '',
    'product_price' => isset($_POST['product_price']) ? $_POST['product_price'] : '',
    'product_cat' => isset($_POST['product_cat']) ? (int)$_POST['product_cat'] : 0,
    'product_brand' => isset($_POST['product_brand']) ? (int)$_POST['product_brand'] : 0,
    'product_desc' => isset($_POST['product_desc']) ? trim($_POST['product_desc']) : '',
    'product_image' => isset($_POST['product_image']) ? trim($_POST['product_image']) : '',
    'product_keywords' => isset($_POST['product_keywords']) ? trim($_POST['product_keywords']) : ''
];

// Validate input
if (empty($product_id) || $product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
    exit();
}

if (empty($product_data['product_title'])) {
    echo json_encode(['status' => 'error', 'message' => 'Product title is required']);
    exit();
}

if (empty($product_data['product_price']) || $product_data['product_price'] <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Valid product price is required']);
    exit();
}

if (empty($product_data['product_cat']) || $product_data['product_cat'] <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a category']);
    exit();
}

if (empty($product_data['product_brand']) || $product_data['product_brand'] <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a brand']);
    exit();
}

if (strlen($product_data['product_title']) < 2) {
    echo json_encode(['status' => 'error', 'message' => 'Product title must be at least 2 characters long']);
    exit();
}

// Update product
$result = update_product_ctr($product_id, $product_data);

if ($result !== false) {
    echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update product. Please check your input.']);
}