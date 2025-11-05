<?php
/**
 * Database Configuration
 * Centralized configuration for the e-commerce platform
 */

// Database Configuration
// Auto-detect environment and use appropriate credentials
$is_local = false;

// Check if running from command line (CLI)
if (php_sapi_name() === 'cli') {
    $is_local = true; // Assume local when running from command line
} else {
    // Check web environment
    $is_local = (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false || 
                 strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false ||
                 strpos($_SERVER['SERVER_NAME'] ?? '', 'localhost') !== false);
}

if ($is_local) {
    // Local Development (XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'ecommerce_2025A_aduot_jok');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // Server Deployment
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'ecommerce_2025A_aduot_jok');
    define('DB_USER', 'aduot.jok');
    define('DB_PASS', 'Aduot12');
}

define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'Taste of Africa');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production, testing

// Security Configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 5);

// File Upload Configuration
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Email Configuration (if needed)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');

// Error Reporting (based on environment)
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>