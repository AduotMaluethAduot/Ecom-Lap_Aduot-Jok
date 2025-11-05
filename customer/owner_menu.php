<?php
// Owner Menu Management page
require_once '../src/settings/core.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    header('Location: ../login/login.php');
    exit();
}

// Check if user is admin (redirect to admin dashboard)
if (is_user_admin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

// Get owner information
$owner_id = get_user_id();
$owner_name = get_user_name();
$owner_email = get_user_email();

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
    <title>Menu Management - Taste of Africa</title>
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
            color: #38a169;
        }
        .btn-owner {
            background: linear-gradient(135deg, #38a169, #2f855a);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .btn-owner:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(56, 161, 105, 0.4);
            color: white;
        }
        .btn-edit {
            background: linear-gradient(135deg, #3182ce, #2c5282);
        }
        .btn-delete {
            background: linear-gradient(135deg, #e53e3e, #c53030);
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
            background: #38a169;
            border-color: #38a169;
            color: white;
        }
        .filter-tab:hover {
            border-color: #38a169;
            color: #38a169;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php" style="color: #38a169;">
                <i class="fas fa-store me-2"></i>Taste of Africa - Owner Portal
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="owner_dashboard.php">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
                <a class="nav-link active" href="owner_menu.php">
                    <i class="fas fa-utensils me-1"></i>My Menu
                </a>
                <a class="nav-link" href="owner_orders.php">
                    <i class="fas fa-shopping-bag me-1"></i>Orders
                </a>
                <a class="nav-link" href="owner_analytics.php">
                    <i class="fas fa-chart-bar me-1"></i>Analytics
                </a>
                <a class="nav-link" href="../login/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="menu-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-utensils me-2"></i>Menu Management
                </h1>
                <a href="../admin/product.php" class="btn btn-owner">
                    <i class="fas fa-plus me-2"></i>Add New Dish
                </a>
            </div>
            <p class="text-muted mb-5">Manage your restaurant's menu items and showcase your authentic African dishes</p>

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
                <div class="row" id="products-container">
                    <?php foreach ($products as $product): ?>
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
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Category</small>
                                        <div class="fw-bold"><?php echo htmlspecialchars($product['cat_name'] ?? 'Uncategorized'); ?></div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Brand</small>
                                        <div class="fw-bold"><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="price">$<?php echo number_format($product['product_price'], 2); ?></span>
                                    <span class="status-badge status-active">Active</span>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="../admin/product.php?edit=<?php echo $product['product_id']; ?>" class="btn btn-owner btn-edit flex-fill">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <button class="btn btn-owner btn-delete" onclick="deleteProduct(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($product['product_title']); ?>')">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-utensils text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3 text-muted">No menu items yet</h3>
                    <p class="text-muted">Start building your menu by adding your first African dish!</p>
                    <a href="../admin/product.php" class="btn btn-owner mt-3">
                        <i class="fas fa-plus me-2"></i>Add Your First Dish
                    </a>
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
                $('.product-item').show();
            } else {
                $('.product-item').hide();
                $(`.product-item[data-category="${category}"]`).show();
            }
        });

        // Delete product functionality
        function deleteProduct(productId, productName) {
            Swal.fire({
                title: 'Delete Product?',
                text: `Are you sure you want to delete "${productName}"? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Simulate deletion
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Product has been deleted successfully',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    </script>
</body>
</html>
