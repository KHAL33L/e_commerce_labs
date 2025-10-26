<?php
// actions/add_category_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../controllers/category_controller.php';

if (!is_logged_in()) {
    echo json_encode(['success'=>false, 'message'=>'Not authenticated']);
    exit;
}

$user_id = (int)($_SESSION['customer_id'] ?? 0);
$name = trim($_POST['category_name'] ?? '');

$ctr = new CategoryController();
$res = $ctr->add_category_ctr(['category_name'=>$name, 'user_id'=>$user_id]);
echo json_encode($res);
exit;
