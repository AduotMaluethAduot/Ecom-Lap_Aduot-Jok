<?php

// Use the enhanced session management from core.php
require_once '../src/settings/core.php';

// Check if this is an AJAX request
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Check if the user is already logged in using the new function
if (is_user_logged_in()) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'User already logged in']);
        exit();
    } else {
        header('Location: index.php');
        exit();
    }
}

require_once '../controllers/customer_controller.php';

// Validate input data - ensure all required fields are present
if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password']) || 
    !isset($_POST['phone_number']) || !isset($_POST['country']) || !isset($_POST['city']) || !isset($_POST['role'])) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit();
    } else {
        header('Location: login/register.php?error=missing_fields');
        exit();
    }
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$phone_number = trim($_POST['phone_number']);
$country = trim($_POST['country']);
$city = trim($_POST['city']);
$role = (int)$_POST['role'];

// Additional validation
if (empty($name) || empty($email) || empty($password)) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Name, email, and password are required']);
        exit();
    } else {
        header('Location: login/register.php?error=empty_fields');
        exit();
    }
}

// Email format validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit();
    } else {
        header('Location: login/register.php?error=invalid_email');
        exit();
    }
}

// Password strength validation
if (strlen($password) < 6) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters long']);
        exit();
    } else {
        header('Location: login/register.php?error=weak_password');
        exit();
    }
}

// Role validation - ensure valid role
if (!in_array($role, [1, 2])) { // 1 = customer, 2 = restaurant owner
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid role selected']);
        exit();
    } else {
        header('Location: login/register.php?error=invalid_role');
        exit();
    }
}

// Map numeric role to string role
$role_string = ($role == 2) ? 'owner' : 'customer';

// Prepare customer data array
$customer_data = array(
    'name' => $name,
    'email' => $email,
    'password' => $password,
    'contact' => $phone_number,
    'country' => $country,
    'city' => $city,
    'role' => $role_string
);

// Use the customer controller for registration
$customer_id = register_customer_ctr($customer_data);

if ($customer_id) {
    // Registration successful
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Registration successful! You can now login.']);
        exit();
    } else {
        header('Location: login/login.php?message=registration_success');
        exit();
    }
} else {
    // Registration failed
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Registration failed. Email might already exist.']);
        exit();
    } else {
        header('Location: login/register.php?error=registration_failed');
        exit();
    }
}