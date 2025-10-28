<?php
// Get filter options action - replaces the old fetch action files
require_once '../src/settings/core.php';
require_once 'product_actions.php';

// Check if user is logged in and is admin (for admin pages)
$is_admin_request = isset($_GET['admin']) && $_GET['admin'] === 'true';

if ($is_admin_request && (!is_user_logged_in() || !is_user_admin())) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

try {
    // Get filter options (categories and brands)
    $result = getFilterOptions();
    
    if ($result['status'] === 'success') {
        echo json_encode($result);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch filter options'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch filter options: ' . $e->getMessage()
    ]);
}
?>
