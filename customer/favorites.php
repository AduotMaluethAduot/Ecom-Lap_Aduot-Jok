<?php
// Favorites page for customers
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

// For demo purposes, we'll simulate some favorite products
// In a real application, you would have a favorites table in the database
$favorite_products = [];
if ($products) {
    // Simulate some favorites (first 3 products)
    $favorite_products = array_slice($products, 0, 3);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .favorites-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            margin: 2rem 0;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .favorite-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            position: relative;
        }
        .favorite-card:hover {
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
        .btn-favorite {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-favorite:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
            color: white;
        }
        .btn-remove-favorite {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc3545;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-remove-favorite:hover {
            background: #dc3545;
            color: white;
        }
        .heart-icon {
            color: #ff6b6b;
            font-size: 1.2rem;
        }
        .empty-favorites {
            text-align: center;
            padding: 4rem 2rem;
        }
        .empty-favorites i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
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
                <a class="nav-link" href="order_food.php">
                    <i class="fas fa-shopping-cart me-1"></i>Order Food
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
        <div class="favorites-container">
            <h1 class="text-center mb-4">
                <i class="fas fa-heart me-2"></i>My Favorites
            </h1>
            <p class="text-center text-muted mb-5">Your favorite African dishes</p>

            <!-- Stats Card -->
            <div class="stats-card">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-number" id="favorites-count"><?php echo count($favorite_products); ?></div>
                        <div>Favorite Items</div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-number"><?php echo count($products); ?></div>
                        <div>Total Menu Items</div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-number">0</div>
                        <div>Orders from Favorites</div>
                    </div>
                </div>
            </div>

            <?php if (count($favorite_products) > 0): ?>
                <div class="row">
                    <?php foreach ($favorite_products as $product): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="favorite-card" data-product-id="<?php echo $product['product_id']; ?>">
                                <button class="btn-remove-favorite" onclick="removeFromFavorites(<?php echo $product['product_id']; ?>)">
                                    <i class="fas fa-times"></i>
                                </button>
                                
                                <?php if (!empty($product['product_image'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($product['product_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['product_title']); ?>" 
                                         class="product-image">
                                <?php else: ?>
                                    <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                        <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <h5 class="mt-3">
                                    <i class="fas fa-heart heart-icon me-2"></i>
                                    <?php echo htmlspecialchars($product['product_title']); ?>
                                </h5>
                                <p class="text-muted"><?php echo htmlspecialchars($product['product_desc']); ?></p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price">$<?php echo number_format($product['product_price'], 2); ?></span>
                                    <button class="btn btn-favorite" onclick="orderFromFavorites(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($product['product_title']); ?>', <?php echo $product['product_price']; ?>)">
                                        <i class="fas fa-shopping-cart me-1"></i>Order Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Quick Actions -->
                <div class="text-center mt-4">
                    <button class="btn btn-outline-primary me-3" onclick="orderAllFavorites()">
                        <i class="fas fa-shopping-cart me-2"></i>Order All Favorites
                    </button>
                    <button class="btn btn-outline-secondary" onclick="clearAllFavorites()">
                        <i class="fas fa-trash me-2"></i>Clear All Favorites
                    </button>
                </div>
            <?php else: ?>
                <div class="empty-favorites">
                    <i class="fas fa-heart-broken"></i>
                    <h3 class="text-muted">No favorites yet</h3>
                    <p class="text-muted">Start exploring our menu and add your favorite dishes!</p>
                    <a href="menu.php" class="btn btn-primary mt-3">
                        <i class="fas fa-utensils me-2"></i>Browse Menu
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Remove from favorites
        function removeFromFavorites(productId) {
            Swal.fire({
                title: 'Remove from Favorites?',
                text: 'This item will be removed from your favorites list',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Remove the card from the page
                    $(`.favorite-card[data-product-id="${productId}"]`).parent().fadeOut(300, function() {
                        $(this).remove();
                        updateFavoritesCount();
                        
                        // Check if no favorites left
                        if ($('.favorite-card').length === 0) {
                            location.reload(); // Reload to show empty state
                        }
                    });
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Removed!',
                        text: 'Item removed from favorites',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        // Order from favorites
        function orderFromFavorites(productId, productName, price) {
            Swal.fire({
                title: 'Quick Order',
                text: `Add ${productName} to your cart?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, add to cart!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to Cart!',
                        text: `${productName} has been added to your cart`,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect to order food page
                        window.location.href = 'order_food.php';
                    });
                }
            });
        }

        // Order all favorites
        function orderAllFavorites() {
            const favoriteCount = $('.favorite-card').length;
            
            Swal.fire({
                title: 'Order All Favorites?',
                text: `Add all ${favoriteCount} favorite items to your cart?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, add all!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'All Added!',
                        text: 'All favorite items have been added to your cart',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect to order food page
                        window.location.href = 'order_food.php';
                    });
                }
            });
        }

        // Clear all favorites
        function clearAllFavorites() {
            Swal.fire({
                title: 'Clear All Favorites?',
                text: 'This will remove all items from your favorites list',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, clear all!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.favorite-card').fadeOut(300, function() {
                        $(this).remove();
                        updateFavoritesCount();
                        
                        // Show empty state
                        if ($('.favorite-card').length === 0) {
                            location.reload();
                        }
                    });
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Cleared!',
                        text: 'All favorites have been removed',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        // Update favorites count
        function updateFavoritesCount() {
            const count = $('.favorite-card').length;
            $('#favorites-count').text(count);
        }
    </script>
</body>
</html>
