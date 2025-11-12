<?php
// checkout.php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/settings/core.php';
require_once __DIR__ . '/includes/header.php';

// Get customer ID and IP address
$customer_id = $_SESSION['customer_id'] ?? 0;
$ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

// Initialize cart controller
$cartController = new CartController();

// Get cart items from database
$cart_result = $cartController->get_user_cart_ctr($customer_id, $ip_address);
$cart_items = $cart_result['items'] ?? [];

// Check if cart is empty
if (empty($cart_items)) {
    $_SESSION['flash_message'] = 'Your cart is empty. Please add items before checking out.';
    $_SESSION['flash_type'] = 'warning';
    header('Location: cart.php');
    exit;
}

// Calculate order totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping = 10.00; // Example shipping cost
$tax_rate = 0.08; // Example tax rate (8%)
$tax = $subtotal * $tax_rate;
$total = $subtotal + $shipping + $tax;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </nav>
            
            <h1 class="mb-4">Checkout</h1>
            
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['flash_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php 
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']);
                ?>
            <?php endif; ?>
            
            <form method="post" id="checkoutForm">
                <div class="row">
                    <!-- Billing & Shipping Information -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Billing & Shipping Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="firstName" name="first_name" 
                                               value="<?= $_SESSION['user']['first_name'] ?? '' ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="lastName" name="last_name" 
                                               value="<?= $_SESSION['user']['last_name'] ?? '' ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= $_SESSION['user']['email'] ?? '' ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?= $_SESSION['user']['phone'] ?? '' ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="city" name="city" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="state" class="form-label">State/Province <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="state" name="state" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="zip" class="form-label">ZIP/Postal Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="zip" name="zip" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                        <select class="form-select" id="country" name="country" required>
                                            <option value="">Select Country</option>
                                            <option value="US">United States</option>
                                            <option value="CA">Canada</option>
                                            <option value="UK">United Kingdom</option>
                                            <option value="AU">Australia</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="shippingSameAsBilling">
                                    <label class="form-check-label" for="shippingSameAsBilling">
                                        Shipping address is the same as billing address
                                    </label>
                                </div>
                                
                                <!-- Payment Method -->
                                <div class="mt-4">
                                    <h5 class="mb-3">Payment Method</h5>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" id="creditCard" value="credit_card" checked>
                                        <label class="form-check-label" for="creditCard">
                                            Credit/Debit Card
                                        </label>
                                    </div>
                                    
                                    <div id="creditCardForm" class="ps-4 mb-4">
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label for="cardNumber" class="form-label">Card Number <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456" required>
                                                    <span class="input-group-text"><i class="fab fa-cc-visa"></i> <i class="fab fa-cc-mastercard ms-1"></i> <i class="fab fa-cc-amex ms-1"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="cardName" class="form-label">Name on Card <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="cardName" required>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="cardExpiry" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="cardExpiry" placeholder="MM/YY" required>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="cardCvv" class="form-label">CVV <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="cardCvv" placeholder="123" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                        <label class="form-check-label" for="paypal">
                                            <i class="fab fa-paypal me-1"></i> PayPal
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" id="bankTransfer" value="bank_transfer">
                                        <label class="form-check-label" for="bankTransfer">
                                            <i class="fas fa-university me-1"></i> Bank Transfer
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I have read and agree to the website's <a href="terms.php" target="_blank">terms and conditions</a> <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <?php foreach ($cart_items as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?= htmlspecialchars($item['image_path']) ?>" 
                                                                 alt="<?= htmlspecialchars($item['title']) ?>" 
                                                                 style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                                            <div>
                                                                <h6 class="mb-0"><?= htmlspecialchars($item['title']) ?></h6>
                                                                <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">₦<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Subtotal</th>
                                                <td class="text-end" data-subtotal="<?= $subtotal ?>">₦<?= number_format($subtotal, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Shipping</th>
                                                <td class="text-end" data-shipping="<?= $shipping ?>">₦<?= number_format($shipping, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Tax (<?= ($tax_rate * 100) ?>%)</th>
                                                <td class="text-end" data-tax="<?= $tax ?>">₦<?= number_format($tax, 2) ?></td>
                                            </tr>
                                            <tr class="fw-bold">
                                                <th>Total</th>
                                                <td class="text-end" data-total="<?= $total ?>">₦<?= number_format($total, 2) ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg" id="placeOrderBtn">
                                        Place Order
                                    </button>
                                </div>
                                
                                <div class="mt-3 text-center">
                                    <p class="small text-muted mb-1">
                                        <i class="fas fa-lock me-1"></i> Secure SSL Encryption
                                    </p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <img src="assets/images/payment-methods/visa.png" alt="Visa" style="height: 20px;">
                                        <img src="assets/images/payment-methods/mastercard.png" alt="Mastercard" style="height: 20px;">
                                        <img src="assets/images/payment-methods/amex.png" alt="American Express" style="height: 20px;">
                                        <img src="assets/images/payment-methods/paypal.png" alt="PayPal" style="height: 20px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle shipping address fields
document.getElementById('shippingSameAsBilling').addEventListener('change', function() {
    const shippingAddress = document.getElementById('shippingAddress');
    const shippingInputs = shippingAddress.querySelectorAll('input, select');
    
    if (this.checked) {
        shippingAddress.style.display = 'none';
        shippingInputs.forEach(input => input.removeAttribute('required'));
    } else {
        shippingAddress.style.display = 'block';
        shippingInputs.forEach(input => input.setAttribute('required', 'required'));
    }
});

// Format credit card number
document.getElementById('cardNumber').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let formatted = value.replace(/(\d{4})/g, '$1 ').trim();
    e.target.value = formatted;
});

// Format expiry date
document.getElementById('cardExpiry').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
});

// Format CVV
document.getElementById('cardCvv').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
});

// Note: Form submission is now handled by checkout.js
</script>
<script src="js/checkout.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>