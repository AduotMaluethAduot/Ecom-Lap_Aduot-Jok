<?php
// Order Food page for customers
require_once '../src/settings/core.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    header('Location: ../login/login.php');
    exit();
}

// Get customer information
$customer_id = get_user_id();
$customer_name = get_user_name();
$customer_role = get_user_role();

// Redirect admin users to admin dashboard
if (is_user_admin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

// Get products from database
require_once '../controllers/product_controller.php';
$products = get_all_products_ctr();

// Get categories for filtering
require_once '../controllers/category_controller.php';
$categories = get_all_categories_ctr();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Food - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .order-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            margin: 2rem 0;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .product-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff6b6b;
        }
        .btn-order {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-order:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
            color: white;
        }
        .cart-sidebar {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 2rem;
        }
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 0.5rem 0;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .quantity-btn:hover {
            background: #f8f9fa;
        }
        .filter-tabs {
            margin-bottom: 2rem;
        }
        .filter-tab {
            background: white;
            border: 2px solid #e9ecef;
            color: #6c757d;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .filter-tab.active {
            background: #ff6b6b;
            border-color: #ff6b6b;
            color: white;
        }
        .filter-tab:hover {
            border-color: #ff6b6b;
            color: #ff6b6b;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php" style="color: #ff6b6b;">
                <i class="fas fa-utensils me-2"></i>Taste of Africa
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="customer_dashboard.php">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="menu.php">
                    <i class="fas fa-utensils me-1"></i>Menu
                </a>
                <a class="nav-link" href="my_orders.php">
                    <i class="fas fa-shopping-bag me-1"></i>Orders
                </a>
                <a class="nav-link" href="../login/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="order-container">
            <h1 class="text-center mb-4">
                <i class="fas fa-shopping-cart me-2"></i>Order Food
            </h1>
            <p class="text-center text-muted mb-5">Select your favorite dishes and place an order</p>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <div class="filter-tab active" data-category="all">
                    <i class="fas fa-th me-2"></i>All Items
                </div>
                <?php if ($categories): ?>
                    <?php foreach ($categories as $category): ?>
                        <div class="filter-tab" data-category="<?php echo $category['cat_id']; ?>">
                            <i class="fas fa-tag me-2"></i><?php echo htmlspecialchars($category['cat_name']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="row">
                <!-- Products Section -->
                <div class="col-lg-8">
                    <?php if ($products && count($products) > 0): ?>
                        <div class="row" id="products-container">
                            <?php foreach ($products as $product): ?>
                                <div class="col-md-6 col-lg-4 product-item" data-category="<?php echo $product['product_cat']; ?>">
                                    <div class="product-card">
                                        <?php if (!empty($product['product_image'])): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($product['product_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['product_title']); ?>" 
                                                 class="product-image">
                                        <?php else: ?>
                                            <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <h5 class="mt-3"><?php echo htmlspecialchars($product['product_title']); ?></h5>
                                        <p class="text-muted"><?php echo htmlspecialchars($product['product_desc']); ?></p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="price">$<?php echo number_format($product['product_price'], 2); ?></span>
                                            <button class="btn btn-order" onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($product['product_title']); ?>', <?php echo $product['product_price']; ?>)">
                                                <i class="fas fa-plus me-1"></i>Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-utensils text-muted" style="font-size: 4rem;"></i>
                            <h3 class="mt-3 text-muted">No products available</h3>
                            <p class="text-muted">Check back later for our delicious African dishes!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Cart Sidebar -->
                <div class="col-lg-4">
                    <div class="cart-sidebar">
                        <h4 class="mb-3">
                            <i class="fas fa-shopping-cart me-2"></i>Your Cart
                        </h4>
                        <div id="cart-items">
                            <p class="text-muted text-center">Your cart is empty</p>
                        </div>
                        <div id="cart-total" class="mt-3" style="display: none;">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total:</strong>
                                <strong id="total-amount">$0.00</strong>
                            </div>
                            <button class="btn btn-order w-100 mt-3" onclick="placeOrder()">
                                <i class="fas fa-credit-card me-2"></i>Place Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let cart = [];

        // Filter functionality
        $('.filter-tab').on('click', function() {
            $('.filter-tab').removeClass('active');
            $(this).addClass('active');
            
            const category = $(this).data('category');
            
            if (category === 'all') {
                $('.product-item').show();
            } else {
                $('.product-item').hide();
                $(`.product-item[data-category="${category}"]`).show();
            }
        });

        // Add to cart functionality
        function addToCart(productId, productName, price) {
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: price,
                    quantity: 1
                });
            }
            
            updateCartDisplay();
            
            Swal.fire({
                icon: 'success',
                title: 'Added to Cart!',
                text: `${productName} has been added to your cart`,
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Update cart display
        function updateCartDisplay() {
            const cartItems = $('#cart-items');
            const cartTotal = $('#cart-total');
            
            if (cart.length === 0) {
                cartItems.html('<p class="text-muted text-center">Your cart is empty</p>');
                cartTotal.hide();
            } else {
                let html = '';
                let total = 0;
                
                cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    
                    html += `
                        <div class="cart-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">${item.name}</h6>
                                    <small class="text-muted">$${item.price.toFixed(2)} each</small>
                                </div>
                                <div class="quantity-controls">
                                    <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="mx-2">${item.quantity}</span>
                                    <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">$${itemTotal.toFixed(2)}</small>
                            </div>
                        </div>
                    `;
                });
                
                cartItems.html(html);
                $('#total-amount').text(`$${total.toFixed(2)}`);
                cartTotal.show();
            }
        }

        // Update quantity
        function updateQuantity(productId, change) {
            const item = cart.find(item => item.id === productId);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    cart = cart.filter(item => item.id !== productId);
                }
                updateCartDisplay();
            }
        }

        // Place order
        function placeOrder() {
            if (cart.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Empty Cart',
                    text: 'Please add some items to your cart first'
                });
                return;
            }

            Swal.fire({
                title: 'Proceed to Checkout?',
                text: `You are about to checkout with ${cart.length} item(s)`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, checkout!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to checkout page with cart data
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'checkout.php';
                    
                    const cartInput = document.createElement('input');
                    cartInput.type = 'hidden';
                    cartInput.name = 'cart_data';
                    cartInput.value = JSON.stringify(cart);
                    
                    form.appendChild(cartInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
