<?php
// Delete a category
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

// Validate input
if ($cat_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Category ID']);
    exit();
}

// Optional: CSRF protection (uncomment if implemented)
// if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
//     echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
//     exit();
// }

// Delete category
$result = delete_category_ctr($cat_id);

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete category']);
}