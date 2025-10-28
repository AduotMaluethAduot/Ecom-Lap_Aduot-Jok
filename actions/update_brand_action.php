<?php
// Update a brand
require_once '../src/settings/core.php';
require_once '../controllers/brand_controller.php';

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
$brand_id = isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : 0;
$brand_name = isset($_POST['brand_name']) ? trim($_POST['brand_name']) : '';
$cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;

// Validate input
if (empty($brand_id) || $brand_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid brand ID']);
    exit();
}

if (empty($brand_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Brand name is required']);
    exit();
}

if (empty($cat_id) || $cat_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a category']);
    exit();
}

if (strlen($brand_name) < 2) {
    echo json_encode(['status' => 'error', 'message' => 'Brand name must be at least 2 characters long']);
    exit();
}

// Update brand
$result = update_brand_ctr($brand_id, $brand_name, $cat_id);

if ($result !== false) {
    echo json_encode(['status' => 'success', 'message' => 'Brand updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update brand. Brand may already exist in this category.']);
}