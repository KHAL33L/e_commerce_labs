<?php
// classes/cart_class.php
require_once __DIR__ . '/db_connection.php';

class Cart extends DBConnection {
    protected $pdo;
    
    public function __construct() {
        parent::__construct();
        $this->pdo = $this->getPDO();
    }

    /**
     * Fetch product details from either the new product table or the legacy products table.
     */
    private function getProductDetails(int $product_id): ?array {
        // Try new product table first
        $stmt = $this->pdo->prepare("
            SELECT id AS product_id, title, price, image_path, description 
            FROM product 
            WHERE id = :id 
            LIMIT 1
        ");
        $stmt->execute([':id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            return [
                'product_id' => (int)$product['product_id'],
                'title' => $product['title'],
                'price' => (float)$product['price'],
                'image_path' => $product['image_path'],
                'description' => $product['description'] ?? ''
            ];
        }

        // Fallback to legacy products table
        $stmt = $this->pdo->prepare("
            SELECT product_id, product_title, product_price, product_image, product_desc 
            FROM products 
            WHERE product_id = :id 
            LIMIT 1
        ");
        $stmt->execute([':id' => $product_id]);
        $legacy = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($legacy) {
            return [
                'product_id' => (int)$legacy['product_id'],
                'title' => $legacy['product_title'],
                'price' => (float)$legacy['product_price'],
                'image_path' => $legacy['product_image'],
                'description' => $legacy['product_desc'] ?? ''
            ];
        }

        return null;
    }

    /**
     * Determine the appropriate WHERE clause and parameters for cart queries
     */
    private function getCartFilter(int $customer_id, string $ip_address, string $alias = 'c'): array {
        if ($customer_id > 0) {
            return [
                'clause' => "$alias.c_id = :cid",
                'params' => [':cid' => $customer_id]
            ];
        }

        return [
            'clause' => "$alias.c_id = 0 AND $alias.ip_add = :ip",
            'params' => [':ip' => $ip_address]
        ];
    }
    
    /**
     * Add a product to the cart
     * If product already exists, increment quantity instead of duplicating
     */
    public function add_to_cart($product_id, $customer_id, $ip_address, $quantity = 1) {
        try {
            // Check if product already exists in cart
            $existing = $this->check_product_in_cart($product_id, $customer_id, $ip_address);
            
            if ($existing) {
                // Update quantity
                $new_qty = $existing['qty'] + $quantity;
                return $this->update_quantity($existing['id'], $new_qty);
            } else {
                // Insert new cart item
                $stmt = $this->pdo->prepare("INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (:pid, :ip, :cid, :qty)");
                $result = $stmt->execute([
                    ':pid' => $product_id,
                    ':ip' => $ip_address,
                    ':cid' => $customer_id,
                    ':qty' => $quantity
                ]);
                
                if ($result) {
                    return [
                        'success' => true,
                        'message' => 'Product added to cart',
                        'cart_id' => $this->pdo->lastInsertId()
                    ];
                } else {
                    return ['success' => false, 'message' => 'Failed to add product to cart'];
                }
            }
        } catch (PDOException $e) {
            error_log("Add to cart error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update the quantity of a cart item
     */
    public function update_quantity($cart_id, $quantity) {
        try {
            if ($quantity <= 0) {
                return $this->remove_from_cart($cart_id);
            }
            
            $stmt = $this->pdo->prepare("UPDATE cart SET qty = :qty WHERE id = :id");
            $result = $stmt->execute([
                ':qty' => $quantity,
                ':id' => $cart_id
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Cart updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update cart'];
            }
        } catch (PDOException $e) {
            error_log("Update cart quantity error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Remove a product from the cart
     */
    public function remove_from_cart($cart_id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM cart WHERE id = :id");
            $result = $stmt->execute([':id' => $cart_id]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Item removed from cart'];
            } else {
                return ['success' => false, 'message' => 'Failed to remove item'];
            }
        } catch (PDOException $e) {
            error_log("Remove from cart error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get all cart items for a user (logged in or guest)
     */
    public function get_user_cart($customer_id, $ip_address) {
        try {
            $filter = $this->getCartFilter((int)$customer_id, $ip_address);

            $stmt = $this->pdo->prepare("
                SELECT c.id AS cart_id, c.p_id, c.qty
                FROM cart c
                WHERE {$filter['clause']}
                ORDER BY c.id DESC
            ");
            $stmt->execute($filter['params']);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format items for easier use and clean up orphaned records
            $formatted_items = [];
            foreach ($items as $item) {
                $product = $this->getProductDetails((int)$item['p_id']);

                if (!$product) {
                    // Product no longer exists; remove from cart
                    $this->remove_from_cart($item['cart_id']);
                    continue;
                }

                $formatted_items[] = [
                    'cart_id' => $item['cart_id'],
                    'product_id' => $product['product_id'],
                    'title' => $product['title'],
                    'price' => $product['price'],
                    'quantity' => (int)$item['qty'],
                    'image_path' => $product['image_path'] ?? 'assets/images/placeholder.jpg',
                    'description' => $product['description'],
                    'subtotal' => $product['price'] * (int)$item['qty']
                ];
            }
            
            return [
                'success' => true,
                'items' => $formatted_items,
                'count' => count($formatted_items)
            ];
        } catch (PDOException $e) {
            error_log("Get user cart error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'items' => []];
        }
    }
    
    /**
     * Empty the cart for a user
     */
    public function empty_cart($customer_id, $ip_address) {
        try {
            $filter = $this->getCartFilter((int)$customer_id, $ip_address, 'cart');
            $stmt = $this->pdo->prepare("DELETE FROM cart WHERE {$filter['clause']}");
            $result = $stmt->execute($filter['params']);
            
            if ($result) {
                return ['success' => true, 'message' => 'Cart emptied successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to empty cart'];
            }
        } catch (PDOException $e) {
            error_log("Empty cart error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Check if a product already exists in the cart
     */
    public function check_product_in_cart($product_id, $customer_id, $ip_address) {
        try {
            $filter = $this->getCartFilter((int)$customer_id, $ip_address);
            $stmt = $this->pdo->prepare("
                SELECT * FROM cart 
                WHERE p_id = :pid AND {$filter['clause']} 
                LIMIT 1
            ");
            $params = array_merge([':pid' => $product_id], $filter['params']);
            $stmt->execute($params);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Check product in cart error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get cart count (total quantity of items)
     */
    public function get_cart_count($customer_id, $ip_address) {
        try {
            $filter = $this->getCartFilter((int)$customer_id, $ip_address);
            $stmt = $this->pdo->prepare("
                SELECT SUM(qty) as total_qty
                FROM cart
                WHERE {$filter['clause']}
            ");
            $stmt->execute($filter['params']);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)($result['total_qty'] ?? 0);
        } catch (PDOException $e) {
            error_log("Get cart count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get cart total amount
     */
    public function get_cart_total($customer_id, $ip_address) {
        try {
            $cart_items = $this->get_user_cart($customer_id, $ip_address);
            if (!$cart_items['success']) {
                return 0;
            }

            $total = 0;
            foreach ($cart_items['items'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            return $total;
        } catch (PDOException $e) {
            error_log("Get cart total error: " . $e->getMessage());
            return 0;
        }
    }
}

