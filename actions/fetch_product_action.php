<?php
// actions/fetch_product_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../controllers/product_controller.php';

if (!is_logged_in()) {
    echo json_encode(['success'=>false,'message'=>'Not authenticated']);
    exit;
}

$uid = (int)($_SESSION['customer_id'] ?? 0);
$ctr = new ProductController();

// If POST request with id, fetch single product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $product = $ctr->get_product_ctr($id, $uid);
    
    if ($product) {
        echo json_encode(['success'=>true,'product'=>$product]);
    } else {
        echo json_encode(['success'=>false,'message'=>'Product not found']);
    }
    exit;
}

// Otherwise fetch all user products
$products = $ctr->fetch_user_products_ctr($uid);
echo json_encode(['success'=>true,'products'=>$products]);
exit;
