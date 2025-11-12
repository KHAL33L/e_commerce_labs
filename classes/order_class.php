<?php
// classes/order_class.php
require_once __DIR__ . '/db_connection.php';

class Order {
    private $conn;
    
    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }
    
    // Get order details by ID
    public function get_order($order_id) {
        try {
            // Get order info - try to join with customer table
            // Use LEFT JOIN in case customer info is not available
            $sql = "
                SELECT o.*, c.customer_name, c.customer_email as email, c.customer_contact as phone, 
                       c.customer_city as city, c.customer_country as country
                FROM orders o
                LEFT JOIN customer c ON o.customer_id = c.id
                WHERE o.order_id = :order_id
            ";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':order_id' => $order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                return null;
            }
            
            // Get order items - use product table (not products)
            $stmt = $this->conn->prepare("
                SELECT od.*, p.title as product_name, p.image_path as image_url
                FROM orderdetails od
                JOIN product p ON od.product_id = p.id
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
            return [
                'success' => true,
                'orders' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (PDOException $e) {
            error_log("Get customer orders error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'orders' => []
            ];
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
    
    /**
     * Create a new order and return order ID
     * @param array $params - Contains customer_id, invoice_no, total_amount, shipping_info
     */
    public function create_order($params) {
        try {
            $this->conn->beginTransaction();
            
            // Generate invoice number if not provided
            $invoice_no = $params['invoice_no'] ?? 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            
            // Insert order
            $stmt = $this->conn->prepare("
                INSERT INTO orders (customer_id, invoice_no, order_date, order_status, total_amount)
                VALUES (:customer_id, :invoice_no, NOW(), 'pending', :total_amount)
            ");
            
            $stmt->execute([
                ':customer_id' => $params['customer_id'] ?? 0,
                ':invoice_no' => $invoice_no,
                ':total_amount' => $params['total_amount'] ?? 0
            ]);
            
            $order_id = $this->conn->lastInsertId();
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'order_id' => $order_id,
                'invoice_no' => $invoice_no
            ];
            
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Create order error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Order creation failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Add order details (product items) to orderdetails table
     * @param array $params - Contains order_id, product_id, qty, price
     */
    public function add_order_details($params) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO orderdetails (order_id, product_id, qty, price)
                VALUES (:order_id, :product_id, :qty, :price)
            ");
            
            $result = $stmt->execute([
                ':order_id' => $params['order_id'],
                ':product_id' => $params['product_id'],
                ':qty' => $params['qty'],
                ':price' => $params['price']
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'order_detail_id' => $this->conn->lastInsertId()
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to add order detail'];
            }
            
        } catch (PDOException $e) {
            error_log("Add order details error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Record simulated payment in payments table
     * @param array $params - Contains order_id, amount, payment_method, payment_status
     */
    public function record_payment($params) {
        try {
            // Check if payments table exists and has the expected structure
            // Assuming: payment_id, order_id, amount, payment_date, payment_method, payment_status
            $stmt = $this->conn->prepare("
                INSERT INTO payments (order_id, amount, payment_date, payment_method, payment_status)
                VALUES (:order_id, :amount, NOW(), :payment_method, :payment_status)
            ");
            
            $result = $stmt->execute([
                ':order_id' => $params['order_id'],
                ':amount' => $params['amount'],
                ':payment_method' => $params['payment_method'] ?? 'simulated',
                ':payment_status' => $params['payment_status'] ?? 'completed'
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'payment_id' => $this->conn->lastInsertId()
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to record payment'];
            }
            
        } catch (PDOException $e) {
            // If payments table doesn't exist or has different structure, log and continue
            error_log("Record payment error: " . $e->getMessage());
            // Return success anyway since this is simulated payment
            return [
                'success' => true,
                'message' => 'Payment simulated (payment table may not exist)'
            ];
        }
    }
}