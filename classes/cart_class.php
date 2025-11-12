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
            $stmt = $this->pdo->prepare("
                SELECT c.id as cart_id, c.p_id, c.qty, 
                       p.id as product_id, p.title, p.price, p.image_path, p.description
                FROM cart c
                JOIN product p ON c.p_id = p.id
                WHERE c.c_id = :cid AND c.ip_add = :ip
                ORDER BY c.id DESC
            ");
            
            $stmt->execute([
                ':cid' => $customer_id,
                ':ip' => $ip_address
            ]);
            
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format items for easier use
            $formatted_items = [];
            foreach ($items as $item) {
                $formatted_items[] = [
                    'cart_id' => $item['cart_id'],
                    'product_id' => $item['product_id'],
                    'title' => $item['title'],
                    'price' => (float)$item['price'],
                    'quantity' => (int)$item['qty'],
                    'image_path' => $item['image_path'] ?? 'assets/images/placeholder.jpg',
                    'subtotal' => (float)$item['price'] * (int)$item['qty']
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
            $stmt = $this->pdo->prepare("DELETE FROM cart WHERE c_id = :cid AND ip_add = :ip");
            $result = $stmt->execute([
                ':cid' => $customer_id,
                ':ip' => $ip_address
            ]);
            
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
            $stmt = $this->pdo->prepare("SELECT * FROM cart WHERE p_id = :pid AND c_id = :cid AND ip_add = :ip LIMIT 1");
            $stmt->execute([
                ':pid' => $product_id,
                ':cid' => $customer_id,
                ':ip' => $ip_address
            ]);
            
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
            $stmt = $this->pdo->prepare("SELECT SUM(qty) as total_qty FROM cart WHERE c_id = :cid AND ip_add = :ip");
            $stmt->execute([
                ':cid' => $customer_id,
                ':ip' => $ip_address
            ]);
            
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
            $stmt = $this->pdo->prepare("
                SELECT SUM(p.price * c.qty) as total
                FROM cart c
                JOIN product p ON c.p_id = p.id
                WHERE c.c_id = :cid AND c.ip_add = :ip
            ");
            
            $stmt->execute([
                ':cid' => $customer_id,
                ':ip' => $ip_address
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Get cart total error: " . $e->getMessage());
            return 0;
        }
    }
}

