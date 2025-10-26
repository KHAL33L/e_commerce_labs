<?php
// check_images.php - Diagnostic script to check product images
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/controllers/product_controller.php';

// HTML output for better display
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #660a38; }
        .status-ok { color: green; font-weight: bold; }
        .status-error { color: red; font-weight: bold; }
        .status-warning { color: orange; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #660a38; color: white; }
        .image-preview { max-width: 100px; max-height: 100px; object-fit: cover; border: 1px solid #ddd; }
        .summary { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info-box { background: #d1ecf1; border-left: 4px solid #0c5460; padding: 10px; margin: 10px 0; }
        a { color: #660a38; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Product Image Diagnostic Tool</h1>
    
    <?php
    $productController = new ProductController();
    
    // Get all products
    $result = $productController->get_all_products_ctr(1, 1000);
    $products = $result['data'] ?? [];
    
    $productsWithImages = 0;
    $productsWithoutImages = 0;
    $imagesFound = 0;
    $imagesMissing = 0;
    
    // Check uploads directory
    $uploadsDir = __DIR__ . '/uploads';
    $uploadsExists = is_dir($uploadsDir);
    $uploadsWritable = $uploadsExists && is_writable($uploadsDir);
    $filesInUploads = [];
    
    if ($uploadsExists) {
        $filesInUploads = glob($uploadsDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
    }
    
    // Get BASE_URL
    $baseUrl = defined('BASE_URL') ? BASE_URL : 'http://' . $_SERVER['HTTP_HOST'];
    ?>
    
    <div class="info-box">
        <strong>📊 Summary</strong><br>
        Total Products: <strong><?= count($products) ?></strong><br>
        Uploads Directory: 
        <?php if ($uploadsExists): ?>
            <span class="status-ok">✓ Exists</span>
        <?php else: ?>
            <span class="status-error">✗ Not Found</span>
        <?php endif; ?><br>
        Uploads Writable: 
        <?php if ($uploadsWritable): ?>
            <span class="status-ok">✓ Yes</span>
        <?php else: ?>
            <span class="status-error">✗ No</span>
        <?php endif; ?><br>
        Files in uploads/: <strong><?= count($filesInUploads) ?></strong><br>
        Base URL: <strong><?= htmlspecialchars($baseUrl) ?></strong>
    </div>
    
    <div class="summary">
        <h2>Products Analysis</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Image Path</th>
                    <th>File Status</th>
                    <th>Preview</th>
                    <th>Test URL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <?php
                    $hasImage = !empty($product['image_path']);
                    $imagePath = $product['image_path'] ?? '';
                    
                    if ($hasImage) {
                        $productsWithImages++;
                        $fullPath = __DIR__ . '/' . $imagePath;
                        $fileExists = file_exists($fullPath);
                        
                        if ($fileExists) {
                            $imagesFound++;
                        } else {
                            $imagesMissing++;
                        }
                    } else {
                        $productsWithoutImages++;
                    }
                ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['title']) ?></td>
                    <td>
                        <?php if ($hasImage): ?>
                            <code><?= htmlspecialchars($imagePath) ?></code>
                        <?php else: ?>
                            <span class="status-warning">No image</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($hasImage): ?>
                            <?php if ($fileExists): ?>
                                <span class="status-ok">✓ Found</span>
                            <?php else: ?>
                                <span class="status-error">✗ Missing</span>
                                <br><small>Looking for: <?= htmlspecialchars($fullPath) ?></small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span>N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($hasImage && $fileExists): ?>
                            <img src="<?= htmlspecialchars($imagePath) ?>" class="image-preview" alt="Preview">
                        <?php elseif ($hasImage): ?>
                            <span class="status-error">✗</span>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($hasImage): ?>
                            <a href="<?= htmlspecialchars($imagePath) ?>" target="_blank">
                                Test Link ↗
                            </a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="summary">
        <h2>📈 Statistics</h2>
        <ul>
            <li>Products with images: <strong><?= $productsWithImages ?></strong></li>
            <li>Products without images: <strong><?= $productsWithoutImages ?></strong></li>
            <li>Images found on server: <span class="status-ok"><?= $imagesFound ?></span></li>
            <li>Images missing: <span class="status-error"><?= $imagesMissing ?></span></li>
        </ul>
    </div>
    
    <?php if (!empty($filesInUploads)): ?>
    <div class="summary">
        <h2>📁 Files in uploads/ Directory (<?= count($filesInUploads) ?>)</h2>
        <ul>
            <?php foreach (array_slice($filesInUploads, 0, 20) as $file): ?>
                <?php $filename = basename($file); ?>
                <li>
                    <code><?= htmlspecialchars($filename) ?></code> 
                    (<?= number_format(filesize($file) / 1024, 2) ?> KB)
                    <a href="uploads/<?= htmlspecialchars($filename) ?>" target="_blank">View ↗</a>
                </li>
            <?php endforeach; ?>
            <?php if (count($filesInUploads) > 20): ?>
                <li><em>... and <?= count($filesInUploads) - 20 ?> more</em></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <div class="info-box">
        <h3>🔧 Next Steps</h3>
        <ol>
            <li>If images are missing → Upload images through admin panel</li>
            <li>If uploads directory not writable → Check server permissions</li>
            <li>If image paths wrong → Uploads folder moved? Check paths in database</li>
            <li>Test upload a new product image to verify the uploads folder works</li>
        </ol>
    </div>
    
    <p><a href="dashboard.php">← Back to Dashboard</a> | <a href="all_products.php">View All Products</a></p>
</div>
</body>
</html>
