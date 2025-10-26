<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once __DIR__ . '/../controllers/customer_controller.php';

$data = $_POST;
if (empty($data)) {
    // maybe JSON body
    $json = json_decode(file_get_contents('php://input'), true);
    if (is_array($json)) $data = $json;
}

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

$ctr = new CustomerController();
$res = $ctr->login_customer_ctr($email, $password);

if ($res['success']) {
    // Start session and set user info
    if (session_status() !== PHP_SESSION_ACTIVE) {
        // configure cookie params if you want (optional)
        session_start();
    }

    // store minimal user info in session
    $_SESSION['customer_id'] = $res['user']['id'] ?? $res['user']['customer_id'] ?? null;
    // store a friendly display name and role if present
    $_SESSION['customer_name'] = $res['user']['customer_name'] ?? null;
    $_SESSION['customer_email'] = $res['user']['customer_email'] ?? null;
    $_SESSION['user_role'] = $res['user']['user_role'] ?? null;

    echo json_encode(['success'=>true, 'message'=>$res['message'] ?? 'Login successful.']);
    exit;
} else {
    echo json_encode(['success'=>false, 'message'=>$res['message'] ?? 'Login failed.']);
    exit;
}
