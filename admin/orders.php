<?php
// Orders Management Page - requires admin privileges
require_once '../src/settings/core.php';
require_once '../database/database.php';

// Require admin access
require_admin();

// Check session validity
if (!is_session_valid()) {
    logout_user();
    header("Location: login/login.php?message=session_expired");
    exit();
}

// Get admin user information
$admin_name = get_user_name();
$admin_email = get_user_email();
$admin_id = get_user_id();

// Get orders with payment information
$orders = [];
try {
    $orders = fetchAll("
        SELECT o.*, c.customer_name, c.customer_email, c.customer_contact,
               p.payment_method, p.payment_status, p.payment_reference, p.receipt_image,
               p.admin_verified, p.verified_by, p.verified_at, p.notes as payment_notes,
               COUNT(od.id) as item_count
        FROM orders o 
        LEFT JOIN customer c ON o.customer_id = c.customer_id
        LEFT JOIN payment p ON o.order_id = p.order_id
        LEFT JOIN orderdetails od ON o.order_id = od.order_id
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
        LIMIT 50
    ");
} catch (Exception $e) {
    error_log("Error fetching orders: " . $e->getMessage());
    $orders = [];
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $order_id = $_POST['order_id'] ?? '';
    $new_status = $_POST['status'] ?? '';
    $admin_notes = $_POST['admin_notes'] ?? '';
    
    if ($order_id && $new_status) {
        try {
            $result = executeQuery("UPDATE orders SET order_status = ?, admin_notes = ?, approved_by = ?, approved_at = NOW() WHERE order_id = ?", 
                [$new_status, $admin_notes, $admin_id, $order_id]);
            if ($result) {
                $success_message = "Order status updated successfully!";
                // Refresh orders
                $orders = fetchAll("
                    SELECT o.*, c.customer_name, c.customer_email, c.customer_contact,
                           p.payment_method, p.payment_status, p.payment_reference, p.receipt_image,
                           p.admin_verified, p.verified_by, p.verified_at, p.notes as payment_notes,
                           COUNT(od.id) as item_count
                    FROM orders o 
                    LEFT JOIN customer c ON o.customer_id = c.customer_id
                    LEFT JOIN payment p ON o.order_id = p.order_id
                    LEFT JOIN orderdetails od ON o.order_id = od.order_id
                    GROUP BY o.order_id
                    ORDER BY o.order_date DESC
                    LIMIT 50
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

// Handle payment verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_payment'])) {
    $order_id = $_POST['order_id'] ?? '';
    $payment_status = $_POST['payment_status'] ?? '';
    $payment_notes = $_POST['payment_notes'] ?? '';
    
    if ($order_id && $payment_status) {
        try {
            $result = executeQuery("UPDATE payment SET payment_status = ?, admin_verified = 1, verified_by = ?, verified_at = NOW(), notes = ? WHERE order_id = ?", 
                [$payment_status, $admin_id, $payment_notes, $order_id]);
            if ($result) {
                $success_message = "Payment verification updated successfully!";
                // Refresh orders
                $orders = fetchAll("
                    SELECT o.*, c.customer_name, c.customer_email, c.customer_contact,
                           p.payment_method, p.payment_status, p.payment_reference, p.receipt_image,
                           p.admin_verified, p.verified_by, p.verified_at, p.notes as payment_notes,
                           COUNT(od.id) as item_count
                    FROM orders o 
                    LEFT JOIN customer c ON o.customer_id = c.customer_id
                    LEFT JOIN payment p ON o.order_id = p.order_id
                    LEFT JOIN orderdetails od ON o.order_id = od.order_id
                    GROUP BY o.order_id
                    ORDER BY o.order_date DESC
                    LIMIT 50
                ");
            } else {
                $error_message = "Failed to update payment verification.";
            }
        } catch (Exception $e) {
            $error_message = "An error occurred while updating payment verification.";
            error_log("Payment verification error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="public/css/admin.css" rel="stylesheet">
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-shopping-bag me-3"></i>
                        Orders Management
                    </h1>
                    <p class="mb-0 mt-2 opacity-75">Manage customer orders</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="dashboard.php" class="btn btn-light me-2">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="../index.php" class="btn btn-light me-2">
                        <i class="fas fa-home"></i> Back to Site
                    </a>
                    <a href="../login/logout.php" class="btn btn-outline-light">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Order Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-shopping-cart text-primary"></i>
                        <div><?php echo count($orders); ?></div>
                    </div>
                    <div>Total Orders</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-clock text-warning"></i>
                        <div><?php echo count(array_filter($orders, function($o) { return $o['order_status'] === 'pending'; })); ?></div>
                    </div>
                    <div>Pending Orders</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-credit-card text-info"></i>
                        <div><?php echo count(array_filter($orders, function($o) { return $o['payment_status'] === 'pending'; })); ?></div>
                    </div>
                    <div>Pending Payments</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-dollar-sign text-success"></i>
                        <div>$<?php echo number_format(array_sum(array_column($orders, 'total_amount')), 2); ?></div>
                    </div>
                    <div>Total Revenue</div>
                </div>
            </div>
        </div>

        <!-- Order Filters -->
        <div class="admin-card mb-4">
            <h4><i class="fas fa-filter me-2"></i>Filter Orders</h4>
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="dateFrom" placeholder="From Date">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="dateTo" placeholder="To Date">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" onclick="filterOrders()">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><i class="fas fa-list me-2"></i>Orders List</h4>
                <button class="btn btn-success" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    <i class="fas fa-shopping-cart" style="font-size: 2rem;"></i>
                                    <h5 class="mt-3">No orders found</h5>
                                    <p>There are no orders in the system yet.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo $order['invoice_no']; ?></strong>
                                        <br>
                                        <small class="text-muted">ID: <?php echo $order['order_id']; ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['customer_contact']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                        <br>
                                        <small class="text-muted"><?php echo date('h:i A', strtotime($order['order_date'])); ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $payment_methods = [
                                            'mobile_money' => '<i class="fas fa-mobile-alt text-primary"></i> Mobile Money',
                                            'bank_transfer' => '<i class="fas fa-university text-success"></i> Bank Transfer',
                                            'pos' => '<i class="fas fa-credit-card text-warning"></i> POS',
                                            'cash' => '<i class="fas fa-money-bill-wave text-info"></i> Cash'
                                        ];
                                        echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $payment_status_colors = [
                                            'pending' => 'warning',
                                            'verified' => 'success',
                                            'failed' => 'danger'
                                        ];
                                        $color = $payment_status_colors[$order['payment_status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $color; ?>">
                                            <?php echo ucfirst($order['payment_status']); ?>
                                        </span>
                                        <?php if ($order['payment_reference']): ?>
                                            <br>
                                            <small class="text-muted">Ref: <?php echo htmlspecialchars($order['payment_reference']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $order_status_colors = [
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $color = $order_status_colors[$order['order_status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $color; ?>">
                                            <?php echo ucfirst($order['order_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo $order['item_count']; ?> item(s)</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="updateOrderStatus(<?php echo $order['order_id']; ?>)"
                                                    title="Update Status">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($order['payment_status'] === 'pending' && in_array($order['payment_method'], ['mobile_money', 'bank_transfer'])): ?>
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="verifyPayment(<?php echo $order['order_id']; ?>)"
                                                        title="Verify Payment">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                            </td>
                        </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-shopping-bag me-2"></i>Order Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Order Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Update Order Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" id="updateOrderId">
                        
                        <div class="mb-3">
                            <label for="orderStatus" class="form-label">Order Status</label>
                            <select class="form-select" name="status" id="orderStatus" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="adminNotes" class="form-label">Admin Notes</label>
                            <textarea class="form-control" name="admin_notes" id="adminNotes" rows="3" placeholder="Add any notes about this order..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Verify Payment Modal -->
    <div class="modal fade" id="verifyPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>Verify Payment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="verify_payment" value="1">
                        <input type="hidden" name="order_id" id="verifyOrderId">
                        
                        <div class="mb-3">
                            <label for="paymentStatus" class="form-label">Payment Status</label>
                            <select class="form-select" name="payment_status" id="paymentStatus" required>
                                <option value="verified">Verified</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="paymentNotes" class="form-label">Verification Notes</label>
                            <textarea class="form-control" name="payment_notes" id="paymentNotes" rows="3" placeholder="Add verification notes..."></textarea>
                        </div>
                        
                        <div id="receiptPreview" class="mb-3" style="display: none;">
                            <label class="form-label">Payment Receipt</label>
                            <div id="receiptImageContainer"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Verify Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // View order details
        function viewOrderDetails(orderId) {
            // For now, show a simple alert. In a real application, you would fetch order details via AJAX
            Swal.fire({
                title: 'Order Details',
                text: 'Order ID: ' + orderId + '. This would show detailed order information including items, customer details, and payment information.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        // Update order status
        function updateOrderStatus(orderId) {
            document.getElementById('updateOrderId').value = orderId;
            const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
            modal.show();
        }

        // Verify payment
        function verifyPayment(orderId) {
            document.getElementById('verifyOrderId').value = orderId;
            const modal = new bootstrap.Modal(document.getElementById('verifyPaymentModal'));
            modal.show();
        }

        // Auto-dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    </script>
</body>
</html>
