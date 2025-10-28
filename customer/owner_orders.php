<?php
// Owner Orders Management page
require_once '../src/settings/core.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    header('Location: ../login/login.php');
    exit();
}

// Check if user is admin (redirect to admin dashboard)
if (is_user_admin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

// Get owner information
$owner_id = get_user_id();
$owner_name = get_user_name();
$owner_email = get_user_email();

// Get orders from database
require_once '../database/database.php';

// For demo purposes, we'll simulate some orders
// In a real application, you would query the orders table
$orders = [];
try {
    $orders = fetchAll("
        SELECT o.*, c.customer_name, c.customer_email, c.customer_contact,
               COUNT(od.id) as item_count,
               SUM(p.product_price * od.qty) as total_amount
        FROM orders o 
        LEFT JOIN customer c ON o.customer_id = c.customer_id
        LEFT JOIN orderdetails od ON o.order_id = od.order_id
        LEFT JOIN products p ON od.product_id = p.product_id
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
        LIMIT 20
    ");
} catch (Exception $e) {
    error_log("Error fetching orders: " . $e->getMessage());
    $orders = [];
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $order_id = $_POST['order_id'] ?? '';
    $new_status = $_POST['status'] ?? '';
    
    if ($order_id && $new_status) {
        try {
            $result = executeQuery("UPDATE orders SET order_status = ? WHERE order_id = ?", [$new_status, $order_id]);
            if ($result) {
                $success_message = "Order status updated successfully!";
                // Refresh orders
                $orders = fetchAll("
                    SELECT o.*, c.customer_name, c.customer_email, c.customer_contact,
                           COUNT(od.id) as item_count,
                           SUM(p.product_price * od.qty) as total_amount
                    FROM orders o 
                    LEFT JOIN customer c ON o.customer_id = c.customer_id
                    LEFT JOIN orderdetails od ON o.order_id = od.order_id
                    LEFT JOIN products p ON od.product_id = p.product_id
                    GROUP BY o.order_id
                    ORDER BY o.order_date DESC
                    LIMIT 20
                ");
            } else {
                $error_message = "Failed to update order status.";
            }
        } catch (Exception $e) {
            $error_message = "An error occurred while updating the order.";
            error_log("Order update error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Taste of Africa</title>
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
        .btn-owner {
            background: linear-gradient(135deg, #38a169, #2f855a);
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .btn-owner:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(56, 161, 105, 0.4);
            color: white;
        }
        .btn-accept {
            background: linear-gradient(135deg, #38a169, #2f855a);
        }
        .btn-decline {
            background: linear-gradient(135deg, #e53e3e, #c53030);
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
            color: #38a169;
        }
        .filter-tabs {
            margin-bottom: 2rem;
        }
        .filter-tab {
            background: white;
            border: 2px solid #e9ecef;
            color: #6c757d;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .filter-tab.active {
            background: #38a169;
            border-color: #38a169;
            color: white;
        }
        .filter-tab:hover {
            border-color: #38a169;
            color: #38a169;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php" style="color: #38a169;">
                <i class="fas fa-store me-2"></i>Taste of Africa - Owner Portal
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="owner_dashboard.php">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="owner_menu.php">
                    <i class="fas fa-utensils me-1"></i>My Menu
                </a>
                <a class="nav-link active" href="owner_orders.php">
                    <i class="fas fa-shopping-bag me-1"></i>Orders
                </a>
                <a class="nav-link" href="owner_analytics.php">
                    <i class="fas fa-chart-bar me-1"></i>Analytics
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
                <i class="fas fa-shopping-bag me-2"></i>Order Management
            </h1>
            <p class="text-center text-muted mb-5">Track and manage incoming orders from customers</p>

            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

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
                            $pending_orders = array_filter($orders, function($order) {
                                return $order['order_status'] === 'pending';
                            });
                            echo count($pending_orders);
                            ?>
                        </div>
                        <div>Pending</div>
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
                            $<?php 
                            $total_revenue = array_sum(array_column($orders, 'total_amount'));
                            echo number_format($total_revenue, 0);
                            ?>
                        </div>
                        <div>Total Revenue</div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <div class="filter-tab active" data-status="all">
                    <i class="fas fa-th me-2"></i>All Orders
                </div>
                <div class="filter-tab" data-status="pending">
                    <i class="fas fa-clock me-2"></i>Pending
                </div>
                <div class="filter-tab" data-status="processing">
                    <i class="fas fa-cog me-2"></i>Processing
                </div>
                <div class="filter-tab" data-status="completed">
                    <i class="fas fa-check me-2"></i>Completed
                </div>
                <div class="filter-tab" data-status="cancelled">
                    <i class="fas fa-times me-2"></i>Cancelled
                </div>
            </div>

            <?php if (count($orders) > 0): ?>
                <div class="row" id="orders-container">
                    <?php foreach ($orders as $order): ?>
                        <div class="col-lg-6 order-item" data-status="<?php echo strtolower($order['order_status']); ?>">
                            <div class="order-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1">Order #<?php echo $order['invoice_no']; ?></h5>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?>
                                        </small>
                                    </div>
                                    <span class="order-status status-<?php echo strtolower($order['order_status']); ?>">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Customer</small>
                                        <div class="fw-bold"><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['customer_email'] ?? ''); ?></small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Items</small>
                                        <div class="fw-bold"><?php echo $order['item_count']; ?> item(s)</div>
                                        <small class="text-muted">Total: $<?php echo number_format($order['total_amount'], 2); ?></small>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button class="btn btn-owner btn-sm" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </button>
                                    
                                    <?php if ($order['order_status'] === 'pending'): ?>
                                        <button class="btn btn-owner btn-accept btn-sm" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'processing')">
                                            <i class="fas fa-check me-1"></i>Accept
                                        </button>
                                        <button class="btn btn-owner btn-decline btn-sm" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'cancelled')">
                                            <i class="fas fa-times me-1"></i>Decline
                                        </button>
                                    <?php elseif ($order['order_status'] === 'processing'): ?>
                                        <button class="btn btn-owner btn-sm" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'completed')">
                                            <i class="fas fa-check-double me-1"></i>Mark Complete
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3 text-muted">No orders yet</h3>
                    <p class="text-muted">Orders from customers will appear here once they start placing orders.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order Status Update Form (Hidden) -->
    <form id="statusUpdateForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="update_status">
        <input type="hidden" name="order_id" id="updateOrderId">
        <input type="hidden" name="status" id="updateStatus">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Filter functionality
        $('.filter-tab').on('click', function() {
            $('.filter-tab').removeClass('active');
            $(this).addClass('active');
            
            const status = $(this).data('status');
            
            if (status === 'all') {
                $('.order-item').show();
            } else {
                $('.order-item').hide();
                $(`.order-item[data-status="${status}"]`).show();
            }
        });

        // View order details
        function viewOrderDetails(orderId) {
            Swal.fire({
                title: 'Order Details',
                text: 'Order details will be displayed here',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        // Update order status
        function updateOrderStatus(orderId, newStatus) {
            const statusText = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            
            Swal.fire({
                title: `Update Order Status?`,
                text: `Change order status to "${statusText}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form
                    document.getElementById('updateOrderId').value = orderId;
                    document.getElementById('updateStatus').value = newStatus;
                    document.getElementById('statusUpdateForm').submit();
                }
            });
        }
    </script>
</body>
</html>
