// js/brand.js
document.addEventListener('DOMContentLoaded', () => {
  const addForm = document.getElementById('addForm');
  const brandName = document.getElementById('brandName');
  const brandCategory = document.getElementById('brandCategory');
  const addBtn = document.getElementById('addBtn');
  const addFeedback = document.getElementById('addFeedback');
  const brandsList = document.getElementById('brandsList');

  const editModal = document.getElementById('editModal');
  const editBrandName = document.getElementById('editBrandName');
  const editBrandId = document.getElementById('editBrandId');
  const saveEdit = document.getElementById('saveEdit');
  const cancelEdit = document.getElementById('cancelEdit');
  const editFeedback = document.getElementById('editFeedback');

  async function loadBrands(){
    brandsList.innerHTML = 'Loading...';
    try {
      const res = await fetch('../actions/fetch_brand_action.php');
      const json = await res.json();
      if (!json.success) {
        brandsList.innerHTML = '<div class="small">Unable to load brands.</div>';
        return;
      }
      renderBrands(json.brands || []);
    } catch (e) {
      brandsList.innerHTML = '<div class="small">Error loading brands.</div>';
      console.error(e);
    }
  }

  function renderBrands(items) {
    if (!items.length) {
      brandsList.innerHTML = '<div class="small">No brands yet.</div>';
      return;
    }
    // group by category_name
    const groups = {};
    items.forEach(i => {
      const cat = i.category_name || 'Uncategorized';
      groups[cat] = groups[cat] || [];
      groups[cat].push(i);
    });
    let html = '';
    for (const cat in groups) {
      html += `<h4>${escapeHtml(cat)}</h4>`;
      html += groups[cat].map(b => `
        <div class="list-item" data-id="${b.id}">
          <div><strong>${escapeHtml(b.brand_name)}</strong><div class="small">Created: ${b.created_at}</div></div>
          <div>
            <button class="btn-secondary editBtn">Edit</button>
            <button class="deleteBtn">Delete</button>
          </div>
        </div>
      `).join('');
    }
    brandsList.innerHTML = html;
    document.querySelectorAll('.editBtn').forEach(b=>b.addEventListener('click', onEdit));
    document.querySelectorAll('.deleteBtn').forEach(b=>b.addEventListener('click', onDelete));
  }

  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  addForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    addFeedback.textContent = '';
    const name = brandName.value.trim();
    const cat = brandCategory.value;
    if (!name || !cat) { addFeedback.textContent = 'Provide name and category.'; return; }
    addBtn.disabled = true; addBtn.textContent='Adding...';
    try {
      const fd = new FormData();
      fd.append('brand_name', name);
      fd.append('category_id', cat);
      const res = await fetch('../actions/add_brand_action.php', { method:'POST', body: fd });
      const json = await res.json();
      if (json.success) {
        addFeedback.style.color='green'; addFeedback.textContent = json.message || 'Added';
        brandName.value = '';
        await loadBrands();
      } else {
        addFeedback.style.color='red'; addFeedback.textContent = json.message || 'Failed';
      }
    } catch (err) {
      addFeedback.style.color='red'; addFeedback.textContent='Error';
      console.error(err);
    } finally { addBtn.disabled=false; addBtn.textContent='Add'; }
  });

  function onEdit(e){
    const id = e.target.closest('.list-item').dataset.id;
    const name = e.target.closest('.list-item').querySelector('strong').textContent;
    editBrandId.value = id;
    editBrandName.value = name;
    editFeedback.textContent = '';
    editModal.classList.add('show');
  }

  cancelEdit.addEventListener('click', ()=> editModal.classList.remove('show'));

  saveEdit.addEventListener('click', async () => {
    const id = editBrandId.value; const name = editBrandName.value.trim();
    if (!name) { editFeedback.textContent = 'Enter a name'; return; }
    saveEdit.disabled = true; saveEdit.textContent = 'Saving...';
    try {
      const fd = new FormData();
      fd.append('id', id);
      fd.append('brand_name', name);
      const res = await fetch('../actions/update_brand_action.php', { method:'POST', body: fd });
      const json = await res.json();
      if (json.success) {
        editFeedback.style.color='green'; editFeedback.textContent = json.message || 'Updated';
        await loadBrands();
        setTimeout(()=> editModal.classList.remove('show'), 700);
      } else {
        editFeedback.style.color='red'; editFeedback.textContent = json.message || 'Update failed';
      }
    } catch (err) {
      editFeedback.style.color='red'; editFeedback.textContent='Error';
      console.error(err);
    } finally { saveEdit.disabled=false; saveEdit.textContent='Save'; }
  });

  async function onDelete(e){
    if (!confirm('Delete this brand?')) return;
    const id = e.target.closest('.list-item').dataset.id;
    try {
      const fd = new FormData(); fd.append('id', id);
      const res = await fetch('../actions/delete_brand_action.php', { method:'POST', body: fd });
      const json = await res.json();
      if (json.success) {
        await loadBrands();
      } else {
        alert(json.message || 'Delete failed');
      }
    } catch (err) { console.error(err); alert('Error'); }
  }

  loadBrands();
});
