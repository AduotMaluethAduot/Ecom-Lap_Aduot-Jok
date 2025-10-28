<?php

require_once '../classes/customer_class.php';

/**
 * Customer Controller - handles customer operations
 */

/**
 * Register a new customer
 * @param array $customer_data - Customer information
 * @return int|false - Customer ID if successful, false otherwise
 */
function register_customer_ctr($customer_data)
{
    $customer = new Customer();
    $customer_id = $customer->createCustomer($customer_data);
    if ($customer_id) {
        return $customer_id;
    }
    return false;
}

/**
 * Get customer by email address
 * @param string $email - Customer email
 * @return array|false - Customer data if found, false otherwise
 */
function get_customer_by_email_ctr($email)
{
    $customer = new Customer();
    return $customer->getCustomerByEmail($email);
}

/**
 * Customer login functionality
 * @param string $email - Customer email
 * @param string $password - Customer password
 * @return array|false - Customer data if login successful, false otherwise
 */
function login_customer_ctr($email, $password)
{
    $customer = new Customer();
    $customer_data = $customer->validateLogin($email, $password);
    
    if ($customer_data) {
        // Return customer data for session management
        return array(
            'status' => 'success',
            'data' => $customer_data,
            'message' => 'Login successful'
        );
    } else {
        return array(
            'status' => 'error',
            'data' => null,
            'message' => 'Invalid email or password'
        );
    }
}

/**
 * Update customer information
 * @param int $customer_id - Customer ID
 * @param array $customer_data - Updated customer information
 * @return bool - True if successful, false otherwise
 */
function update_customer_ctr($customer_id, $customer_data)
{
    $customer = new Customer($customer_id);
    return $customer->updateCustomer($customer_data);
}

/**
 * Get customer by ID
 * @param int $customer_id - Customer ID
 * @return Customer|false - Customer object if found, false otherwise
 */
function get_customer_by_id_ctr($customer_id)
{
    $customer = new Customer($customer_id);
    if ($customer->getCustomerId()) {
        return $customer;
    }
    return false;
}

/**
 * Validate customer credentials
 * @param string $email - Customer email
 * @param string $password - Customer password
 * @return array - Response array with status and message
 */
function validate_customer_login_ctr($email, $password)
{
    // Input validation
    if (empty($email) || empty($password)) {
        return array(
            'status' => 'error',
            'message' => 'Email and password are required'
        );
    }
    
    // Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return array(
            'status' => 'error',
            'message' => 'Invalid email format'
        );
    }
    
    // Password length validation
    if (strlen($password) < 6) {
        return array(
            'status' => 'error',
            'message' => 'Password must be at least 6 characters long'
        );
    }
    
    return login_customer_ctr($email, $password);
}