<?php
// controllers/order_controller.php
require_once __DIR__ . '/../classes/order_class.php';

class OrderController {
    private $order;
    
    public function __construct() {
        $this->order = new Order();
    }
    
    /**
     * Create a new order
     */
    public function create_order_ctr($params) {
        // Validate required fields
        if (empty($params['total_amount']) || $params['total_amount'] <= 0) {
            return ['success' => false, 'message' => 'Invalid total amount'];
        }
        
        return $this->order->create_order($params);
    }
    
    /**
     * Add order details (product items)
     */
    public function add_order_details_ctr($params) {
        // Validate required fields
        if (empty($params['order_id']) || empty($params['product_id']) || 
            empty($params['qty']) || empty($params['price'])) {
            return ['success' => false, 'message' => 'Missing required fields'];
        }
        
        return $this->order->add_order_details($params);
    }
    
    /**
     * Record simulated payment
     */
    public function record_payment_ctr($params) {
        // Validate required fields
        if (empty($params['order_id']) || empty($params['amount'])) {
            return ['success' => false, 'message' => 'Missing required fields'];
        }
        
        return $this->order->record_payment($params);
    }
    
    /**
     * Get customer orders
     */
    public function get_customer_orders_ctr($customer_id) {
        if ($customer_id <= 0) {
            return ['success' => false, 'message' => 'Invalid customer ID', 'orders' => []];
        }
        
        return $this->order->get_customer_orders($customer_id);
    }
    
    /**
     * Get order by ID
     */
    public function get_order_ctr($order_id) {
        if ($order_id <= 0) {
            return ['success' => false, 'message' => 'Invalid order ID'];
        }
        
        $order = $this->order->get_order($order_id);
        
        if ($order) {
            return ['success' => true, 'data' => $order];
        } else {
            return ['success' => false, 'message' => 'Order not found'];
        }
    }
    
    /**
     * Update order status
     */
    public function update_order_status_ctr($order_id, $status) {
        if ($order_id <= 0 || empty($status)) {
            return ['success' => false, 'message' => 'Invalid input'];
        }
        
        $result = $this->order->update_order_status($order_id, $status);
        
        if ($result) {
            return ['success' => true, 'message' => 'Order status updated'];
        } else {
            return ['success' => false, 'message' => 'Failed to update order status'];
        }
    }
}

