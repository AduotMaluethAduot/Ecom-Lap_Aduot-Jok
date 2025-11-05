<?php
// Use the enhanced session management from core.php
require_once '../src/settings/core.php';

// Determine redirect URL based on user role and referrer BEFORE destroying session
$redirect_url = '../index.php?message=logged_out';

// Check user role before destroying session
$user_role = null;
if (is_user_logged_in()) {
    $user_role = get_user_role();
    
    // Check referrer if available
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referrer = $_SERVER['HTTP_REFERER'];
        
        // Check if the referrer is an admin page
        if (strpos($referrer, '/admin/') !== false) {
            $redirect_url = '../index.php?message=logged_out';
        }
        // Check if the referrer is an owner dashboard
        elseif (strpos($referrer, '/owner_') !== false || strpos($referrer, 'owner_dashboard') !== false) {
            $redirect_url = '../index.php?message=logged_out';
        }
        // Check if the referrer is a customer dashboard
        elseif (strpos($referrer, '/customer/') !== false || strpos($referrer, 'customer_dashboard') !== false) {
            $redirect_url = '../index.php?message=logged_out';
        }
    }
    
    // Fallback: redirect based on role if referrer not available
    if ($user_role === 'admin') {
        $redirect_url = '../index.php?message=logged_out';
    }
}

// Use the secure logout function from core.php
logout_user();

// Redirect based on the determined URL
header('Location: ' . $redirect_url);
exit();
