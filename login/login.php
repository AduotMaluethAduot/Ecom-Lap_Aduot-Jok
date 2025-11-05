<?php
// Use the enhanced session management from core.php
require_once '../src/settings/core.php';

// Redirect if user is already logged in using the new function
if (is_user_logged_in()) {
    // Check if there's a redirect URL stored
    $redirect_url = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : '../index.php';
    
    // If the user is an admin, always redirect to admin dashboard
    if (is_user_admin()) {
        $redirect_url = '../admin/dashboard.php';
    }
    
    unset($_SESSION['redirect_after_login']);
    header("Location: $redirect_url");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login - Taste of Africa</title>
   <link rel="stylesheet" href="../public/css/login.css">
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
   <div class="login-container">
       <!-- Back to Index Button -->
       <div class="back-to-index" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
           <a href="../index.php" class="btn-back" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%); color: white; text-decoration: none; border-radius: 25px; font-weight: 500; font-size: 14px; box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3); transition: all 0.3s ease;">
               <i class="fas fa-arrow-left"></i> Back to Home
           </a>
       </div>
       
       <form id="login-form" class="login-form">
           <h2>Login</h2>
           <label for="email">Email</label>
           <input type="email" id="email" name="email" placeholder="Enter your email" required>
           <label for="password">Password</label>
           <input type="password" id="password" name="password" placeholder="Enter your password" required>
           <button type="submit">Login</button>
           <div class="options">
               <label><input type="checkbox" name="remember"> Remember Me</label>
               <a href="#">Forgot Password?</a>
           </div>
           <div class="register-link">
               <p>Don't have an account? <a href="register.php">Register here</a></p>
           </div>
       </form>
   </div>

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script src="../public/js/login.js"></script>
</body>
</html>