<?php
// order_confirmation.php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/settings/core.php';
require_once __DIR__ . '/includes/header.php';

// Check if there's a recent order
if (!isset($_SESSION['order_id'])) {
    header('Location: index.php');
    exit;
}

// The $order object is already available from init.php
$order_details = $order->get_order($_SESSION['order_id']);

if (!$order_details) {
    // Order not found
    header('Location: index.php');
    exit;
}

// Clear the order ID from session to prevent refresh issues
$order_id = $_SESSION['order_id'];
unset($_SESSION['order_id']);

// Calculate order totals
$subtotal = 0;
$tax_rate = 0.08; // Same as in checkout
$shipping = 10.00; // Same as in checkout

foreach ($order_details['items'] as $item) {
    $subtotal += $item['price'] * $item['qty'];
}

$tax = $subtotal * $tax_rate;
$total = $subtotal + $shipping + $tax;
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <div class="bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; margin-bottom: 1.5rem;">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                        </div>
                        
                        <h1 class="h3 mb-3">Thank You for Your Order!</h1>
                        <p class="text-muted mb-4">Your order has been placed and will be processed as soon as possible.</p>
                        
                        <div class="bg-light p-4 rounded d-inline-block" style="max-width: 500px;">
                            <p class="mb-2">Order Number:</p>
                            <h4 class="mb-0"><?= htmlspecialchars($order_details['invoice_no']) ?></h4>
                            <p class="small text-muted mb-0"><?= date('F j, Y', strtotime($order_details['order_date'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info text-start">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <p class="mb-1">We've sent an email with your order confirmation and details to <strong><?= htmlspecialchars($order_details['email']) ?></strong>.</p>
                                <p class="mb-0">You'll receive a shipping confirmation email when your order is on its way.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-5">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Order Details</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><strong>Order Number:</strong> <span class="text-muted"><?= htmlspecialchars($order_details['invoice_no']) ?></span></p>
                                    <p class="mb-2"><strong>Date:</strong> <span class="text-muted"><?= date('F j, Y', strtotime($order_details['order_date'])) ?></span></p>
                                    <p class="mb-2"><strong>Status:</strong> 
                                        <span class="badge bg-<?= 
                                            $order_details['order_status'] === 'completed' ? 'success' : 
                                            ($order_details['order_status'] === 'processing' ? 'primary' : 'warning') 
                                        ?>">
                                            <?= ucfirst($order_details['order_status']) ?>
                                        </span>
                                    </p>
                                    <p class="mb-0"><strong>Total:</strong> <span class="text-muted">₦<?= number_format($total, 2) ?></span></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Shipping Address</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><?= htmlspecialchars($order_details['first_name'] . ' ' . $order_details['last_name']) ?></p>
                                    <p class="mb-1"><?= htmlspecialchars($order_details['address']) ?></p>
                                    <p class="mb-1"><?= htmlspecialchars($order_details['city'] . ', ' . $order_details['state'] . ' ' . $order_details['zip_code']) ?></p>
                                    <p class="mb-0"><?= htmlspecialchars($order_details['country']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_details['items'] as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= htmlspecialchars($item['image_url'] ?? 'assets/images/placeholder.jpg') ?>" 
                                                         alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                                         style="width: 50px; height: 50px; object-fit: cover; margin-right: 1rem;">
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($item['product_name']) ?></h6>
                                                        <small class="text-muted">SKU: <?= $item['product_id'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">₦<?= number_format($item['price'], 2) ?></td>
                                            <td class="text-center"><?= $item['qty'] ?></td>
                                            <td class="text-end">₦<?= number_format($item['price'] * $item['qty'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Subtotal:</th>
                                        <td class="text-end">₦<?= number_format($subtotal, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Shipping:</th>
                                        <td class="text-end">₦<?= number_format($shipping, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Tax (<?= ($tax_rate * 100) ?>%):</th>
                                        <td class="text-end">₦<?= number_format($tax, 2) ?></td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <th colspan="3" class="text-end">Total:</th>
                                        <td class="text-end">₦<?= number_format($total, 2) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">
                        <a href="all_products.php" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                        </a>
                        <a href="user/orders.php" class="btn btn-primary">
                            <i class="fas fa-box-open me-2"></i> View My Orders
                        </a>
                    </div>
                    
                    <div class="mt-5 pt-4 border-top text-center">
                        <h5 class="mb-3">Need Help?</h5>
                        <p class="text-muted mb-4">If you have any questions about your order, please don't hesitate to contact our customer service team.</p>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="contact.php" class="btn btn-outline-secondary">
                                <i class="fas fa-headset me-2"></i> Contact Support
                            </a>
                            <a href="faq.php" class="btn btn-outline-secondary">
                                <i class="fas fa-question-circle me-2"></i> FAQ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>