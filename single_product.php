<?php
// single_product.php
require_once __DIR__ . '/includes/header.php';

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: all_products.php');
    exit();
}

// $page_title = 'Page Title'; // Set the appropriate title
// require_once __DIR__ . '/includes/header.php'; 

$product_id = (int)$_GET['id'];

// Initialize product controller
require_once __DIR__ . '/controllers/product_controller.php';
$productController = new ProductController();

// Get product details
$result = $productController->get_single_product_ctr($product_id);

if (!$result['success'] || empty($result['data'])) {
    // Product not found, redirect to all products
    header('Location: all_products.php');
    exit();
}

$product = $result['data'];

// Get related products (products from the same category, excluding current product)
$related_products = [];
$related_result = $productController->filter_by_category_ctr($product['category_id'], 1, 4);
if (!empty($related_result['data'])) {
    $related_products = array_filter($related_result['data'], function($item) use ($product_id) {
        return $item['id'] != $product_id;
    });
    // Limit to 3 related products
    $related_products = array_slice($related_products, 0, 3);
}

?>

<div class="container my-5">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="all_products.php">Products</a></li>
            <li class="breadcrumb-item"><a href="all_products.php?category=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name'] ?? 'Category') ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['title']) ?></li>
        </ol>
    </nav>

    <!-- Product Details -->
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6">
            <div class="product-image-container mb-4">
                <?php if (!empty($product['image_path'])): ?>
                    <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                         class="img-fluid rounded" 
                         alt="<?= htmlspecialchars($product['title']) ?>"
                         id="mainProductImage">
                <?php else: ?>
                    <div class="text-center p-5 bg-light rounded" style="height: 400px; display: flex; align-items: center; justify-content: center;">
                        <span class="text-muted">No image available</span>
                    </div>
                <?php endif; ?>
                
                <!-- Thumbnails (if multiple images were available) -->
                <div class="row mt-3 g-2">
                    <!-- In a real implementation, you would loop through product images here -->
                    <div class="col-3">
                        <img src="<?= htmlspecialchars($product['image_path'] ?? '') ?>" 
                             class="img-thumbnail" 
                             style="cursor: pointer; max-height: 80px; object-fit: cover;"
                             onmouseover="document.getElementById('mainProductImage').src = this.src">
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-md-6">
            <h1 class="mb-3"><?= htmlspecialchars($product['title']) ?></h1>
            
            <!-- Price -->
                         <div class="mb-4">
                 <span class="h3 text-primary">₦<?= number_format($product['price'], 2) ?></span>
                 <?php if (isset($product['original_price']) && $product['original_price'] > $product['price']): ?>
                     <span class="text-muted text-decoration-line-through ms-2">₦<?= number_format($product['original_price'], 2) ?></span>
                    <span class="badge bg-danger ms-2">Save <?= number_format((($product['original_price'] - $product['price']) / $product['original_price']) * 100, 0) ?>%</span>
                <?php endif; ?>
            </div>
            
            <!-- Availability -->
            <div class="mb-4">
                <span class="text-success">
                    <i class="fas fa-check-circle"></i> In Stock
                </span>
                <small class="text-muted ms-2">Ships in 1-2 business days</small>
            </div>
            
            <!-- Add to Cart -->
            <div class="row mb-4">
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="input-group">
                        <button class="btn btn-outline-secondary" type="button" id="decrementQty">-</button>
                        <input type="number" class="form-control text-center" value="1" min="1" id="productQty">
                        <button class="btn btn-outline-secondary" type="button" id="incrementQty">+</button>
                    </div>
                </div>
                <div class="col-md-8">
                    <button class="btn w-100" id="addToCartBtn" style="background-color: #660a38; color: white; border: none;">
                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                    </button>
                </div>
            </div>
            
            <!-- Product Meta -->
            <div class="product-meta mb-4">
                <div><strong>SKU:</strong> <?= strtoupper(substr(md5($product['id']), 0, 8)) ?></div>
                <div><strong>Category:</strong> 
                    <a href="all_products.php?category=<?= $product['category_id'] ?>" class="text-decoration-none">
                        <?= htmlspecialchars($product['category_name'] ?? 'N/A') ?>
                    </a>
                </div>
                <div><strong>Brand:</strong> 
                    <a href="all_products.php?brand=<?= $product['brand_id'] ?>" class="text-decoration-none">
                        <?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?>
                    </a>
                </div>
                <?php if (!empty($product['keywords'])): ?>
                    <div class="mt-2">
                        <?php 
                        $keywords = explode(',', $product['keywords']);
                        foreach ($keywords as $keyword): 
                            $keyword = trim($keyword);
                            if (!empty($keyword)):
                        ?>
                            <span class="badge bg-light text-dark border me-1"><?= htmlspecialchars($keyword) ?></span>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Share Buttons -->
            <div class="share-buttons mb-4">
                <span class="me-2">Share:</span>
                <a href="#" class="text-muted me-2" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-muted me-2" title="Share on Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-muted me-2" title="Share on Pinterest"><i class="fab fa-pinterest"></i></a>
                <a href="#" class="text-muted" title="Share via Email"><i class="fas fa-envelope"></i></a>
            </div>
        </div>
    </div>
    
    <!-- Product Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                        Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab">
                        Specifications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                        Reviews (0)
                    </button>
                </li>
            </ul>
            
            <div class="tab-content p-3 border border-top-0 rounded-bottom" id="productTabsContent">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <?php if (!empty($product['description'])): ?>
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    <?php else: ?>
                        <p class="text-muted">No description available for this product.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Specifications Tab -->
                <div class="tab-pane fade" id="specs" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;">Category</th>
                                    <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Brand</th>
                                    <td><?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>SKU</th>
                                    <td><?= strtoupper(substr(md5($product['id']), 0, 8)) ?></td>
                                </tr>
                                <tr>
                                    <th>Weight</th>
                                    <td>1.5 lbs</td>
                                </tr>
                                <tr>
                                    <th>Dimensions</th>
                                    <td>10 × 5 × 2 in</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="mb-4">
                        <h5>Customer Reviews</h5>
                        <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            Write a Review
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">You May Also Like</h3>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($related_products as $related): ?>
                    <div class="col">
                        <div class="card h-100">
                            <a href="single_product.php?id=<?= $related['id'] ?>" class="text-decoration-none text-dark">
                                <?php if (!empty($related['image_path'])): ?>
                                    <img src="<?= htmlspecialchars($related['image_path']) ?>" 
                                         class="card-img-top" 
                                         alt="<?= htmlspecialchars($related['title']) ?>"
                                         style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="text-center p-5 bg-light" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                        <span class="text-muted">No image available</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($related['title']) ?></h5>
                                    <p class="card-text text-muted">
                                        <small>
                                            <?= htmlspecialchars($related['category_name'] ?? 'Uncategorized') ?>
                                        </small>
                                    </p>
                                                                         <p class="card-text font-weight-bold text-primary">₦<?= number_format($related['price'], 2) ?></p>
                                </div>
                            </a>
                            <div class="card-footer bg-transparent">
                                <button class="btn btn-sm btn-outline-primary add-to-cart" 
                                        data-product-id="<?= $related['id'] ?>">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reviewForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <div class="rating-stars">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>>
                                <label for="star<?= $i ?>" title="<?= $i ?> stars">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reviewTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="reviewTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="reviewText" class="form-label">Your Review</label>
                        <textarea class="form-control" id="reviewText" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="reviewerName" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="reviewerName" required>
                    </div>
                    <div class="mb-3">
                        <label for="reviewerEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="reviewerEmail" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add to Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Added to Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <p class="mb-1">"<?= htmlspecialchars($product['title']) ?>" has been added to your cart.</p>
                        <p class="mb-0 text-muted small">Quantity: <span id="cartQty">1</span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                <a href="cart.php" class="btn btn-primary">View Cart & Checkout</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- JavaScript for Product Page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity controls
    const decrementBtn = document.getElementById('decrementQty');
    const incrementBtn = document.getElementById('incrementQty');
    const quantityInput = document.getElementById('productQty');
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    if (decrementBtn) {
        decrementBtn.addEventListener('click', function() {
            let currentVal = parseInt(quantityInput.value);
            if (currentVal > 1) {
                quantityInput.value = currentVal - 1;
            }
        });
    }
    
    if (incrementBtn) {
        incrementBtn.addEventListener('click', function() {
            let currentVal = parseInt(quantityInput.value);
            quantityInput.value = currentVal + 1;
        });
    }
    
    // Add to cart functionality for single product page
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const productId = <?= $product_id ?>;
            const quantity = parseInt(quantityInput.value);
            
            // Disable button
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
            
            // Call global addToCart function
            addToCart(productId, quantity)
                .then(result => {
                    if (result.success) {
                        // Show the cart modal with correct quantity
                        document.getElementById('cartQty').textContent = quantity;
                        const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
                        cartModal.show();
                    } else {
                        alert('Failed to add to cart: ' + (result.message || 'Unknown error'));
                    }
                    
                    // Re-enable button
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
        });
    }
    
    // Handle review form submission
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would typically submit the review via AJAX
            alert('Thank you for your review! It will be published after moderation.');
            
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
            modal.hide();
            
            // Reset form
            reviewForm.reset();
        });
    }
});
</script>

<style>
/* Rating stars styling */
.rating-stars {
    direction: rtl;
    unicode-bidi: bidi-override;
    text-align: left;
}

.rating-stars input[type="radio"] {
    display: none;
}

.rating-stars label {
    font-size: 24px;
    color: #ddd;
    cursor: pointer;
    margin: 0 2px;
}

.rating-stars label:before {
    content: "★";
}

.rating-stars input[type="radio"]:checked ~ label {
    color: #ffd700;
}

.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #ffd700;
}

/* Product image hover effect */
.product-image-container img {
    transition: transform 0.3s ease;
}

.product-image-container:hover img {
    transform: scale(1.03);
}

/* Tab styling */
.nav-tabs .nav-link {
    color: #495057;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    font-weight: 600;
    border-bottom: 3px solid #0d6efd;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>