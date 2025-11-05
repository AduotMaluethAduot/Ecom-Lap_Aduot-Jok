<?php
// Admin Dashboard - requires admin privileges
require_once '../src/settings/core.php';

// Require admin access
require_admin();

// Check session validity
if (!is_session_valid()) {
    logout_user();
    header("Location: ../login/login.php?message=session_expired");
    exit();
}

// Get admin user information
$admin_name = get_user_name();
$admin_email = get_user_email();
$admin_id = get_user_id();

// Store redirect URL for admin users
$_SESSION['redirect_after_login'] = '../admin/dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Taste of Africa</title>
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
                    <h1 class="mb-0">
                        <i class="fas fa-tachometer-alt me-3"></i>
                        Admin Dashboard
                    </h1>
                    <p class="mb-0 mt-2 opacity-75">Welcome, <?php echo htmlspecialchars($admin_name); ?></p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="../customer/customer_dashboard.php" class="btn btn-light me-2">
                        <i class="fas fa-user"></i> Customer Dashboard
                    </a>
                    <a href="../index.php" class="btn btn-light me-2">
                        <i class="fas fa-home"></i> Public Site
                    </a>
                    <a href="../login/logout.php" class="btn btn-outline-light">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4" 
         data-user-id="<?php echo htmlspecialchars($admin_id); ?>"
         data-user-name="<?php echo htmlspecialchars($admin_name); ?>"
         data-user-email="<?php echo htmlspecialchars($admin_email); ?>"
         data-user-role="<?php echo htmlspecialchars(get_user_role()); ?>"
         data-is-admin="<?php echo is_user_admin() ? 'true' : 'false'; ?>"
         data-session-valid="<?php echo is_session_valid() ? 'true' : 'false'; ?>"
         data-login-time="<?php echo isset($_SESSION['login_time']) ? date('Y-m-d H:i:s', $_SESSION['login_time']) : 'Unknown'; ?>"
         data-is-logged-in="<?php echo is_user_logged_in() ? 'true' : 'false'; ?>"
         data-has-admin-role="<?php echo has_role('admin') ? 'true' : 'false'; ?>">
        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-users"></i>
                        <div id="total-users">Loading...</div>
                    </div>
                    <div>Total Users</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-crown"></i>
                        <div id="total-admins">Loading...</div>
                    </div>
                    <div>Administrators</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-shopping-cart"></i>
                        <div id="total-orders">0</div>
                    </div>
                    <div>Total Orders</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <i class="fas fa-clock"></i>
                        <div><?php echo date('H:i'); ?></div>
                    </div>
                    <div>Current Time</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Admin Menu -->
            <div class="col-md-4">
                <div class="admin-menu">
                    <h4 class="mb-3">
                        <i class="fas fa-cogs text-primary"></i> Admin Functions
                    </h4>
                    <a href="users.php" class="menu-item">
                        <i class="fas fa-users me-2"></i> Manage Users
                    </a>
                    <a href="roles.php" class="menu-item">
                        <i class="fas fa-user-shield me-2"></i> User Roles & Permissions
                    </a>
                    <a href="category.php" class="menu-item">
                        <i class="fas fa-tags me-2"></i> Manage Categories
                    </a>
                    <a href="brand.php" class="menu-item">
                        <i class="fas fa-copyright me-2"></i> Manage Brands
                    </a>
                    <a href="product.php" class="menu-item">
                        <i class="fas fa-box-open me-2"></i> Manage Products
                    </a>
                    <a href="orders.php" class="menu-item">
                        <i class="fas fa-shopping-bag me-2"></i> View Orders
                    </a>
                    <a href="reports.php" class="menu-item">
                        <i class="fas fa-chart-bar me-2"></i> Reports & Analytics
                    </a>
                    <a href="settings.php" class="menu-item">
                        <i class="fas fa-cog me-2"></i> System Settings
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-8">
                <!-- Session Information -->
                <div class="admin-card">
                    <h4>
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Session Information
                    </h4>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Admin ID:</strong> <?php echo htmlspecialchars($admin_id); ?><br>
                            <strong>Name:</strong> <?php echo htmlspecialchars($admin_name); ?><br>
                            <strong>Email:</strong> <?php echo htmlspecialchars($admin_email); ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Role:</strong> <?php echo htmlspecialchars(get_user_role()); ?><br>
                            <strong>Login Time:</strong> <?php echo isset($_SESSION['login_time']) ? date('Y-m-d H:i:s', $_SESSION['login_time']) : 'Unknown'; ?><br>
                            <strong>Session Valid:</strong> 
                            <span class="text-success">
                                <i class="fas fa-check-circle"></i> Yes
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="admin-card">
                    <h4>
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Quick Actions
                    </h4>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-admin w-100" onclick="testSessionManagement()">
                                <i class="fas fa-vial me-2"></i>Test Session Management
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-admin w-100" onclick="refreshStats()">
                                <i class="fas fa-sync me-2"></i>Refresh Statistics
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-admin w-100" onclick="checkPermissions()">
                                <i class="fas fa-shield-alt me-2"></i>Check Permissions
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-admin w-100" onclick="regenerateSession()">
                                <i class="fas fa-key me-2"></i>Regenerate Session
                            </button>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="admin-card">
                    <h4>
                        <i class="fas fa-server text-success me-2"></i>
                        System Status
                    </h4>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div id="system-status">
                                <p><i class="fas fa-database text-success"></i> Database: <span class="text-success">Connected</span></p>
                                <p><i class="fas fa-shield-alt text-success"></i> Session Security: <span class="text-success">Active</span></p>
                                <p><i class="fas fa-lock text-success"></i> Admin Privileges: <span class="text-success">Verified</span></p>
                                <p><i class="fas fa-clock text-info"></i> Server Time: <?php echo date('Y-m-d H:i:s'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="danger-zone">
                    <h4 class="text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Danger Zone
                    </h4>
                    <p class="text-muted">These actions are irreversible. Use with extreme caution.</p>
                    <button class="btn btn-danger-custom" onclick="confirmClearSessions()">
                        <i class="fas fa-trash me-2"></i>Clear All Sessions
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../public/js/dashboard.js"></script>
</body>
</html>