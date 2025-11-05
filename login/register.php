<?php
// Use the enhanced session management from core.php
require_once '../src/settings/core.php';

// Redirect if user is already logged in using the new function
if (is_user_logged_in()) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../public/css/register.css" rel="stylesheet">
</head>

<body>
    <div class="register-container">
        <!-- Back to Index Button -->
        <div class="back-to-index" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <a href="../index.php" class="btn-back" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%); color: white; text-decoration: none; border-radius: 25px; font-weight: 500; font-size: 14px; box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3); transition: all 0.3s ease;">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10 col-12">
                <div class="card">
            <div class="card-header">
                <h4>Register</h4>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <?php 
                        switch ($_GET['error']) {
                            case 'missing_fields':
                                echo 'Please fill in all required fields.';
                                break;
                            case 'empty_fields':
                                echo 'Name, email, and password cannot be empty.';
                                break;
                            case 'invalid_email':
                                echo 'Please enter a valid email address.';
                                break;
                            case 'weak_password':
                                echo 'Password must be at least 6 characters long.';
                                break;
                            case 'invalid_role':
                                echo 'Invalid role selected.';
                                break;
                            case 'registration_failed':
                                echo 'Registration failed. Email might already exist.';
                                break;
                            default:
                                echo 'An error occurred. Please try again.';
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="../actions/register_user_action.php" id="register-form">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <i class="fa fa-user"></i></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <i class="fa fa-envelope"></i></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <i class="fa fa-lock"></i></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number <i class="fa fa-phone"></i></label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">Country <i class="fa fa-globe"></i></label>
                        <select class="form-select" id="country" name="country" required>
                            <option value="">Select Country</option>
                            <option value="Ghana">Ghana</option>
                            <option value="Nigeria">Nigeria</option>
                            <option value="Kenya">Kenya</option>
                            <option value="South Africa">South Africa</option>
                            <option value="Ethiopia">Ethiopia</option>
                            <option value="Egypt">Egypt</option>
                            <option value="Morocco">Morocco</option>
                            <option value="Tanzania">Tanzania</option>
                            <option value="Uganda">Uganda</option>
                            <option value="Zimbabwe">Zimbabwe</option>
                            <option value="Zambia">Zambia</option>
                            <option value="Senegal">Senegal</option>
                            <option value="Cameroon">Cameroon</option>
                            <option value="Cote d'Ivoire">Côte d'Ivoire</option>
                            <option value="United States">United States</option>
                            <option value="United Kingdom">United Kingdom</option>
                            <option value="Canada">Canada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City <i class="fa fa-map-marker"></i></label>
                        <input type="text" class="form-control" id="city" name="city" placeholder="Enter your city" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Register As</label>
                        <div class="d-flex">
                            <div class="form-check me-3 custom-radio">
                                <input class="form-check-input" type="radio" name="role" id="customer" value="1" checked>
                                <label class="form-check-label" for="customer">Customer</label>
                            </div>
                            <div class="form-check custom-radio">
                                <input class="form-check-input" type="radio" name="role" id="owner" value="2">
                                <label class="form-check-label" for="owner">Restaurant Owner</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-custom w-100">Register</button>
                </form>
            </div>
            <div class="card-footer">
                Already have an account? <a href="login.php" class="highlight">Login here</a>
            </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../public/js/register.js"></script>
</body>

</html>