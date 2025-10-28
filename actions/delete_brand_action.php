<?php
// Delete a brand
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

// Validate input
if (empty($brand_id) || $brand_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid brand ID']);
    exit();
}

// Delete brand
$result = delete_brand_ctr($brand_id);

if ($result !== false) {
    echo json_encode(['status' => 'success', 'message' => 'Brand deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete brand']);
}