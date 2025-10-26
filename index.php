<?php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/settings/core.php';

// Set page title
$page_title = 'Sure Shop - Welcome';
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="py-5">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 mb-4" style="color: #660a38;">Welcome to Sure Shop</h1>
                                <p class="lead mb-5">Nigeria's premier online marketplace for quality products</p>

                    <div class="row g-4 mt-5">
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-shipping-fast fa-3x mb-3" style="color: #660a38;"></i>
                                    <h5 class="card-title">Fast Delivery</h5>
                                    <p class="card-text text-muted">Quick and reliable shipping across Nigeria</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-shield-alt fa-3x mb-3" style="color: #660a38;"></i>
                            <h5 class="card-title">Secure Shopping</h5>
                            <p class="card-text text-muted">Your data is safe with our secure payment system</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-headset fa-3x mb-3" style="color: #660a38;"></i>
                            <h5 class="card-title">24/7 Support</h5>
                            <p class="card-text text-muted">Our team is always here to help you</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <a href="all_products.php" class="btn btn-lg me-3" style="background-color: #660a38; color: white; border: none;">
                    <i class="fas fa-shopping-bag me-2"></i>Browse Products
                </a>
                <?php if (!is_logged_in()): ?>
                    <a href="login/login.php" class="btn btn-lg" style="border: 2px solid #660a38; color: #660a38; background: transparent;">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Your Account
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>