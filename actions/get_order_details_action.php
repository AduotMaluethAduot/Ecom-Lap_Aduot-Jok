<?php
// Get order details action
require_once '../src/settings/core.php';

// Check if user is logged in and is admin
if (!is_user_logged_in() || !is_user_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Get order ID
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (empty($order_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Order ID is required']);
    exit();
}

try {
    // For demo purposes, we'll create sample order details
    // In a real application, you would query the database
    $order_details = [
        'order_id' => $order_id,
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '+1-555-0123',
        'order_date' => '2024-01-15 10:30:00',
        'status' => 'pending',
        'total_amount' => 45.99,
        'shipping_address' => '123 Main St, City, State 12345',
        'billing_address' => '123 Main St, City, State 12345',
        'payment_method' => 'Credit Card',
        'items' => [
            [
                'product_id' => 1,
                'product_name' => 'Jollof Rice',
                'quantity' => 2,
                'price' => 12.99,
                'total' => 25.98
            ],
            [
                'product_id' => 2,
                'product_name' => 'Fried Plantain',
                'quantity' => 1,
                'price' => 8.99,
                'total' => 8.99
            ],
            [
                'product_id' => 3,
                'product_name' => 'Chicken Stew',
                'quantity' => 1,
                'price' => 11.02,
                'total' => 11.02
            ]
        ]
    ];
    
    echo json_encode([
        'status' => 'success',
        'data' => $order_details,
        'message' => 'Order details fetched successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch order details: ' . $e->getMessage()
    ]);
}
?>
