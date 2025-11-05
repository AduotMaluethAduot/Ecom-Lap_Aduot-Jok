<?php
// Admin Users Management - requires admin privileges
require_once '../src/settings/core.php';
require_once '../controllers/customer_controller.php';

// Require admin access
require_admin();

// Check session validity
if (!is_session_valid()) {
    logout_user();
    header("Location: login/login.php?message=session_expired");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-users me-3"></i>
                        User Management
                    </h1>
                    <p class="mb-0 mt-2 opacity-75">Manage system users and permissions</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="dashboard.php" class="btn btn-light me-2">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="../index.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-home"></i> Back to Site
                    </a>
                    <a href="../login/logout.php" class="btn btn-outline-light">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4" 
         data-current-user-id="<?php echo htmlspecialchars(get_user_id()); ?>">
        <!-- Search and Filter Section -->
        <div class="content-card">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4><i class="fas fa-search me-2"></i>Find Users</h4>
                    <input type="text" class="form-control search-box" id="searchUsers" placeholder="Search by name, email, or ID...">
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <button class="btn btn-admin" onclick="filterUsers('all')">
                            <i class="fas fa-users"></i> All Users
                        </button>
                        <button class="btn btn-outline-primary" onclick="filterUsers('admin')">
                            <i class="fas fa-crown"></i> Admins
                        </button>
                        <button class="btn btn-outline-secondary" onclick="filterUsers('customer')">
                            <i class="fas fa-user"></i> Customers
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><i class="fas fa-list me-2"></i>System Users</h4>
                <span class="badge bg-primary" id="userCount">Loading...</span>
            </div>
            
            <div id="usersList">
                <!-- Sample Users - in real implementation, this would come from database -->
                <div class="user-card" data-role="admin">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="text-center">
                                <i class="fas fa-user-circle fa-3x text-primary"></i>
                                <div class="mt-2">
                                    <span class="role-badge role-admin">
                                        <i class="fas fa-crown"></i> Admin
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-1"><?php echo htmlspecialchars(get_user_name()); ?></h5>
                            <p class="mb-1 text-muted">
                                <i class="fas fa-envelope me-1"></i>
                                <?php echo htmlspecialchars(get_user_email()); ?>
                            </p>
                            <p class="mb-0 text-muted">
                                <i class="fas fa-id-badge me-1"></i>
                                ID: <?php echo htmlspecialchars(get_user_id()); ?>
                            </p>
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> Currently Online
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group-vertical btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm mb-1" onclick="viewUser(<?php echo get_user_id(); ?>)">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                <button class="btn btn-outline-warning btn-sm mb-1" onclick="editUser(<?php echo get_user_id(); ?>)">
                                    <i class="fas fa-edit"></i> Edit User
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="resetPassword(<?php echo get_user_id(); ?>)">
                                    <i class="fas fa-key"></i> Reset Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sample Customer User -->
                <div class="user-card" data-role="customer">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="text-center">
                                <i class="fas fa-user-circle fa-3x text-secondary"></i>
                                <div class="mt-2">
                                    <span class="role-badge role-customer">
                                        <i class="fas fa-user"></i> Customer
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-1">John Customer</h5>
                            <p class="mb-1 text-muted">
                                <i class="fas fa-envelope me-1"></i>
                                john.customer@example.com
                            </p>
                            <p class="mb-0 text-muted">
                                <i class="fas fa-id-badge me-1"></i>
                                ID: 102
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> Last active: 2 hours ago
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group-vertical btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm mb-1" onclick="viewUser(102)">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                <button class="btn btn-outline-warning btn-sm mb-1" onclick="editUser(102)">
                                    <i class="fas fa-edit"></i> Edit User
                                </button>
                                <button class="btn btn-outline-success btn-sm mb-1" onclick="promoteUser(102)">
                                    <i class="fas fa-arrow-up"></i> Promote to Admin
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="suspendUser(102)">
                                    <i class="fas fa-ban"></i> Suspend User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Another Sample Customer -->
                <div class="user-card" data-role="customer">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="text-center">
                                <i class="fas fa-user-circle fa-3x text-secondary"></i>
                                <div class="mt-2">
                                    <span class="role-badge role-customer">
                                        <i class="fas fa-user"></i> Customer
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-1">Jane Customer</h5>
                            <p class="mb-1 text-muted">
                                <i class="fas fa-envelope me-1"></i>
                                jane.customer@example.com
                            </p>
                            <p class="mb-0 text-muted">
                                <i class="fas fa-id-badge me-1"></i>
                                ID: 103
                            </p>
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> Online
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group-vertical btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm mb-1" onclick="viewUser(103)">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                <button class="btn btn-outline-warning btn-sm mb-1" onclick="editUser(103)">
                                    <i class="fas fa-edit"></i> Edit User
                                </button>
                                <button class="btn btn-outline-success btn-sm mb-1" onclick="promoteUser(103)">
                                    <i class="fas fa-arrow-up"></i> Promote to Admin
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="suspendUser(103)">
                                    <i class="fas fa-ban"></i> Suspend User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Statistics -->
        <div class="content-card">
            <h4><i class="fas fa-chart-bar me-2"></i>User Statistics</h4>
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-primary" id="totalUsers">3</h3>
                        <p class="text-muted">Total Users</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-danger" id="totalAdmins">1</h3>
                        <p class="text-muted">Administrators</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-warning" id="totalCustomers">2</h3>
                        <p class="text-muted">Customers</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-success" id="onlineUsers">2</h3>
                        <p class="text-muted">Online Now</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/users.js"></script>
</body>
</html>