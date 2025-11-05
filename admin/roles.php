<?php
// User Roles & Permissions Management - requires admin privileges
require_once '../src/settings/core.php';

// Require admin access
require_admin();

// Check session validity
if (!is_session_valid()) {
    logout_user();
    header("Location: ../login/login.php?message=session_expired");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Roles & Permissions - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../public/css/admin.css" rel="stylesheet">
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="admin-title">
                        <i class="fas fa-user-shield me-2"></i>
                        User Roles & Permissions
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <div class="admin-user-info">
                        <span class="admin-user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="../login/logout.php" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Navigation -->
    <div class="admin-nav">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <div class="navbar-nav">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                    <a href="users.php" class="nav-link">
                        <i class="fas fa-users me-1"></i> Manage Users
                    </a>
                    <a href="roles.php" class="nav-link active">
                        <i class="fas fa-user-shield me-1"></i> User Roles
                    </a>
                    <a href="category.php" class="nav-link">
                        <i class="fas fa-tags me-1"></i> Categories
                    </a>
                    <a href="brand.php" class="nav-link">
                        <i class="fas fa-copyright me-1"></i> Brands
                    </a>
                    <a href="product.php" class="nav-link">
                        <i class="fas fa-box-open me-1"></i> Products
                    </a>
                    <a href="orders.php" class="nav-link">
                        <i class="fas fa-shopping-bag me-1"></i> Orders
                    </a>
                    <a href="reports.php" class="nav-link">
                        <i class="fas fa-chart-bar me-1"></i> Reports
                    </a>
                    <a href="settings.php" class="nav-link">
                        <i class="fas fa-cog me-1"></i> Settings
                    </a>
                </div>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">
                            <i class="fas fa-user-shield me-2"></i>
                            User Roles & Permissions Management
                        </h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Role Management System</strong><br>
                            This section allows you to manage user roles and permissions. Currently, the system supports three main roles:
                            <ul class="mt-2">
                                <li><strong>Customer:</strong> Can browse products, place orders, and manage their profile</li>
                                <li><strong>Restaurant Owner:</strong> Can manage their restaurant, view orders, and access owner dashboard</li>
                                <li><strong>Admin:</strong> Full system access including user management, system settings, and all administrative functions</li>
                            </ul>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-user me-2"></i>Customer Role
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <h6>Permissions:</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success me-2"></i>Browse products</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Place orders</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Manage profile</li>
                                            <li><i class="fas fa-check text-success me-2"></i>View order history</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Add to favorites</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-warning text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-store me-2"></i>Restaurant Owner Role
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <h6>Permissions:</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success me-2"></i>All customer permissions</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Manage restaurant profile</li>
                                            <li><i class="fas fa-check text-success me-2"></i>View restaurant orders</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Access owner dashboard</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Update order status</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-crown me-2"></i>Admin Role
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <h6>Permissions:</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success me-2"></i>All permissions</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Manage users</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Manage categories & brands</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Manage products</li>
                                            <li><i class="fas fa-check text-success me-2"></i>View all orders</li>
                                            <li><i class="fas fa-check text-success me-2"></i>System settings</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Reports & analytics</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4>Role Assignment</h4>
                            <p class="text-muted">
                                User roles are assigned during registration and can be modified by administrators through the 
                                <a href="users.php" class="text-decoration-none">Manage Users</a> section.
                            </p>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> Role changes take effect immediately and may affect user access to certain features.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

