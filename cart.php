<?php
// cart.php
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

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping = $subtotal > 0 ? 10.00 : 0;
$tax_rate = 0.08; // 8% tax
$tax = $subtotal * $tax_rate;
$total = $subtotal + $shipping + $tax;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Shopping Cart</h1>
            
            <?php if (empty($cart_items)): ?>
                <div class="alert alert-info">
                    <h4 class="alert-heading">Your cart is empty</h4>
                    <p>Looks like you haven't added any items to your cart yet.</p>
                    <hr>
                    <p class="mb-0">
                        <a href="all_products.php" class="alert-link">Continue shopping</a> to find products you like.
                    </p>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 120px;">Product</th>
                                        <th>Details</th>
                                        <th style="width: 150px;">Price</th>
                                        <th style="width: 150px;">Quantity</th>
                                        <th style="width: 150px; text-align: right;">Total</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): 
                                        $itemTotal = $item['price'] * $item['quantity'];
                                    ?>
                                        <tr data-cart-id="<?= $item['cart_id'] ?>">
                                            <td>
                                                <img src="<?= htmlspecialchars($item['image_path']) ?>" 
                                                     alt="<?= htmlspecialchars($item['title']) ?>" 
                                                     class="img-fluid" 
                                                     style="max-height: 80px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <h6 class="mb-1"><?= htmlspecialchars($item['title']) ?></h6>
                                                <p class="text-muted small mb-0">
                                                    Product ID: <?= $item['product_id'] ?>
                                                </p>
                                            </td>
                                            <td>₦<?= number_format($item['price'], 2) ?></td>
                                            <td>
                                                <div class="input-group" style="max-width: 120px;">
                                                    <button class="btn btn-outline-secondary btn-sm update-quantity" 
                                                            type="button"
                                                            data-cart-id="<?= $item['cart_id'] ?>"
                                                            data-action="decrease">-</button>
                                                    <input type="number" 
                                                           class="form-control text-center cart-quantity-input" 
                                                           value="<?= $item['quantity'] ?>" 
                                                           min="1"
                                                           data-cart-id="<?= $item['cart_id'] ?>">
                                                    <button class="btn btn-outline-secondary btn-sm update-quantity" 
                                                            type="button"
                                                            data-cart-id="<?= $item['cart_id'] ?>"
                                                            data-action="increase">+</button>
                                                </div>
                                            </td>
                                            <td class="text-end">₦<?= number_format($itemTotal, 2) ?></td>
                                            <td class="text-center">
                                                <button type="button" 
                                                        class="btn btn-link text-danger remove-cart-item" 
                                                        title="Remove item"
                                                        data-cart-id="<?= $item['cart_id'] ?>"
                                                        data-product-name="<?= htmlspecialchars($item['title']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex gap-2">
                                        <a href="all_products.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" id="emptyCartBtn">
                                            <i class="fas fa-trash-alt me-2"></i> Empty Cart
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="checkout.php" class="btn btn-primary btn-lg w-100">
                                        Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Estimate Shipping</h5>
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">Country</label>
                                        <select class="form-select">
                                            <option>Select Country</option>
                                            <option>United States</option>
                                            <option>Canada</option>
                                            <option>United Kingdom</option>
                                            <option>Australia</option>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">State/Province</label>
                                            <input type="text" class="form-control" placeholder="State/Province">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Zip/Postal Code</label>
                                            <input type="text" class="form-control" placeholder="Zip/Postal Code">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary">Calculate Shipping</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Order Summary</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal</span>
                                    <span data-subtotal="<?= $subtotal ?>">₦<?= number_format($subtotal, 2) ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping</span>
                                    <span data-shipping="<?= $shipping ?>"><?= $shipping > 0 ? '₦' . number_format($shipping, 2) : 'Free' ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax (<?= ($tax_rate * 100) ?>%)</span>
                                    <span data-tax="<?= $tax ?>">₦<?= number_format($tax, 2) ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <h5>Total</h5>
                                    <h5 class="text-primary" data-total="<?= $total ?>">₦<?= number_format($total, 2) ?></h5>
                                </div>
                                <div class="d-grid">
                                    <a href="checkout.php" class="btn btn-primary btn-lg">
                                        Proceed to Checkout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="js/cart.js"></script>
