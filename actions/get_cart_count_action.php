<?php
// actions/get_cart_count_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';

try {
    $customer_id = $_SESSION['customer_id'] ?? 0;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    $cartController = new CartController();
    $cart_data = $cartController->get_user_cart_ctr($customer_id, $ip_address);
    
    $cart_count = 0;
    if (!empty($cart_data['success']) && !empty($cart_data['items'])) {
        foreach ($cart_data['items'] as $item) {
            $cart_count += (int)($item['quantity'] ?? 0);
        }
    } else {
        // Fallback to count query in case items retrieval failed
        $cart_count = $cartController->get_cart_count_ctr($customer_id, $ip_address);
    }
    
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

