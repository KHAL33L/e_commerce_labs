<?php

?>
        </div><!-- End of container -->
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row">
                                        <div class="col-md-4 mb-4">
                            <h5>About Us</h5>
                            <p>Nigeria's trusted online marketplace. We offer high-quality products with fast nationwide delivery and excellent customer service.</p>
                    <div class="social-links mt-3">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-pinterest"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-md-2 mb-4">
                    <h5>Shop</h5>
                    <ul class="list-unstyled">
                        <li><a href="all_products.php" class="text-white-50 text-decoration-none">All Products</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">New Arrivals</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Featured</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Deals & Promotions</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Gift Cards</a></li>
                    </ul>
                </div>
                
                <div class="col-md-2 mb-4">
                    <h5>Customer Service</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">Contact Us</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">FAQs</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Shipping Info</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Returns & Exchanges</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Privacy Policy</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Terms & Conditions</a></li>
                    </ul>
                </div>
                
                                        <div class="col-md-4 mb-4">
                            <h5>Newsletter</h5>
                            <p>Stay updated with the latest deals and promotions from Nigeria's premier online store.</p>
                    <form id="newsletterForm" class="mb-3">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your email address" required>
                            <button class="btn" type="submit" style="background-color: #660a38; color: white; border: none;">Subscribe</button>
                        </div>
                    </form>
                    <div class="payment-methods mt-3">
                        <p class="mb-2">We Accept:</p>
                        <div class="text-muted small">
                            <i class="fab fa-cc-visa me-2"></i>
                            <i class="fab fa-cc-mastercard me-2"></i>
                            <i class="fab fa-cc-paypal me-2"></i>
                            <i class="fab fa-cc-discover"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 bg-secondary">
            
            <div class="row">
                                        <div class="col-md-6 text-center text-md-start">
                            <p class="mb-0">&copy; <?= date('Y') ?> Sure Shop Nigeria. All rights reserved.</p>
                        </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-white-50 text-decoration-none me-3">Sitemap</a>
                    <a href="#" class="text-white-50 text-decoration-none">Accessibility</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button type="button" class="btn btn-primary btn-floating btn-lg rounded-circle" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (required for AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
    
    <!-- Initialize tooltips -->
    <script>
        // Enable tooltips everywhere
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Back to top button
        const backToTopButton = document.getElementById('backToTop');
        
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
        
        // Load categories for dropdown
        document.addEventListener('DOMContentLoaded', function() {
            fetch('actions/product_actions.php?action=get_categories')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const categoriesMenu = document.getElementById('categoriesMenu');
                        categoriesMenu.innerHTML = ''; // Clear loading spinner
                        
                        data.data.forEach(category => {
                            const li = document.createElement('li');
                            li.innerHTML = `<a class="dropdown-item" href="all_products.php?category=${category.id}">${category.category_name}</a>`;
                            categoriesMenu.appendChild(li);
                        });
                    }
                })
                .catch(error => console.error('Error loading categories:', error));
                
            // Update cart count
            updateCartCount();
        });
        
        function updateCartCount() {
            // This would be replaced with actual cart count from your session/cookie
            // For now, we'll set it to 0
            document.getElementById('cartCount').textContent = '0';
        }
    </script>
</body>
</html>