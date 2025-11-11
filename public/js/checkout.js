/**
 * Checkout JavaScript
 * Manages the simulated payment modal and checkout flow
 */

$(document).ready(function() {
    // Initialize checkout page
    if (window.location.pathname.includes('checkout.php')) {
        initializeCheckout();
    }
});

/**
 * Initialize checkout page
 */
function initializeCheckout() {
    // Payment method selection
    $('.payment-method-card').on('click', function() {
        selectPaymentMethod($(this).data('method'));
    });
    
    // Receipt image preview
    $('#receipt_image').on('change', function(e) {
        handleReceiptPreview(e);
    });
}

/**
 * Select payment method
 * @param {string} method - Payment method
 */
function selectPaymentMethod(method) {
    // Remove selected class from all cards
    $('.payment-method-card').removeClass('selected');
    
    // Add selected class to clicked card
    $(`.payment-method-card[data-method="${method}"]`).addClass('selected');
    
    // Set radio button
    $(`#payment_method_${method}`).prop('checked', true);
    
    // Show/hide receipt upload based on payment method
    const receiptUpload = $('#receiptUpload');
    if (method === 'mobile_money' || method === 'bank_transfer') {
        receiptUpload.addClass('show');
    } else {
        receiptUpload.removeClass('show');
    }
}

/**
 * Handle receipt image preview
 * @param {Event} e - File input event
 */
function handleReceiptPreview(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#previewImg').attr('src', e.target.result);
            $('#receiptPreview').show();
        };
        reader.readAsDataURL(file);
    }
}

/**
 * Show simulated payment modal
 */
function showPaymentModal() {
    // Validate form first
    if (!validateCheckoutForm()) {
        return;
    }
    
    const totalAmount = parseFloat($('#total-amount').data('total') || $('#totalAmount').text().replace('$', '').replace(',', ''));
    
    Swal.fire({
        title: 'Simulate Payment',
        html: `
            <div class="text-center">
                <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                <p class="mb-2"><strong>Total Amount:</strong> $${totalAmount.toFixed(2)}</p>
                <p class="text-muted">This is a simulated payment. Click "Yes, I've paid" to proceed.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: "Yes, I've paid",
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            processCheckout();
        }
    });
}

/**
 * Validate checkout form
 * @returns {boolean} - True if valid, false otherwise
 */
function validateCheckoutForm() {
    const deliveryPhone = $('#delivery_phone').val().trim();
    const deliveryAddress = $('#delivery_address').val().trim();
    const paymentMethod = $('input[name="payment_method"]:checked').val();
    
    if (!deliveryPhone) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please enter your delivery phone number'
        });
        $('#delivery_phone').focus();
        return false;
    }
    
    if (!deliveryAddress) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please enter your delivery address'
        });
        $('#delivery_address').focus();
        return false;
    }
    
    if (!paymentMethod) {
        Swal.fire({
            icon: 'error',
            title: 'Payment Method Required',
            text: 'Please select a payment method'
        });
        return false;
    }
    
    // Validate payment reference and receipt for mobile money and bank transfer
    if (paymentMethod === 'mobile_money' || paymentMethod === 'bank_transfer') {
        const paymentReference = $('#payment_reference').val().trim();
        const receiptImage = $('#receipt_image')[0].files[0];
        
        if (!paymentReference) {
            Swal.fire({
                icon: 'error',
                title: 'Payment Details Required',
                text: 'Please provide payment reference/transaction ID'
            });
            $('#payment_reference').focus();
            return false;
        }
        
        if (!receiptImage) {
            Swal.fire({
                icon: 'error',
                title: 'Payment Details Required',
                text: 'Please upload your payment receipt'
            });
            return false;
        }
    }
    
    return true;
}

/**
 * Process checkout after payment confirmation
 */
function processCheckout() {
    // Show loading
    Swal.fire({
        title: 'Processing Order...',
        text: 'Please wait while we process your order',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Prepare form data
    const formData = new FormData();
    formData.append('delivery_phone', $('#delivery_phone').val().trim());
    formData.append('delivery_address', $('#delivery_address').val().trim());
    formData.append('special_instructions', $('#special_instructions').val().trim());
    formData.append('payment_method', $('input[name="payment_method"]:checked').val());
    formData.append('payment_reference', $('#payment_reference').val().trim());
    
    // Add receipt image if uploaded
    const receiptImage = $('#receipt_image')[0].files[0];
    if (receiptImage) {
        formData.append('receipt_image', receiptImage);
    }
    
    // Submit checkout
    $.ajax({
        url: '../actions/process_checkout_action.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Order Placed Successfully!',
                    html: `
                        <p>${response.message}</p>
                        <p class="mt-2"><strong>Order Reference:</strong> ${response.invoice_no}</p>
                        <p><strong>Total Amount:</strong> $${parseFloat(response.total_amount).toFixed(2)}</p>
                    `,
                    confirmButtonText: 'View Orders'
                }).then(() => {
                    // Redirect to orders page
                    window.location.href = 'my_orders.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Order Failed',
                    text: response.message || 'Failed to process your order'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Checkout error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while processing your order. Please try again.'
            });
        }
    });
}

/**
 * Go back to cart or menu
 */
function goBackToCart() {
    Swal.fire({
        title: 'Leave Checkout?',
        text: 'Are you sure you want to go back? Your information will be lost.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, go back',
        cancelButtonText: 'Stay here'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'cart.php';
        }
    });
}

// Make functions globally available
window.showPaymentModal = showPaymentModal;
window.selectPaymentMethod = selectPaymentMethod;
window.goBackToCart = goBackToCart;

