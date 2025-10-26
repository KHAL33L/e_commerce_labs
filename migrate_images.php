<?php
// migrate_images.php - Migrate old nested image paths to new structure
require_once __DIR__ . '/config/init.php';

header('Content-Type: text/plain');

echo "=== Product Image Path Migration ===\n\n";

require_once __DIR__ . '/controllers/product_controller.php';
$productController = new ProductController();

// Get all products
$result = $productController->get_all_products_ctr(1, 1000);
$products = $result['data'] ?? [];

echo "Total products to check: " . count($products) . "\n\n";

$migrated = 0;
$notFound = 0;
$noAction = 0;

foreach ($products as $product) {
    if (empty($product['image_path'])) {
        $noAction++;
        continue;
    }
    
    $oldPath = $product['image_path'];
    
    // Check if it's the old nested path format: uploads/u{uid}/p{pid}/filename
    if (preg_match('#^uploads/u(\d+)/p(\d+)/([^/]+)$#', $oldPath, $matches)) {
        $oldFullPath = __DIR__ . '/' . $oldPath;
        
        if (!file_exists($oldFullPath)) {
            echo "SKIP (file not found): Product ID {$product['id']} - {$product['title']}\n";
            echo "  Old path: $oldPath\n\n";
            $notFound++;
            continue;
        }
        
        // New simplified path: uploads/filename
        $filename = $matches[3];
        $newPath = 'uploads/' . $filename;
        $newFullPath = __DIR__ . '/' . $newPath;
        
        // Copy file to new location
        if (!copy($oldFullPath, $newFullPath)) {
            echo "ERROR copying file for Product ID {$product['id']} - {$product['title']}\n";
            echo "  From: $oldFullPath\n";
            echo "  To: $newFullPath\n\n";
            continue;
        }
        
        // Update database
        $updateData = [
            'title' => $product['title'],
            'price' => $product['price'],
            'description' => $product['description'] ?? '',
            'category_id' => $product['category_id'],
            'brand_id' => $product['brand_id'],
            'keywords' => $product['keywords'] ?? '',
            'image_path' => $newPath,
            'user_id' => $product['user_id']
        ];
        
        $updateResult = $productController->update_product_ctr($product['id'], $updateData);
        
        if ($updateResult['success']) {
            echo "MIGRATED: Product ID {$product['id']} - {$product['title']}\n";
            echo "  Old: $oldPath\n";
            echo "  New: $newPath\n\n";
            $migrated++;
            
            // Optionally delete old file
            // unlink($oldFullPath);
        } else {
            echo "ERROR updating database for Product ID {$product['id']} - {$product['title']}\n";
            // Delete the new file if database update failed
            if (file_exists($newFullPath)) {
                unlink($newFullPath);
            }
        }
    } else {
        $noAction++;
    }
}

echo "\n=== Migration Summary ===\n";
echo "Products migrated: $migrated\n";
echo "Products skipped (file not found): $notFound\n";
echo "Products with no action needed: $noAction\n";
echo "\nMigration complete!\n";

