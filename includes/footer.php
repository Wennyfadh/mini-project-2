  <!-- Global Quick View Modal (Read Detail Feature) -->
  <div class="modal-backdrop" id="quickViewModal" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="modal-dialog modal-large">
      <div class="modal-header">
        <h3 class="modal-title">
          <i class="fas fa-tshirt" style="color: var(--primary);"></i>
          <span>Detail Produk Pakaian</span>
        </h3>
        <button type="button" class="modal-close-btn" aria-label="Tutup Modal">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <div class="quick-view-grid">
          <div class="quick-view-media">
            <img id="qvModalImage" src="assets/images/products/default_cloth.svg" alt="Preview Produk">
          </div>
          <div class="quick-view-info">
            <div class="badge-category-chip" id="qvModalCategory" style="position: static; display: inline-block; margin-bottom: 12px; background: var(--primary); color: #fff;">Kategori</div>
            <h2 id="qvModalName" style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">Nama Produk</h2>
            <div id="qvModalPrice" style="font-size: 1.6rem; font-weight: 800; color: var(--primary); margin-bottom: 12px;">Rp 0</div>
            <div id="qvModalStock" style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 16px; padding: 6px 12px; background: var(--bg-alt); border-radius: var(--radius-sm); display: inline-block;">
              <i class="fas fa-boxes"></i> Stok: 0
            </div>
            
            <div style="margin-top: 14px; border-top: 1px solid var(--border-subtle); padding-top: 14px;">
              <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--text-dim); margin-bottom: 6px;">Deskripsi Material & Spesifikasi</h4>
              <p id="qvModalDescription" style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.6;">Deskripsi...</p>
            </div>

            <div style="margin-top: 14px; font-size: 0.8rem; color: var(--text-dim);">
              <i class="far fa-calendar-alt"></i> Terdaftar sejak: <span id="qvModalDate">-</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-modal-cancel">Tutup</button>
        <a href="#" id="qvModalEditLink" class="btn btn-primary">
          <i class="fas fa-edit"></i> Edit Produk
        </a>
      </div>
    </div>
  </div>

  <!-- Global Secure Delete Confirmation Modal (POST + CSRF Verification) -->
  <div class="modal-backdrop" id="deleteConfirmModal" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-header">
        <h3 class="modal-title" style="color: var(--danger);">
          <i class="fas fa-exclamation-triangle"></i>
          <span>Konfirmasi Hapus Produk</span>
        </h3>
        <button type="button" class="modal-close-btn" aria-label="Tutup Modal">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <p style="font-size: 0.95rem; color: var(--text-main); margin-bottom: 12px;">
          Apakah Anda yakin ingin menghapus produk <strong id="deleteProductName" style="color: var(--danger);">-</strong>?
        </p>
        <p style="font-size: 0.85rem; color: var(--text-muted); background: var(--danger-bg); border: 1px solid var(--danger-border); padding: 12px; border-radius: var(--radius-sm);">
          <i class="fas fa-info-circle"></i> Tindakan ini bersifat permanen. Data produk yang dihapus tidak dapat dipulihkan kembali dari database.
        </p>

        <!-- Secure Form with CSRF Token and POST Method -->
        <form id="secureDeleteForm" action="delete.php" method="POST">
          <?= csrf_input(); ?>
          <input type="hidden" name="id" id="deleteProductId" value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-modal-cancel">Batal</button>
        <button type="submit" form="secureDeleteForm" class="btn btn-danger" id="confirmDeleteSubmitBtn">
          <i class="fas fa-trash-alt"></i> Ya, Hapus Sekarang
        </button>
      </div>
    </div>
  </div>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-inner">
        <div>
          <p style="font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
            VOGUEORA APPAREL &bull; Product Management System
          </p>
          <p style="font-size: 0.825rem;">
            Implementasi PHP Data Objects (PDO) &bull; MySQL &bull; Prepared Statements &bull; CSRF Protection &bull; PRG Pattern
          </p>
        </div>
        <div class="footer-badge">
          <i class="fas fa-shield-alt"></i> Secured with CSRF & Prepared Queries
        </div>
      </div>
    </div>
  </footer>

  <!-- Main JavaScript File -->
  <script src="assets/js/main.js"></script>
</body>
</html>
