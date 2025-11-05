<?php

header('Content-Type: application/json');

// Use the enhanced session management from core.php
require_once '../src/settings/core.php';

$response = array();

// Check if the user is already logged in using the new function
if (is_user_logged_in()) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    $response['user_data'] = array(
        'id' => get_user_id(),
        'name' => get_user_name(),
        'email' => get_user_email(),
        'role' => get_user_role()
    );
    echo json_encode($response);
    exit();
}

require_once '../controllers/customer_controller.php';

// Validate input data
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    $response['status'] = 'error';
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit();
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Use the customer controller for login
$login_result = login_customer_ctr($email, $password);

if ($login_result['status'] === 'success') {
    $customer_data = $login_result['data'];
    
    // Prepare user data for session initialization
    $user_session_data = array(
        'user_id' => $customer_data['customer_id'],
        'role' => $customer_data['user_role'],
        'name' => $customer_data['customer_name'],
        'email' => $customer_data['customer_email'],
        'contact' => $customer_data['customer_contact'],
        'country' => $customer_data['customer_country'],
        'city' => $customer_data['customer_city'],
        'image' => $customer_data['customer_image'] ?? null
    );
    
    // Initialize session using the new secure session management
    init_user_session($user_session_data);
    
    // Regenerate session ID for security
    regenerate_session();
    
    $response['status'] = 'success';
    $response['message'] = $login_result['message'];
    $response['user_data'] = array(
        'id' => $customer_data['customer_id'],
        'name' => $customer_data['customer_name'],
        'email' => $customer_data['customer_email'],
        'role' => $customer_data['user_role'],
        'contact' => $customer_data['customer_contact'],
        'country' => $customer_data['customer_country'],
        'city' => $customer_data['customer_city'],
        'is_admin' => ($customer_data['user_role'] === 'admin')
    );
} else {
    $response['status'] = 'error';
    $response['message'] = $login_result['message'];
}

echo json_encode($response);