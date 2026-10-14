/**
 * VOGUEORA APPAREL - PRODUCT MANAGER
 * Client-Side Interactivity, Live Filters, Modals & Security UX
 */

document.addEventListener('DOMContentLoaded', function () {
  // ==========================================
  // 1. THEME SWITCHER (DARK / LIGHT MODE)
  // ==========================================
  const themeToggleBtn = document.getElementById('themeToggleBtn');
  const htmlRoot = document.documentElement;

  // Cek theme tersimpan di localStorage atau preferensi sistem
  const savedTheme = localStorage.getItem('vogueora_theme') || 
    (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

  function applyTheme(theme) {
    htmlRoot.setAttribute('data-theme', theme);
    localStorage.setItem('vogueora_theme', theme);
    if (themeToggleBtn) {
      const icon = themeToggleBtn.querySelector('i');
      if (icon) {
        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
      }
    }
  }

  applyTheme(savedTheme);

  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', function () {
      const currentTheme = htmlRoot.getAttribute('data-theme') || 'light';
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      applyTheme(newTheme);
    });
  }

  // ==========================================
  // 2. FLASH ALERT AUTO-DISMISS & CLOSE
  // ==========================================
  const alertElements = document.querySelectorAll('.alert');
  alertElements.forEach(alert => {
    // Auto dismiss setelah 5 detik
    const timer = setTimeout(() => {
      dismissAlert(alert);
    }, 5000);

    const closeBtn = alert.querySelector('.alert-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        clearTimeout(timer);
        dismissAlert(alert);
      });
    }
  });

  function dismissAlert(el) {
    el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    el.style.opacity = '0';
    el.style.transform = 'translateY(-10px)';
    setTimeout(() => {
      if (el.parentNode) el.parentNode.removeChild(el);
    }, 300);
  }

  // ==========================================
  // 3. SECURE CSRF DELETE MODAL
  // ==========================================
  const deleteModal = document.getElementById('deleteConfirmModal');
  const deleteForm = document.getElementById('secureDeleteForm');
  const deleteProductIdInput = document.getElementById('deleteProductId');
  const deleteProductNameSpan = document.getElementById('deleteProductName');
  const deleteButtons = document.querySelectorAll('.btn-trigger-delete');

  deleteButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-name');

      if (deleteProductIdInput) deleteProductIdInput.value = id;
      if (deleteProductNameSpan) deleteProductNameSpan.textContent = name;

      openModal(deleteModal);
    });
  });

  // ==========================================
  // 4. QUICK VIEW MODAL (DETAIL PRODUK)
  // ==========================================
  const quickViewModal = document.getElementById('quickViewModal');
  const detailButtons = document.querySelectorAll('.btn-trigger-view');

  detailButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-name');
      const category = this.getAttribute('data-category');
      const price = this.getAttribute('data-price');
      const stock = this.getAttribute('data-stock');
      const desc = this.getAttribute('data-desc') || 'Tidak ada deskripsi produk.';
      const img = this.getAttribute('data-image');
      const date = this.getAttribute('data-date') || '-';

      // Set target elemen di dalam modal
      const qvImg = document.getElementById('qvModalImage');
      const qvName = document.getElementById('qvModalName');
      const qvCat = document.getElementById('qvModalCategory');
      const qvPrice = document.getElementById('qvModalPrice');
      const qvStock = document.getElementById('qvModalStock');
      const qvDesc = document.getElementById('qvModalDescription');
      const qvDate = document.getElementById('qvModalDate');
      const qvEditLink = document.getElementById('qvModalEditLink');

      if (qvImg) qvImg.src = img;
      if (qvName) qvName.textContent = name;
      if (qvCat) qvCat.textContent = category;
      if (qvPrice) qvPrice.textContent = price;
      if (qvStock) qvStock.innerHTML = `<i class="fas fa-boxes"></i> Stok: <strong>${stock}</strong> unit`;
      if (qvDesc) qvDesc.textContent = desc;
      if (qvDate) qvDate.textContent = date;
      if (qvEditLink) qvEditLink.href = `edit.php?id=${encodeURIComponent(id)}`;

      openModal(quickViewModal);
    });
  });

  // Helper Buka / Tutup Modal
  function openModal(modal) {
    if (!modal) return;
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  // Tutup modal pada tombol close atau klik backdrop luar
  document.querySelectorAll('.modal-close-btn, .btn-modal-cancel').forEach(btn => {
    btn.addEventListener('click', function () {
      const modal = this.closest('.modal-backdrop');
      closeModal(modal);
    });
  });

  document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', function (e) {
      if (e.target === this) {
        closeModal(this);
      }
    });
  });

  // Tutup dengan tombol Escape
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-backdrop.is-open').forEach(modal => {
        closeModal(modal);
      });
    }
  });

  // ==========================================
  // 5. VIEW SWITCHER (CARD GRID VS TABLE)
  // ==========================================
  const viewGridBtn = document.getElementById('viewGridBtn');
  const viewTableBtn = document.getElementById('viewTableBtn');
  const productsGrid = document.getElementById('productsGridContainer');
  const productsTable = document.getElementById('productsTableContainer');

  if (viewGridBtn && viewTableBtn && productsGrid && productsTable) {
    const savedView = localStorage.getItem('vogueora_view') || 'grid';

    function setView(view) {
      if (view === 'table') {
        productsGrid.style.display = 'none';
        productsTable.style.display = 'block';
        viewTableBtn.classList.add('active');
        viewGridBtn.classList.remove('active');
        localStorage.setItem('vogueora_view', 'table');
      } else {
        productsGrid.style.display = 'grid';
        productsTable.style.display = 'none';
        viewGridBtn.classList.add('active');
        viewTableBtn.classList.remove('active');
        localStorage.setItem('vogueora_view', 'grid');
      }
    }

    setView(savedView);

    viewGridBtn.addEventListener('click', () => setView('grid'));
    viewTableBtn.addEventListener('click', () => setView('table'));
  }

  // ==========================================
  // 6. INSTANT LIVE SEARCH (CLIENT-SIDE FILTER)
  // ==========================================
  const searchInput = document.getElementById('liveSearchInput');
  const clearSearchBtn = document.getElementById('clearSearchBtn');
  const productCards = document.querySelectorAll('.product-card');
  const tableRows = document.querySelectorAll('.data-table tbody tr');
  const emptyStateCard = document.getElementById('liveEmptyState');
  const resultCounter = document.getElementById('visibleResultCount');

  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const query = this.value.toLowerCase().trim();

      if (clearSearchBtn) {
        clearSearchBtn.style.display = query.length > 0 ? 'block' : 'none';
      }

      let visibleCount = 0;

      // Filter Cards
      productCards.forEach(card => {
        const title = (card.getAttribute('data-name') || '').toLowerCase();
        const category = (card.getAttribute('data-category') || '').toLowerCase();
        const desc = (card.getAttribute('data-desc') || '').toLowerCase();

        const match = title.includes(query) || category.includes(query) || desc.includes(query);
        card.style.display = match ? 'flex' : 'none';
        if (match) visibleCount++;
      });

      // Filter Table Rows
      tableRows.forEach(row => {
        const title = (row.getAttribute('data-name') || '').toLowerCase();
        const category = (row.getAttribute('data-category') || '').toLowerCase();

        const match = title.includes(query) || category.includes(query);
        row.style.display = match ? '' : 'none';
      });

      // Update Result Counter
      if (resultCounter) {
        resultCounter.textContent = visibleCount;
      }

      // Empty State
      if (emptyStateCard) {
        emptyStateCard.style.display = visibleCount === 0 ? 'block' : 'none';
      }
    });

    if (clearSearchBtn) {
      clearSearchBtn.addEventListener('click', function () {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
      });
    }
  }

  // ==========================================
  // 7. IMAGE UPLOAD PREVIEW
  // ==========================================
  const imageFileInput = document.getElementById('imageFileInput');
  const imagePreviewThumb = document.getElementById('imagePreviewThumb');

  if (imageFileInput && imagePreviewThumb) {
    imageFileInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          imagePreviewThumb.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // ==========================================
  // 8. REAL-TIME CLIENT VALIDATION ENHANCEMENT
  // ==========================================
  const productForm = document.getElementById('productForm');
  if (productForm) {
    const nameInput = document.getElementById('name');
    const priceInput = document.getElementById('price');
    const stockInput = document.getElementById('stock');

    function checkField(input, condition, errorMsg) {
      let errSpan = input.parentElement.querySelector('.live-feedback');
      if (!errSpan) {
        errSpan = document.createElement('span');
        errSpan.className = 'error-feedback live-feedback';
        input.parentElement.appendChild(errSpan);
      }

      if (!condition) {
        input.classList.add('is-invalid');
        errSpan.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${errorMsg}`;
        return false;
      } else {
        input.classList.remove('is-invalid');
        errSpan.textContent = '';
        return true;
      }
    }

    if (nameInput) {
      nameInput.addEventListener('input', function () {
        const val = this.value.trim();
        checkField(this, val.length >= 3, 'Nama produk minimal 3 karakter (nama ≥ 3).');
      });
    }

    if (priceInput) {
      priceInput.addEventListener('input', function () {
        const val = parseFloat(this.value);
        checkField(this, !isNaN(val) && val > 0, 'Harga harus lebih besar dari 0 (harga > 0).');
      });
    }

    if (stockInput) {
      stockInput.addEventListener('input', function () {
        const val = parseInt(this.value, 10);
        checkField(this, !isNaN(val) && val >= 0, 'Stok harus berupa bilangan bulat non-negatif (stok ≥ 0).');
      });
    }
  }
});
