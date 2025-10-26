<?php
// actions/update_product_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';
require_once __DIR__.'/../controllers/product_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['success'=>false,'message'=>'Not authorised']);
    exit;
}
$uid = (int)($_SESSION['customer_id'] ?? 0);
$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$cat = (int)($_POST['category_id'] ?? 0);
$brand = (int)($_POST['brand_id'] ?? 0);
$desc = trim($_POST['description'] ?? '');
$keywords = trim($_POST['keywords'] ?? '');
$image_path = trim($_POST['image_path'] ?? null);

$ctr = new ProductController();
$res = $ctr->update_product_ctr($id, [
    'title'=>$title,'description'=>$desc,'price'=>$price,
    'category_id'=>$cat,'brand_id'=>$brand,'user_id'=>$uid,
    'image_path'=>$image_path,'keywords'=>$keywords
]);
echo json_encode($res);
exit;
