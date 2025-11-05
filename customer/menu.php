<?php
// Menu page for customers
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
    <title>Menu - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .menu-container {
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
        .btn-menu {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-menu:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
            color: white;
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
        .category-section {
            margin-bottom: 3rem;
        }
        .category-title {
            color: #ff6b6b;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #ff6b6b;
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
                <a class="nav-link active" href="menu.php">
                    <i class="fas fa-utensils me-1"></i>Menu
                </a>
                <a class="nav-link" href="order_food.php">
                    <i class="fas fa-shopping-cart me-1"></i>Order Food
                </a>
                <a class="nav-link" href="favorites.php">
                    <i class="fas fa-heart me-1"></i>Favorites
                </a>
                <a class="nav-link" href="../login/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="menu-container">
            <h1 class="text-center mb-4">
                <i class="fas fa-utensils me-2"></i>Our Menu
            </h1>
            <p class="text-center text-muted mb-5">Discover the authentic flavors of Africa</p>

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

            <?php if ($products && count($products) > 0): ?>
                <!-- Group products by category -->
                <?php 
                $products_by_category = [];
                foreach ($products as $product) {
                    $cat_id = $product['product_cat'];
                    if (!isset($products_by_category[$cat_id])) {
                        $products_by_category[$cat_id] = [
                            'category_name' => $product['cat_name'] ?? 'Uncategorized',
                            'products' => []
                        ];
                    }
                    $products_by_category[$cat_id]['products'][] = $product;
                }
                ?>

                <?php foreach ($products_by_category as $cat_id => $category_data): ?>
                    <div class="category-section" data-category="<?php echo $cat_id; ?>">
                        <h2 class="category-title">
                            <i class="fas fa-tag me-2"></i><?php echo htmlspecialchars($category_data['category_name']); ?>
                        </h2>
                        <div class="row">
                            <?php foreach ($category_data['products'] as $product): ?>
                                <div class="col-md-6 col-lg-4 product-item" data-category="<?php echo $product['product_cat']; ?>">
                                    <div class="product-card">
                                        <?php if (!empty($product['product_image'])): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($product['product_image']); ?>" 
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
                                            <div>
                                                <button class="btn btn-menu me-2" onclick="addToFavorites(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($product['product_title']); ?>')">
                                                    <i class="fas fa-heart me-1"></i>Favorite
                                                </button>
                                                <a href="order_food.php" class="btn btn-menu">
                                                    <i class="fas fa-shopping-cart me-1"></i>Order
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-utensils text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3 text-muted">No products available</h3>
                    <p class="text-muted">Check back later for our delicious African dishes!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Filter functionality
        $('.filter-tab').on('click', function() {
            $('.filter-tab').removeClass('active');
            $(this).addClass('active');
            
            const category = $(this).data('category');
            
            if (category === 'all') {
                $('.category-section').show();
                $('.product-item').show();
            } else {
                $('.category-section').hide();
                $(`.category-section[data-category="${category}"]`).show();
                $('.product-item').hide();
                $(`.product-item[data-category="${category}"]`).show();
            }
        });

        // Add to favorites functionality
        function addToFavorites(productId, productName) {
            Swal.fire({
                icon: 'success',
                title: 'Added to Favorites!',
                text: `${productName} has been added to your favorites`,
                timer: 1500,
                showConfirmButton: false
            });
        }
    </script>
</body>
</html>
