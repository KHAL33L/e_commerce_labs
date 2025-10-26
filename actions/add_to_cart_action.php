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
    
    // Check if product already exists in cart for this user/ip
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE p_id = :pid AND c_id = :cid AND ip_add = :ip");
    $stmt->execute([':pid' => $product_id, ':cid' => $customer_id, ':ip' => $ip_address]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Update quantity
        $new_qty = $existing['qty'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET qty = :qty WHERE p_id = :pid AND c_id = :cid AND ip_add = :ip");
        $stmt->execute([':qty' => $new_qty, ':pid' => $product_id, ':cid' => $customer_id, ':ip' => $ip_address]);
    } else {
        // Temporarily disable foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        
        // Insert new cart item
        $stmt = $pdo->prepare("INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (:pid, :ip, :cid, :qty)");
        $stmt->execute([':pid' => $product_id, ':ip' => $ip_address, ':cid' => $customer_id, ':qty' => $quantity]);
        
        // Re-enable foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    }
    
    // Also update session cart for immediate display
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Get product details
    $stmt = $pdo->prepare("SELECT * FROM product WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        $_SESSION['cart'][$product_id] = [
            'name' => $product['title'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image_path'] ?? 'assets/images/placeholder.jpg'
        ];
    }
    
    // Get cart count
    $stmt = $pdo->prepare("SELECT SUM(qty) as total_qty FROM cart WHERE c_id = :cid AND ip_add = :ip");
    $stmt->execute([':cid' => $customer_id, ':ip' => $ip_address]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cart_count = (int)($result['total_qty'] ?? 0);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Product added to cart',
        'cart_count' => $cart_count
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
exit;
