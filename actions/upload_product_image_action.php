<?php
// Upload product image
require_once '../src/settings/core.php';

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

// Check if file was uploaded
if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded or upload error']);
    exit();
}

$file = $_FILES['product_image'];
$user_id = get_user_id();
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

// Validate file
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
$max_size = 5 * 1024 * 1024; // 5MB

// Check file size
if ($file['size'] > $max_size) {
    echo json_encode(['status' => 'error', 'message' => 'File size too large. Maximum 5MB allowed.']);
    exit();
}

// Check file type
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($file_extension, $allowed_types)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.']);
    exit();
}

// Create upload directory structure
$upload_dir = '../../uploads/';
$user_dir = $upload_dir . 'u' . $user_id . '/';
$product_dir = $user_dir . 'p' . $product_id . '/';

// Ensure uploads directory exists
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
        exit();
    }
}

// Ensure user directory exists
if (!is_dir($user_dir)) {
    if (!mkdir($user_dir, 0755, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create user directory']);
        exit();
    }
}

// Ensure product directory exists
if (!is_dir($product_dir)) {
    if (!mkdir($product_dir, 0755, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create product directory']);
        exit();
    }
}

// Generate unique filename
$filename = 'image_' . time() . '_' . uniqid() . '.' . $file_extension;
$file_path = $product_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    // Return relative path from uploads directory
    $relative_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $filename;
    echo json_encode([
        'status' => 'success', 
        'message' => 'Image uploaded successfully',
        'file_path' => $relative_path
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
}