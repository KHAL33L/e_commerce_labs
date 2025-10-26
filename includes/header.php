<?php
// includes/header.php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../settings/core.php';

// Get flash message if exists
$flash = get_flash_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?= $page_title ?? 'Sure Shop Nigeria - Online Shopping' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

    <!-- Top Bar -->
    <div class="bg-dark text-white py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                                                <small>
                                <i class="fas fa-phone-alt me-2"></i> +234 701 234 5678
                                <span class="mx-3">|</span>
                                <i class="fas fa-envelope me-2"></i> support@sureshop.ng
                            </small>
                </div>
                <div class="col-md-6 text-end">
                    <?php if (is_logged_in()): ?>
                        <div class="dropdown d-inline-block me-3">
                            <a class="text-white text-decoration-none dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['customer_name'] ?? 'My Account' ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <?php if (is_admin()): ?>
                                <li><a class="dropdown-item" href="admin/category.php"><i class="fas fa-folder me-2"></i>Categories</a></li>
                                <li><a class="dropdown-item" href="admin/brand.php"><i class="fas fa-tags me-2"></i>Brands</a></li>
                                <li><a class="dropdown-item" href="admin/product.php"><i class="fas fa-box me-2"></i>Products</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="actions/logout_action.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login/login.php" class="text-white text-decoration-none me-3"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                        <a href="login/register.php" class="text-white text-decoration-none"><i class="fas fa-user-plus me-1"></i> Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand fw-bold" href="index.php">
                <span class="fw-bold" style="color: #660a38;">Sure Shop</span>
            </a>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Main Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_products.php">All Products</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu" id="categoriesMenu" aria-labelledby="categoriesDropdown">
                            <!-- Categories will be loaded via JavaScript -->
                            <li class="text-center py-2">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
                
                <!-- Search Form -->
                <form class="d-flex me-3" action="product_search_result.php" method="get">
                    <div class="input-group">
                        <input class="form-control" type="search" name="q" placeholder="Search products..." aria-label="Search" id="mainSearchInput">
                        <button class="btn" type="submit" style="background-color: #660a38; color: white; border: none;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Cart and Wishlist -->
                <div class="d-flex">
                    <div class="dropdown me-3">
                        <a href="#" class="text-dark position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-heart fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="wishlistCount">
                                0
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 300px;">
                            <h6 class="dropdown-header">Wishlist</h6>
                            <div id="wishlistItems">
                                <p class="text-muted small mb-0">Your wishlist is empty</p>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="wishlist.php" class="dropdown-item text-center">View Wishlist</a>
                        </div>
                    </div>
                    
                    <div class="dropdown">
                        <a href="#" class="text-dark position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" id="cartCount" style="background-color: #660a38;">
                                0
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 320px;">
                            <h6 class="dropdown-header">Shopping Cart</h6>
                            <div id="cartItems">
                                <p class="text-muted small mb-0">Your cart is empty</p>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">Subtotal:</span>
                                                                        <span class="fw-bold" id="cartSubtotal">₦0.00</span>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="cart.php" class="btn btn-sm" style="background-color: #660a38; color: white; border: none;">View Cart</a>
                                <a href="checkout.php" class="btn btn-sm" style="border: 1px solid #660a38; color: #660a38; background: transparent;">Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="py-4">
        <div class="container">
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['flash_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php 
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']);
                ?>
            <?php endif; ?>

<!-- Global Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add to Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Product added to cart successfully!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                <a href="cart.php" class="btn" style="background-color: #660a38; color: white; border: none;">View Cart</a>
            </div>
        </div>
    </div>
</div>