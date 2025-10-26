<?php
// actions/upload_product_image_action.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/init.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['success'=>false,'message'=>'Not authorised']);
    exit;
}

$uid = (int)($_SESSION['customer_id'] ?? 0);
$product_id = (int)($_POST['product_id'] ?? 0);

// ensure uploads directory exists and is writable
$uploadsBase = realpath(__DIR__ . '/../uploads');
if ($uploadsBase === false) {
    echo json_encode(['success'=>false,'message'=>'Server uploads directory not found']);
    exit;
}

if (empty($_FILES['image'])) {
    echo json_encode(['success'=>false,'message'=>'No file uploaded']);
    exit;
}

$file = $_FILES['image'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success'=>false,'message'=>'Upload error']);
    exit;
}

// basic mime & extension check
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
if (!isset($allowed[$mime])) {
    echo json_encode(['success'=>false,'message'=>'Invalid image type']);
    exit;
}
$ext = $allowed[$mime];

// create user/product subfolders inside uploads (only inside)
$userDir = $uploadsBase . DIRECTORY_SEPARATOR . 'u' . $uid;
$productDir = $userDir . DIRECTORY_SEPARATOR . 'p' . ($product_id ?: 'tmp'); // tmp if no product_id yet
if (!is_dir($userDir)) mkdir($userDir, 0755, true);
if (!is_dir($productDir)) mkdir($productDir, 0755, true);

// generate unique filename
$filename = 'image_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$dest = $productDir . DIRECTORY_SEPARATOR . $filename;

// security: ensure dest is still inside uploadsBase
$realDestDir = realpath(dirname($dest));
if (strpos($realDestDir, $uploadsBase) !== 0) {
    echo json_encode(['success'=>false,'message'=>'Invalid upload path']);
    exit;
}

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['success'=>false,'message'=>'Failed to store file']);
    exit;
}

// Return web-accessible relative path (assuming uploads/ is web accessible at ../uploads)
$relativePath = 'uploads/u' . $uid . '/p' . ($product_id ?: 'tmp') . '/' . $filename;
echo json_encode(['success'=>true,'message'=>'Uploaded','image_path'=>$relativePath]);
exit;
