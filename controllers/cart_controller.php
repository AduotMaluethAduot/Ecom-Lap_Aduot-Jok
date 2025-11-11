<?php

require_once '../classes/cart_class.php';

/**
 * Cart Controller - handles cart operations
 */

/**
 * Add product to cart
 * @param array $params - Parameters array (product_id, quantity)
 * @return bool - True if successful, false otherwise
 */
function add_to_cart_ctr($params)
{
    // Validate input
    if (empty($params['product_id'])) {
        return false;
    }
    
    $product_id = (int)$params['product_id'];
    $quantity = isset($params['quantity']) ? (int)$params['quantity'] : 1;
    
    if ($product_id <= 0 || $quantity <= 0) {
        return false;
    }
    
    $cart = new Cart();
    return $cart->addToCart($product_id, $quantity);
}

/**
 * Update cart item quantity
 * @param int $product_id - Product ID
 * @param int $qty - New quantity
 * @return bool - True if successful, false otherwise
 */
function update_cart_item_ctr($product_id, $qty)
{
    // Validate input
    $product_id = (int)$product_id;
    $qty = (int)$qty;
    
    if ($product_id <= 0 || $qty <= 0) {
        return false;
    }
    
    $cart = new Cart();
    return $cart->updateCartItem($product_id, $qty);
}

/**
 * Remove item from cart
 * @param int $product_id - Product ID
 * @return bool - True if successful, false otherwise
 */
function remove_from_cart_ctr($product_id)
{
    // Validate input
    $product_id = (int)$product_id;
    
    if ($product_id <= 0) {
        return false;
    }
    
    $cart = new Cart();
    return $cart->removeFromCart($product_id);
}

/**
 * Get user cart items
 * @param int|null $customer_id - Customer ID (optional, will use current user if not provided)
 * @return array|false - Cart items if successful, false otherwise
 */
function get_user_cart_ctr($customer_id = null)
{
    $cart = new Cart();
    return $cart->getUserCart();
}

/**
 * Empty user cart
 * @param int|null $customer_id - Customer ID (optional, will use current user if not provided)
 * @return bool - True if successful, false otherwise
 */
function empty_cart_ctr($customer_id = null)
{
    $cart = new Cart();
    return $cart->emptyCart();
}

/**
 * Get cart item count
 * @return int - Number of items in cart
 */
function get_cart_item_count_ctr()
{
    $cart = new Cart();
    return $cart->getCartItemCount();
}

/**
 * Get cart total amount
 * @return float - Total amount
 */
function get_cart_total_ctr()
{
    $cart = new Cart();
    return $cart->getCartTotal();
}
?>

