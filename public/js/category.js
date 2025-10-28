// Category Management JavaScript
$(document).ready(function() {
    // Load categories on page load
    loadCategories();
    
    // Handle add category form submission
    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        
        const catName = $('#cat_name').val().trim();
        
        if (!catName) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Category name is required'
            });
            return;
        }
        
        if (catName.length < 2) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Category name must be at least 2 characters long'
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Adding Category...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Submit form data
        $.ajax({
            url: '../actions/add_category_action.php',
            type: 'POST',
            data: {
                cat_name: catName
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
                        $('#addCategoryForm')[0].reset();
                        loadCategories();
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
    
    // Handle update category form submission
    $('#updateCategoryForm').on('submit', function(e) {
        e.preventDefault();
        
        const catId = $('#update_cat_id').val();
        const catName = $('#update_cat_name').val().trim();
        
        if (!catName) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Category name is required'
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Updating Category...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Submit form data
        $.ajax({
            url: '../actions/update_category_action.php',
            type: 'POST',
            data: {
                cat_id: catId,
                cat_name: catName
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
                        $('#updateCategoryModal').modal('hide');
                        loadCategories();
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
                    displayCategories(response.data.categories);
                } else {
                    $('#noCategoriesMessage').removeClass('d-none');
                    $('#categoriesTableBody').empty();
                }
            },
            error: function() {
                $('#noCategoriesMessage').removeClass('d-none');
                $('#categoriesTableBody').empty();
            }
        });
    }
    
    // Display categories in table
    function displayCategories(categories) {
        const tbody = $('#categoriesTableBody');
        tbody.empty();
        
        if (categories.length === 0) {
            $('#noCategoriesMessage').removeClass('d-none');
            return;
        }
        
        $('#noCategoriesMessage').addClass('d-none');
        
        categories.forEach(function(category) {
            const row = `
                <tr>
                    <td>${category.cat_id}</td>
                    <td>${category.cat_name}</td>
                    <td>
                        <button class="btn btn-sm btn-warning me-2" onclick="editCategory(${category.cat_id}, '${category.cat_name}')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCategory(${category.cat_id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    // Edit category function
    window.editCategory = function(catId, catName) {
        $('#update_cat_id').val(catId);
        $('#update_cat_name').val(catName);
        $('#updateCategoryModal').modal('show');
    };
    
    // Delete category function
    window.deleteCategory = function(catId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the category and all associated data!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../actions/delete_category_action.php',
                    type: 'POST',
                    data: {
                        cat_id: catId
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
                                loadCategories();
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
