<?php

require_once '../classes/order_class.php';

/**
 * Order Controller - handles order operations
 */

/**
 * Create order
 * @param array $params - Order parameters (customer_id, invoice_no, total_amount, delivery_address, delivery_phone, special_instructions)
 * @return int|false - Order ID if successful, false otherwise
 */
function create_order_ctr($params)
{
    // Validate required fields
    if (empty($params['customer_id']) || empty($params['total_amount'])) {
        return false;
    }
    
    // Generate invoice number if not provided
    if (empty($params['invoice_no'])) {
        $order = new Order();
        $params['invoice_no'] = $order->generateInvoiceNo();
    }
    
    // Validate and sanitize
    $params['customer_id'] = (int)$params['customer_id'];
    $params['total_amount'] = (float)$params['total_amount'];
    $params['delivery_address'] = trim($params['delivery_address'] ?? '');
    $params['delivery_phone'] = trim($params['delivery_phone'] ?? '');
    $params['special_instructions'] = trim($params['special_instructions'] ?? '');
    
    if ($params['total_amount'] <= 0) {
        return false;
    }
    
    $order = new Order();
    return $order->createOrder($params);
}

/**
 * Add order details
 * @param array $params - Order detail parameters (order_id, product_id, qty)
 * @return bool - True if successful, false otherwise
 */
function add_order_details_ctr($params)
{
    // Validate required fields
    if (empty($params['order_id']) || empty($params['product_id']) || empty($params['qty'])) {
        return false;
    }
    
    // Validate and sanitize
    $params['order_id'] = (int)$params['order_id'];
    $params['product_id'] = (int)$params['product_id'];
    $params['qty'] = (int)$params['qty'];
    
    if ($params['order_id'] <= 0 || $params['product_id'] <= 0 || $params['qty'] <= 0) {
        return false;
    }
    
    $order = new Order();
    return $order->addOrderDetails($params);
}

/**
 * Record payment
 * @param array $params - Payment parameters (customer_id, order_id, amt, payment_method, payment_status, payment_reference, receipt_image)
 * @return int|false - Payment ID if successful, false otherwise
 */
function record_payment_ctr($params)
{
    // Validate required fields
    if (empty($params['customer_id']) || empty($params['order_id']) || empty($params['amt'])) {
        return false;
    }
    
    // Validate and sanitize
    $params['customer_id'] = (int)$params['customer_id'];
    $params['order_id'] = (int)$params['order_id'];
    $params['amt'] = (float)$params['amt'];
    $params['payment_method'] = trim($params['payment_method'] ?? 'cash');
    $params['payment_status'] = trim($params['payment_status'] ?? 'pending');
    $params['payment_reference'] = trim($params['payment_reference'] ?? '');
    $params['receipt_image'] = trim($params['receipt_image'] ?? '');
    
    if ($params['amt'] <= 0) {
        return false;
    }
    
    $order = new Order();
    return $order->recordPayment($params);
}

/**
 * Get past orders for user
 * @param int $customer_id - Customer ID
 * @return array|false - Array of orders if successful, false otherwise
 */
function get_past_orders_ctr($customer_id)
{
    $customer_id = (int)$customer_id;
    
    if ($customer_id <= 0) {
        return false;
    }
    
    $order = new Order();
    return $order->getPastOrders($customer_id);
}

/**
 * Get order by ID
 * @param int $order_id - Order ID
 * @return array|false - Order data if successful, false otherwise
 */
function get_order_by_id_ctr($order_id)
{
    $order_id = (int)$order_id;
    
    if ($order_id <= 0) {
        return false;
    }
    
    $order = new Order();
    return $order->getOrderById($order_id);
}

/**
 * Get order details (products)
 * @param int $order_id - Order ID
 * @return array|false - Order details if successful, false otherwise
 */
function get_order_details_ctr($order_id)
{
    $order_id = (int)$order_id;
    
    if ($order_id <= 0) {
        return false;
    }
    
    $order = new Order();
    return $order->getOrderDetails($order_id);
}

/**
 * Get payment by order ID
 * @param int $order_id - Order ID
 * @return array|false - Payment data if successful, false otherwise
 */
function get_payment_by_order_id_ctr($order_id)
{
    $order_id = (int)$order_id;
    
    if ($order_id <= 0) {
        return false;
    }
    
    $order = new Order();
    return $order->getPaymentByOrderId($order_id);
}
?>

