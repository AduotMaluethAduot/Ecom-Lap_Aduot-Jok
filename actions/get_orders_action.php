<?php
// Get orders action
require_once '../src/settings/core.php';
require_once '../controllers/customer_controller.php';

// Check if user is logged in and is admin
if (!is_user_logged_in() || !is_user_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

try {
    // For demo purposes, we'll create sample order data
    // In a real application, you would query the orders table
    $orders = [
        [
            'order_id' => 1,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+1-555-0123',
            'order_date' => '2024-01-15 10:30:00',
            'status' => 'pending',
            'total_amount' => 45.99
        ],
        [
            'order_id' => 2,
            'customer_name' => 'Jane Smith',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '+1-555-0124',
            'order_date' => '2024-01-14 14:20:00',
            'status' => 'processing',
            'total_amount' => 32.50
        ],
        [
            'order_id' => 3,
            'customer_name' => 'Mike Johnson',
            'customer_email' => 'mike@example.com',
            'customer_phone' => '+1-555-0125',
            'order_date' => '2024-01-13 09:15:00',
            'status' => 'delivered',
            'total_amount' => 67.25
        ],
        [
            'order_id' => 4,
            'customer_name' => 'Sarah Wilson',
            'customer_email' => 'sarah@example.com',
            'customer_phone' => '+1-555-0126',
            'order_date' => '2024-01-12 16:45:00',
            'status' => 'shipped',
            'total_amount' => 28.75
        ],
        [
            'order_id' => 5,
            'customer_name' => 'David Brown',
            'customer_email' => 'david@example.com',
            'customer_phone' => '+1-555-0127',
            'order_date' => '2024-01-11 11:30:00',
            'status' => 'cancelled',
            'total_amount' => 15.99
        ]
    ];
    
    // Apply filters
    if (!empty($status)) {
        $orders = array_filter($orders, function($order) use ($status) {
            return $order['status'] === $status;
        });
    }
    
    if (!empty($date_from)) {
        $orders = array_filter($orders, function($order) use ($date_from) {
            return strtotime($order['order_date']) >= strtotime($date_from);
        });
    }
    
    if (!empty($date_to)) {
        $orders = array_filter($orders, function($order) use ($date_to) {
            return strtotime($order['order_date']) <= strtotime($date_to . ' 23:59:59');
        });
    }
    
    // Re-index array
    $orders = array_values($orders);
    
    echo json_encode([
        'status' => 'success',
        'data' => $orders,
        'message' => 'Orders fetched successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch orders: ' . $e->getMessage()
    ]);
}
?>
