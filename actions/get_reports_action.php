<?php
// Get reports action
require_once '../src/settings/core.php';

// Check if user is logged in and is admin
if (!is_user_logged_in() || !is_user_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Get parameters
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'sales';
$time_period = isset($_GET['time_period']) ? $_GET['time_period'] : 'month';
$custom_from = isset($_GET['custom_from']) ? $_GET['custom_from'] : '';
$custom_to = isset($_GET['custom_to']) ? $_GET['custom_to'] : '';

try {
    // For demo purposes, we'll create sample report data
    // In a real application, you would query the database based on parameters
    
    $metrics = [
        'total_revenue' => 12547.50,
        'total_orders' => 156,
        'total_customers' => 89,
        'total_products' => 24
    ];
    
    $charts = [
        'sales' => [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [1200, 1900, 3000, 5000, 2000, 3000]
        ],
        'orders' => [
            'labels' => ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
            'data' => [12, 8, 15, 118, 3]
        ],
        'products' => [
            'labels' => ['Jollof Rice', 'Fried Plantain', 'Chicken Stew', 'Beef Curry', 'Fish Soup'],
            'data' => [45, 38, 32, 28, 15]
        ],
        'customers' => [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [5, 8, 12, 15, 18, 22]
        ]
    ];
    
    $table = [
        ['date' => '2024-01-15', 'orders' => 5, 'revenue' => 245.50, 'customers' => 3],
        ['date' => '2024-01-14', 'orders' => 8, 'revenue' => 389.75, 'customers' => 5],
        ['date' => '2024-01-13', 'orders' => 12, 'revenue' => 567.25, 'customers' => 8],
        ['date' => '2024-01-12', 'orders' => 6, 'revenue' => 298.50, 'customers' => 4],
        ['date' => '2024-01-11', 'orders' => 9, 'revenue' => 445.80, 'customers' => 6],
        ['date' => '2024-01-10', 'orders' => 7, 'revenue' => 334.20, 'customers' => 5],
        ['date' => '2024-01-09', 'orders' => 11, 'revenue' => 512.40, 'customers' => 7]
    ];
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'metrics' => $metrics,
            'charts' => $charts,
            'table' => $table
        ],
        'message' => 'Report data generated successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to generate report: ' . $e->getMessage()
    ]);
}
?>
