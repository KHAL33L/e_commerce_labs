<?php
// actions/process_checkout_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $customer_id = $_SESSION['customer_id'] ?? 0;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    // Initialize controllers
    $cartController = new CartController();
    $orderController = new OrderController();
    $productController = new ProductController();
    
    // Get cart items
    $cart_result = $cartController->get_user_cart_ctr($customer_id, $ip_address);
    
    if (!$cart_result['success'] || empty($cart_result['items'])) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit;
    }
    
    $cart_items = $cart_result['items'];
    
    // Calculate totals
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    $shipping = 10.00; // Fixed shipping cost
    $tax_rate = 0.08; // 8% tax
    $tax = $subtotal * $tax_rate;
    $total_amount = $subtotal + $shipping + $tax;
    
    // Get payment method from POST
    $payment_method = $_POST['payment_method'] ?? 'simulated';
    
    // Create order
    $order_result = $orderController->create_order_ctr([
        'customer_id' => $customer_id,
        'total_amount' => $total_amount
    ]);
    
    if (!$order_result['success']) {
        echo json_encode(['success' => false, 'message' => 'Failed to create order: ' . ($order_result['message'] ?? 'Unknown error')]);
        exit;
    }
    
    $order_id = $order_result['order_id'];
    $invoice_no = $order_result['invoice_no'];
    
    // Add order details
    $order_details_success = true;
    foreach ($cart_items as $item) {
        $detail_result = $orderController->add_order_details_ctr([
            'order_id' => $order_id,
            'product_id' => $item['product_id'],
            'qty' => $item['quantity'],
            'price' => $item['price']
        ]);
        
        if (!$detail_result['success']) {
            $order_details_success = false;
            error_log("Failed to add order detail for product {$item['product_id']}: " . ($detail_result['message'] ?? 'Unknown error'));
        }
    }
    
    if (!$order_details_success) {
        // Some order details failed, but order was created
        // In production, you might want to rollback the order
        error_log("Warning: Some order details failed to be added for order $order_id");
    }
    
    // Record payment
    $payment_result = $orderController->record_payment_ctr([
        'order_id' => $order_id,
        'amount' => $total_amount,
        'payment_method' => $payment_method,
        'payment_status' => 'completed'
    ]);
    
    // Empty the cart
    $empty_cart_result = $cartController->empty_cart_ctr($customer_id, $ip_address);
    
    // Clear session cart
    unset($_SESSION['cart']);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $order_id,
        'invoice_no' => $invoice_no,
        'order_reference' => $invoice_no,
        'total_amount' => $total_amount,
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'tax' => $tax
    ]);
    
} catch (Exception $e) {
    error_log("Checkout error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Checkout failed: ' . $e->getMessage()]);
}
exit;

