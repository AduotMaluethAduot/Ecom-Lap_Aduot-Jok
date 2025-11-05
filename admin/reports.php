<?php
// Reports & Analytics Page - requires admin privileges
require_once '../src/settings/core.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="public/css/admin.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-chart-bar me-3"></i>
                        Reports & Analytics
                    </h1>
                    <p class="mb-0 mt-2 opacity-75">Business insights and analytics</p>
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
        <!-- Report Filters -->
        <div class="admin-card mb-4">
            <h4><i class="fas fa-filter me-2"></i>Report Filters</h4>
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select" id="reportType">
                        <option value="sales">Sales Report</option>
                        <option value="orders">Orders Report</option>
                        <option value="products">Products Report</option>
                        <option value="customers">Customers Report</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="timePeriod">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="customFrom" placeholder="From Date" style="display: none;">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="customTo" placeholder="To Date" style="display: none;">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" onclick="generateReport()">
                        <i class="fas fa-chart-line"></i> Generate
                    </button>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-dollar-sign text-success"></i>
                        <div id="totalRevenue">$0.00</div>
                    </div>
                    <div>Total Revenue</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-shopping-cart text-primary"></i>
                        <div id="totalOrders">0</div>
                    </div>
                    <div>Total Orders</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-users text-info"></i>
                        <div id="totalCustomers">0</div>
                    </div>
                    <div>Total Customers</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-box-open text-warning"></i>
                        <div id="totalProducts">0</div>
                    </div>
                    <div>Total Products</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="admin-card">
                    <h4><i class="fas fa-chart-line me-2"></i>Sales Trend</h4>
                    <canvas id="salesChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="admin-card">
                    <h4><i class="fas fa-chart-pie me-2"></i>Orders by Status</h4>
                    <canvas id="ordersChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="admin-card">
                    <h4><i class="fas fa-chart-bar me-2"></i>Top Products</h4>
                    <canvas id="productsChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="admin-card">
                    <h4><i class="fas fa-chart-area me-2"></i>Customer Growth</h4>
                    <canvas id="customersChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Reports -->
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><i class="fas fa-table me-2"></i>Detailed Report</h4>
                <div>
                    <button class="btn btn-success me-2" onclick="exportReport()">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <button class="btn btn-primary" onclick="printReport()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr id="reportTableHeader">
                            <th>Date</th>
                            <th>Orders</th>
                            <th>Revenue</th>
                            <th>Customers</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin"></i> Loading report data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="public/js/reports.js"></script>
</body>
</html>
