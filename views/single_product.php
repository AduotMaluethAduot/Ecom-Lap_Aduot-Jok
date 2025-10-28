<?php
// Single Product Page - Customer-facing product details
require_once '../src/settings/core.php';
require_once '../actions/product_actions.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (empty($product_id)) {
    header('Location: all_product.php');
    exit();
}

// Get product details
$result = handleProductRequest('view_single', ['product_id' => $product_id]);

if ($result['status'] !== 'success') {
    header('Location: all_product.php');
    exit();
}

$product = $result['data'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_title']); ?> - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .product-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            margin: 2rem 0;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .product-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .product-details {
            padding: 2rem;
        }
        .product-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }
        .product-price {
            font-size: 2rem;
            font-weight: bold;
            color: #ff6b6b;
            margin-bottom: 1rem;
        }
        .product-description {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #666;
            margin-bottom: 2rem;
        }
        .product-meta {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .btn-add-cart {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
            color: white;
        }
        .btn-secondary-custom {
            background: #6c757d;
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-secondary-custom:hover {
            background: #5a6268;
            color: white;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
        }
        .breadcrumb-item a {
            color: #ff6b6b;
            text-decoration: none;
        }
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php" style="color: #ff6b6b;">
                <i class="fas fa-utensils me-2"></i>Taste of Africa
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home me-1"></i>Home
                </a>
                <a class="nav-link" href="all_product.php">
                    <i class="fas fa-box-open me-1"></i>All Products
                </a>
                <?php if (is_user_logged_in()): ?>
                    <a class="nav-link" href="customer_dashboard.php">
                        <i class="fas fa-user me-1"></i>Dashboard
                    </a>
                    <a class="nav-link" href="login/logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                <?php else: ?>
                    <a class="nav-link" href="login/register.php">
                        <i class="fas fa-user-plus me-1"></i>Register
                    </a>
                    <a class="nav-link" href="login/login.php">
                        <i class="fas fa-sign-in-alt me-1"></i>Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="all_product.php">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['product_title']); ?></li>
            </ol>
        </nav>

        <div class="product-container">
            <div class="row">
                <!-- Product Image -->
                <div class="col-md-6">
                    <?php if (!empty($product['product_image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($product['product_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_title']); ?>" 
                             class="product-image">
                    <?php else: ?>
                        <div class="product-image d-flex align-items-center justify-content-center bg-light">
                            <i class="fas fa-image text-muted" style="font-size: 5rem;"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Product Details -->
                <div class="col-md-6">
                    <div class="product-details">
                        <h1 class="product-title"><?php echo htmlspecialchars($product['product_title']); ?></h1>
                        
                        <div class="product-price">$<?php echo number_format($product['product_price'], 2); ?></div>
                        
                        <div class="product-description">
                            <?php echo nl2br(htmlspecialchars($product['product_desc'])); ?>
                        </div>

                        <!-- Product Meta Information -->
                        <div class="product-meta">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Product ID:</strong><br>
                                    <span class="text-muted">#<?php echo $product['product_id']; ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Category:</strong><br>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($product['cat_name']); ?></span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Brand:</strong><br>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($product['brand_name']); ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Keywords:</strong><br>
                                    <span class="text-muted"><?php echo htmlspecialchars($product['product_keywords']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <button class="btn-add-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn-secondary-custom" onclick="addToFavorites(<?php echo $product['product_id']; ?>)">
                                    <i class="fas fa-heart me-2"></i>Add to Favorites
                                </button>
                            </div>
                        </div>

                        <!-- Additional Actions -->
                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <a href="all_product.php?category=<?php echo $product['product_cat']; ?>" 
                                   class="btn btn-outline-primary w-100">
                                    <i class="fas fa-tags me-2"></i>More in <?php echo htmlspecialchars($product['cat_name']); ?>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="all_product.php?brand=<?php echo $product['product_brand']; ?>" 
                                   class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-copyright me-2"></i>More from <?php echo htmlspecialchars($product['brand_name']); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section (Placeholder) -->
        <div class="product-container">
            <h3 class="mb-4">
                <i class="fas fa-star me-2"></i>You Might Also Like
            </h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-utensils text-muted" style="font-size: 3rem;"></i>
                            <h5 class="card-title mt-3">Related Products</h5>
                            <p class="card-text">More products from the same category will be displayed here.</p>
                            <a href="all_product.php?category=<?php echo $product['product_cat']; ?>" class="btn btn-primary">
                                View All
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-heart text-muted" style="font-size: 3rem;"></i>
                            <h5 class="card-title mt-3">Popular Items</h5>
                            <p class="card-text">Check out our most popular African dishes.</p>
                            <a href="all_product.php" class="btn btn-primary">
                                View All
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-fire text-muted" style="font-size: 3rem;"></i>
                            <h5 class="card-title mt-3">New Arrivals</h5>
                            <p class="card-text">Discover our latest additions to the menu.</p>
                            <a href="all_product.php" class="btn btn-primary">
                                View All
                            </a>
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
        function addToCart(productId) {
            Swal.fire({
                title: 'Add to Cart',
                text: 'This feature will be available soon!',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        function addToFavorites(productId) {
            Swal.fire({
                title: 'Add to Favorites',
                text: 'This feature will be available soon!',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }
    </script>
</body>
</html>
