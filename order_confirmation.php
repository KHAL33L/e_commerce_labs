<?php
// order_confirmation.php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/settings/core.php';
require_once __DIR__ . '/includes/header.php';

// Get order ID from GET parameter or session
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : (isset($_SESSION['order_id']) ? (int)$_SESSION['order_id'] : 0);

if ($order_id <= 0) {
    header('Location: index.php');
    exit;
}

// Initialize order controller
$orderController = new OrderController();
$order_result = $orderController->get_order_ctr($order_id);

if (!$order_result['success'] || empty($order_result['data'])) {
    // Order not found
    header('Location: index.php');
    exit;
}

$order_details = $order_result['data'];

// Clear the order ID from session to prevent refresh issues
unset($_SESSION['order_id']);

// Calculate order totals
$subtotal = 0;
$tax_rate = 0.08; // Same as in checkout
$shipping = 10.00; // Same as in checkout

// Get order items - check if items exist in the order_details
$order_items = $order_details['items'] ?? [];

// If items are not in the expected format, fetch them separately
if (empty($order_items)) {
    // Try to get order details from orderdetails table
    try {
        $stmt = $pdo->prepare("
            SELECT od.*, p.title as product_name, p.image_path as image_url
            FROM orderdetails od
            JOIN product p ON od.product_id = p.id
            WHERE od.order_id = :order_id
        ");
        $stmt->execute([':order_id' => $order_id]);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching order items: " . $e->getMessage());
        $order_items = [];
    }
}

foreach ($order_items as $item) {
    $subtotal += (float)($item['price'] ?? 0) * (int)($item['qty'] ?? 0);
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
                            <h4 class="mb-0"><?= htmlspecialchars($order_details['invoice_no'] ?? 'N/A') ?></h4>
                            <p class="small text-muted mb-0"><?= isset($order_details['order_date']) ? date('F j, Y', strtotime($order_details['order_date'])) : date('F j, Y') ?></p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info text-start">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                    <p class="mb-1">We've sent an email with your order confirmation and details<?= isset($order_details['email']) ? ' to <strong>' . htmlspecialchars($order_details['email']) . '</strong>' : '' ?>.</p>
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
                                    <p class="mb-2"><strong>Order Number:</strong> <span class="text-muted"><?= htmlspecialchars($order_details['invoice_no'] ?? 'N/A') ?></span></p>
                                    <p class="mb-2"><strong>Date:</strong> <span class="text-muted"><?= isset($order_details['order_date']) ? date('F j, Y', strtotime($order_details['order_date'])) : date('F j, Y') ?></span></p>
                                    <p class="mb-2"><strong>Status:</strong> 
                                        <span class="badge bg-<?= 
                                            ($order_details['order_status'] ?? 'pending') === 'completed' ? 'success' : 
                                            (($order_details['order_status'] ?? 'pending') === 'processing' ? 'primary' : 'warning') 
                                        ?>">
                                            <?= ucfirst($order_details['order_status'] ?? 'pending') ?>
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
                                    <?php if (isset($order_details['first_name']) || isset($order_details['last_name'])): ?>
                                        <p class="mb-1"><?= htmlspecialchars(($order_details['first_name'] ?? '') . ' ' . ($order_details['last_name'] ?? '')) ?></p>
                                    <?php endif; ?>
                                    <?php if (isset($order_details['address'])): ?>
                                        <p class="mb-1"><?= htmlspecialchars($order_details['address']) ?></p>
                                    <?php endif; ?>
                                    <?php if (isset($order_details['city']) || isset($order_details['state']) || isset($order_details['zip_code'])): ?>
                                        <p class="mb-1"><?= htmlspecialchars(($order_details['city'] ?? '') . ', ' . ($order_details['state'] ?? '') . ' ' . ($order_details['zip_code'] ?? '')) ?></p>
                                    <?php endif; ?>
                                    <?php if (isset($order_details['country'])): ?>
                                        <p class="mb-0"><?= htmlspecialchars($order_details['country']) ?></p>
                                    <?php endif; ?>
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
                                    <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= htmlspecialchars($item['image_url'] ?? $item['image_path'] ?? 'assets/images/placeholder.jpg') ?>" 
                                                         alt="<?= htmlspecialchars($item['product_name'] ?? 'Product') ?>" 
                                                         style="width: 50px; height: 50px; object-fit: cover; margin-right: 1rem;">
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($item['product_name'] ?? 'Product') ?></h6>
                                                        <small class="text-muted">Product ID: <?= $item['product_id'] ?? 'N/A' ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">₦<?= number_format((float)($item['price'] ?? 0), 2) ?></td>
                                            <td class="text-center"><?= $item['qty'] ?? 0 ?></td>
                                            <td class="text-end">₦<?= number_format((float)($item['price'] ?? 0) * (int)($item['qty'] ?? 0), 2) ?></td>
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