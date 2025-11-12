<?php
// actions/add_to_cart_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

try {
    // Get user ID or use 0 for guest
    $customer_id = $_SESSION['customer_id'] ?? 0;
    
    // Get IP address for guest tracking
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    // Use cart controller
    $cartController = new CartController();
    $result = $cartController->add_to_cart_ctr($product_id, $customer_id, $ip_address, $quantity);
    
    if ($result['success']) {
        // Get updated cart count
        $cart_count = $cartController->get_cart_count_ctr($customer_id, $ip_address);
        $result['cart_count'] = $cart_count;
        
        // Also update session cart for backward compatibility
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Get product details for session
        $productController = new ProductController();
        $product_result = $productController->get_single_product_ctr($product_id);
        
        if ($product_result['success'] && !empty($product_result['data'])) {
            $product = $product_result['data'];
            $_SESSION['cart'][$product_id] = [
                'name' => $product['title'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image' => $product['image_path'] ?? 'assets/images/placeholder.jpg'
            ];
        }
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
exit;
