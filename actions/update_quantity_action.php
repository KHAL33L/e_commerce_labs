<?php
// actions/update_quantity_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$cart_id = (int)($_POST['cart_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

if ($cart_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart ID']);
    exit;
}

if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be greater than 0']);
    exit;
}

try {
    $cartController = new CartController();
    $result = $cartController->update_cart_item_ctr($cart_id, $quantity);
    
    if ($result['success']) {
        // Get updated cart count and total
        $customer_id = $_SESSION['customer_id'] ?? 0;
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $cart_count = $cartController->get_cart_count_ctr($customer_id, $ip_address);
        $cart_total = $cartController->get_cart_total_ctr($customer_id, $ip_address);
        
        $result['cart_count'] = $cart_count;
        $result['cart_total'] = $cart_total;
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
exit;

