// js/cart.js
// Handle all UI interactions for the cart: adding, removing, updating, and emptying items

document.addEventListener('DOMContentLoaded', function() {
    // Update quantity handlers
    document.querySelectorAll('.update-quantity').forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.dataset.cartId;
            const action = this.dataset.action; // 'increase' or 'decrease'
            const quantityInput = document.querySelector(`input[data-cart-id="${cartId}"]`);
            
            if (!quantityInput) return;
            
            let currentQty = parseInt(quantityInput.value) || 1;
            
            if (action === 'increase') {
                currentQty += 1;
            } else if (action === 'decrease' && currentQty > 1) {
                currentQty -= 1;
            }
            
            updateCartQuantity(cartId, currentQty);
        });
    });
    
    // Quantity input change handlers
    document.querySelectorAll('.cart-quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.dataset.cartId;
            const quantity = parseInt(this.value) || 1;
            
            if (quantity < 1) {
                this.value = 1;
                return;
            }
            
            updateCartQuantity(cartId, quantity);
        });
    });
    
    // Remove item handlers
    document.querySelectorAll('.remove-cart-item').forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.dataset.cartId;
            const productName = this.dataset.productName || 'this item';
            
            if (confirm(`Are you sure you want to remove ${productName} from your cart?`)) {
                removeFromCart(cartId);
            }
        });
    });
    
    // Empty cart handler
    const emptyCartBtn = document.getElementById('emptyCartBtn');
    if (emptyCartBtn) {
        emptyCartBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to empty your cart? This action cannot be undone.')) {
                emptyCart();
            }
        });
    }
});

/**
 * Update cart item quantity
 */
function updateCartQuantity(cartId, quantity) {
    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);
    
    // Show loading state
    const quantityInput = document.querySelector(`input[data-cart-id="${cartId}"]`);
    const originalValue = quantityInput.value;
    quantityInput.disabled = true;
    
    fetch('actions/update_quantity_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        quantityInput.disabled = false;
        
        if (data.success) {
            // Update the quantity input
            quantityInput.value = quantity;
            
            // Reload cart to update totals
            location.reload();
        } else {
            // Revert to original value
            quantityInput.value = originalValue;
            showMessage(data.message || 'Failed to update quantity', 'error');
        }
    })
    .catch(error => {
        quantityInput.disabled = false;
        quantityInput.value = originalValue;
        console.error('Error:', error);
        showMessage('An error occurred while updating the cart', 'error');
    });
}

/**
 * Remove item from cart
 */
function removeFromCart(cartId) {
    const formData = new FormData();
    formData.append('cart_id', cartId);
    
    // Find the cart item row
    const cartItem = document.querySelector(`tr[data-cart-id="${cartId}"]`);
    if (cartItem) {
        cartItem.style.opacity = '0.5';
        cartItem.style.pointerEvents = 'none';
    }
    
    fetch('actions/remove_from_cart_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the item from DOM
            if (cartItem) {
                cartItem.style.transition = 'opacity 0.3s';
                cartItem.style.opacity = '0';
                setTimeout(() => {
                    cartItem.remove();
                    // Check if cart is now empty
                    const cartTable = document.querySelector('table tbody');
                    if (cartTable && cartTable.children.length === 0) {
                        location.reload();
                    } else {
                        location.reload();
                    }
                }, 300);
            } else {
                location.reload();
            }
            
            // Update cart count in header
            updateCartCount(data.cart_count || 0);
            
            showMessage(data.message || 'Item removed from cart', 'success');
        } else {
            if (cartItem) {
                cartItem.style.opacity = '1';
                cartItem.style.pointerEvents = 'auto';
            }
            showMessage(data.message || 'Failed to remove item', 'error');
        }
    })
    .catch(error => {
        if (cartItem) {
            cartItem.style.opacity = '1';
            cartItem.style.pointerEvents = 'auto';
        }
        console.error('Error:', error);
        showMessage('An error occurred while removing the item', 'error');
    });
}

/**
 * Empty the entire cart
 */
function emptyCart() {
    fetch('actions/empty_cart_action.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount(0);
            
            // Reload page to show empty cart message
            location.reload();
        } else {
            showMessage(data.message || 'Failed to empty cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while emptying the cart', 'error');
    });
}

/**
 * Update cart count in header
 */
function updateCartCount(count) {
    const cartCountEl = document.getElementById('cartCount');
    if (cartCountEl) {
        cartCountEl.textContent = count;
        
        // Hide/show cart badge
        if (count > 0) {
            cartCountEl.style.display = 'inline-block';
        } else {
            cartCountEl.style.display = 'none';
        }
    }
}

/**
 * Show message to user
 */
function showMessage(message, type = 'info') {
    // Remove existing messages
    const existingMsg = document.querySelector('.cart-message');
    if (existingMsg) {
        existingMsg.remove();
    }
    
    // Create message element
    const msgDiv = document.createElement('div');
    msgDiv.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show cart-message`;
    msgDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Insert at top of cart container
    const cartContainer = document.querySelector('.container.my-5, .container');
    if (cartContainer) {
        cartContainer.insertBefore(msgDiv, cartContainer.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (msgDiv.parentNode) {
                msgDiv.remove();
            }
        }, 5000);
    } else {
        // Fallback: use alert
        alert(message);
    }
}

