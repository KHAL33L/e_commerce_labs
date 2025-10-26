<?php
// check_images.php - Diagnostic script to check product images
require_once __DIR__ . '/config/init.php';

header('Content-Type: text/plain');

echo "=== Product Image Diagnostic ===\n\n";

require_once __DIR__ . '/controllers/product_controller.php';
$productController = new ProductController();

// Get all products
$result = $productController->get_all_products_ctr(1, 1000); // Get first 1000 products
$products = $result['data'] ?? [];

echo "Total products found: " . count($products) . "\n\n";

$productsWithImages = 0;
$productsWithoutImages = 0;
$invalidPaths = [];

foreach ($products as $product) {
    $hasImage = !empty($product['image_path']);
    
    if ($hasImage) {
        $productsWithImages++;
        $imagePath = $product['image_path'];
        
        // Check if it's the old nested path format
        if (preg_match('/uploads\/u\d+\/p\d+\//', $imagePath)) {
            $invalidPaths[] = [
                'id' => $product['id'],
                'title' => $product['title'],
                'old_path' => $imagePath
            ];
        }
        
        // Check if file exists
        $fullPath = __DIR__ . '/' . $imagePath;
        if (!file_exists($fullPath)) {
            echo "WARNING: Image file not found for product ID {$product['id']} ({$product['title']}):\n";
            echo "  Path: $imagePath\n";
            echo "  Full path: $fullPath\n\n";
        } else {
            echo "OK: Product ID {$product['id']} - {$product['title']}\n";
            echo "  Path: $imagePath\n";
            echo "  File size: " . filesize($fullPath) . " bytes\n\n";
        }
    } else {
        $productsWithoutImages++;
        echo "NO IMAGE: Product ID {$product['id']} - {$product['title']}\n\n";
    }
}

echo "\n=== Summary ===\n";
echo "Products with images: $productsWithImages\n";
echo "Products without images: $productsWithoutImages\n";
echo "Products with old path format: " . count($invalidPaths) . "\n";

if (count($invalidPaths) > 0) {
    echo "\n=== Products with old path format ===\n";
    foreach ($invalidPaths as $item) {
        echo "ID {$item['id']}: {$item['title']} - {$item['old_path']}\n";
    }
}

// Check uploads directory
echo "\n=== Uploads Directory Check ===\n";
$uploadsDir = __DIR__ . '/uploads';
if (is_dir($uploadsDir)) {
    echo "Uploads directory exists: YES\n";
    echo "Uploads directory writable: " . (is_writable($uploadsDir) ? 'YES' : 'NO') . "\n";
    
    $files = glob($uploadsDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
    echo "Image files in uploads/: " . count($files) . "\n";
    
    if (count($files) > 0) {
        echo "\nFirst 10 image files:\n";
        foreach (array_slice($files, 0, 10) as $file) {
            echo "  - " . basename($file) . " (" . filesize($file) . " bytes)\n";
        }
    }
} else {
    echo "Uploads directory exists: NO\n";
    echo "ERROR: Uploads directory not found at: $uploadsDir\n";
}

