<?php
// Clear cache action
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
    // For demo purposes, we'll simulate clearing cache
    // In a real application, you would clear actual cache files or entries
    
    $success = true; // Simulate successful cache clearing
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Cache cleared successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to clear cache'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to clear cache: ' . $e->getMessage()
    ]);
}
?>
