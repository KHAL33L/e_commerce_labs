<?php
// actions/register_customer_action.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/customer_controller.php';

// Accept POST (FormData) or JSON
$data = $_POST;
if (empty($data)) {
    // maybe JSON body
    $json = json_decode(file_get_contents('php://input'), true);
    if (is_array($json)) $data = $json;
}

// Simple required fields check (server-side)
$required = ['customer_name','customer_email','customer_pass','customer_country','customer_city','customer_contact'];
foreach ($required as $r) {
    if (empty($data[$r])) {
        echo json_encode(['success'=>false, 'message'=>"Field {$r} is required."]);
        exit;
    }
}

// if image upload via file input, you'd handle $_FILES here (we skip for simplicity)

// instantiate controller
$ctr = new CustomerController();
$result = $ctr->register_customer_ctr($data);

// Return JSON
echo json_encode($result);
