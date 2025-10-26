<?php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/settings/core.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions - MUST be before header.php to allow redirects
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                foreach ($_POST['quantities'] as $productId => $quantity) {
                    if (isset($_SESSION['cart'][$productId]) && $quantity > 0) {
                        $_SESSION['cart'][$productId]['quantity'] = (int)$quantity;
                    }
                }
                $_SESSION['flash_message'] = 'Cart updated successfully';
                $_SESSION['flash_type'] = 'success';
                header('Location: cart.php');
                exit;
                
            case 'remove':
                $productId = $_POST['product_id'] ?? 0;
                if (isset($_SESSION['cart'][$productId])) {
                    unset($_SESSION['cart'][$productId]);
                    $_SESSION['flash_message'] = 'Item removed from cart';
                    $_SESSION['flash_type'] = 'success';
                }
                header('Location: cart.php');
                exit;
                
            case 'clear':
                $_SESSION['cart'] = [];
                $_SESSION['flash_message'] = 'Cart cleared successfully';
                $_SESSION['flash_type'] = 'success';
                header('Location: cart.php');
                exit;
        }
    }
}

// Include header after handling redirects
require_once __DIR__ . '/includes/header.php';

// Calculate totals
$subtotal = 0;
$shipping = 0;
$total = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = $subtotal > 0 ? 10.00 : 0; // Example shipping calculation
$total = $subtotal + $shipping;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Shopping Cart</h1>
            
            <?php if (empty($_SESSION['cart'])): ?>
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
                        <form method="post" action="cart.php">
                            <input type="hidden" name="action" value="update">
                            
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
                                        <?php foreach ($_SESSION['cart'] as $productId => $item): 
                                            $itemTotal = $item['price'] * $item['quantity'];
                                        ?>
                                            <tr>
                                                <td>
                                                    <img src="<?= htmlspecialchars($item['image'] ?? 'assets/images/placeholder.jpg') ?>" 
                                                         alt="<?= htmlspecialchars($item['name']) ?>" 
                                                         class="img-fluid" 
                                                         style="max-height: 80px;">
                                                </td>
                                                <td>
                                                    <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                                    <p class="text-muted small mb-0">
                                                        SKU: <?= htmlspecialchars($item['sku'] ?? 'N/A') ?>
                                                    </p>
                                                </td>
                                                <td>₦<?= number_format($item['price'], 2) ?></td>
                                                <td>
                                                    <div class="input-group" style="max-width: 120px;">
                                                        <input type="number" 
                                                               name="quantities[<?= $productId ?>]" 
                                                               class="form-control text-center" 
                                                               value="<?= $item['quantity'] ?>" 
                                                               min="1" 
                                                               onchange="this.form.submit()">
                                                    </div>
                                                </td>
                                                <td class="text-end">₦<?= number_format($itemTotal, 2) ?></td>
                                                <td class="text-center">
                                                    <form method="post" action="cart.php" class="d-inline">
                                                        <input type="hidden" name="action" value="remove">
                                                        <input type="hidden" name="product_id" value="<?= $productId ?>">
                                                        <button type="submit" class="btn btn-link text-danger" title="Remove item">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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
                                            <button type="submit" class="btn btn-outline-primary">
                                                <i class="fas fa-sync-alt me-2"></i> Update Cart
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearCartModal">
                                                <i class="fas fa-trash-alt me-2"></i> Clear Cart
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
                        </form>
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
                                    <span>₦<?= number_format($subtotal, 2) ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping</span>
                                    <span><?= $shipping > 0 ? '₦' . number_format($shipping, 2) : 'Free' ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax</span>
                                    <span>Calculated at checkout</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <h5>Total</h5>
                                    <h5 class="text-primary">₦<?= number_format($total, 2) ?></h5>
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

<!-- Clear Cart Modal -->
<div class="modal fade" id="clearCartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="cart.php">
                <input type="hidden" name="action" value="clear">
                <div class="modal-body">
                    <p>Are you sure you want to remove all items from your cart?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Clear Cart</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>