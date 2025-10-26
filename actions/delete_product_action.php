<?php
// actions/delete_product_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../controllers/product_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['success'=>false,'message'=>'Not authorised']);
    exit;
}

$uid = (int)($_SESSION['customer_id'] ?? 0);
$id = (int)($_POST['id'] ?? 0);

$ctr = new ProductController();
$res = $ctr->delete_product_ctr($id, $uid);
echo json_encode($res);
exit;
