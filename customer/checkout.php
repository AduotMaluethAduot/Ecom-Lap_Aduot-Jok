<?php
// Checkout page with payment integration
require_once '../src/settings/core.php';
require_once '../controllers/cart_controller.php';

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

// Get cart data from database
$cart_items = get_user_cart_ctr();
$total_amount = get_cart_total_ctr();

// Redirect to cart if cart is empty
if (empty($cart_items)) {
    header('Location: cart.php');
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
                                        <div class="payment-method-card" data-method="mobile_money" onclick="selectPaymentMethod('mobile_money')">
                                            <input type="radio" name="payment_method" value="mobile_money" id="payment_method_mobile_money">
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
                                        <div class="payment-method-card" data-method="bank_transfer" onclick="selectPaymentMethod('bank_transfer')">
                                            <input type="radio" name="payment_method" value="bank_transfer" id="payment_method_bank_transfer">
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
                                        <div class="payment-method-card" data-method="pos" onclick="selectPaymentMethod('pos')">
                                            <input type="radio" name="payment_method" value="pos" id="payment_method_pos">
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
                                        <div class="payment-method-card" data-method="cash" onclick="selectPaymentMethod('cash')">
                                            <input type="radio" name="payment_method" value="cash" id="payment_method_cash">
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
                                                <strong><?php echo htmlspecialchars($item['product_title']); ?></strong>
                                                <br>
                                                <small class="text-muted">Qty: <?php echo $item['qty']; ?></small>
                                            </div>
                                            <div class="text-end">
                                                <strong>$<?php echo number_format(floatval($item['product_price']) * intval($item['qty']), 2); ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total Amount:</strong>
                                    <strong id="totalAmount" data-total="<?php echo $total_amount; ?>">$<?php echo number_format($total_amount, 2); ?></strong>
                                </div>

                                <div class="d-grid gap-2 mt-3">
                                    <button type="button" class="btn btn-checkout" onclick="showPaymentModal()">
                                        <i class="fas fa-check me-2"></i>Simulate Payment
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" onclick="goBackToCart()">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Cart
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
    <script src="../public/js/checkout.js"></script>
</body>
</html>
