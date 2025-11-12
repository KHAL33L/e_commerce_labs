<?php
// actions/get_cart_count_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';

try {
    $customer_id = $_SESSION['customer_id'] ?? 0;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    $cartController = new CartController();
    $cart_count = $cartController->get_cart_count_ctr($customer_id, $ip_address);
    
    echo json_encode([
        'success' => true,
        'count' => $cart_count
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'count' => 0,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
exit;

