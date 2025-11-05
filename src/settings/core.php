<?php
/**
 * Core Session Management and Authentication Functions
 * Enhanced session management for the Taste of Africa e-commerce platform
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../../database/database.php';

/**
 * Initialize user session with secure data
 * @param array $user_data - User data array
 */
function init_user_session($user_data) {
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Set session data
    $_SESSION['user_id'] = $user_data['user_id'];
    $_SESSION['user_name'] = $user_data['name'];
    $_SESSION['user_email'] = $user_data['email'];
    $_SESSION['user_role'] = $user_data['role'];
    $_SESSION['user_contact'] = $user_data['contact'] ?? null;
    $_SESSION['user_country'] = $user_data['country'] ?? null;
    $_SESSION['user_city'] = $user_data['city'] ?? null;
    $_SESSION['user_image'] = $user_data['image'] ?? null;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['session_valid'] = true;
    
    // If user is admin, set redirect URL to admin dashboard
    if ($user_data['role'] === 'admin') {
        $_SESSION['redirect_after_login'] = '../admin/dashboard.php';
    }
}

/**
 * Check if user is logged in
 * @return bool
 */
function is_user_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['session_valid']) && $_SESSION['session_valid'] === true;
}

/**
 * Check if current user is admin
 * @return bool
 */
function is_user_admin() {
    return is_user_logged_in() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get current user ID
 * @return int|null
 */
function get_user_id() {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

/**
 * Get current user name
 * @return string|null
 */
function get_user_name() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
}

/**
 * Get current user email
 * @return string|null
 */
function get_user_email() {
    return isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
}

/**
 * Get current user role
 * @return string|null
 */
function get_user_role() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

/**
 * Check if user has specific role
 * @param string $role
 * @return bool
 */
function has_role($role) {
    return is_user_logged_in() && $_SESSION['user_role'] === $role;
}

/**
 * Check if user has any of the specified roles
 * @param array $roles
 * @return bool
 */
function has_any_role($roles) {
    if (!is_user_logged_in()) {
        return false;
    }
    return in_array($_SESSION['user_role'], $roles);
}

/**
 * Require admin access - redirect if not admin
 */
function require_admin() {
    if (!is_user_admin()) {
        header('Location: ../login/login.php?message=admin_required');
        exit();
    }
}

/**
 * Require specific role - redirect if not authorized
 * @param string $role
 */
function require_role($role) {
    if (!has_role($role)) {
        header('Location: ../login/login.php?message=access_denied');
        exit();
    }
}

/**
 * Check if session is valid (not expired)
 * @return bool
 */
function is_session_valid() {
    if (!is_user_logged_in()) {
        return false;
    }
    
    $session_lifetime = defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 3600; // 1 hour default
    
    // Check if session has expired
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_lifetime) {
        return false;
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
    
    return true;
}

/**
 * Regenerate session ID for security
 */
function regenerate_session() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

/**
 * Logout user and clear session
 */
function logout_user() {
    // Clear all session data
    $_SESSION = array();
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
}

/**
 * Update user session data
 * @param array $user_data
 */
function update_user_session($user_data) {
    if (is_user_logged_in()) {
        foreach ($user_data as $key => $value) {
            if (isset($_SESSION['user_' . $key])) {
                $_SESSION['user_' . $key] = $value;
            }
        }
    }
}

/**
 * Get session information
 * @return array
 */
function get_session_info() {
    return [
        'user_id' => get_user_id(),
        'user_name' => get_user_name(),
        'user_email' => get_user_email(),
        'user_role' => get_user_role(),
        'login_time' => isset($_SESSION['login_time']) ? $_SESSION['login_time'] : null,
        'last_activity' => isset($_SESSION['last_activity']) ? $_SESSION['last_activity'] : null,
        'session_valid' => is_session_valid(),
        'is_admin' => is_user_admin()
    ];
}

/**
 * Security: Prevent session hijacking
 */
function secure_session() {
    // Regenerate session ID periodically
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// Call secure_session on every page load
secure_session();
?>