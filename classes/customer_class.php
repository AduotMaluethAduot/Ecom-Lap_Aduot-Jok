<?php

require_once '../database/database.php';

/**
 * Customer class for handling customer operations
 * Updated to use PDO database system
 */
class Customer
{
    private $customer_id;
    private $customer_name;
    private $customer_email;
    private $customer_pass;
    private $customer_country;
    private $customer_city;
    private $customer_contact;
    private $customer_image;
    private $user_role;

    public function __construct($customer_id = null)
    {
        if ($customer_id) {
            $this->customer_id = $customer_id;
            $this->loadCustomer();
        }
    }

    /**
     * Load customer data by customer ID
     */
    private function loadCustomer($customer_id = null)
    {
        if ($customer_id) {
            $this->customer_id = $customer_id;
        }
        if (!$this->customer_id) {
            return false;
        }
        
        $result = fetchOne("SELECT * FROM customer WHERE customer_id = ?", [$this->customer_id]);
        
        if ($result) {
            $this->customer_name = $result['customer_name'];
            $this->customer_email = $result['customer_email'];
            $this->customer_pass = $result['customer_pass'];
            $this->customer_country = $result['customer_country'];
            $this->customer_city = $result['customer_city'];
            $this->customer_contact = $result['customer_contact'];
            $this->customer_image = $result['customer_image'];
            $this->user_role = $result['user_role'];
            return true;
        }
        return false;
    }

    /**
     * Get customer by email address with password verification
     * @param string $email - Customer email
     * @param string $password - Plain text password to verify
     * @return array|false - Customer data if login successful, false otherwise
     */
    public function getCustomerByEmail($email, $password = null)
    {
        $result = fetchOne("SELECT * FROM customer WHERE customer_email = ?", [$email]);
        
        if ($result && $password !== null) {
            // Verify password if provided
            if (password_verify($password, $result['customer_pass'])) {
                return $result;
            } else {
                return false;
            }
        } elseif ($result && $password === null) {
            // Return customer data without password verification
            return $result;
        }
        
        return false;
    }

    /**
     * Create a new customer
     * @param array $customer_data - Customer information
     * @return int|false - Customer ID if successful, false otherwise
     */
    public function createCustomer($customer_data)
    {
        // Hash the password
        $hashed_password = password_hash($customer_data['password'], PASSWORD_DEFAULT);
        
        $name = $customer_data['name'];
        $email = $customer_data['email'];
        $country = $customer_data['country'];
        $city = $customer_data['city'];
        $contact = $customer_data['contact'];
        $role = isset($customer_data['role']) ? $customer_data['role'] : 'customer';
        
        try {
            $result = executeQuery(
                "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$name, $email, $hashed_password, $country, $city, $contact, $role]
            );
            
            if ($result) {
                return getDB()->lastInsertId();
            }
        } catch (Exception $e) {
            error_log("Customer creation failed: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Update customer information
     * @param array $customer_data - Updated customer information
     * @return bool - True if successful, false otherwise
     */
    public function updateCustomer($customer_data)
    {
        if (!$this->customer_id) {
            return false;
        }
        
        $allowed_fields = ['customer_name', 'customer_email', 'customer_pass', 'customer_country', 'customer_city', 'customer_contact'];
        $update_fields = [];
        $values = [];
        
        foreach ($customer_data as $field => $value) {
            if (in_array($field, $allowed_fields)) {
                if ($field === 'customer_pass') {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                }
                $update_fields[] = "$field = ?";
                $values[] = $value;
            }
        }
        
        if (empty($update_fields)) {
            return false;
        }
        
        $values[] = $this->customer_id;
        $sql = "UPDATE customer SET " . implode(', ', $update_fields) . " WHERE customer_id = ?";
        
        try {
            $result = executeQuery($sql, $values);
            if ($result) {
                // Update local properties
                foreach ($customer_data as $field => $value) {
                    switch ($field) {
                        case 'customer_name':
                            $this->customer_name = $value;
                            break;
                        case 'customer_email':
                            $this->customer_email = $value;
                            break;
                        case 'customer_country':
                            $this->customer_country = $value;
                            break;
                        case 'customer_city':
                            $this->customer_city = $value;
                            break;
                        case 'customer_contact':
                            $this->customer_contact = $value;
                            break;
                    }
                }
                return true;
            }
        } catch (Exception $e) {
            error_log("Customer update failed: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Validate customer login credentials
     * @param string $email - Customer email
     * @param string $password - Plain text password
     * @return array|false - Customer data if valid, false otherwise
     */
    public function validateLogin($email, $password)
    {
        return $this->getCustomerByEmail($email, $password);
    }

    // Getter methods
    public function getCustomerId() { return $this->customer_id; }
    public function getCustomerName() { return $this->customer_name; }
    public function getCustomerEmail() { return $this->customer_email; }
    public function getCustomerCountry() { return $this->customer_country; }
    public function getCustomerCity() { return $this->customer_city; }
    public function getCustomerContact() { return $this->customer_contact; }
    public function getCustomerImage() { return $this->customer_image; }
    public function getUserRole() { return $this->user_role; }
}