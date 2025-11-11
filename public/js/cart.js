/**
 * Cart Management JavaScript
 * Handles all UI interactions for the cart: adding, removing, updating, and emptying items
 * Communicates asynchronously with the corresponding action scripts
 */

$(document).ready(function() {
    // Load cart on page load
    loadCart();
    
    // Update cart count in navigation
    updateCartCount();
});

/**
 * Add product to cart
 * @param {number} productId - Product ID
 * @param {number} quantity - Quantity to add (default: 1)
 */
function addToCart(productId, quantity = 1) {
    // Show loading indicator
    const btn = event?.target || document.querySelector(`[data-product-id="${productId}"]`);
    const originalText = btn?.innerHTML;
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    }
    
    $.ajax({
        url: '../actions/add_to_cart_action.php',
        type: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart!',
                    text: response.product_name + ' has been added to your cart',
                    timer: 1500,
                    showConfirmButton: false
                });
                
                // Update cart count
                updateCartCount();
                
                // Reload cart if on cart page
                if (window.location.pathname.includes('cart.php')) {
                    loadCart();
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to add product to cart'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Add to cart error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while adding product to cart'
            });
        },
        complete: function() {
            // Restore button
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    });
}

/**
 * Remove product from cart
 * @param {number} productId - Product ID
 */
function removeFromCart(productId) {
    Swal.fire({
        title: 'Remove Item?',
        text: 'Are you sure you want to remove this item from your cart?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../actions/remove_from_cart_action.php',
                type: 'POST',
                data: {
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Removed!',
                            text: 'Item has been removed from your cart',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        // Update cart count
                        updateCartCount();
                        
                        // Reload cart
                        loadCart();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to remove item from cart'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Remove from cart error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while removing item from cart'
                    });
                }
            });
        }
    });
}

/**
 * Update quantity of a cart item
 * @param {number} productId - Product ID
 * @param {number} quantity - New quantity
 */
function updateQuantity(productId, quantity) {
    if (quantity <= 0) {
        removeFromCart(productId);
        return;
    }
    
    $.ajax({
        url: '../actions/update_quantity_action.php',
        type: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Update cart display
                loadCart();
                
                // Update cart count
                updateCartCount();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to update quantity'
                });
                // Reload cart to restore correct values
                loadCart();
            }
        },
        error: function(xhr, status, error) {
            console.error('Update quantity error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while updating quantity'
            });
            // Reload cart to restore correct values
            loadCart();
        }
    });
}

/**
 * Empty the entire cart
 */
function emptyCart() {
    Swal.fire({
        title: 'Empty Cart?',
        text: 'Are you sure you want to remove all items from your cart?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, empty cart',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../actions/empty_cart_action.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cart Emptied!',
                            text: 'All items have been removed from your cart',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        // Update cart count
                        updateCartCount();
                        
                        // Reload cart
                        loadCart();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to empty cart'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Empty cart error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while emptying cart'
                    });
                }
            });
        }
    });
}

/**
 * Load and display cart items
 */
function loadCart() {
    // Only load if on cart page
    if (!window.location.pathname.includes('cart.php')) {
        return;
    }
    
    $.ajax({
        url: '../actions/get_cart_action.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                displayCart(response.cart_items, response.cart_total);
            } else {
                displayCart([], 0);
            }
        },
        error: function(xhr, status, error) {
            console.error('Load cart error:', error);
            displayCart([], 0);
        }
    });
}

/**
 * Display cart items
 * @param {Array} cartItems - Array of cart items
 * @param {number} cartTotal - Total amount
 */
function displayCart(cartItems, cartTotal) {
    const cartContainer = $('#cart-items');
    const cartTotalElement = $('#cart-total');
    
    if (!cartContainer.length) {
        return; // Not on cart page
    }
    
    if (cartItems.length === 0) {
        cartContainer.html('<div class="alert alert-info text-center"><i class="fas fa-shopping-cart me-2"></i>Your cart is empty</div>');
        cartTotalElement.hide();
        $('#checkout-button').prop('disabled', true);
        return;
    }
    
    let html = '';
    cartItems.forEach(function(item) {
        const itemSubtotal = parseFloat(item.product_price) * parseInt(item.qty);
        html += `
            <div class="cart-item mb-3 p-3 border rounded" data-product-id="${item.product_id}">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <img src="../${item.product_image || 'uploads/products/default.svg'}" 
                             alt="${item.product_title}" 
                             class="img-fluid rounded">
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-1">${item.product_title}</h6>
                        <p class="text-muted mb-0 small">$${parseFloat(item.product_price).toFixed(2)} each</p>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${item.product_id}, ${parseInt(item.qty) - 1})">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" 
                                   value="${item.qty}" 
                                   min="1" 
                                   onchange="updateQuantity(${item.product_id}, parseInt(this.value))">
                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${item.product_id}, ${parseInt(item.qty) + 1})">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <strong>$${itemSubtotal.toFixed(2)}</strong>
                    </div>
                    <div class="col-md-1 text-end">
                        <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.product_id})" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    cartContainer.html(html);
    cartTotalElement.html(`<h5>Total: $${parseFloat(cartTotal).toFixed(2)}</h5>`);
    cartTotalElement.show();
    $('#checkout-button').prop('disabled', false);
}

/**
 * Update cart count in navigation
 */
function updateCartCount() {
    $.ajax({
        url: '../actions/get_cart_count_action.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const count = response.cart_count || 0;
                $('.cart-count').text(count);
                if (count > 0) {
                    $('.cart-count').show();
                } else {
                    $('.cart-count').hide();
                }
            }
        },
        error: function() {
            // Silently fail - cart count is not critical
        }
    });
}

// Make functions globally available
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateQuantity = updateQuantity;
window.emptyCart = emptyCart;
window.loadCart = loadCart;

