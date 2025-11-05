<?php
// Get order statistics action
require_once '../src/settings/core.php';

// Check if user is logged in and is admin
if (!is_user_logged_in() || !is_user_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

try {
    // For demo purposes, we'll create sample statistics
    // In a real application, you would query the database
    $stats = [
        'total_orders' => 156,
        'pending_orders' => 12,
        'processing_orders' => 8,
        'shipped_orders' => 15,
        'delivered_orders' => 118,
        'cancelled_orders' => 3,
        'total_revenue' => 12547.50,
        'average_order_value' => 80.43,
        'orders_today' => 5,
        'orders_this_week' => 23,
        'orders_this_month' => 89
    ];
    
    echo json_encode([
        'status' => 'success',
        'data' => $stats,
        'message' => 'Order statistics fetched successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch order statistics: ' . $e->getMessage()
    ]);
}
?>
