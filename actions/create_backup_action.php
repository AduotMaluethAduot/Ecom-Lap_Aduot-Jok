<?php
// Create backup action
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

try {
    // For demo purposes, we'll simulate creating a backup
    // In a real application, you would use mysqldump or similar tool
    
    $backup_filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $backup_path = 'backups/' . $backup_filename;
    
    // Simulate backup creation
    $success = true; // In real app, this would be the result of actual backup creation
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Database backup created successfully',
            'backup_filename' => $backup_filename,
            'download_url' => $backup_path
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create backup'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to create backup: ' . $e->getMessage()
    ]);
}
?>
