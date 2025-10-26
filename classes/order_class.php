<?php
// classes/order_class.php
require_once __DIR__ . '/db_connection.php';

class Order {
    private $conn;
    
    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }
    
    // Create a new order
    public function create_order($customer_id, $cart_items, $shipping_info) {
        try {
            $this->conn->beginTransaction();
            
            // Generate invoice number (format: INV-YYYYMMDD-XXXX)
            $invoice_no = 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
            
            // Insert order
            $stmt = $this->conn->prepare("
                INSERT INTO orders (customer_id, invoice_no, order_date, order_status, total_amount)
                VALUES (:customer_id, :invoice_no, NOW(), 'pending', :total_amount)
            ");
            
            // Calculate total amount
            $total_amount = 0;
            foreach ($cart_items as $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }
            
            $stmt->execute([
                ':customer_id' => $customer_id,
                ':invoice_no' => $invoice_no,
                ':total_amount' => $total_amount
            ]);
            
            $order_id = $this->conn->lastInsertId();
            
            // Insert order details
            $stmt = $this->conn->prepare("
                INSERT INTO orderdetails (order_id, product_id, qty, price)
                VALUES (:order_id, :product_id, :qty, :price)
            ");
            
            foreach ($cart_items as $product_id => $item) {
                $stmt->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $product_id,
                    ':qty' => $item['quantity'],
                    ':price' => $item['price']
                ]);
            }
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'order_id' => $order_id,
                'invoice_no' => $invoice_no
            ];
            
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return [
                'success' => false,
                'message' => 'Order creation failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Get order details by ID
    public function get_order($order_id) {
        try {
            // Get order info
            $stmt = $this->conn->prepare("
                SELECT o.*, c.first_name, c.last_name, c.email, c.phone, 
                       c.address, c.city, c.state, c.zip_code, c.country
                FROM orders o
                JOIN customers c ON o.customer_id = c.customer_id
                WHERE o.order_id = :order_id
            ");
            
            $stmt->execute([':order_id' => $order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                return null;
            }
            
            // Get order items
            $stmt = $this->conn->prepare("
                SELECT od.*, p.product_name, p.image_url
                FROM orderdetails od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = :order_id
            ");
            
            $stmt->execute([':order_id' => $order_id]);
            $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $order;
            
        } catch (PDOException $e) {
            error_log("Error getting order: " . $e->getMessage());
            return null;
        }
    }
    
    // Get all orders for a customer
    public function get_customer_orders($customer_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT o.*, 
                       (SELECT COUNT(*) FROM orderdetails WHERE order_id = o.order_id) as item_count
                FROM orders o
                WHERE o.customer_id = :customer_id
                ORDER BY o.order_date DESC
            ");
            
            $stmt->execute([':customer_id' => $customer_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting customer orders: " . $e->getMessage());
            return [];
        }
    }
    
    // Update order status
    public function update_order_status($order_id, $status) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE orders 
                SET order_status = :status
                WHERE order_id = :order_id
            ");
            
            return $stmt->execute([
                ':order_id' => $order_id,
                ':status' => $status
            ]);
            
        } catch (PDOException $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }
}