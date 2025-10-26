<?php
// actions/fetch_brand_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

if (!is_logged_in()) {
    echo json_encode(['success'=>false,'message'=>'Not authenticated']);
    exit;
}

$uid = (int)($_SESSION['customer_id'] ?? 0);
$ctr = new BrandController();
$brands = $ctr->fetch_user_brands_ctr($uid);
echo json_encode(['success'=>true,'brands'=>$brands]);
exit;
