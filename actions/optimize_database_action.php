<?php
// Optimize database action
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
    // For demo purposes, we'll simulate database optimization
    // In a real application, you would run OPTIMIZE TABLE commands
    
    $success = true; // Simulate successful optimization
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Database optimized successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to optimize database'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to optimize database: ' . $e->getMessage()
    ]);
}
?>
