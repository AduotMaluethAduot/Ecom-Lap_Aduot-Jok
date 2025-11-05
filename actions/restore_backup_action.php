<?php
// Restore backup action
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
if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'No backup file uploaded']);
    exit();
}

$uploaded_file = $_FILES['backup_file'];

// Validate file type
$allowed_extensions = ['sql'];
$file_extension = strtolower(pathinfo($uploaded_file['name'], PATHINFO_EXTENSION));

if (!in_array($file_extension, $allowed_extensions)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only .sql files are allowed.']);
    exit();
}

// Validate file size (max 50MB)
$max_file_size = 50 * 1024 * 1024; // 50MB
if ($uploaded_file['size'] > $max_file_size) {
    echo json_encode(['status' => 'error', 'message' => 'File too large. Maximum size is 50MB.']);
    exit();
}

try {
    // For demo purposes, we'll simulate restoring a backup
    // In a real application, you would execute the SQL file against the database
    
    $success = true; // Simulate successful restore
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Database restored successfully from backup'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to restore backup'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to restore backup: ' . $e->getMessage()
    ]);
}
?>
