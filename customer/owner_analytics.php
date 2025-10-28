<?php
// Owner Analytics page
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

// Get analytics data from database
require_once '../database/database.php';

// Get basic statistics
$stats = [];
try {
    // Total products
    $total_products = fetchOne("SELECT COUNT(*) as count FROM products");
    $stats['total_products'] = $total_products['count'] ?? 0;
    
    // Total orders
    $total_orders = fetchOne("SELECT COUNT(*) as count FROM orders");
    $stats['total_orders'] = $total_orders['count'] ?? 0;
    
    // Total revenue
    $total_revenue = fetchOne("
        SELECT COALESCE(SUM(p.product_price * od.qty), 0) as total
        FROM orders o 
        LEFT JOIN orderdetails od ON o.order_id = od.order_id
        LEFT JOIN products p ON od.product_id = p.product_id
        WHERE o.order_status = 'completed'
    ");
    $stats['total_revenue'] = $total_revenue['total'] ?? 0;
    
    // Total customers
    $total_customers = fetchOne("SELECT COUNT(*) as count FROM customer WHERE user_role = 'customer'");
    $stats['total_customers'] = $total_customers['count'] ?? 0;
    
    // Recent orders (last 7 days)
    $recent_orders = fetchAll("
        SELECT DATE(order_date) as order_date, COUNT(*) as order_count, 
               COALESCE(SUM(p.product_price * od.qty), 0) as daily_revenue
        FROM orders o 
        LEFT JOIN orderdetails od ON o.order_id = od.order_id
        LEFT JOIN products p ON od.product_id = p.product_id
        WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(order_date)
        ORDER BY order_date DESC
    ");
    
    // Popular products
    $popular_products = fetchAll("
        SELECT p.product_title, p.product_price, SUM(od.qty) as total_sold,
               COALESCE(SUM(p.product_price * od.qty), 0) as revenue
        FROM orderdetails od
        LEFT JOIN products p ON od.product_id = p.product_id
        LEFT JOIN orders o ON od.order_id = o.order_id
        WHERE o.order_status = 'completed'
        GROUP BY p.product_id
        ORDER BY total_sold DESC
        LIMIT 5
    ");
    
    // Order status distribution
    $order_status = fetchAll("
        SELECT order_status, COUNT(*) as count
        FROM orders
        GROUP BY order_status
    ");
    
} catch (Exception $e) {
    error_log("Analytics error: " . $e->getMessage());
    $stats = ['total_products' => 0, 'total_orders' => 0, 'total_revenue' => 0, 'total_customers' => 0];
    $recent_orders = [];
    $popular_products = [];
    $order_status = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics & Reports - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .analytics-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            margin: 2rem 0;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 2rem;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #38a169;
            margin-bottom: 0.5rem;
        }
        .chart-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        .table-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        .btn-owner {
            background: linear-gradient(135deg, #38a169, #2f855a);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .btn-owner:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(56, 161, 105, 0.4);
            color: white;
        }
        .metric-icon {
            font-size: 2rem;
            color: #38a169;
            margin-bottom: 1rem;
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
                <a class="nav-link" href="owner_orders.php">
                    <i class="fas fa-shopping-bag me-1"></i>Orders
                </a>
                <a class="nav-link active" href="owner_analytics.php">
                    <i class="fas fa-chart-bar me-1"></i>Analytics
                </a>
                <a class="nav-link" href="../login/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="analytics-container">
            <h1 class="text-center mb-4">
                <i class="fas fa-chart-line me-2"></i>Analytics & Reports
            </h1>
            <p class="text-center text-muted mb-5">Track your restaurant's performance and growth</p>

            <!-- Key Metrics -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="metric-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['total_products']; ?></div>
                        <div>Total Menu Items</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="metric-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['total_orders']; ?></div>
                        <div>Total Orders</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="metric-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-number">$<?php echo number_format($stats['total_revenue'], 0); ?></div>
                        <div>Total Revenue</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="metric-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['total_customers']; ?></div>
                        <div>Total Customers</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Revenue Chart -->
                <div class="col-lg-8">
                    <div class="chart-card">
                        <h4 class="mb-3">
                            <i class="fas fa-chart-line me-2"></i>Revenue Trend (Last 7 Days)
                        </h4>
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Order Status Distribution -->
                <div class="col-lg-4">
                    <div class="chart-card">
                        <h4 class="mb-3">
                            <i class="fas fa-pie-chart me-2"></i>Order Status
                        </h4>
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Popular Products -->
                <div class="col-lg-8">
                    <div class="table-card">
                        <h4 class="mb-3">
                            <i class="fas fa-star me-2"></i>Top Selling Products
                        </h4>
                        <?php if (count($popular_products) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Units Sold</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($popular_products as $product): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($product['product_title']); ?></td>
                                                <td>$<?php echo number_format($product['product_price'], 2); ?></td>
                                                <td><?php echo $product['total_sold']; ?></td>
                                                <td>$<?php echo number_format($product['revenue'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-chart-bar text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">No sales data yet</h5>
                                <p class="text-muted">Sales data will appear here once customers start ordering.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-lg-4">
                    <div class="table-card">
                        <h4 class="mb-3">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h4>
                        <div class="d-grid gap-2">
                            <a href="owner_menu.php" class="btn btn-owner">
                                <i class="fas fa-utensils me-2"></i>Manage Menu
                            </a>
                            <a href="owner_orders.php" class="btn btn-owner">
                                <i class="fas fa-shopping-bag me-2"></i>View Orders
                            </a>
                            <a href="../admin/product.php" class="btn btn-owner">
                                <i class="fas fa-plus me-2"></i>Add New Dish
                            </a>
                            <button class="btn btn-outline-primary" onclick="exportReport()">
                                <i class="fas fa-download me-2"></i>Export Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueData = <?php echo json_encode($recent_orders); ?>;
        
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueData.map(item => new Date(item.order_date).toLocaleDateString()).reverse(),
                datasets: [{
                    label: 'Daily Revenue',
                    data: revenueData.map(item => parseFloat(item.daily_revenue)).reverse(),
                    borderColor: '#38a169',
                    backgroundColor: 'rgba(56, 161, 105, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = <?php echo json_encode($order_status); ?>;
        
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.map(item => item.order_status.charAt(0).toUpperCase() + item.order_status.slice(1)),
                datasets: [{
                    data: statusData.map(item => item.count),
                    backgroundColor: [
                        '#ffc107',
                        '#17a2b8',
                        '#28a745',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Export report functionality
        function exportReport() {
            Swal.fire({
                title: 'Export Report',
                text: 'This feature will export your analytics data to a PDF or Excel file.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }
    </script>
</body>
</html>
