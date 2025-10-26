// js/product.js
document.addEventListener('DOMContentLoaded', () => {
  const categorySelect = document.getElementById('categorySelect');
  const brandSelect = document.getElementById('brandSelect');
  const productForm = document.getElementById('productForm');
  const saveProduct = document.getElementById('saveProduct');
  const clearForm = document.getElementById('clearForm');
  const prodFeedback = document.getElementById('prodFeedback');
  const productsList = document.getElementById('productsList');
  const imageInput = document.getElementById('image');

  // BRANDS is provided in the page
  function populateBrandsForCategory(catId) {
    brandSelect.innerHTML = '<option value="">Select brand</option>';
    const list = BRANDS.filter(b => String(b.category_id) === String(catId));
    list.forEach(b => {
      const opt = document.createElement('option');
      opt.value = b.id;
      opt.textContent = b.brand_name;
      brandSelect.appendChild(opt);
    });
  }

  categorySelect.addEventListener('change', () => {
    populateBrandsForCategory(categorySelect.value);
  });

  clearForm.addEventListener('click', () => {
    productForm.reset(); document.getElementById('productId').value='';
  });

  async function loadProducts() {
    productsList.innerHTML = 'Loading...';
    try {
      const res = await fetch('../actions/fetch_product_action.php');
      const json = await res.json();
      if (!json.success) { productsList.innerHTML = '<div class="small">Unable to load products</div>'; return; }
      renderProducts(json.products || []);
    } catch (err) {
      productsList.innerHTML = '<div class="small">Error loading products</div>'; console.error(err);
    }
  }

  function renderProducts(items) {
    if (!items.length) { productsList.innerHTML = '<div class="small">No products yet.</div>'; return; }
    const html = items.map(p => `
      <div class="product-item" data-id="${p.id}">
        <img src="${p.image_path ? '../' + p.image_path : 'https://via.placeholder.com/80'}" alt="">
        <div>
          <strong>${escapeHtml(p.title)}</strong>
          <div class="small">${escapeHtml(p.brand_name || '')} • ${escapeHtml(p.category_name || '')}</div>
          <div class="small">₦ ${p.price}</div>
        </div>
        <div class="actions">
          <button class="editBtn">Edit</button>
          <button class="deleteBtn">Delete</button>
        </div>
      </div>
    `).join('');
    productsList.innerHTML = html;
    document.querySelectorAll('.editBtn').forEach(b=>b.addEventListener('click', onEdit));
    document.querySelectorAll('.deleteBtn').forEach(b=>b.addEventListener('click', onDelete));
  }

  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  async function onEdit(e){
    const id = e.target.closest('.product-item').dataset.id;
    // fetch product data
    const fd = new FormData(); fd.append('id', id);
    const res = await fetch('../actions/fetch_product_action.php', { method: 'POST', body: fd });
    const json = await res.json();
    if (!json.success) { alert('Could not load product'); return; }
    const p = json.product;
    document.getElementById('productId').value = p.id;
    document.getElementById('title').value = p.title;
    document.getElementById('price').value = p.price;
    document.getElementById('description').value = p.description || '';
    document.getElementById('keywords').value = p.keywords || '';
    categorySelect.value = p.category_id;
    populateBrandsForCategory(p.category_id);
    brandSelect.value = p.brand_id;
    window.scrollTo({top:0,behavior:'smooth'});
  }

  async function onDelete(e){
    if (!confirm('Delete this product?')) return;
    const id = e.target.closest('.product-item').dataset.id;
    try {
      const fd = new FormData(); fd.append('id', id);
      const res = await fetch('../actions/delete_product_action.php', { method:'POST', body: fd });
      const json = await res.json();
      if (json.success) loadProducts(); else alert(json.message || 'Delete failed');
    } catch (err) { console.error(err); alert('Error'); }
  }

  // Save product first, then upload image
  saveProduct.addEventListener('click', async () => {
    prodFeedback.textContent = '';
    saveProduct.disabled = true; saveProduct.textContent='Saving...';
    const id = document.getElementById('productId').value;
    
    // Build FormData manually to exclude image file
    const fd = new FormData();
    fd.append('title', document.getElementById('title').value);
    fd.append('price', document.getElementById('price').value);
    fd.append('category_id', document.getElementById('categorySelect').value);
    fd.append('brand_id', document.getElementById('brandSelect').value);
    fd.append('description', document.getElementById('description').value);
    fd.append('keywords', document.getElementById('keywords').value);
    
    // Store image file separately
    const imageFile = imageInput.files && imageInput.files[0] ? imageInput.files[0] : null;

    // Save/update product first
    const actionUrl = id ? '../actions/update_product_action.php' : '../actions/add_product_action.php';
    try {
      const res = await fetch(actionUrl, { method:'POST', body: fd });
      const json = await res.json();
      
      if (!json.success) {
        prodFeedback.style.color='red'; prodFeedback.textContent = json.message || 'Failed';
        saveProduct.disabled=false; saveProduct.textContent='Save';
        return;
      }
      
      // If we have an image to upload and product was created, upload it now
      if (imageFile) {
        const newProductId = json.product_id || id;
        if (newProductId) {
          const up = new FormData();
          up.append('image', imageFile);
          up.append('product_id', newProductId);
          
          try {
            const upRes = await fetch('../actions/upload_product_image_action.php', { method:'POST', body: up });
            const upJson = await upRes.json();
            if (upJson.success && upJson.image_path) {
              // Update product with image path
              const updateFd = new FormData();
              updateFd.append('id', newProductId);
              updateFd.append('title', fd.get('title'));
              updateFd.append('price', fd.get('price'));
              updateFd.append('description', fd.get('description'));
              updateFd.append('category_id', fd.get('category_id'));
              updateFd.append('brand_id', fd.get('brand_id'));
              updateFd.append('keywords', fd.get('keywords'));
              updateFd.append('image_path', upJson.image_path);
              
              const updateRes = await fetch('../actions/update_product_action.php', { method:'POST', body: updateFd });
              const updateJson = await updateRes.json();
              if (!updateJson.success) {
                console.warn('Product saved but image update failed');
              }
            }
          } catch (err) {
            console.error('Image upload error:', err);
          }
        }
      }
      
      prodFeedback.style.color='green'; prodFeedback.textContent = json.message || 'Saved';
      productForm.reset(); document.getElementById('productId').value='';
      await loadProducts();
      
    } catch (err) {
      prodFeedback.style.color='red'; prodFeedback.textContent='Error saving';
      console.error(err);
    } finally {
      saveProduct.disabled=false; saveProduct.textContent='Save';
    }
  });

  // initial load
  loadProducts();
});
