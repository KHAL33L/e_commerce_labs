<?php
// admin/product.php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../settings/core.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ../login/login.php');
    exit;
}
$uid = $_SESSION['customer_id'];
$catCtr = new CategoryController();
$cats = $catCtr->fetch_user_categories_ctr($uid);
$brandCtr = new BrandController();
$brands_all = $brandCtr->fetch_user_brands_ctr($uid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Products - Sure Shop</title>
<style>
:root{--brand:#660a38;--bg:#fff}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial, sans-serif;background:var(--bg);min-height:100vh}
nav{display:flex;justify-content:space-between;padding:1rem 2rem;border-bottom:1px solid #eee}
.brand{color:var(--brand);font-weight:700}
.container{max-width:1000px;margin:2rem auto;padding:1rem}
.card{background:#fff;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.06);padding:1rem;margin-bottom:1rem}
.form-row{display:flex;gap:.6rem;align-items:center;margin-bottom:.8rem}
input[type="text"],textarea,select,input[type="number"]{flex:1;padding:.6rem;border-radius:6px;border:1px solid #ddd}
button{background:var(--brand);color:#fff;border:none;padding:.6rem 1rem;border-radius:6px;cursor:pointer}
.btn-secondary{background:#fff;color:var(--brand);border:1px solid var(--brand)}
.small{font-size:.9rem;color:#666}
.product-item{display:flex;gap:1rem;padding:.6rem;border-bottom:1px solid #f1f1f1;align-items:center}
.product-item img{width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #eee}
.actions{margin-left:auto;display:flex;gap:.5rem}
.deleteBtn{background:#dc3545}
</style>
</head>
<body>
<nav><div class="brand">Sure Shop - Admin</div><div>Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?> | <a href="../actions/logout_action.php">Logout</a></div></nav>

<div class="container">
  <div class="card">
    <h2>Add / Edit Product</h2>
    <form id="productForm" enctype="multipart/form-data" onsubmit="return false;">
      <input type="hidden" id="productId" name="id" value="">
      <div class="form-row">
        <input type="text" id="title" name="title" placeholder="Product title" required>
        <input type="number" id="price" name="price" placeholder="Price" step="0.01" required>
      </div>
      <div class="form-row">
        <select id="categorySelect" name="category_id" required>
          <option value="">Select category</option>
          <?php foreach($cats as $c): ?>
            <option value="<?php echo (int)$c['id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
          <?php endforeach; ?>
        </select>
        <select id="brandSelect" name="brand_id" required>
          <option value="">Select brand</option>
          <!-- brands will be populated by JS filtered by category -->
        </select>
      </div>
      <div class="form-row">
        <textarea id="description" name="description" placeholder="Description" rows="3"></textarea>
      </div>
      <div class="form-row">
        <input type="file" id="image" name="image" accept="image/*">
        <input type="text" id="keywords" name="keywords" placeholder="Keywords (comma separated)">
      </div>
      <div style="display:flex;gap:.5rem">
        <button id="saveProduct">Save</button>
        <button id="clearForm" type="button" class="btn-secondary">Clear</button>
      </div>
      <div id="prodFeedback" class="small" style="margin-top:.6rem"></div>
    </form>
  </div>

  <div class="card">
    <h3>Your Products</h3>
    <div id="productsList" class="list"></div>
  </div>
</div>

<script>
  // Pass brands dataset from PHP to JS
  const BRANDS = <?php echo json_encode($brands_all); ?>;
</script>
<script src="../js/product.js"></script>
</body>
</html>
