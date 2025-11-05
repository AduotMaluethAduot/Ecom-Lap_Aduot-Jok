// Product Search and Filtering JavaScript
$(document).ready(function() {
    // Auto-complete search functionality
    let searchTimeout;
    
    // Search input with auto-complete
    $('#searchInput').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(function() {
                performAutoComplete(query);
            }, 300);
        } else {
            hideAutoComplete();
        }
    });
    
    // Hide auto-complete when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-container').length) {
            hideAutoComplete();
        }
    });
    
    // Perform auto-complete search
    function performAutoComplete(query) {
        $.ajax({
            url: '../actions/ajax_search_suggestions.php',
            type: 'GET',
            data: { q: query },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayAutoComplete(response.data);
                }
            },
            error: function() {
                console.log('Auto-complete search failed');
            }
        });
    }
    
    // Display auto-complete suggestions
    function displayAutoComplete(suggestions) {
        const container = $('.auto-complete-container');
        container.empty();
        
        if (suggestions.length > 0) {
            suggestions.forEach(function(suggestion) {
                const item = $(`
                    <div class="auto-complete-item" data-query="${suggestion.title}">
                        <i class="fas fa-search me-2"></i>
                        <span>${suggestion.title}</span>
                        <small class="text-muted">${suggestion.category}</small>
                    </div>
                `);
                
                item.on('click', function() {
                    $('#searchInput').val(suggestion.title);
                    hideAutoComplete();
                    performSearch();
                });
                
                container.append(item);
            });
            
            container.show();
        } else {
            hideAutoComplete();
        }
    }
    
    // Hide auto-complete
    function hideAutoComplete() {
        $('.auto-complete-container').hide().empty();
    }
    
    // Advanced search form
    $('#advancedSearchForm').on('submit', function(e) {
        e.preventDefault();
        performAdvancedSearch();
    });
    
    // Perform advanced search
    function performAdvancedSearch() {
        const formData = new FormData(this);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                params.append(key, value);
            }
        }
        
        window.location.href = 'product_search_result.php?' + params.toString();
    }
    
    // Filter products dynamically
    $('.filter-select').on('change', function() {
        applyFilters();
    });
    
    // Apply filters
    function applyFilters() {
        const category = $('#categoryFilter').val();
        const brand = $('#brandFilter').val();
        const minPrice = $('#minPriceFilter').val();
        const maxPrice = $('#maxPriceFilter').val();
        const sortBy = $('#sortFilter').val();
        
        const params = new URLSearchParams();
        
        if (category) params.append('category', category);
        if (brand) params.append('brand', brand);
        if (minPrice) params.append('min_price', minPrice);
        if (maxPrice) params.append('max_price', maxPrice);
        if (sortBy) params.append('sort', sortBy);
        
        // Update URL without page reload
        const newUrl = window.location.pathname + '?' + params.toString();
        window.history.pushState({}, '', newUrl);
        
        // Reload products
        loadFilteredProducts(params);
    }
    
    // Load filtered products via AJAX
    function loadFilteredProducts(params) {
        $.ajax({
            url: '../actions/ajax_filter_products.php',
            type: 'GET',
            data: params.toString(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayProducts(response.data.products);
                    updatePagination(response.data.pagination);
                }
            },
            error: function() {
                console.log('Failed to load filtered products');
            }
        });
    }
    
    // Display products
    function displayProducts(products) {
        const container = $('#productsContainer');
        container.empty();
        
        if (products.length === 0) {
            container.html(`
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open text-muted" style="font-size: 4rem;"></i>
                    <h3 class="text-muted mt-3">No products found</h3>
                    <p class="text-muted">Try adjusting your filters.</p>
                </div>
            `);
            return;
        }
        
        products.forEach(function(product) {
            const productCard = createProductCard(product);
            container.append(productCard);
        });
    }
    
    // Create product card HTML
    function createProductCard(product) {
        const imageHtml = product.product_image ? 
            `<img src="uploads/${product.product_image}" alt="${product.product_title}" class="product-image">` :
            `<div class="product-image d-flex align-items-center justify-content-center bg-light">
                <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
            </div>`;
        
        return $(`
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="product-card">
                    ${imageHtml}
                    <h5 class="mt-3">${product.product_title}</h5>
                    <p class="text-muted small">${product.product_desc}</p>
                    <div class="mb-2">
                        <span class="badge bg-primary me-1">${product.cat_name}</span>
                        <span class="badge bg-secondary">${product.brand_name}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="price">$${parseFloat(product.product_price).toFixed(2)}</span>
                        <small class="text-muted">ID: ${product.product_id}</small>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="single_product.php?id=${product.product_id}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>View Details
                        </a>
                        <button class="btn-add-cart" onclick="addToCart(${product.product_id})">
                            <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        `);
    }
    
    // Update pagination
    function updatePagination(pagination) {
        const container = $('.pagination-container');
        container.empty();
        
        if (pagination.total_pages <= 1) return;
        
        const paginationHtml = createPaginationHtml(pagination);
        container.html(paginationHtml);
    }
    
    // Create pagination HTML
    function createPaginationHtml(pagination) {
        let html = '<nav aria-label="Products pagination"><ul class="pagination">';
        
        // Previous button
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            </li>`;
        }
        
        // Page numbers
        for (let i = 1; i <= pagination.total_pages; i++) {
            const activeClass = i === pagination.current_page ? 'active' : '';
            html += `<li class="page-item ${activeClass}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }
        
        // Next button
        if (pagination.current_page < pagination.total_pages) {
            html += `<li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            </li>`;
        }
        
        html += '</ul></nav>';
        return html;
    }
    
    // Handle pagination clicks
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        const currentParams = new URLSearchParams(window.location.search);
        currentParams.set('page', page);
        
        window.location.href = window.location.pathname + '?' + currentParams.toString();
    });
    
    // Quick search functionality
    $('#quickSearchForm').on('submit', function(e) {
        e.preventDefault();
        const query = $('#quickSearchInput').val().trim();
        if (query) {
            window.location.href = `product_search_result.php?q=${encodeURIComponent(query)}`;
        }
    });
    
    // Price range slider
    if ($('#priceRangeSlider').length) {
        $('#priceRangeSlider').on('input', function() {
            const value = $(this).val();
            $('#priceRangeValue').text('$' + value);
        });
    }
    
    // Clear all filters
    $('#clearFilters').on('click', function() {
        $('.filter-select').val('');
        $('#minPriceFilter, #maxPriceFilter').val('');
        window.location.href = window.location.pathname;
    });
    
    // Add to cart functionality
    window.addToCart = function(productId) {
        $.ajax({
            url: '../actions/ajax_add_to_cart.php',
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to Cart!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
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
                    text: 'Failed to add product to cart'
                });
            }
        });
    };
    
    // Add to favorites functionality
    window.addToFavorites = function(productId) {
        $.ajax({
            url: '../actions/ajax_add_to_favorites.php',
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to Favorites!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
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
                    text: 'Failed to add product to favorites'
                });
            }
        });
    };
});
