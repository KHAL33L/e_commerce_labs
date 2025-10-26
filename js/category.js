// js/category.js
document.addEventListener('DOMContentLoaded', () => {
  const addForm = document.getElementById('addForm');
  const newCategory = document.getElementById('newCategory');
  const addBtn = document.getElementById('addBtn');
  const addFeedback = document.getElementById('addFeedback');
  const categoriesList = document.getElementById('categoriesList');
  const generalFeedback = document.getElementById('generalFeedback');

  const editModal = document.getElementById('editModal');
  const editName = document.getElementById('editName');
  const editId = document.getElementById('editId');
  const saveEdit = document.getElementById('saveEdit');
  const cancelEdit = document.getElementById('cancelEdit');
  const editFeedback = document.getElementById('editFeedback');

  // helper to fetch and render categories
  async function loadCategories() {
    categoriesList.innerHTML = 'Loading...';
    try {
      const res = await fetch('../actions/fetch_category_action.php', { method: 'GET' });
      const json = await res.json();
      if (!json.success) {
        categoriesList.innerHTML = '<div class="small">Could not load categories.</div>';
        return;
      }
      renderCategories(json.categories || []);
    } catch (err) {
      categoriesList.innerHTML = '<div class="small">Error loading categories.</div>';
      console.error(err);
    }
  }

  function renderCategories(items) {
    if (!items.length) {
      categoriesList.innerHTML = '<div class="small">You have not added any categories yet.</div>';
      return;
    }
    const html = items.map(i => `
      <div class="list-item" data-id="${i.id}">
        <div>
          <strong>${escapeHtml(i.category_name)}</strong>
          <div class="small">Created: ${i.created_at}</div>
        </div>
        <div class="actions">
          <button class="btn-secondary editBtn">Edit</button>
          <button class="btn deleteBtn">Delete</button>
        </div>
      </div>
    `).join('');
    categoriesList.innerHTML = html;
    // bind buttons
    document.querySelectorAll('.editBtn').forEach(b => b.addEventListener('click', onEdit));
    document.querySelectorAll('.deleteBtn').forEach(b => b.addEventListener('click', onDelete));
  }

  // escape function
  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  // add category
  addForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    addFeedback.textContent = '';
    const name = newCategory.value.trim();
    if (!name) { addFeedback.textContent = 'Enter a name.'; return; }
    addBtn.disabled = true;
    addBtn.textContent = 'Adding...';
    try {
      const fd = new FormData();
      fd.append('category_name', name);
      const res = await fetch('../actions/add_category_action.php', { method:'POST', body: fd });
      const json = await res.json();
      if (json.success) {
        addFeedback.style.color = 'green';
        addFeedback.textContent = json.message || 'Added';
        newCategory.value = '';
        await loadCategories();
      } else {
        addFeedback.style.color = 'red';
        addFeedback.textContent = json.message || 'Failed to add';
      }
    } catch (err) {
      addFeedback.style.color = 'red';
      addFeedback.textContent = 'Error adding category';
      console.error(err);
    } finally {
      addBtn.disabled = false;
      addBtn.textContent = 'Add';
    }
  });

  // edit flow
  function onEdit(e){
    const li = e.target.closest('.list-item');
    const id = li.dataset.id;
    const name = li.querySelector('strong').textContent;
    editId.value = id;
    editName.value = name;
    editFeedback.textContent = '';
    editModal.classList.add('show');
  }

  cancelEdit.addEventListener('click', () => {
    editModal.classList.remove('show');
  });

  saveEdit.addEventListener('click', async () => {
    const id = editId.value;
    const name = editName.value.trim();
    editFeedback.textContent = '';
    if (!name) { editFeedback.textContent = 'Enter a name.'; return; }
    saveEdit.disabled = true;
    saveEdit.textContent = 'Saving...';
    try {
      const fd = new FormData();
      fd.append('id', id);
      fd.append('category_name', name);
      const res = await fetch('../actions/update_category_action.php', { method: 'POST', body: fd });
      const json = await res.json();
      if (json.success) {
        editFeedback.style.color = 'green';
        editFeedback.textContent = json.message || 'Updated';
        await loadCategories();
        setTimeout(()=> editModal.classList.remove('show'), 700);
      } else {
        editFeedback.style.color = 'red';
        editFeedback.textContent = json.message || 'Update failed';
      }
    } catch (err) {
      editFeedback.style.color = 'red';
      editFeedback.textContent = 'Error';
      console.error(err);
    } finally {
      saveEdit.disabled = false;
      saveEdit.textContent = 'Save';
    }
  });

  // delete
  async function onDelete(e) {
    if (!confirm('Delete this category?')) return;
    const li = e.target.closest('.list-item');
    const id = li.dataset.id;
    try {
      const fd = new FormData();
      fd.append('id', id);
      const res = await fetch('../actions/delete_category_action.php', { method:'POST', body: fd });
      const json = await res.json();
      if (json.success) {
        generalFeedback.style.color = 'green';
        generalFeedback.textContent = json.message || 'Deleted';
        await loadCategories();
      } else {
        generalFeedback.style.color = 'red';
        generalFeedback.textContent = json.message || 'Delete failed';
      }
    } catch (err) {
      generalFeedback.style.color = 'red';
      generalFeedback.textContent = 'Error deleting';
      console.error(err);
    }
  }

  // initial load
  loadCategories();
});
