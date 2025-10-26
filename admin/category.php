<?php
// admin/category.php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../settings/core.php';

// Must be logged in and admin
if (!is_logged_in() || !is_admin()) {
    header('Location: ../login/login.php');
    exit;
}

$name = $_SESSION['customer_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Categories - Sure Shop</title>
<style>
:root { --brand: #660a38; --bg:#ffffff; }
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:Arial, sans-serif;background:var(--bg);min-height:100vh;}
nav{display:flex;justify-content:space-between;align-items:center;padding:1rem 2rem;border-bottom:1px solid #eee;}
.brand{color:var(--brand);font-weight:700;}
.container{max-width:900px;margin:2rem auto;padding:1rem;}
.card{background:#fff;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.06);padding:1rem;}
.header-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;}
.form-row{display:flex;gap:.5rem;margin-bottom:.8rem;}
input[type="text"]{flex:1;padding:.6rem;border-radius:6px;border:1px solid #ddd;}
button{background:var(--brand);color:#fff;border:none;padding:.6rem 1rem;border-radius:6px;cursor:pointer;}
.btn-secondary{background:#fff;color:var(--brand);border:1px solid var(--brand);}
.list{margin-top:1rem;}
.list-item{display:flex;justify-content:space-between;align-items:center;padding:.5rem;border-bottom:1px solid #f1f1f1;}
.list-item .actions button{margin-left:.5rem;}
.feedback{margin-top:.6rem;color:#333;}
.small{font-size:.9rem;color:#666;}
.modal{
  position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);
}
.modal .dialog{background:#fff;padding:1rem;border-radius:8px;width:360px;}
.modal.show{display:flex;}
</style>
</head>
<body>
  <nav>
    <div class="brand">Sure Shop - Admin</div>
    <div>
      Welcome, <?php echo htmlspecialchars($name); ?> |
      <a href="../actions/logout_action.php" style="color:var(--brand);text-decoration:none;margin-left:8px;">Logout</a>
    </div>
  </nav>

  <div class="container">
    <div class="card">
      <div class="header-row">
        <h2>Categories</h2>
        <div class="small">Only categories created by you are shown.</div>
      </div>

      <form id="addForm" onsubmit="return false;">
        <div class="form-row">
          <input type="text" id="newCategory" name="category_name" placeholder="New category name" required>
          <button id="addBtn">Add</button>
        </div>
        <div id="addFeedback" class="small"></div>
      </form>

      <div class="list card" id="categoriesList">
        <!-- categories injected here -->
      </div>

      <div id="generalFeedback" class="feedback"></div>
    </div>
  </div>

  <!-- Edit modal -->
  <div id="editModal" class="modal">
    <div class="dialog">
      <h3>Edit category</h3>
      <input type="text" id="editName" placeholder="Category name">
      <input type="hidden" id="editId">
      <div style="margin-top:.6rem;text-align:right;">
        <button id="cancelEdit" class="btn-secondary">Cancel</button>
        <button id="saveEdit">Save</button>
      </div>
      <div id="editFeedback" class="small" style="margin-top:.5rem;"></div>
    </div>
  </div>

<script src="../js/category.js"></script>
</body>
</html>
