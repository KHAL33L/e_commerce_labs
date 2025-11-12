<?php
// actions/empty_cart_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $customer_id = $_SESSION['customer_id'] ?? 0;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    $cartController = new CartController();
    $result = $cartController->empty_cart_ctr($customer_id, $ip_address);
    
    if ($result['success']) {
        // Clear session cart as well
        unset($_SESSION['cart']);
        $result['cart_count'] = 0;
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
exit;

