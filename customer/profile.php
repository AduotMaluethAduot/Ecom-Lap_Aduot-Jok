<?php
// Profile page for customers
require_once '../src/settings/core.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    header('Location: ../login/login.php');
    exit();
}

// Get customer information
$customer_id = get_user_id();
$customer_name = get_user_name();
$customer_email = get_user_email();
$customer_role = get_user_role();

// Redirect admin users to admin dashboard
if (is_user_admin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

// Get customer details from database
require_once '../database/database.php';
$customer_details = fetchOne("SELECT * FROM customer WHERE customer_id = ?", [$customer_id]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    
    if (!empty($name) && !empty($email)) {
        try {
            $result = executeQuery("
                UPDATE customer 
                SET customer_name = ?, customer_email = ?, customer_country = ?, customer_city = ?, customer_contact = ?
                WHERE customer_id = ?
            ", [$name, $email, $country, $city, $contact, $customer_id]);
            
            if ($result) {
                // Update session data
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                
                $success_message = "Profile updated successfully!";
                // Refresh customer details
                $customer_details = fetchOne("SELECT * FROM customer WHERE customer_id = ?", [$customer_id]);
            } else {
                $error_message = "Failed to update profile. Please try again.";
            }
        } catch (Exception $e) {
            $error_message = "An error occurred while updating your profile.";
            error_log("Profile update error: " . $e->getMessage());
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .profile-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            margin: 2rem 0;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .profile-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            margin: 0 auto 1rem;
        }
        .btn-profile {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-profile:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
            color: white;
        }
        .form-control:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
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
                <a class="nav-link" href="my_orders.php">
                    <i class="fas fa-shopping-bag me-1"></i>My Orders
                </a>
                <a class="nav-link active" href="profile.php">
                    <i class="fas fa-user-circle me-1"></i>Profile
                </a>
                <a class="nav-link" href="../login/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="profile-container">
            <h1 class="text-center mb-4">
                <i class="fas fa-user-circle me-2"></i>My Profile
            </h1>
            <p class="text-center text-muted mb-5">Manage your account information</p>

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

            <div class="row">
                <!-- Profile Information -->
                <div class="col-lg-8">
                    <div class="profile-card">
                        <h4 class="mb-4">
                            <i class="fas fa-user me-2"></i>Personal Information
                        </h4>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($customer_details['customer_name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($customer_details['customer_email'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" 
                                           value="<?php echo htmlspecialchars($customer_details['customer_country'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="<?php echo htmlspecialchars($customer_details['customer_city'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contact" class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" id="contact" name="contact" 
                                           value="<?php echo htmlspecialchars($customer_details['customer_contact'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Account Type</label>
                                    <input type="text" class="form-control" id="role" 
                                           value="<?php echo ucfirst($customer_details['user_role'] ?? 'customer'); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-profile">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Profile Stats -->
                <div class="col-lg-4">
                    <div class="profile-card">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="text-center mb-3"><?php echo htmlspecialchars($customer_name); ?></h5>
                        <p class="text-center text-muted mb-4">Member since <?php echo date('F Y', strtotime($customer_details['created_at'] ?? 'now')); ?></p>
                        
                        <div class="stats-card">
                            <div class="row">
                                <div class="col-6">
                                    <div class="stats-number">
                                        <?php 
                                        $order_count = fetchOne("SELECT COUNT(*) as count FROM orders WHERE customer_id = ?", [$customer_id]);
                                        echo $order_count['count'] ?? 0;
                                        ?>
                                    </div>
                                    <div>Total Orders</div>
                                </div>
                                <div class="col-6">
                                    <div class="stats-number">
                                        <?php 
                                        $total_spent = fetchOne("
                                            SELECT COALESCE(SUM(p.product_price * od.qty), 0) as total
                                            FROM orders o 
                                            LEFT JOIN orderdetails od ON o.order_id = od.order_id
                                            LEFT JOIN products p ON od.product_id = p.product_id
                                            WHERE o.customer_id = ?
                                        ", [$customer_id]);
                                        echo '$' . number_format($total_spent['total'] ?? 0, 0);
                                        ?>
                                    </div>
                                    <div>Total Spent</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="my_orders.php" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-bag me-2"></i>View Orders
                            </a>
                            <a href="favorites.php" class="btn btn-outline-success">
                                <i class="fas fa-heart me-2"></i>My Favorites
                            </a>
                            <button class="btn btn-outline-warning" onclick="changePassword()">
                                <i class="fas fa-key me-2"></i>Change Password
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
        // Reset form to original values
        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All unsaved changes will be lost.')) {
                location.reload();
            }
        }

        // Change password functionality
        function changePassword() {
            Swal.fire({
                title: 'Change Password',
                html: `
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" placeholder="Enter current password">
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" placeholder="Enter new password">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" placeholder="Confirm new password">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Change Password',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const currentPassword = document.getElementById('current_password').value;
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (!currentPassword || !newPassword || !confirmPassword) {
                        Swal.showValidationMessage('Please fill in all fields');
                        return false;
                    }
                    
                    if (newPassword !== confirmPassword) {
                        Swal.showValidationMessage('New passwords do not match');
                        return false;
                    }
                    
                    if (newPassword.length < 6) {
                        Swal.showValidationMessage('Password must be at least 6 characters long');
                        return false;
                    }
                    
                    return { currentPassword, newPassword };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Changed!',
                        text: 'Your password has been updated successfully',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    </script>
</body>
</html>
