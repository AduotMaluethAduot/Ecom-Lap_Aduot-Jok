<?php
// Brand Management Page - requires admin privileges
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
    <title>Brand Management - Admin Panel</title>
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
                        <i class="fas fa-copyright me-3"></i>
                        Brand Management
                    </h1>
                    <p class="mb-0 mt-2 opacity-75">Manage product brands by category</p>
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
            <!-- Add Brand Form -->
            <div class="col-md-12">
                <div class="admin-card">
                    <h4>
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        Add New Brand
                    </h4>
                    <form id="addBrandForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cat_id" class="form-label">Category</label>
                                    <select class="form-select" id="cat_id" name="cat_id" required>
                                        <option value="">Select a category</option>
                                        <!-- Categories will be loaded here via AJAX -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="brand_name" class="form-label">Brand Name</label>
                                    <input type="text" class="form-control" id="brand_name" name="brand_name" placeholder="Enter brand name" required>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-admin w-100">
                                    <i class="fas fa-plus me-2"></i>Add Brand
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Brands List -->
            <div class="col-md-12">
                <div class="admin-card">
                    <h4>
                        <i class="fas fa-list me-2"></i>
                        Existing Brands
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Brand Name</th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="brandsTableBody">
                                <!-- Brands will be loaded here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="noBrandsMessage" class="text-center py-4 d-none">
                        <i class="fas fa-info-circle fa-2x text-info"></i>
                        <p class="mt-2">No brands found. Add your first brand above.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Brand Modal -->
    <div class="modal fade" id="updateBrandModal" tabindex="-1" aria-labelledby="updateBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateBrandModalLabel">Update Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateBrandForm">
                    <div class="modal-body">
                        <input type="hidden" id="update_brand_id" name="brand_id">
                        <div class="mb-3">
                            <label for="update_cat_id" class="form-label">Category</label>
                            <select class="form-select" id="update_cat_id" name="cat_id" required>
                                <option value="">Select a category</option>
                                <!-- Categories will be loaded here via AJAX -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="update_brand_name" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="update_brand_name" name="brand_name" placeholder="Enter brand name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-admin">Update Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../public/js/brand.js"></script>
</body>
</html>