<?php

require_once '../database/database.php';

/**
 * Order class for handling order operations
 * Manages orders, order details, and payments
 */
class Order
{
    /**
     * Create a new order in the orders table
     * @param array $order_data - Order data array (customer_id, invoice_no, total_amount, delivery_address, delivery_phone, special_instructions)
     * @return int|false - Order ID if successful, false otherwise
     */
    public function createOrder($order_data)
    {
        try {
            $required_fields = ['customer_id', 'invoice_no', 'total_amount'];
            
            // Validate required fields
            foreach ($required_fields as $field) {
                if (!isset($order_data[$field]) || empty($order_data[$field])) {
                    return false;
                }
            }
            
            $sql = "INSERT INTO orders (customer_id, invoice_no, order_status, total_amount, delivery_address, delivery_phone, special_instructions) 
                    VALUES (?, ?, 'pending', ?, ?, ?, ?)";
            
            $params = [
                $order_data['customer_id'],
                $order_data['invoice_no'],
                $order_data['total_amount'],
                $order_data['delivery_address'] ?? null,
                $order_data['delivery_phone'] ?? null,
                $order_data['special_instructions'] ?? null
            ];
            
            $result = executeQuery($sql, $params);
            
            if ($result) {
                return getDB()->lastInsertId();
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Order creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add order details (product ID, quantity, price) to the orderdetails table
     * @param array $order_detail_data - Order detail data array (order_id, product_id, qty)
     * @return bool - True if successful, false otherwise
     */
    public function addOrderDetails($order_detail_data)
    {
        try {
            $required_fields = ['order_id', 'product_id', 'qty'];
            
            // Validate required fields
            foreach ($required_fields as $field) {
                if (!isset($order_detail_data[$field]) || empty($order_detail_data[$field])) {
                    return false;
                }
            }
            
            $sql = "INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)";
            
            $params = [
                $order_detail_data['order_id'],
                $order_detail_data['product_id'],
                $order_detail_data['qty']
            ];
            
            $result = executeQuery($sql, $params);
            return $result !== false;
        } catch (Exception $e) {
            error_log("Order detail creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Record simulated payment entries in the payments table
     * @param array $payment_data - Payment data array (customer_id, order_id, amt, payment_method, payment_status, payment_reference, receipt_image)
     * @return int|false - Payment ID if successful, false otherwise
     */
    public function recordPayment($payment_data)
    {
        try {
            $required_fields = ['customer_id', 'order_id', 'amt'];
            
            // Validate required fields
            foreach ($required_fields as $field) {
                if (!isset($payment_data[$field]) || empty($payment_data[$field])) {
                    return false;
                }
            }
            
            $sql = "INSERT INTO payment (customer_id, order_id, amt, currency, payment_method, payment_status, payment_reference, receipt_image) 
                    VALUES (?, ?, ?, 'USD', ?, ?, ?, ?)";
            
            $params = [
                $payment_data['customer_id'],
                $payment_data['order_id'],
                $payment_data['amt'],
                $payment_data['payment_method'] ?? 'cash',
                $payment_data['payment_status'] ?? 'pending',
                $payment_data['payment_reference'] ?? null,
                $payment_data['receipt_image'] ?? null
            ];
            
            $result = executeQuery($sql, $params);
            
            if ($result) {
                return getDB()->lastInsertId();
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Payment recording failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve past orders for a user
     * @param int $customer_id - Customer ID
     * @return array|false - Array of orders if successful, false otherwise
     */
    public function getPastOrders($customer_id)
    {
        try {
            $customer_id = (int)$customer_id;
            
            if ($customer_id <= 0) {
                return false;
            }
            
            $sql = "SELECT o.*, 
                           COUNT(od.id) as item_count,
                           SUM(od.qty) as total_items
                    FROM orders o
                    LEFT JOIN orderdetails od ON o.order_id = od.order_id
                    WHERE o.customer_id = ?
                    GROUP BY o.order_id
                    ORDER BY o.order_date DESC";
            
            return fetchAll($sql, [$customer_id]);
        } catch (Exception $e) {
            error_log("Get past orders failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get order by ID with full details
     * @param int $order_id - Order ID
     * @return array|false - Order data with details if successful, false otherwise
     */
    public function getOrderById($order_id)
    {
        try {
            $order_id = (int)$order_id;
            
            if ($order_id <= 0) {
                return false;
            }
            
            $sql = "SELECT * FROM orders WHERE order_id = ?";
            return fetchOne($sql, [$order_id]);
        } catch (Exception $e) {
            error_log("Get order by ID failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get order details (products) for an order
     * @param int $order_id - Order ID
     * @return array|false - Array of order details if successful, false otherwise
     */
    public function getOrderDetails($order_id)
    {
        try {
            $order_id = (int)$order_id;
            
            if ($order_id <= 0) {
                return false;
            }
            
            $sql = "SELECT od.*, p.product_title, p.product_price, p.product_image
                    FROM orderdetails od
                    INNER JOIN products p ON od.product_id = p.product_id
                    WHERE od.order_id = ?
                    ORDER BY od.id";
            
            return fetchAll($sql, [$order_id]);
        } catch (Exception $e) {
            error_log("Get order details failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get payment information for an order
     * @param int $order_id - Order ID
     * @return array|false - Payment data if successful, false otherwise
     */
    public function getPaymentByOrderId($order_id)
    {
        try {
            $order_id = (int)$order_id;
            
            if ($order_id <= 0) {
                return false;
            }
            
            $sql = "SELECT * FROM payment WHERE order_id = ? ORDER BY payment_date DESC LIMIT 1";
            return fetchOne($sql, [$order_id]);
        } catch (Exception $e) {
            error_log("Get payment by order ID failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate a unique order reference/invoice number
     * @return string - Unique invoice number
     */
    public function generateInvoiceNo()
    {
        $prefix = 'INV';
        $date = date('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . '-' . $date . '-' . $random;
    }
}
?>

