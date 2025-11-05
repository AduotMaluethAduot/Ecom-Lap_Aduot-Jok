<?php
// Update a category
require_once '../src/settings/core.php';
require_once '../controllers/category_controller.php';

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
$cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
$cat_name = isset($_POST['cat_name']) ? trim($_POST['cat_name']) : '';

// Validate inputs
if (empty($cat_id) || empty($cat_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Category ID and name are required']);
    exit();
}

if (strlen($cat_name) < 2) {
    echo json_encode(['status' => 'error', 'message' => 'Category name must be at least 2 characters long']);
    exit();
}

// Update category
$result = update_category_ctr($cat_id, $cat_name);

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Category updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update category. Category may already exist.']);
}