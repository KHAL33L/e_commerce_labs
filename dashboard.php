<?php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/settings/core.php';

// Redirect to login if not logged in
if (!is_logged_in()) {
    header('Location: login/login.php');
    exit;
}

// Set page title
$page_title = 'Dashboard - Sure Shop';

$name  = $_SESSION['customer_name'] ?? 'User';
$email = $_SESSION['customer_email'] ?? '';
$is_admin = is_admin();
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3" style="color: #660a38;">Welcome, <?= htmlspecialchars($name) ?>!</h1>
                    <p class="text-muted mb-0">Your registered email is <strong><?= htmlspecialchars($email) ?></strong>.</p>
                </div>
            </div>

            <?php if ($is_admin): ?>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-folder fa-3x mb-3" style="color: #660a38;"></i>
                            <h5 class="card-title">Categories</h5>
                            <p class="card-text text-muted">Manage product categories</p>
                            <a href="admin/category.php" class="btn" style="background-color: #660a38; color: white; border: none;">Manage Categories</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-tags fa-3x mb-3" style="color: #660a38;"></i>
                            <h5 class="card-title">Brands</h5>
                            <p class="card-text text-muted">Manage product brands</p>
                            <a href="admin/brand.php" class="btn" style="background-color: #660a38; color: white; border: none;">Manage Brands</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-box fa-3x mb-3" style="color: #660a38;"></i>
                            <h5 class="card-title">Products</h5>
                            <p class="card-text text-muted">Manage product inventory</p>
                            <a href="admin/product.php" class="btn" style="background-color: #660a38; color: white; border: none;">Manage Products</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
                            <div class="card shadow-sm">
                    <div class="card-body p-5 text-center">
                        <i class="fas fa-shopping-bag fa-5x mb-4" style="color: #660a38;"></i>
                        <h3 class="mb-3">Start Shopping</h3>
                        <p class="text-muted mb-4">Browse our wide selection of products and find what you're looking for.</p>
                        <a href="all_products.php" class="btn btn-lg" style="background-color: #660a38; color: white; border: none;">
                            <i class="fas fa-arrow-right me-2"></i>Browse Products
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
