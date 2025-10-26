<?php
// controllers/product_controller.php
require_once __DIR__ . '/../classes/product_class.php';

class ProductController {
    private $model;
    private $items_per_page = 10;
    
    public function __construct() {
        $this->model = new Product();
    }

    public function add_product_ctr(array $kwargs) {
        try {
            // basic validation
            $title = trim($kwargs['title'] ?? '');
            $cat = (int)($kwargs['category_id'] ?? 0);
            $brand = (int)($kwargs['brand_id'] ?? 0);
            $uid = (int)($kwargs['user_id'] ?? 0);
            $price = (float)($kwargs['price'] ?? 0);
            
            if ($title==='' || $cat<=0 || $brand<=0 || $uid<=0) {
                return ['success'=>false, 'message'=>'Missing required fields'];
            }
            
            $productId = $this->model->add([
                'title' => $title,
                'description' => $kwargs['description'] ?? null,
                'price' => $price,
                'category_id' => $cat,
                'brand_id' => $brand,
                'user_id' => $uid,
                'image_path' => $kwargs['image_path'] ?? null,
                'keywords' => $kwargs['keywords'] ?? null
            ]);
            
            if ($productId === false || $productId === 0) {
                return ['success' => false, 'message' => 'Failed to add product'];
            }
            
            return ['success' => true, 'message' => 'Product added successfully', 'product_id' => $productId];
        } catch (Exception $e) {
            error_log("Add product error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function update_product_ctr(int $id, array $kwargs) {
        $uid = (int)($kwargs['user_id'] ?? 0);
        if ($id <= 0 || $uid <= 0) {
            return ['success' => false, 'message' => 'Invalid input'];
        }
        
        $ok = $this->model->update($id, $kwargs);
        return $ok 
            ? ['success' => true, 'message' => 'Product updated'] 
            : ['success' => false, 'message' => 'Update failed'];
    }

    public function delete_product_ctr(int $id, int $user_id) {
        if ($id <= 0 || $user_id <= 0) {
            return ['success' => false, 'message' => 'Invalid input'];
        }
        
        $ok = $this->model->delete($id, $user_id);
        return $ok 
            ? ['success' => true, 'message' => 'Product deleted'] 
            : ['success' => false, 'message' => 'Delete failed'];
    }

    public function fetch_user_products_ctr(int $user_id) {
        return $this->model->getByUser($user_id);
    }

    public function get_product_ctr(int $id, int $user_id = 0) {
        return $this->model->getById($id, $user_id);
    }
    
    // New methods for product display and search
    
    /**
     * Get all products with pagination
     */
    public function get_all_products_ctr(int $page = 1, int $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        $products = $this->model->view_all_products($per_page, $offset);
        $total = $this->model->count_products();
        
        return [
            'success' => true,
            'data' => $products,
            'pagination' => [
                'total_items' => $total,
                'current_page' => $page,
                'items_per_page' => $per_page,
                'total_pages' => ceil($total / $per_page)
            ]
        ];
    }
    
    /**
     * Search products by query string
     */
    public function search_products_ctr(string $query, int $page = 1, int $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        $products = $this->model->search_products($query, $per_page, $offset);
        $total = $this->model->count_products('search', $query);
        
        return [
            'success' => true,
            'query' => $query,
            'data' => $products,
            'pagination' => [
                'total_items' => $total,
                'current_page' => $page,
                'items_per_page' => $per_page,
                'total_pages' => ceil($total / $per_page)
            ]
        ];
    }
    
    /**
     * Filter products by category
     */
    public function filter_by_category_ctr(int $category_id, int $page = 1, int $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        $products = $this->model->filter_products_by_category($category_id, $per_page, $offset);
        $total = $this->model->count_products('category', $category_id);
        
        return [
            'success' => true,
            'filter' => ['type' => 'category', 'id' => $category_id],
            'data' => $products,
            'pagination' => [
                'total_items' => $total,
                'current_page' => $page,
                'items_per_page' => $per_page,
                'total_pages' => ceil($total / $per_page)
            ]
        ];
    }
    
    /**
     * Filter products by brand
     */
    public function filter_by_brand_ctr(int $brand_id, int $page = 1, int $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        $products = $this->model->filter_products_by_brand($brand_id, $per_page, $offset);
        $total = $this->model->count_products('brand', $brand_id);
        
        return [
            'success' => true,
            'filter' => ['type' => 'brand', 'id' => $brand_id],
            'data' => $products,
            'pagination' => [
                'total_items' => $total,
                'current_page' => $page,
                'items_per_page' => $per_page,
                'total_pages' => ceil($total / $per_page)
            ]
        ];
    }
    
    /**
     * Get a single product by ID (public)
     */
    public function get_single_product_ctr(int $id) {
        $product = $this->model->view_single_product($id);
        
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        
        return [
            'success' => true,
            'data' => $product
        ];
    }
    
    /**
     * Get all categories for filtering
     */
    public function get_all_categories_ctr() {
        $sql = "SELECT id, category_name FROM category ORDER BY category_name";
        $stmt = $this->model->getPDO()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all brands for filtering
     */
    public function get_all_brands_ctr() {
        $sql = "SELECT id, brand_name FROM brand ORDER BY brand_name";
        $stmt = $this->model->getPDO()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Count products for a given type
     */
    public function count_products(string $type = 'all', $filter_value = null) {
        return $this->model->count_products($type, $filter_value);
    }
}
