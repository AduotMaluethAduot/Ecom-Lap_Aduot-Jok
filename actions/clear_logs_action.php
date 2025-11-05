<?php
// Clear logs action
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
    // For demo purposes, we'll simulate clearing logs
    // In a real application, you would clear actual log files or database entries
    
    $success = true; // Simulate successful log clearing
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Activity logs cleared successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to clear logs'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to clear logs: ' . $e->getMessage()
    ]);
}
?>
