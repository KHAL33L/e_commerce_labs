<?php
// product_search_result.php
require_once __DIR__ . '/includes/header.php';

// Get search query
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$brand_id = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12; // Items per page

// Redirect if no search query
if (empty($search_query) && $category_id === 0 && $brand_id === 0) {
    header('Location: all_products.php');
    exit();
}

// Initialize product controller
require_once __DIR__ . '/controllers/product_controller.php';
$productController = new ProductController();

// Get filter data
$categories = $productController->get_all_categories_ctr();
$brands = $productController->get_all_brands_ctr();

// Fetch products based on search and filters
$result = [];
if (!empty($search_query)) {
    $result = $productController->search_products_ctr($search_query, $page, $per_page);
} elseif ($category_id > 0) {
    $result = $productController->filter_by_category_ctr($category_id, $page, $per_page);
} elseif ($brand_id > 0) {
    $result = $productController->filter_by_brand_ctr($brand_id, $page, $per_page);
}

$products = $result['data'] ?? [];
$pagination = $result['pagination'] ?? [];
$total_results = $pagination['total_items'] ?? 0;
?>

<div class="container my-5">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Search Results</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Refine Your Search</h5>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form id="searchForm" action="product_search_result.php" method="get" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Search products..." 
                                   value="<?= htmlspecialchars($search_query) ?>" required>
                            <button class="btn" type="submit" style="background-color: #660a38; color: white; border: none;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Category Filter -->
                    <div class="mb-4">
                        <h6>Categories</h6>
                        <div class="list-group">
                            <a href="?q=<?= urlencode($search_query) ?>&brand=<?= $brand_id ?>" 
                               class="list-group-item list-group-item-action <?= $category_id == 0 ? 'active' : '' ?>">
                                All Categories
                            </a>
                            <?php foreach ($categories as $category): ?>
                                <a href="?q=<?= urlencode($search_query) ?>&category=<?= $category['id'] ?><?= $brand_id ? "&brand=$brand_id" : '' ?>" 
                                   class="list-group-item list-group-item-action <?= $category_id == $category['id'] ? 'active' : '' ?>">
                                    <?= htmlspecialchars($category['category_name']) ?>
                                    <span class="badge bg-secondary float-end">
                                        <?= $productController->count_products('category', $category['id']) ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Brand Filter -->
                    <div class="mb-4">
                        <h6>Brands</h6>
                        <div class="list-group">
                            <a href="?q=<?= urlencode($search_query) ?>&category=<?= $category_id ?>" 
                               class="list-group-item list-group-item-action <?= $brand_id == 0 ? 'active' : '' ?>">
                                All Brands
                            </a>
                            <?php foreach ($brands as $brand): ?>
                                <a href="?q=<?= urlencode($search_query) ?>&brand=<?= $brand['id'] ?><?= $category_id ? "&category=$category_id" : '' ?>" 
                                   class="list-group-item list-group-item-action <?= $brand_id == $brand['id'] ? 'active' : '' ?>">
                                    <?= htmlspecialchars($brand['brand_name']) ?>
                                    <span class="badge bg-secondary float-end">
                                        <?= $productController->count_products('brand', $brand['id']) ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <?php if (!empty($search_query)): ?>
                        Search Results for "<?= htmlspecialchars($search_query) ?>"
                    <?php elseif ($category_id > 0): ?>
                        <?= htmlspecialchars(array_column(array_filter($categories, fn($c) => $c['id'] == $category_id), 'category_name')[0] ?? 'Category') ?>
                    <?php elseif ($brand_id > 0): ?>
                        <?= htmlspecialchars(array_column(array_filter($brands, fn($b) => $b['id'] == $brand_id), 'brand_name')[0] ?? 'Brand') ?>
                    <?php endif; ?>
                    <small class="text-muted">(<?= $total_results ?> results)</small>
                </h2>
                
                <!-- Sort Options -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Sort by: <?= isset($_GET['sort']) ? ucfirst(str_replace('_', ' ', $_GET['sort'])) : 'Relevance' ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                        <li><a class="dropdown-item" href="?q=<?= urlencode($search_query) ?>&sort=relevance<?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?>">Relevance</a></li>
                        <li><a class="dropdown-item" href="?q=<?= urlencode($search_query) ?>&sort=price_low_high<?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?>">Price: Low to High</a></li>
                        <li><a class="dropdown-item" href="?q=<?= urlencode($search_query) ?>&sort=price_high_low<?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?>">Price: High to Low</a></li>
                        <li><a class="dropdown-item" href="?q=<?= urlencode($search_query) ?>&sort=newest<?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?>">Newest Arrivals</a></li>
                        <li><a class="dropdown-item" href="?q=<?= urlencode($search_query) ?>&sort=top_rated<?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?>">Top Rated</a></li>
                    </ul>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    <h4 class="alert-heading">No products found</h4>
                    <p>Try adjusting your search or filter criteria to find what you're looking for.</p>
                    <hr>
                    <p class="mb-0">
                        <a href="all_products.php" class="alert-link">Browse all products</a> or try a different search term.
                    </p>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col mb-4">
                            <div class="card h-100 product-card">
                                <a href="single_product.php?id=<?= $product['id'] ?>" class="text-decoration-none text-dark">
                                    <?php if (!empty($product['image_path'])): ?>
                                        <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                                             class="card-img-top" 
                                             alt="<?= htmlspecialchars($product['title']) ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="text-center p-5 bg-light" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                            <span class="text-muted">No image available</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                                        <p class="card-text text-muted">
                                            <small>
                                                <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?> | 
                                                <?= htmlspecialchars($product['brand_name'] ?? 'No Brand') ?>
                                            </small>
                                        </p>
                                                                                 <p class="card-text font-weight-bold text-primary">₦<?= number_format($product['price'], 2) ?></p>
                                    </div>
                                </a>
                                <div class="card-footer bg-transparent">
                                    <button class="btn btn-sm add-to-cart" 
                                            data-product-id="<?= $product['id'] ?>"
                                            style="background-color: #660a38; color: white; border: none;">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if (($pagination['total_pages'] ?? 0) > 1): ?>
                    <nav aria-label="Search results pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if (($pagination['current_page'] ?? 1) > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" 
                                       href="?q=<?= urlencode($search_query) ?>&page=<?= $pagination['current_page'] - 1 ?><?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?><?= isset($_GET['sort']) ? "&sort={$_GET['sort']}" : '' ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php 
                            // Show first page
                            if (($pagination['current_page'] ?? 1) > 3): ?>
                                <li class="page-item">
                                    <a class="page-link" 
                                       href="?q=<?= urlencode($search_query) ?>&page=1<?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?><?= isset($_GET['sort']) ? "&sort={$_GET['sort']}" : '' ?>">
                                        1
                                    </a>
                                </li>
                                <?php if (($pagination['current_page'] ?? 1) > 4): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; 
                            endif;

                            // Show page numbers
                            $start = max(1, ($pagination['current_page'] ?? 1) - 2);
                            $end = min(($pagination['total_pages'] ?? 1), ($pagination['current_page'] ?? 1) + 2);
                            
                            for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= $i == ($pagination['current_page'] ?? 1) ? 'active' : '' ?>">
                                    <a class="page-link" 
                                       href="?q=<?= urlencode($search_query) ?>&page=<?= $i ?><?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?><?= isset($_GET['sort']) ? "&sort={$_GET['sort']}" : '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor;

                            // Show last page
                            if (($pagination['total_pages'] ?? 1) - ($pagination['current_page'] ?? 1) >= 3): 
                                if (($pagination['total_pages'] ?? 1) - ($pagination['current_page'] ?? 1) > 3): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" 
                                       href="?q=<?= urlencode($search_query) ?>&page=<?= $pagination['total_pages'] ?><?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?><?= isset($_GET['sort']) ? "&sort={$_GET['sort']}" : '' ?>">
                                        <?= $pagination['total_pages'] ?>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (($pagination['current_page'] ?? 1) < ($pagination['total_pages'] ?? 1)): ?>
                                <li class="page-item">
                                    <a class="page-link" 
                                       href="?q=<?= urlencode($search_query) ?>&page=<?= ($pagination['current_page'] ?? 1) + 1 ?><?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?><?= isset($_GET['sort']) ? "&sort={$_GET['sort']}" : '' ?>">
                                        Next
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
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
                <p>Product has been added to your cart successfully!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                <a href="cart.php" class="btn btn-primary">View Cart</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- JavaScript for Search Results -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            
            // Here you would typically make an AJAX call to add the item to the cart
            // For now, we'll just show the modal
            const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
            cartModal.show();
            
            // Example AJAX call (uncomment and implement your cart endpoint)
            /*
            fetch('actions/cart_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&product_id=${productId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartModal.show();
                    // Update cart count if needed
                    if (data.cart_count) {
                        const cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                        }
                    }
                } else {
                    alert('Failed to add item to cart: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the item to cart');
            });
            */
        });
    });

    // Live search functionality
    const searchInput = document.querySelector('input[name="q"]');
    const searchForm = document.getElementById('searchForm');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.trim().length > 2 || this.value.trim().length === 0) {
                    searchForm.submit();
                }
            }, 500);
        });
    }
});
</script>

<style>
/* Product card hover effect */
.product-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid rgba(0,0,0,.125);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Active filter styling */
.list-group-item.active {
    background-color: #660a38;
    border-color: #660a38;
}

/* Pagination active state */
.page-item.active .page-link {
    background-color: #660a38;
    border-color: #660a38;
    color: white;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dropdown {
        margin-top: 1rem;
        width: 100%;
    }
    
    .dropdown .btn {
        width: 100%;
    }
}
</style>