<?php
// actions/delete_category_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../controllers/category_controller.php';

if (!is_logged_in()) {
    echo json_encode(['success'=>false, 'message'=>'Not authenticated']);
    exit;
}

$user_id = (int)($_SESSION['customer_id'] ?? 0);
$id = (int)($_POST['id'] ?? 0);

$ctr = new CategoryController();
$res = $ctr->delete_category_ctr($id, $user_id);
echo json_encode($res);
exit;
