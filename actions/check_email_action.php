<?php
// actions/check_email_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../controllers/customer_controller.php';

$payload = json_decode(file_get_contents('php://input'), true);
$email = $payload['email'] ?? null;

$ctr = new CustomerController();
$res = $ctr->check_email_ctr($email);
echo json_encode($res);
