<?php
// actions/update_brand_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['success'=>false,'message'=>'Not authorised']);
    exit;
}

$uid = (int)($_SESSION['customer_id'] ?? 0);
$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['brand_name'] ?? '');

$ctr = new BrandController();
$res = $ctr->update_brand_ctr(['id'=>$id,'brand_name'=>$name,'user_id'=>$uid]);
echo json_encode($res);
exit;
