<?php
// controllers/cart_controller.php
require_once __DIR__ . '/../classes/cart_class.php';

class CartController {
    private $cart;
    
    public function __construct() {
        $this->cart = new Cart();
    }
    
    /**
     * Add product to cart
     */
    public function add_to_cart_ctr($product_id, $customer_id, $ip_address, $quantity = 1) {
        if ($product_id <= 0) {
            return ['success' => false, 'message' => 'Invalid product ID'];
        }
        
        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'Quantity must be greater than 0'];
        }
        
        return $this->cart->add_to_cart($product_id, $customer_id, $ip_address, $quantity);
    }
    
    /**
     * Update cart item quantity
     */
    public function update_cart_item_ctr($cart_id, $quantity) {
        if ($cart_id <= 0) {
            return ['success' => false, 'message' => 'Invalid cart ID'];
        }
        
        return $this->cart->update_quantity($cart_id, $quantity);
    }
    
    /**
     * Remove item from cart
     */
    public function remove_from_cart_ctr($cart_id) {
        if ($cart_id <= 0) {
            return ['success' => false, 'message' => 'Invalid cart ID'];
        }
        
        return $this->cart->remove_from_cart($cart_id);
    }
    
    /**
     * Get user's cart items
     */
    public function get_user_cart_ctr($customer_id, $ip_address) {
        return $this->cart->get_user_cart($customer_id, $ip_address);
    }
    
    /**
     * Empty user's cart
     */
    public function empty_cart_ctr($customer_id, $ip_address) {
        return $this->cart->empty_cart($customer_id, $ip_address);
    }
    
    /**
     * Get cart count
     */
    public function get_cart_count_ctr($customer_id, $ip_address) {
        return $this->cart->get_cart_count($customer_id, $ip_address);
    }
    
    /**
     * Get cart total
     */
    public function get_cart_total_ctr($customer_id, $ip_address) {
        return $this->cart->get_cart_total($customer_id, $ip_address);
    }
}

