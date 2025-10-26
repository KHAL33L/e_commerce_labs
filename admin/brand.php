<?php
// admin/brand.php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../settings/core.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ../login/login.php');
    exit;
}
$uid = $_SESSION['customer_id'];
// fetch categories for dropdown
$catCtr = new CategoryController();
$cats = $catCtr->fetch_user_categories_ctr($uid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Brands - Sure Shop</title>
<style>
:root{--brand:#660a38;--bg:#fff}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial, sans-serif;background:var(--bg);min-height:100vh}
nav{display:flex;justify-content:space-between;padding:1rem 2rem;border-bottom:1px solid #eee}
.brand{color:var(--brand);font-weight:700}
.container{max-width:900px;margin:2rem auto;padding:1rem}
.card{background:#fff;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.06);padding:1rem}
.form-row{display:flex;gap:.5rem;margin-bottom:.8rem}
input[type="text"],select{flex:1;padding:.6rem;border-radius:6px;border:1px solid #ddd}
button{background:var(--brand);color:#fff;border:none;padding:.6rem 1rem;border-radius:6px;cursor:pointer}
.btn-secondary{background:#fff;color:var(--brand);border:1px solid var(--brand)}
.list-item{display:flex;justify-content:space-between;align-items:center;padding:.5rem;border-bottom:1px solid #f1f1f1}
.small{font-size:.9rem;color:#666}
.modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.4)}
.modal .dialog{background:#fff;padding:1rem;border-radius:8px;width:360px}
.modal.show{display:flex}
</style>
</head>
<body>
<nav>
  <div class="brand">Sure Shop - Admin</div>
  <div>Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?> | <a href="../actions/logout_action.php">Logout</a></div>
</nav>

<div class="container">
  <div style="margin-bottom:1rem;">
    <a href="../dashboard.php" style="text-decoration:none;color:var(--brand);font-weight:500;">← Back to Dashboard</a>
  </div>
  <div class="card">
    <h2>Brands</h2>
    <p class="small">Brands are grouped under categories. Only your brands are shown.</p>

    <form id="addForm" onsubmit="return false;">
      <div class="form-row">
        <input type="text" id="brandName" name="brand_name" placeholder="Brand name" required>
        <select id="brandCategory">
          <option value="">Select category</option>
          <?php foreach($cats as $c): ?>
            <option value="<?php echo htmlspecialchars($c['id']); ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
          <?php endforeach; ?>
        </select>
        <button id="addBtn">Add</button>
      </div>
      <div id="addFeedback" class="small"></div>
    </form>

    <div id="brandsList" style="margin-top:1rem"></div>
  </div>
</div>

<!-- edit modal -->
<div id="editModal" class="modal">
  <div class="dialog">
    <h3>Edit Brand</h3>
    <input type="text" id="editBrandName" placeholder="Brand name">
    <input type="hidden" id="editBrandId">
    <div style="margin-top:.6rem;text-align:right">
      <button id="cancelEdit" class="btn-secondary">Cancel</button>
      <button id="saveEdit">Save</button>
    </div>
    <div id="editFeedback" class="small" style="margin-top:.5rem"></div>
  </div>
</div>

<script src="../js/brand.js"></script>
</body>
</html>
