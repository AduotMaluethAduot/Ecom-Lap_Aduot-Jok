// Product Management JavaScript
$(document).ready(function() {
    // Load categories and brands on page load
    loadCategories();
    loadBrands();
    loadProducts();
    
    // Handle product form submission
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const productId = $('#product_id').val();
        
        // Validate required fields
        if (!formData.get('product_title') || !formData.get('product_price') || 
            !formData.get('product_cat') || !formData.get('product_brand')) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please fill in all required fields'
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: productId ? 'Updating Product...' : 'Adding Product...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Determine the action URL
        const actionUrl = productId ? '../actions/update_product_action.php' : '../actions/add_product_action.php';
        
        // Submit form data
        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#productForm')[0].reset();
                        $('#product_id').val('');
                        $('#formTitle').text('Add New Product');
                        $('#submitButtonText').text('Add Product');
                        $('#cancelEdit').hide();
                        loadProducts();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    });
    
    // Handle cancel edit
    $('#cancelEdit').on('click', function() {
        $('#productForm')[0].reset();
        $('#product_id').val('');
        $('#formTitle').text('Add New Product');
        $('#submitButtonText').text('Add Product');
        $(this).hide();
    });
    
    // Load categories function
    function loadCategories() {
        $.ajax({
            url: '../actions/get_filter_options_action.php?admin=true',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    populateCategorySelect(response.data.categories);
                }
            },
            error: function() {
                console.log('Error loading categories');
            }
        });
    }
    
    // Load brands function
    function loadBrands() {
        $.ajax({
            url: '../actions/get_filter_options_action.php?admin=true',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    populateBrandSelect(response.data.brands);
                }
            },
            error: function() {
                console.log('Error loading brands');
            }
        });
    }
    
    // Load products function
    function loadProducts() {
        $.ajax({
            url: '../actions/get_filter_options_action.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayProducts(response.data);
                } else {
                    $('#noProductsMessage').removeClass('d-none');
                    $('#productsTableBody').empty();
                }
            },
            error: function() {
                $('#noProductsMessage').removeClass('d-none');
                $('#productsTableBody').empty();
            }
        });
    }
    
    // Populate category select
    function populateCategorySelect(categories) {
        const select = $('#product_cat');
        select.empty();
        select.append('<option value="">Select a category</option>');
        
        categories.forEach(function(category) {
            select.append(`<option value="${category.cat_id}">${category.cat_name}</option>`);
        });
    }
    
    // Populate brand select
    function populateBrandSelect(brands) {
        const select = $('#product_brand');
        select.empty();
        select.append('<option value="">Select a brand</option>');
        
        brands.forEach(function(brand) {
            select.append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
        });
    }
    
    // Display products in table
    function displayProducts(products) {
        const tbody = $('#productsTableBody');
        tbody.empty();
        
        if (products.length === 0) {
            $('#noProductsMessage').removeClass('d-none');
            return;
        }
        
        $('#noProductsMessage').addClass('d-none');
        
        products.forEach(function(product) {
            const imageHtml = product.product_image ? 
                `<img src="../uploads/${product.product_image}" alt="${product.product_title}" class="img-thumbnail" style="width: 50px; height: 50px;">` :
                '<i class="fas fa-image text-muted" style="font-size: 2rem;"></i>';
            
            const row = `
                <tr>
                    <td>${product.product_id}</td>
                    <td>${imageHtml}</td>
                    <td>${product.product_title}</td>
                    <td>${product.cat_name || 'N/A'}</td>
                    <td>${product.brand_name || 'N/A'}</td>
                    <td>$${parseFloat(product.product_price).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-warning me-2" onclick="editProduct(${product.product_id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.product_id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    // Edit product function
    window.editProduct = function(productId) {
        $.ajax({
            url: '../actions/get_filter_options_action.php',
            type: 'GET',
            data: { product_id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    const product = response.data;
                    $('#product_id').val(product.product_id);
                    $('#product_cat').val(product.product_cat);
                    $('#product_brand').val(product.product_brand);
                    $('#product_title').val(product.product_title);
                    $('#product_price').val(product.product_price);
                    $('#product_desc').val(product.product_desc);
                    $('#product_keywords').val(product.product_keywords);
                    
                    $('#formTitle').text('Edit Product');
                    $('#submitButtonText').text('Update Product');
                    $('#cancelEdit').show();
                    
                    // Scroll to form
                    $('html, body').animate({
                        scrollTop: $('#productForm').offset().top - 100
                    }, 500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Product not found'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while loading product data'
                });
            }
        });
    };
    
    // Delete product function
    window.deleteProduct = function(productId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the product and all associated data!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../actions/delete_product_action.php',
                    type: 'POST',
                    data: {
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                loadProducts();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.'
                        });
                    }
                });
            }
        });
    };
});
