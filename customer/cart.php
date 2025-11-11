<?php
// Cart page - displays all items in the user's cart
require_once '../src/settings/core.php';
require_once '../controllers/cart_controller.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    header('Location: ../login/login.php');
    exit();
}

// Redirect admin users to admin dashboard
if (is_user_admin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

// Get customer information
$customer_id = get_user_id();
$customer_name = get_user_name();

// Get cart items
$cart_items = get_user_cart_ctr();
$cart_total = get_cart_total_ctr();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .cart-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            margin: 2rem 0;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .cart-item {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 0;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .cart-item img {
            max-width: 100px;
            height: auto;
            border-radius: 8px;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .quantity-controls input {
            width: 60px;
            text-align: center;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }
        .empty-cart {
            text-align: center;
            padding: 3rem;
        }
        .empty-cart i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="customer_dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="order_food.php">Menu</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-12">
                <div class="cart-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
                        </h2>
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="window.location.href='order_food.php'">
                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                            </button>
                            <?php if (!empty($cart_items)): ?>
                            <button type="button" class="btn btn-outline-danger" onclick="emptyCart()">
                                <i class="fas fa-trash me-2"></i>Empty Cart
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Cart Items -->
                    <div id="cart-items">
                        <?php if (empty($cart_items)): ?>
                            <div class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <h4>Your cart is empty</h4>
                                <p class="text-muted">Add some delicious items to your cart!</p>
                                <a href="order_food.php" class="btn btn-primary">
                                    <i class="fas fa-utensils me-2"></i>Browse Menu
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($cart_items as $item): ?>
                                <?php 
                                $item_subtotal = floatval($item['product_price']) * intval($item['qty']);
                                ?>
                                <div class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <img src="../<?php echo htmlspecialchars($item['product_image'] ?? 'uploads/products/default.svg'); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['product_title']); ?>" 
                                                 class="img-fluid rounded">
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($item['product_title']); ?></h6>
                                            <p class="text-muted mb-0 small">$<?php echo number_format($item['product_price'], 2); ?> each</p>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo intval($item['qty']) - 1; ?>)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control text-center" 
                                                       value="<?php echo $item['qty']; ?>" 
                                                       min="1" 
                                                       id="qty-<?php echo $item['product_id']; ?>"
                                                       onchange="updateQuantity(<?php echo $item['product_id']; ?>, parseInt(this.value))">
                                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo intval($item['qty']) + 1; ?>)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <strong>$<?php echo number_format($item_subtotal, 2); ?></strong>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button class="btn btn-sm btn-danger" onclick="removeFromCart(<?php echo $item['product_id']; ?>)" title="Remove">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Cart Summary -->
                    <?php if (!empty($cart_items)): ?>
                    <div class="mt-4 pt-4 border-top">
                        <div class="row">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Order Summary</h5>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span id="cart-subtotal">$<?php echo number_format($cart_total, 2); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <strong>Total:</strong>
                                            <strong id="cart-total">$<?php echo number_format($cart_total, 2); ?></strong>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-checkout" id="checkout-button" onclick="window.location.href='checkout.php'">
                                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                            </button>
                                            <button type="button" class="btn btn-outline-primary" onclick="window.location.href='order_food.php'">
                                                <i class="fas fa-plus me-2"></i>Continue Shopping
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../public/js/cart.js"></script>
</body>
</html>

