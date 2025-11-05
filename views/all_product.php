<?php
// All Products Page - Customer-facing product listing
require_once '../src/settings/core.php';
require_once '../actions/product_actions.php';

// Get filter options
$filter_options = getFilterOptions();
$categories = $filter_options['data']['categories'];
$brands = $filter_options['data']['brands'];

// Get current page and filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : '';
$brand_filter = isset($_GET['brand']) ? (int)$_GET['brand'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build parameters for product request
$params = [
    'page' => $page,
    'limit' => 12
];

// Determine action based on filters
if (!empty($category_filter)) {
    $action = 'filter_category';
    $params['cat_id'] = $category_filter;
} elseif (!empty($brand_filter)) {
    $action = 'filter_brand';
    $params['brand_id'] = $brand_filter;
} else {
    $action = 'view_all';
}

// Get products
$result = handleProductRequest($action, $params);
$products = $result['status'] === 'success' ? $result['data'] : [];
$pagination = $result['status'] === 'success' ? $result['pagination'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .products-container {
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
            height: 100%;
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
        .btn-add-cart {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
            color: white;
        }
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
        .no-products {
            text-align: center;
            padding: 4rem 2rem;
        }
        .no-products i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1rem;
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
                <a class="nav-link active" href="all_product.php">
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
        <div class="products-container">
            <h1 class="text-center mb-4">
                <i class="fas fa-box-open me-2"></i>All Products
            </h1>
            <p class="text-center text-muted mb-5">Discover our amazing collection of African cuisine</p>

            <!-- Filters Section -->
            <div class="filter-section">
                <h4><i class="fas fa-filter me-2"></i>Filter Products</h4>
                <form method="GET" action="all_product.php" class="row g-3">
                    <div class="col-md-4">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['cat_id']; ?>" 
                                        <?php echo $category_filter == $category['cat_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="brand" class="form-label">Brand</label>
                        <select class="form-select" id="brand" name="brand">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand['brand_id']; ?>" 
                                        <?php echo $brand_filter == $brand['brand_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="sort" class="form-label">Sort By</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="newest" <?php echo $sort_by == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="oldest" <?php echo $sort_by == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                            <option value="price_low" <?php echo $sort_by == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort_by == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="name" <?php echo $sort_by == 'name' ? 'selected' : ''; ?>>Name: A to Z</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="all_product.php" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>

            <!-- Products Grid -->
            <?php if (!empty($products)): ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
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
                                <p class="text-muted small"><?php echo htmlspecialchars($product['product_desc']); ?></p>
                                
                                <div class="mb-2">
                                    <span class="badge bg-primary me-1"><?php echo htmlspecialchars($product['cat_name']); ?></span>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($product['brand_name']); ?></span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="price">$<?php echo number_format($product['product_price'], 2); ?></span>
                                    <small class="text-muted">ID: <?php echo $product['product_id']; ?></small>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="single_product.php?id=<?php echo $product['product_id']; ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                    <button class="btn-add-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                        <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pagination && $pagination['total_pages'] > 1): ?>
                    <div class="pagination-container">
                        <nav aria-label="Products pagination">
                            <ul class="pagination">
                                <?php if ($pagination['current_page'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&category=<?php echo $category_filter; ?>&brand=<?php echo $brand_filter; ?>&sort=<?php echo $sort_by; ?>">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                    <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category_filter; ?>&brand=<?php echo $brand_filter; ?>&sort=<?php echo $sort_by; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&category=<?php echo $category_filter; ?>&brand=<?php echo $brand_filter; ?>&sort=<?php echo $sort_by; ?>">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-products">
                    <i class="fas fa-box-open"></i>
                    <h3 class="text-muted">No products found</h3>
                    <p class="text-muted">Try adjusting your filters or check back later for new products.</p>
                    <a href="all_product.php" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>View All Products
                    </a>
                </div>
            <?php endif; ?>
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
    </script>
</body>
</html>
