<?php
// actions/product_actions.php
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../includes/session.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$productController = new ProductController();
$action = $_GET['action'] ?? '';
$response = [];

// Set default content type to JSON
header('Content-Type: application/json');

try {
    switch ($action) {
        case 'get_all_products':
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
            $response = $productController->get_all_products_ctr($page, $per_page);
            break;
            
        case 'search_products':
            $query = $_GET['q'] ?? '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
            
            if (empty($query)) {
                $response = [
                    'success' => false,
                    'message' => 'Search query is required'
                ];
            } else {
                $response = $productController->search_products_ctr($query, $page, $per_page);
            }
            break;
            
        case 'filter_by_category':
            $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
            
            if ($category_id <= 0) {
                $response = [
                    'success' => false,
                    'message' => 'Invalid category ID'
                ];
            } else {
                $response = $productController->filter_by_category_ctr($category_id, $page, $per_page);
            }
            break;
            
        case 'filter_by_brand':
            $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
            
            if ($brand_id <= 0) {
                $response = [
                    'success' => false,
                    'message' => 'Invalid brand ID'
                ];
            } else {
                $response = $productController->filter_by_brand_ctr($brand_id, $page, $per_page);
            }
            break;
            
        case 'get_product':
            $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($product_id <= 0) {
                $response = [
                    'success' => false,
                    'message' => 'Invalid product ID'
                ];
            } else {
                $response = $productController->get_single_product_ctr($product_id);
            }
            break;
            
        case 'get_categories':
            $response = [
                'success' => true,
                'data' => $productController->get_all_categories_ctr()
            ];
            break;
            
        case 'get_brands':
            $response = [
                'success' => true,
                'data' => $productController->get_all_brands_ctr()
            ];
            break;
            
        default:
            $response = [
                'success' => false,
                'message' => 'Invalid action'
            ];
            http_response_code(400);
            break;
    }
} catch (Exception $e) {
    error_log('Product action error: ' . $e->getMessage());
    $response = [
        'success' => false,
        'message' => 'An error occurred while processing your request.'
    ];
    http_response_code(500);
}

// Output the JSON response
echo json_encode($response);