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

// Ensure uploads directory exists and is writable
$uploadsBase = __DIR__ . '/../uploads';
if (!is_dir($uploadsBase)) {
    if (!mkdir($uploadsBase, 0755, true)) {
        echo json_encode(['success'=>false,'message'=>'Failed to create uploads directory']);
        exit;
    }
}

// Ensure directory is writable
if (!is_writable($uploadsBase)) {
    echo json_encode(['success'=>false,'message'=>'Uploads directory is not writable']);
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

// Basic mime & extension check
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
if (!isset($allowed[$mime])) {
    echo json_encode(['success'=>false,'message'=>'Invalid image type']);
    exit;
}
$ext = $allowed[$mime];

// Generate unique filename
$filename = 'product_' . $product_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$dest = $uploadsBase . DIRECTORY_SEPARATOR . $filename;

// Security: ensure dest is still inside uploadsBase
$realDestDir = realpath(dirname($dest));
$realUploadsBase = realpath($uploadsBase);
if ($realDestDir === false || $realUploadsBase === false || strpos($realDestDir, $realUploadsBase) !== 0) {
    echo json_encode(['success'=>false,'message'=>'Invalid upload path']);
    exit;
}

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['success'=>false,'message'=>'Failed to store file']);
    exit;
}

// Return web-accessible relative path
$relativePath = 'uploads/' . $filename;
echo json_encode(['success'=>true,'message'=>'Uploaded','image_path'=>$relativePath]);
exit;
