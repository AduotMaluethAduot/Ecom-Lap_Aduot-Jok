<?php
// Owner Settings page
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

// Get customer details from database
require_once '../database/database.php';
$owner_details = fetchOne("SELECT * FROM customer WHERE customer_id = ?", [$owner_id]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $restaurant_name = trim($_POST['restaurant_name'] ?? '');
    $restaurant_address = trim($_POST['restaurant_address'] ?? '');
    $restaurant_phone = trim($_POST['restaurant_phone'] ?? '');
    $restaurant_description = trim($_POST['restaurant_description'] ?? '');
    
    if (!empty($name) && !empty($email)) {
        try {
            $result = executeQuery("
                UPDATE customer 
                SET customer_name = ?, customer_email = ?, customer_country = ?, customer_city = ?, customer_contact = ?
                WHERE customer_id = ?
            ", [$name, $email, $country, $city, $contact, $owner_id]);
            
            if ($result) {
                // Update session data
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                
                $success_message = "Settings updated successfully!";
                // Refresh owner details
                $owner_details = fetchOne("SELECT * FROM customer WHERE customer_id = ?", [$owner_id]);
            } else {
                $error_message = "Failed to update settings. Please try again.";
            }
        } catch (Exception $e) {
            $error_message = "An error occurred while updating your settings.";
            error_log("Settings update error: " . $e->getMessage());
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
    <title>Restaurant Settings - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .settings-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            margin: 2rem 0;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .settings-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .btn-owner {
            background: linear-gradient(135deg, #38a169, #2f855a);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-owner:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(56, 161, 105, 0.4);
            color: white;
        }
        .form-control:focus {
            border-color: #38a169;
            box-shadow: 0 0 0 0.2rem rgba(56, 161, 105, 0.25);
        }
        .settings-section {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 2rem;
            margin-bottom: 2rem;
        }
        .settings-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .section-title {
            color: #38a169;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #38a169;
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
                <a class="nav-link" href="owner_analytics.php">
                    <i class="fas fa-chart-bar me-1"></i>Analytics
                </a>
                <a class="nav-link active" href="owner_settings.php">
                    <i class="fas fa-cog me-1"></i>Settings
                </a>
                <a class="nav-link" href="../login/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="settings-container">
            <h1 class="text-center mb-4">
                <i class="fas fa-cog me-2"></i>Restaurant Settings
            </h1>
            <p class="text-center text-muted mb-5">Manage your restaurant information and preferences</p>

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

            <form method="POST" action="">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-lg-6">
                        <div class="settings-card">
                            <h4 class="section-title">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </h4>
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($owner_details['customer_name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($owner_details['customer_email'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" 
                                           value="<?php echo htmlspecialchars($owner_details['customer_country'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="<?php echo htmlspecialchars($owner_details['customer_city'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="contact" class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" id="contact" name="contact" 
                                           value="<?php echo htmlspecialchars($owner_details['customer_contact'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Restaurant Information -->
                    <div class="col-lg-6">
                        <div class="settings-card">
                            <h4 class="section-title">
                                <i class="fas fa-store me-2"></i>Restaurant Information
                            </h4>
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="restaurant_name" class="form-label">Restaurant Name</label>
                                    <input type="text" class="form-control" id="restaurant_name" name="restaurant_name" 
                                           value="Taste of Africa" placeholder="Enter your restaurant name">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="restaurant_address" class="form-label">Restaurant Address</label>
                                    <textarea class="form-control" id="restaurant_address" name="restaurant_address" rows="3" 
                                              placeholder="Enter your restaurant address"></textarea>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="restaurant_phone" class="form-label">Restaurant Phone</label>
                                    <input type="tel" class="form-control" id="restaurant_phone" name="restaurant_phone" 
                                           placeholder="Enter restaurant phone number">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="restaurant_status" class="form-label">Status</label>
                                    <select class="form-control" id="restaurant_status" name="restaurant_status">
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="maintenance">Under Maintenance</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="restaurant_description" class="form-label">Restaurant Description</label>
                                    <textarea class="form-control" id="restaurant_description" name="restaurant_description" rows="4" 
                                              placeholder="Describe your restaurant and what makes it special"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Hours -->
                <div class="settings-card">
                    <h4 class="section-title">
                        <i class="fas fa-clock me-2"></i>Business Hours
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Opening Time</label>
                                <input type="time" class="form-control" value="09:00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Closing Time</label>
                                <input type="time" class="form-control" value="22:00">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="open_24_7">
                                <label class="form-check-label" for="open_24_7">
                                    24/7 Operation
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="settings-card">
                    <h4 class="section-title">
                        <i class="fas fa-bell me-2"></i>Notification Settings
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="email_notifications" checked>
                                <label class="form-check-label" for="email_notifications">
                                    Email Notifications
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="order_notifications" checked>
                                <label class="form-check-label" for="order_notifications">
                                    New Order Notifications
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="review_notifications">
                                <label class="form-check-label" for="review_notifications">
                                    Review Notifications
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="promotion_notifications">
                                <label class="form-check-label" for="promotion_notifications">
                                    Promotion Notifications
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <button type="submit" class="btn btn-owner me-3">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                        <i class="fas fa-undo me-2"></i>Reset
                    </button>
                </div>
            </form>
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

        // Handle 24/7 checkbox
        document.getElementById('open_24_7').addEventListener('change', function() {
            const openingTime = document.querySelector('input[type="time"]');
            const closingTime = document.querySelectorAll('input[type="time"]')[1];
            
            if (this.checked) {
                openingTime.disabled = true;
                closingTime.disabled = true;
                openingTime.value = '00:00';
                closingTime.value = '23:59';
            } else {
                openingTime.disabled = false;
                closingTime.disabled = false;
                openingTime.value = '09:00';
                closingTime.value = '22:00';
            }
        });
    </script>
</body>
</html>
