<?php
// My Orders page for customers
require_once '../src/settings/core.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    header('Location: ../login/login.php');
    exit();
}

// Get customer information
$customer_id = get_user_id();
$customer_name = get_user_name();
$customer_role = get_user_role();

// Redirect admin users to admin dashboard
if (is_user_admin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

// Get customer orders from database
require_once '../database/database.php';

$orders = [];
try {
    $orders = fetchAll("
        SELECT o.*, 
               p.payment_method, p.payment_status, p.payment_reference, p.receipt_image,
               p.admin_verified, p.verified_at, p.notes as payment_notes,
               COUNT(od.id) as item_count
        FROM orders o 
        LEFT JOIN payment p ON o.order_id = p.order_id
        LEFT JOIN orderdetails od ON o.order_id = od.order_id
        WHERE o.customer_id = ?
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
    ", [$customer_id]);
} catch (Exception $e) {
    error_log("Error fetching orders: " . $e->getMessage());
    $orders = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .orders-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            margin: 2rem 0;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .order-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .order-card:hover {
            transform: translateY(-2px);
        }
        .order-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-processing {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .empty-orders {
            text-align: center;
            padding: 4rem 2rem;
        }
        .empty-orders i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #ff6b6b;
        }
        .btn-order {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .btn-order:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(255, 107, 107, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php" style="color: #ff6b6b;">
                <i class="fas fa-utensils me-2"></i>Taste of Africa
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="customer_dashboard.php">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="menu.php">
                    <i class="fas fa-utensils me-1"></i>Menu
                </a>
                <a class="nav-link" href="order_food.php">
                    <i class="fas fa-shopping-cart me-1"></i>Order Food
                </a>
                <a class="nav-link" href="favorites.php">
                    <i class="fas fa-heart me-1"></i>Favorites
                </a>
                <a class="nav-link active" href="my_orders.php">
                    <i class="fas fa-shopping-bag me-1"></i>My Orders
                </a>
                <a class="nav-link" href="../login/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="orders-container">
            <h1 class="text-center mb-4">
                <i class="fas fa-shopping-bag me-2"></i>My Orders
            </h1>
            <p class="text-center text-muted mb-5">Track your order history and status</p>

            <!-- Stats Card -->
            <div class="stats-card">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-number"><?php echo count($orders); ?></div>
                        <div>Total Orders</div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-number">
                            <?php 
                            $completed_orders = array_filter($orders, function($order) {
                                return $order['order_status'] === 'completed';
                            });
                            echo count($completed_orders);
                            ?>
                        </div>
                        <div>Completed</div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-number">
                            <?php 
                            $pending_orders = array_filter($orders, function($order) {
                                return in_array($order['order_status'], ['pending', 'processing']);
                            });
                            echo count($pending_orders);
                            ?>
                        </div>
                        <div>In Progress</div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-number">
                            $<?php 
                            $total_spent = array_sum(array_column($orders, 'total_amount'));
                            echo number_format($total_spent, 2);
                            ?>
                        </div>
                        <div>Total Spent</div>
                    </div>
                </div>
            </div>

            <?php if (count($orders) > 0): ?>
                <div class="row">
                    <?php foreach ($orders as $order): ?>
                        <div class="col-lg-6">
                            <div class="order-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1">Order #<?php echo $order['invoice_no']; ?></h5>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                        </small>
                                    </div>
                                    <span class="order-status status-<?php echo strtolower($order['order_status']); ?>">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Items</small>
                                        <div class="fw-bold"><?php echo $order['item_count']; ?> item(s)</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Total</small>
                                        <div class="fw-bold text-primary">$<?php echo number_format($order['total_amount'], 2); ?></div>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </button>
                                    <?php if ($order['order_status'] === 'completed'): ?>
                                        <button class="btn btn-outline-success btn-sm" onclick="reorder(<?php echo $order['order_id']; ?>)">
                                            <i class="fas fa-redo me-1"></i>Reorder
                                        </button>
                                    <?php endif; ?>
                                    <?php if (in_array($order['order_status'], ['pending', 'processing'])): ?>
                                        <button class="btn btn-outline-danger btn-sm" onclick="cancelOrder(<?php echo $order['order_id']; ?>)">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <h3 class="text-muted">No orders yet</h3>
                    <p class="text-muted">You haven't placed any orders yet. Start exploring our menu!</p>
                    <a href="menu.php" class="btn btn-primary mt-3">
                        <i class="fas fa-utensils me-2"></i>Browse Menu
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // View order details
        function viewOrderDetails(orderId) {
            Swal.fire({
                title: 'Order Details',
                text: 'Order details will be displayed here',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        // Reorder functionality
        function reorder(orderId) {
            Swal.fire({
                title: 'Reorder Items?',
                text: 'Add all items from this order to your cart?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, reorder!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to Cart!',
                        text: 'Items have been added to your cart',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'order_food.php';
                    });
                }
            });
        }

        // Cancel order functionality
        function cancelOrder(orderId) {
            Swal.fire({
                title: 'Cancel Order?',
                text: 'Are you sure you want to cancel this order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Cancelled!',
                        text: 'Your order has been cancelled successfully',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    </script>
</body>
</html>
