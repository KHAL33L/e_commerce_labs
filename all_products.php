<?php
// all_products.php
require_once __DIR__ . '/includes/header.php';

// Initialize variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10; // Items per page
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$brand_id = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Initialize product controller
require_once __DIR__ . '/controllers/product_controller.php';
$productController = new ProductController();

// Get filter data
$categories = $productController->get_all_categories_ctr();
$brands = $productController->get_all_brands_ctr();

// Fetch products based on filters
$products = [];
$pagination = [];

if (!empty($search_query)) {
    $result = $productController->search_products_ctr($search_query, $page, $per_page);
    $products = $result['data'] ?? [];
    $pagination = $result['pagination'] ?? [];
} elseif ($category_id > 0) {
    $result = $productController->filter_by_category_ctr($category_id, $page, $per_page);
    $products = $result['data'] ?? [];
    $pagination = $result['pagination'] ?? [];
} elseif ($brand_id > 0) {
    $result = $productController->filter_by_brand_ctr($brand_id, $page, $per_page);
    $products = $result['data'] ?? [];
    $pagination = $result['pagination'] ?? [];
} else {
    $result = $productController->get_all_products_ctr($page, $per_page);
    $products = $result['data'] ?? [];
    $pagination = $result['pagination'] ?? [];
}

?>

<div class="container mt-4">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Filters</h5>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form id="searchForm" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search products..." 
                                   value="<?= htmlspecialchars($search_query) ?>">
                            <button class="btn" type="submit" style="background-color: #660a38; color: white; border: none;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Category Filter -->
                    <div class="mb-4">
                        <h6>Categories</h6>
                        <div class="list-group">
                            <a href="?category=0" class="list-group-item list-group-item-action <?= $category_id == 0 ? 'active' : '' ?>">
                                All Categories
                            </a>
                            <?php foreach ($categories as $category): ?>
                                <a href="?category=<?= $category['id'] ?>" 
                                   class="list-group-item list-group-item-action <?= $category_id == $category['id'] ? 'active' : '' ?>">
                                    <?= htmlspecialchars($category['category_name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Brand Filter -->
                    <div class="mb-4">
                        <h6>Brands</h6>
                        <div class="list-group">
                            <a href="?brand=0" class="list-group-item list-group-item-action <?= $brand_id == 0 ? 'active' : '' ?>">
                                All Brands
                            </a>
                            <?php foreach ($brands as $brand): ?>
                                <a href="?brand=<?= $brand['id'] ?>" 
                                   class="list-group-item list-group-item-action <?= $brand_id == $brand['id'] ? 'active' : '' ?>">
                                    <?= htmlspecialchars($brand['brand_name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="row mb-4">
                <div class="col">
                    <h2>
                        <?php if (!empty($search_query)): ?>
                            Search Results for "<?= htmlspecialchars($search_query) ?>"
                        <?php elseif ($category_id > 0): ?>
                            <?= htmlspecialchars(array_column(array_filter($categories, fn($c) => $c['id'] == $category_id), 'category_name')[0] ?? 'Category') ?>
                        <?php elseif ($brand_id > 0): ?>
                            <?= htmlspecialchars(array_column(array_filter($brands, fn($b) => $b['id'] == $brand_id), 'brand_name')[0] ?? 'Brand') ?>
                        <?php else: ?>
                            All Products
                        <?php endif; ?>
                        <small class="text-muted">(<?= $pagination['total_items'] ?? 0 ?> items)</small>
                    </h2>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div class="alert alert-info">No products found.</div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col mb-4">
                            <div class="card h-100 product-card">
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
                                <div class="card-footer bg-transparent">
                                    <a href="single_product.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm">
                                        View Details
                                    </a>
                                    <button class="btn btn-sm float-end add-to-cart" 
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
                    <nav aria-label="Product pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if (($pagination['current_page'] ?? 1) > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" 
                                       href="?page=<?= $pagination['current_page'] - 1 ?><?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?><?= $search_query ? "&q=" . urlencode($search_query) : '' ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= ($pagination['total_pages'] ?? 1); $i++): ?>
                                <li class="page-item <?= $i == ($pagination['current_page'] ?? 1) ? 'active' : '' ?>">
                                    <a class="page-link" 
                                       href="?page=<?= $i ?><?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?><?= $search_query ? "&q=" . urlencode($search_query) : '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if (($pagination['current_page'] ?? 1) < ($pagination['total_pages'] ?? 1)): ?>
                                <li class="page-item">
                                    <a class="page-link" 
                                       href="?page=<?= ($pagination['current_page'] ?? 1) + 1 ?><?= $category_id ? "&category=$category_id" : '' ?><?= $brand_id ? "&brand=$brand_id" : '' ?><?= $search_query ? "&q=" . urlencode($search_query) : '' ?>">
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
                <h5 class="modal-title">Add to Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Product added to cart successfully!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                <a href="cart.php" class="btn btn-primary">View Cart</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- JavaScript for AJAX functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
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
                        document.getElementById('cart-count').textContent = data.cart_count;
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