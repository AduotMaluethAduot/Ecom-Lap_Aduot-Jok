<?php
// Checkout page with payment integration
require_once '../src/settings/core.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    header('Location: ../login/login.php');
    exit();
}

// Get customer information
$customer_id = get_user_id();
$customer_name = get_user_name();
$customer_email = get_user_email();
$customer_role = get_user_role();

// Redirect admin users to admin dashboard
if (is_user_admin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

// Get cart data from session or POST
$cart_items = [];
$total_amount = 0;

if (isset($_POST['cart_data'])) {
    $cart_items = json_decode($_POST['cart_data'], true);
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
} else {
    // Redirect to order page if no cart data
    header('Location: order_food.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .checkout-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            margin: 2rem 0;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .payment-method-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method-card:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .payment-method-card.selected {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
        .payment-method-card input[type="radio"] {
            display: none;
        }
        .receipt-upload {
            display: none;
            margin-top: 1rem;
        }
        .receipt-upload.show {
            display: block;
        }
        .order-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            position: sticky;
            top: 2rem;
        }
        .order-item {
            border-bottom: 1px solid #dee2e6;
            padding: 0.5rem 0;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="customer_dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="order_food.php">Menu</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-12">
                <div class="checkout-container">
                    <div class="row">
                        <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Checkout
                        </h2>
                        <button type="button" class="btn btn-outline-secondary" onclick="goBackToMenu()">
                            <i class="fas fa-arrow-left me-2"></i>Back to Menu
                        </button>
                    </div>
                            
                            <!-- Customer Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-user me-2"></i>Customer Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
                                            <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_email); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="delivery_phone" class="form-label">Delivery Phone</label>
                                                <input type="tel" class="form-control" id="delivery_phone" name="delivery_phone" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="delivery_address" class="form-label">Delivery Address</label>
                                        <textarea class="form-control" id="delivery_address" name="delivery_address" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="special_instructions" class="form-label">Special Instructions (Optional)</label>
                                        <textarea class="form-control" id="special_instructions" name="special_instructions" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Methods -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-credit-card me-2"></i>Payment Method</h5>
                                </div>
                                <div class="card-body">
                                    <form id="paymentForm">
                                        <!-- Mobile Money -->
                                        <div class="payment-method-card" onclick="selectPaymentMethod('mobile_money')">
                                            <input type="radio" name="payment_method" value="mobile_money" id="mobile_money">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-mobile-alt fa-2x text-primary"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Mobile Money</h6>
                                                    <p class="mb-0 text-muted">Pay via MTN, Vodafone, or AirtelTigo</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bank Transfer -->
                                        <div class="payment-method-card" onclick="selectPaymentMethod('bank_transfer')">
                                            <input type="radio" name="payment_method" value="bank_transfer" id="bank_transfer">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-university fa-2x text-success"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Bank Transfer</h6>
                                                    <p class="mb-0 text-muted">Direct bank transfer or deposit</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- POS Payment -->
                                        <div class="payment-method-card" onclick="selectPaymentMethod('pos')">
                                            <input type="radio" name="payment_method" value="pos" id="pos">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-credit-card fa-2x text-warning"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">POS Payment</h6>
                                                    <p class="mb-0 text-muted">Pay with card at pickup/delivery</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cash on Delivery -->
                                        <div class="payment-method-card" onclick="selectPaymentMethod('cash')">
                                            <input type="radio" name="payment_method" value="cash" id="cash">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-money-bill-wave fa-2x text-info"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Cash on Delivery</h6>
                                                    <p class="mb-0 text-muted">Pay with cash when order is delivered</p>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Receipt Upload Section -->
                                    <div class="receipt-upload" id="receiptUpload">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6><i class="fas fa-receipt me-2"></i>Payment Receipt</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="payment_reference" class="form-label">Payment Reference/Transaction ID</label>
                                                    <input type="text" class="form-control" id="payment_reference" name="payment_reference" placeholder="Enter transaction ID or reference number">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="receipt_image" class="form-label">Upload Receipt</label>
                                                    <input type="file" class="form-control" id="receipt_image" name="receipt_image" accept="image/*">
                                                    <div class="form-text">Upload a clear photo of your payment receipt</div>
                                                </div>
                                                <div id="receiptPreview" class="mt-3" style="display: none;">
                                                    <img id="previewImg" src="" alt="Receipt Preview" class="img-thumbnail" style="max-width: 200px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="col-lg-4">
                            <div class="order-summary">
                                <h5 class="mb-3">
                                    <i class="fas fa-shopping-bag me-2"></i>Order Summary
                                </h5>
                                
                                <div id="orderItems">
                                    <?php foreach ($cart_items as $item): ?>
                                    <div class="order-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                <br>
                                                <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                            </div>
                                            <div class="text-end">
                                                <strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total Amount:</strong>
                                    <strong id="totalAmount">$<?php echo number_format($total_amount, 2); ?></strong>
                                </div>

                                <div class="d-grid gap-2 mt-3">
                                    <button type="button" class="btn btn-checkout" onclick="placeOrder()">
                                        <i class="fas fa-check me-2"></i>Place Order
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" onclick="goBackToMenu()">
                                        <i class="fas fa-plus me-2"></i>Continue Shopping
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let selectedPaymentMethod = '';

        function selectPaymentMethod(method) {
            // Remove selected class from all cards
            document.querySelectorAll('.payment-method-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');
            
            // Set radio button
            document.getElementById(method).checked = true;
            selectedPaymentMethod = method;
            
            // Show/hide receipt upload based on payment method
            const receiptUpload = document.getElementById('receiptUpload');
            if (method === 'mobile_money' || method === 'bank_transfer') {
                receiptUpload.classList.add('show');
            } else {
                receiptUpload.classList.remove('show');
            }
        }

        // Handle receipt image preview
        document.getElementById('receipt_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('receiptPreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Go back to menu with confirmation
        function goBackToMenu() {
            // Check if user has filled any form data
            const deliveryPhone = document.getElementById('delivery_phone').value;
            const deliveryAddress = document.getElementById('delivery_address').value;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            
            if (deliveryPhone || deliveryAddress || paymentMethod) {
                Swal.fire({
                    title: 'Leave Checkout?',
                    text: 'You have entered some information. Are you sure you want to go back to the menu?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, go back',
                    cancelButtonText: 'Stay here'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'order_food.php';
                    }
                });
            } else {
                window.location.href = 'order_food.php';
            }
        }

        function placeOrder() {
            // Validate form
            const deliveryPhone = document.getElementById('delivery_phone').value;
            const deliveryAddress = document.getElementById('delivery_address').value;
            
            if (!deliveryPhone || !deliveryAddress) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please fill in delivery phone and address'
                });
                return;
            }
            
            if (!selectedPaymentMethod) {
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Method Required',
                    text: 'Please select a payment method'
                });
                return;
            }
            
            // Validate receipt upload for mobile money and bank transfer
            if ((selectedPaymentMethod === 'mobile_money' || selectedPaymentMethod === 'bank_transfer')) {
                const paymentReference = document.getElementById('payment_reference').value;
                const receiptImage = document.getElementById('receipt_image').files[0];
                
                if (!paymentReference || !receiptImage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Details Required',
                        text: 'Please provide payment reference and upload receipt'
                    });
                    return;
                }
            }

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
            formData.append('action', 'place_order');
            formData.append('cart_data', JSON.stringify(<?php echo json_encode($cart_items); ?>));
            formData.append('delivery_phone', deliveryPhone);
            formData.append('delivery_address', deliveryAddress);
            formData.append('special_instructions', document.getElementById('special_instructions').value);
            formData.append('payment_method', selectedPaymentMethod);
            formData.append('payment_reference', document.getElementById('payment_reference').value);
            formData.append('total_amount', <?php echo $total_amount; ?>);
            
            // Add receipt image if uploaded
            const receiptImage = document.getElementById('receipt_image').files[0];
            if (receiptImage) {
                formData.append('receipt_image', receiptImage);
            }

            // Submit order
            fetch('../actions/place_order_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Placed Successfully!',
                        text: data.message,
                        confirmButtonText: 'View Orders'
                    }).then(() => {
                        window.location.href = 'my_orders.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Order Failed',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while placing your order'
                });
            });
        }
    </script>
</body>
</html>
