<?php
// Restaurant Owner Dashboard - for restaurant owners
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

// Check if user is a restaurant owner (role should be 'owner' or similar)
$user_role = get_user_role();
if ($user_role !== 'owner' && $user_role !== 'restaurant_owner') {
    // If not owner, redirect to customer dashboard
    header('Location: customer_dashboard.php');
    exit();
}

// Get owner information
$owner_id = get_user_id();
$owner_name = get_user_name();
$owner_email = get_user_email();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Owner Dashboard - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .owner-header {
            background: linear-gradient(135deg, #38a169, #2f855a);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .owner-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        .welcome-section {
            text-align: center;
            padding: 3rem 0;
        }
        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .welcome-subtitle {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
        }
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 3rem;
            color: #38a169;
            margin-bottom: 1rem;
        }
        .btn-owner {
            background: linear-gradient(135deg, #38a169, #2f855a);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-owner:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(56, 161, 105, 0.4);
            color: white;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
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
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php" style="color: #38a169;">
                <i class="fas fa-store me-2"></i>Taste of Africa - Owner Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="owner_menu.php">My Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="owner_orders.php">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="owner_analytics.php">Analytics</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($owner_name); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="owner_settings.php"><i class="fas fa-cog me-2"></i>Restaurant Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../login/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Welcome Section -->
    <section class="welcome-section" style="padding-top: 100px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="welcome-title">Welcome, <?php echo htmlspecialchars($owner_name); ?>!</h1>
                    <p class="welcome-subtitle">Manage your restaurant and grow your African cuisine business</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Owner Dashboard Content -->
    <div class="container">
        <!-- Statistics Row -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-utensils"></i>
                        <span id="totalDishes">12</span>
                    </div>
                    <div>Total Dishes</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-shopping-bag"></i>
                        <span id="totalOrders">24</span>
                    </div>
                    <div>Total Orders</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-dollar-sign"></i>
                        <span id="totalRevenue">$1,250</span>
                    </div>
                    <div>Total Revenue</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-star"></i>
                        <span id="avgRating">4.8</span>
                    </div>
                    <div>Average Rating</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Quick Actions -->
            <div class="col-lg-4 mb-4">
                <div class="owner-card">
                    <h4 class="mb-3">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Quick Actions
                    </h4>
                    <div class="d-grid gap-2">
                        <a href="../admin/product.php" class="btn-owner">
                            <i class="fas fa-plus me-2"></i>Add New Dish
                        </a>
                        <a href="owner_orders.php" class="btn-owner">
                            <i class="fas fa-shopping-bag me-2"></i>View Orders
                        </a>
                        <a href="owner_analytics.php" class="btn-owner">
                            <i class="fas fa-chart-bar me-2"></i>View Analytics
                        </a>
                        <a href="owner_settings.php" class="btn-owner">
                            <i class="fas fa-cog me-2"></i>Restaurant Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Restaurant Information -->
            <div class="col-lg-8 mb-4">
                <div class="owner-card">
                    <h4 class="mb-3">
                        <i class="fas fa-store text-primary me-2"></i>
                        Restaurant Information
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Owner Name:</strong> <?php echo htmlspecialchars($owner_name); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($owner_email); ?></p>
                            <p><strong>Role:</strong> 
                                <span class="badge bg-success">Restaurant Owner</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Restaurant Status:</strong> 
                                <span class="badge bg-success">Active</span>
                            </p>
                            <p><strong>Joined:</strong> <?php echo date('F Y'); ?></p>
                            <p><strong>Last Login:</strong> <?php echo date('M d, Y H:i'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Features -->
        <div class="row mb-5">
            <div class="col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h5>Menu Management</h5>
                    <p>Add, edit, and manage your restaurant's menu items. Showcase your authentic African dishes to customers.</p>
                    <a href="owner_menu.php" class="btn-owner">Manage Menu</a>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h5>Order Management</h5>
                    <p>Track and manage incoming orders. Update order status and communicate with customers.</p>
                    <a href="owner_orders.php" class="btn-owner">View Orders</a>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5>Analytics & Reports</h5>
                    <p>View detailed analytics about your restaurant's performance, popular dishes, and revenue.</p>
                    <a href="owner_analytics.php" class="btn-owner">View Analytics</a>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="row">
            <div class="col-lg-12">
                <div class="owner-card">
                    <h4 class="mb-3">
                        <i class="fas fa-clock text-info me-2"></i>
                        Recent Orders
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#001</td>
                                    <td>John Customer</td>
                                    <td>Jollof Rice Special</td>
                                    <td>$15.99</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>2 min ago</td>
                                    <td>
                                        <button class="btn btn-sm btn-success">Accept</button>
                                        <button class="btn btn-sm btn-danger">Decline</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#002</td>
                                    <td>Jane Smith</td>
                                    <td>Injera with Doro Wat</td>
                                    <td>$18.50</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>1 hour ago</td>
                                    <td>
                                        <button class="btn btn-sm btn-info">View Details</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
