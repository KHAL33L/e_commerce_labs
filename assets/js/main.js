// main.js - Global cart functionality

// Function to add product to cart
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    return fetch('actions/add_to_cart_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in header
            if (data.cart_count !== undefined) {
                const cartCountEl = document.getElementById('cartCount');
                if (cartCountEl) {
                    cartCountEl.textContent = data.cart_count;
                }
            }
            return { success: true, data: data };
        } else {
            console.error('Cart error:', data.message);
            return { success: false, message: data.message };
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        return { success: false, message: 'Network error occurred' };
    });
}

// Initialize cart functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Handle all "Add to Cart" buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            
            if (!productId) {
                alert('Invalid product ID');
                return;
            }

            // Disable button during request
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

            addToCart(productId, 1)
                .then(result => {
                    if (result.success) {
                        // Show success message instead of modal
                        alert('Product added to cart successfully!');
                    } else {
                        alert('Failed to add to cart: ' + (result.message || 'Unknown error'));
                    }
                    
                    // Re-enable button
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
        });
    });

    // Back to top button
    const backToTopButton = document.getElementById('backToTop');
    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.style.display = 'block';
            } else {
                backToTopButton.style.display = 'none';
            }
        });

        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Load categories for dropdown
    fetch('actions/product_actions.php?action=get_categories')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const categoriesMenu = document.getElementById('categoriesMenu');
                if (categoriesMenu) {
                    categoriesMenu.innerHTML = '';

                    data.data.forEach(category => {
                        const li = document.createElement('li');
                        li.innerHTML = `<a class="dropdown-item" href="all_products.php?category=${category.id}">${category.category_name}</a>`;
                        categoriesMenu.appendChild(li);
                    });
                }
            }
        })
        .catch(error => console.error('Error loading categories:', error));
});
