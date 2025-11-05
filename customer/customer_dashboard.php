<?php
// Customer Dashboard - for regular customers
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

// Get customer information
$customer_id = get_user_id();
$customer_name = get_user_name();
$customer_email = get_user_email();
$customer_role = get_user_role();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .customer-header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .customer-card {
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
            color: #ff6b6b;
            margin-bottom: 1rem;
        }
        .btn-customer {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-customer:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
            color: white;
        }
        .order-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            <a class="navbar-brand fw-bold" href="../index.php" style="color: #ff6b6b;">
                <i class="fas fa-utensils me-2"></i>Taste of Africa
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="menu.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_orders.php">My Orders</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($customer_name); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#settings"><i class="fas fa-cog me-2"></i>Settings</a></li>
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
                    <h1 class="welcome-title">Welcome, <?php echo htmlspecialchars($customer_name); ?>!</h1>
                    <p class="welcome-subtitle">Ready to explore the authentic flavors of Africa?</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Customer Dashboard Content -->
    <div class="container">
        <div class="row">
            <!-- Quick Actions -->
            <div class="col-lg-4 mb-4">
                <div class="customer-card">
                    <h4 class="mb-3">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Quick Actions
                    </h4>
                    <div class="d-grid gap-2">
                        <a href="menu.php" class="btn-customer">
                            <i class="fas fa-utensils me-2"></i>Browse Menu
                        </a>
                        <a href="my_orders.php" class="btn-customer">
                            <i class="fas fa-shopping-bag me-2"></i>My Orders
                        </a>
                        <a href="profile.php" class="btn-customer">
                            <i class="fas fa-user-circle me-2"></i>My Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="col-lg-8 mb-4">
                <div class="customer-card">
                    <h4 class="mb-3">
                        <i class="fas fa-user-circle text-primary me-2"></i>
                        Account Information
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_email); ?></p>
                            <p><strong>Role:</strong> 
                                <span class="badge bg-success">Customer</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Member Since:</strong> <?php echo date('F Y'); ?></p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-success">Active</span>
                            </p>
                            <p><strong>Total Orders:</strong> 0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="row mb-5">
            <div class="col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h5>Explore Menu</h5>
                    <p>Discover our authentic African dishes from across the continent. From Jollof Rice to Injera, we have it all!</p>
                    <a href="menu.php" class="btn-customer">Browse Menu</a>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h5>Order Food</h5>
                    <p>Place your order and have delicious African cuisine delivered to your doorstep in no time!</p>
                    <a href="order_food.php" class="btn-customer">Start Ordering</a>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h5>Favorites</h5>
                    <p>Save your favorite dishes and reorder them easily. Build your personal African cuisine collection!</p>
                    <a href="favorites.php" class="btn-customer">View Favorites</a>
                </div>
            </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="customer-card">
                    <h4 class="mb-3">
                        <i class="fas fa-history text-info me-2"></i>
                        Recent Orders
                    </h4>
                    <div id="recentOrders">
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No orders yet</h5>
                            <p class="text-muted">Start exploring our menu and place your first order!</p>
                            <a href="menu.php" class="btn-customer">Browse Menu</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
