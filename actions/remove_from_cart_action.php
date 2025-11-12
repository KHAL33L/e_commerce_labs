<?php
// actions/remove_from_cart_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$cart_id = (int)($_POST['cart_id'] ?? 0);

if ($cart_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart ID']);
    exit;
}

try {
    $cartController = new CartController();
    $result = $cartController->remove_from_cart_ctr($cart_id);
    
    if ($result['success']) {
        // Get updated cart count
        $customer_id = $_SESSION['customer_id'] ?? 0;
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $cart_count = $cartController->get_cart_count_ctr($customer_id, $ip_address);
        
        $result['cart_count'] = $cart_count;
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
exit;

