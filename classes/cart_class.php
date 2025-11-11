<?php

require_once '../database/database.php';

/**
 * Cart class for handling cart operations
 * Supports both logged-in users (c_id) and guest users (ip_add)
 */
class Cart
{
    /**
     * Get user identifier (customer ID if logged in, IP address if guest)
     * @return array - ['c_id' => int|null, 'ip_add' => string]
     */
    private function getUserIdentifier()
    {
        require_once '../src/settings/core.php';
        
        $c_id = null;
        $ip_add = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        if (is_user_logged_in()) {
            $c_id = get_user_id();
        }
        
        return ['c_id' => $c_id, 'ip_add' => $ip_add];
    }

    /**
     * Add a product to the cart
     * If product already exists, increment quantity instead of duplicating
     * @param int $product_id - Product ID
     * @param int $quantity - Quantity to add (default: 1)
     * @return bool - True if successful, false otherwise
     */
    public function addToCart($product_id, $quantity = 1)
    {
        try {
            $user = $this->getUserIdentifier();
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            
            if ($product_id <= 0 || $quantity <= 0) {
                return false;
            }
            
            // Check if product exists in cart
            $existing = $this->checkProductInCart($product_id);
            
            if ($existing) {
                // Update quantity
                $new_quantity = $existing['qty'] + $quantity;
                return $this->updateCartItem($product_id, $new_quantity);
            } else {
                // Insert new cart item
                if ($user['c_id']) {
                    $sql = "INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (?, ?, ?, ?)";
                    $params = [$product_id, $user['ip_add'], $user['c_id'], $quantity];
                } else {
                    $sql = "INSERT INTO cart (p_id, ip_add, qty) VALUES (?, ?, ?)";
                    $params = [$product_id, $user['ip_add'], $quantity];
                }
                
                $result = executeQuery($sql, $params);
                return $result !== false;
            }
        } catch (Exception $e) {
            error_log("Cart add failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the quantity of a product in the cart
     * @param int $product_id - Product ID
     * @param int $quantity - New quantity
     * @return bool - True if successful, false otherwise
     */
    public function updateCartItem($product_id, $quantity)
    {
        try {
            $user = $this->getUserIdentifier();
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            
            if ($product_id <= 0 || $quantity <= 0) {
                return false;
            }
            
            if ($user['c_id']) {
                $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
                $params = [$quantity, $product_id, $user['c_id']];
            } else {
                $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ? AND (c_id IS NULL OR c_id = 0)";
                $params = [$quantity, $product_id, $user['ip_add']];
            }
            
            $result = executeQuery($sql, $params);
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Cart update failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a product from the cart
     * @param int $product_id - Product ID
     * @return bool - True if successful, false otherwise
     */
    public function removeFromCart($product_id)
    {
        try {
            $user = $this->getUserIdentifier();
            $product_id = (int)$product_id;
            
            if ($product_id <= 0) {
                return false;
            }
            
            if ($user['c_id']) {
                $sql = "DELETE FROM cart WHERE p_id = ? AND c_id = ?";
                $params = [$product_id, $user['c_id']];
            } else {
                $sql = "DELETE FROM cart WHERE p_id = ? AND ip_add = ? AND (c_id IS NULL OR c_id = 0)";
                $params = [$product_id, $user['ip_add']];
            }
            
            $result = executeQuery($sql, $params);
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Cart remove failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all cart items for the current user
     * @return array|false - Array of cart items with product details if successful, false otherwise
     */
    public function getUserCart()
    {
        try {
            $user = $this->getUserIdentifier();
            
            if ($user['c_id']) {
                $sql = "SELECT c.p_id, c.qty, p.product_title, p.product_price, p.product_image, 
                               p.product_desc, p.product_id
                        FROM cart c
                        INNER JOIN products p ON c.p_id = p.product_id
                        WHERE c.c_id = ?
                        ORDER BY c.p_id";
                $params = [$user['c_id']];
            } else {
                $sql = "SELECT c.p_id, c.qty, p.product_title, p.product_price, p.product_image, 
                               p.product_desc, p.product_id
                        FROM cart c
                        INNER JOIN products p ON c.p_id = p.product_id
                        WHERE c.ip_add = ? AND (c.c_id IS NULL OR c.c_id = 0)
                        ORDER BY c.p_id";
                $params = [$user['ip_add']];
            }
            
            return fetchAll($sql, $params);
        } catch (Exception $e) {
            error_log("Get cart failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Empty the cart for the current user
     * @return bool - True if successful, false otherwise
     */
    public function emptyCart()
    {
        try {
            $user = $this->getUserIdentifier();
            
            if ($user['c_id']) {
                $sql = "DELETE FROM cart WHERE c_id = ?";
                $params = [$user['c_id']];
            } else {
                $sql = "DELETE FROM cart WHERE ip_add = ? AND (c_id IS NULL OR c_id = 0)";
                $params = [$user['ip_add']];
            }
            
            $result = executeQuery($sql, $params);
            return $result !== false;
        } catch (Exception $e) {
            error_log("Empty cart failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a product already exists in the cart
     * @param int $product_id - Product ID
     * @return array|false - Cart item data if exists, false otherwise
     */
    public function checkProductInCart($product_id)
    {
        try {
            $user = $this->getUserIdentifier();
            $product_id = (int)$product_id;
            
            if ($product_id <= 0) {
                return false;
            }
            
            if ($user['c_id']) {
                $sql = "SELECT * FROM cart WHERE p_id = ? AND c_id = ?";
                $params = [$product_id, $user['c_id']];
            } else {
                $sql = "SELECT * FROM cart WHERE p_id = ? AND ip_add = ? AND (c_id IS NULL OR c_id = 0)";
                $params = [$product_id, $user['ip_add']];
            }
            
            return fetchOne($sql, $params);
        } catch (Exception $e) {
            error_log("Check product in cart failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cart item count for the current user
     * @return int - Number of items in cart
     */
    public function getCartItemCount()
    {
        try {
            $user = $this->getUserIdentifier();
            
            if ($user['c_id']) {
                $sql = "SELECT SUM(qty) as total FROM cart WHERE c_id = ?";
                $params = [$user['c_id']];
            } else {
                $sql = "SELECT SUM(qty) as total FROM cart WHERE ip_add = ? AND (c_id IS NULL OR c_id = 0)";
                $params = [$user['ip_add']];
            }
            
            $result = fetchOne($sql, $params);
            return (int)($result['total'] ?? 0);
        } catch (Exception $e) {
            error_log("Get cart count failed: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get cart total amount for the current user
     * @return float - Total amount
     */
    public function getCartTotal()
    {
        try {
            $user = $this->getUserIdentifier();
            
            if ($user['c_id']) {
                $sql = "SELECT SUM(c.qty * p.product_price) as total 
                        FROM cart c
                        INNER JOIN products p ON c.p_id = p.product_id
                        WHERE c.c_id = ?";
                $params = [$user['c_id']];
            } else {
                $sql = "SELECT SUM(c.qty * p.product_price) as total 
                        FROM cart c
                        INNER JOIN products p ON c.p_id = p.product_id
                        WHERE c.ip_add = ? AND (c.c_id IS NULL OR c.c_id = 0)";
                $params = [$user['ip_add']];
            }
            
            $result = fetchOne($sql, $params);
            return (float)($result['total'] ?? 0.00);
        } catch (Exception $e) {
            error_log("Get cart total failed: " . $e->getMessage());
            return 0.00;
        }
    }
}
?>

