<?php
// actions/add_brand_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['success'=>false,'message'=>'Not authorised']);
    exit;
}

$uid = (int)($_SESSION['customer_id'] ?? 0);
$name = trim($_POST['brand_name'] ?? '');
$cat = (int)($_POST['category_id'] ?? 0);

$ctr = new BrandController();
$res = $ctr->add_brand_ctr(['brand_name'=>$name,'category_id'=>$cat,'user_id'=>$uid]);
echo json_encode($res);
exit;
