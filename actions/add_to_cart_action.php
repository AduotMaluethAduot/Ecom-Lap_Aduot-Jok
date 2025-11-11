<?php
// Add to Cart Action
require_once '../src/settings/core.php';
require_once '../controllers/cart_controller.php';
require_once '../controllers/product_controller.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to add items to cart']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Get product ID and quantity
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if (empty($product_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
    exit();
}

if ($quantity <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Quantity must be greater than 0']);
    exit();
}

try {
    // Verify product exists
    $product = get_product_by_id_ctr($product_id);
    if (!$product) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        exit();
    }
    
    // Add to cart
    $params = [
        'product_id' => $product_id,
        'quantity' => $quantity
    ];
    
    $result = add_to_cart_ctr($params);
    
    if ($result) {
        // Get updated cart count
        $cart_count = get_cart_item_count_ctr();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Product added to cart successfully',
            'cart_count' => $cart_count,
            'product_name' => $product['product_title']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add product to cart'
        ]);
    }
} catch (Exception $e) {
    error_log("Add to cart error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while adding product to cart'
    ]);
}
?>

