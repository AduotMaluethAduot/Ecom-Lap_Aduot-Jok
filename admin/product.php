<?php
// Product Management Page - requires admin privileges
require_once '../src/settings/core.php';

// Require admin access
require_admin();

// Check session validity
if (!is_session_valid()) {
    logout_user();
    header("Location: login/login.php?message=session_expired");
    exit();
}

// Get admin user information
$admin_name = get_user_name();
$admin_email = get_user_email();
$admin_id = get_user_id();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../public/css/admin.css" rel="stylesheet">
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-box-open me-3"></i>
                        Product Management
                    </h1>
                    <p class="mb-0 mt-2 opacity-75">Add and manage products</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="dashboard.php" class="btn btn-light me-2">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <a href="../index.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a href="../login/logout.php" class="btn btn-outline-light">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <!-- Add/Edit Product Form -->
            <div class="col-md-12">
                <div class="admin-card">
                    <h4>
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        <span id="formTitle">Add New Product</span>
                    </h4>
                    <form id="productForm" enctype="multipart/form-data">
                        <input type="hidden" id="product_id" name="product_id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_cat" class="form-label">Category</label>
                                    <select class="form-select" id="product_cat" name="product_cat" required>
                                        <option value="">Select a category</option>
                                        <!-- Categories will be loaded here via AJAX -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_brand" class="form-label">Brand</label>
                                    <select class="form-select" id="product_brand" name="product_brand" required>
                                        <option value="">Select a brand</option>
                                        <!-- Brands will be loaded here via AJAX -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="product_title" class="form-label">Product Title</label>
                                    <input type="text" class="form-control" id="product_title" name="product_title" placeholder="Enter product title" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="product_price" class="form-label">Price ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="product_price" name="product_price" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="product_desc" class="form-label">Product Description</label>
                            <textarea class="form-control" id="product_desc" name="product_desc" rows="3" placeholder="Enter product description"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_image" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*">
                                    <div class="form-text">Upload JPG, PNG, or GIF (max 5MB)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_keywords" class="form-label">Keywords</label>
                                    <input type="text" class="form-control" id="product_keywords" name="product_keywords" placeholder="Enter keywords separated by commas">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button type="submit" class="btn btn-admin me-2">
                                <i class="fas fa-save me-2"></i><span id="submitButtonText">Add Product</span>
                            </button>
                            <button type="button" class="btn btn-secondary" id="cancelEdit" style="display: none;">
                                <i class="fas fa-times me-2"></i>Cancel Edit
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products List -->
            <div class="col-md-12">
                <div class="admin-card">
                    <h4>
                        <i class="fas fa-list me-2"></i>
                        Existing Products
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <!-- Products will be loaded here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="noProductsMessage" class="text-center py-4 d-none">
                        <i class="fas fa-info-circle fa-2x text-info"></i>
                        <p class="mt-2">No products found. Add your first product above.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../public/js/product.js"></script>
</body>
</html>