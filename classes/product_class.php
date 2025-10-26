<?php
// classes/product_class.php
require_once __DIR__ . '/db_connection.php';

class Product extends DBConnection {
    protected $pdo;
    public function __construct(){
        parent::__construct();
        $this->pdo = $this->getPDO();
    }

    public function add(array $data): int|false {
        try {
            $sql = "INSERT INTO product (title, description, price, category_id, brand_id, user_id, image_path, keywords)
                    VALUES (:title, :desc, :price, :cat, :brand, :uid, :img, :kw)";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':title'=>$data['title'],
                ':desc'=>$data['description'] ?? null,
                ':price'=>$data['price'] ?? 0,
                ':cat'=>$data['category_id'],
                ':brand'=>$data['brand_id'],
                ':uid'=>$data['user_id'],
                ':img'=>$data['image_path'] ?? null,
                ':kw'=>$data['keywords'] ?? null
            ]);
            if ($ok) return (int)$this->pdo->lastInsertId();
            return false;
        } catch (PDOException $e) {
            error_log("Product add error: " . $e->getMessage());
            return false;
        }
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE product SET title=:title, description=:desc, price=:price, category_id=:cat, brand_id=:brand, image_path=:img, keywords=:kw WHERE id=:id AND user_id=:uid";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':title'=>$data['title'],
            ':desc'=>$data['description'] ?? null,
            ':price'=>$data['price'] ?? 0,
            ':cat'=>$data['category_id'],
            ':brand'=>$data['brand_id'],
            ':img'=>$data['image_path'] ?? null,
            ':kw'=>$data['keywords'] ?? null,
            ':id'=>$id,
            ':uid'=>$data['user_id']
        ]);
    }

    public function delete(int $id, int $user_id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM product WHERE id = :id AND user_id = :uid");
        return $stmt->execute([':id' => $id, ':uid' => $user_id]);
    }

    public function getByUser(int $user_id): array {
        $stmt = $this->pdo->prepare("SELECT p.*, c.category_name, b.brand_name FROM product p 
                                     LEFT JOIN category c ON c.id = p.category_id
                                     LEFT JOIN brand b ON b.id = p.brand_id
                                     WHERE p.user_id = :uid ORDER BY p.created_at DESC");
        $stmt->execute([':uid'=>$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id, int $user_id = 0) {
        $sql = "SELECT p.*, c.category_name, b.brand_name 
                FROM product p 
                LEFT JOIN category c ON c.id = p.category_id 
                LEFT JOIN brand b ON b.id = p.brand_id 
                WHERE p.id = :id";
        
        $params = [':id' => $id];
        
        if ($user_id > 0) {
            $sql .= " AND p.user_id = :uid";
            $params[':uid'] = $user_id;
        }
        
        $sql .= " LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all products with optional pagination
     */
    public function view_all_products(int $limit = 10, int $offset = 0): array {
        $sql = "SELECT p.*, c.category_name, b.brand_name 
                FROM product p 
                LEFT JOIN category c ON c.id = p.category_id 
                LEFT JOIN brand b ON b.id = p.brand_id 
                ORDER BY p.created_at DESC 
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Search products by title, keywords, category, or brand
     */
    public function search_products(string $query, int $limit = 10, int $offset = 0): array {
        $search = "%$query%";
        $sql = "SELECT p.*, c.category_name, b.brand_name 
                FROM product p 
                LEFT JOIN category c ON c.id = p.category_id 
                LEFT JOIN brand b ON b.id = p.brand_id 
                WHERE p.title LIKE :query1 
                OR p.keywords LIKE :query2
                OR c.category_name LIKE :query3
                OR b.brand_name LIKE :query4
                ORDER BY p.created_at DESC 
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':query1', $search, PDO::PARAM_STR);
        $stmt->bindValue(':query2', $search, PDO::PARAM_STR);
        $stmt->bindValue(':query3', $search, PDO::PARAM_STR);
        $stmt->bindValue(':query4', $search, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Filter products by category
     */
    public function filter_products_by_category(int $cat_id, int $limit = 10, int $offset = 0): array {
        $sql = "SELECT p.*, c.category_name, b.brand_name 
                FROM product p 
                LEFT JOIN category c ON c.id = p.category_id 
                LEFT JOIN brand b ON b.id = p.brand_id 
                WHERE p.category_id = :cat_id 
                ORDER BY p.created_at DESC 
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Filter products by brand
     */
    public function filter_products_by_brand(int $brand_id, int $limit = 10, int $offset = 0): array {
        $sql = "SELECT p.*, c.category_name, b.brand_name 
                FROM product p 
                LEFT JOIN category c ON c.id = p.category_id 
                LEFT JOIN brand b ON b.id = p.brand_id 
                WHERE p.brand_id = :brand_id 
                ORDER BY p.created_at DESC 
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':brand_id', $brand_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a single product by ID (public version without user_id check)
     */
    public function view_single_product(int $id) {
        return $this->getById($id);
    }
    
    /**
     * Count total products for pagination
     */
    public function count_products(string $type = 'all', $filter_value = null): int {
        $sql = "SELECT COUNT(*) as total FROM product p";
        
        switch ($type) {
            case 'search':
                $sql .= " LEFT JOIN category c ON c.id = p.category_id 
                          LEFT JOIN brand b ON b.id = p.brand_id 
                          WHERE p.title LIKE :query1 
                          OR p.keywords LIKE :query2
                          OR c.category_name LIKE :query3
                          OR b.brand_name LIKE :query4";
                $params = [
                    ':query1' => "%$filter_value%",
                    ':query2' => "%$filter_value%",
                    ':query3' => "%$filter_value%",
                    ':query4' => "%$filter_value%"
                ];
                break;
            case 'category':
                $sql .= " WHERE p.category_id = :cat_id";
                $params = [':cat_id' => $filter_value];
                break;
            case 'brand':
                $sql .= " WHERE p.brand_id = :brand_id";
                $params = [':brand_id' => $filter_value];
                break;
            default:
                $params = [];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }
}

