// Brand Management JavaScript
$(document).ready(function() {
    // Load brands and categories on page load
    loadCategories();
    loadBrands();
    
    // Handle add brand form submission
    $('#addBrandForm').on('submit', function(e) {
        e.preventDefault();
        
        const brandName = $('#brand_name').val().trim();
        const catId = $('#cat_id').val();
        
        if (!brandName) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Brand name is required'
            });
            return;
        }
        
        if (!catId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select a category'
            });
            return;
        }
        
        if (brandName.length < 2) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Brand name must be at least 2 characters long'
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Adding Brand...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Submit form data
        $.ajax({
            url: '../actions/add_brand_action.php',
            type: 'POST',
            data: {
                brand_name: brandName,
                cat_id: catId
            },
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
                        $('#addBrandForm')[0].reset();
                        loadBrands();
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
    
    // Handle update brand form submission
    $('#updateBrandForm').on('submit', function(e) {
        e.preventDefault();
        
        const brandId = $('#update_brand_id').val();
        const brandName = $('#update_brand_name').val().trim();
        const catId = $('#update_cat_id').val();
        
        if (!brandName) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Brand name is required'
            });
            return;
        }
        
        if (!catId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select a category'
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Updating Brand...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Submit form data
        $.ajax({
            url: '../actions/update_brand_action.php',
            type: 'POST',
            data: {
                brand_id: brandId,
                brand_name: brandName,
                cat_id: catId
            },
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
                        $('#updateBrandModal').modal('hide');
                        loadBrands();
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
    
    // Load categories function
    function loadCategories() {
        $.ajax({
            url: '../actions/get_filter_options_action.php?admin=true',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    populateCategorySelects(response.data);
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
                    displayBrands(response.data.brands);
                } else {
                    $('#noBrandsMessage').removeClass('d-none');
                    $('#brandsTableBody').empty();
                }
            },
            error: function() {
                $('#noBrandsMessage').removeClass('d-none');
                $('#brandsTableBody').empty();
            }
        });
    }
    
    // Populate category selects
    function populateCategorySelects(categories) {
        const selects = ['#cat_id', '#update_cat_id'];
        
        selects.forEach(function(selectId) {
            const select = $(selectId);
            select.empty();
            select.append('<option value="">Select a category</option>');
            
            categories.forEach(function(category) {
                select.append(`<option value="${category.cat_id}">${category.cat_name}</option>`);
            });
        });
    }
    
    // Display brands in table
    function displayBrands(brands) {
        const tbody = $('#brandsTableBody');
        tbody.empty();
        
        if (brands.length === 0) {
            $('#noBrandsMessage').removeClass('d-none');
            return;
        }
        
        $('#noBrandsMessage').addClass('d-none');
        
        brands.forEach(function(brand) {
            const row = `
                <tr>
                    <td>${brand.brand_id}</td>
                    <td>${brand.brand_name}</td>
                    <td>${brand.cat_name || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning me-2" onclick="editBrand(${brand.brand_id}, '${brand.brand_name}', ${brand.cat_id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteBrand(${brand.brand_id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    // Edit brand function
    window.editBrand = function(brandId, brandName, catId) {
        $('#update_brand_id').val(brandId);
        $('#update_brand_name').val(brandName);
        $('#update_cat_id').val(catId);
        $('#updateBrandModal').modal('show');
    };
    
    // Delete brand function
    window.deleteBrand = function(brandId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the brand and all associated data!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../actions/delete_brand_action.php',
                    type: 'POST',
                    data: {
                        brand_id: brandId
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
                                loadBrands();
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
