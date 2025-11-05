<?php
// Get user statistics for admin dashboard
require_once '../src/settings/core.php';

// Check if user is logged in and is admin
if (!is_user_logged_in() || !is_user_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

try {
    // Get total users
    $total_users = fetchOne("SELECT COUNT(*) as count FROM customer")['count'];
    
    // Get total admins
    $total_admins = fetchOne("SELECT COUNT(*) as count FROM customer WHERE user_role = 'admin'")['count'];
    
    // Get total customers
    $total_customers = fetchOne("SELECT COUNT(*) as count FROM customer WHERE user_role = 'customer'")['count'];
    
    // Get total restaurant owners
    $total_owners = fetchOne("SELECT COUNT(*) as count FROM customer WHERE user_role = 'owner'")['count'];
    
    // Get total orders (if orders table exists)
    $total_orders = 0;
    try {
        $total_orders = fetchOne("SELECT COUNT(*) as count FROM orders")['count'];
    } catch (Exception $e) {
        // Orders table might not exist yet
    }
    
    // Get recent users (last 7 days)
    $recent_users = fetchOne("SELECT COUNT(*) as count FROM customer WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")['count'];
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'total_users' => (int)$total_users,
            'total_admins' => (int)$total_admins,
            'total_customers' => (int)$total_customers,
            'total_owners' => (int)$total_owners,
            'total_orders' => (int)$total_orders,
            'recent_users' => (int)$recent_users
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to load statistics: ' . $e->getMessage()
    ]);
}
?>
