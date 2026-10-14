<?php
/**
 * Tambah Produk Baju Baru (Create Feature)
 * Mini Project: Product Manager - Penjualan Baju
 * INSERT -> $pdo->prepare(...) + Validasi + PRG Pattern + CSRF
 */

require_once __DIR__ . '/config/database.php';

$page_title = 'Tambah Produk Pakaian Baru';

$errors = [];
$name = '';
$category = '';
$price = '';
$stock = '';
$description = '';

// Proses Form POST (Menghindari Data Ganda dengan PRG Pattern)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verifikasi Token Keamanan CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Validasi token CSRF gagal. Silakan muat ulang halaman dan coba kembali.');
        header('Location: add.php');
        exit;
    }

    // 2. Ambil dan bersihkan input
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // 3. Validasi Wajib Rubrik (nama >= 3, harga > 0, stok >= 0, nama unik)
    $errors = validate_product($pdo, $name, $category, $price, $stock);

    // 4. Proses Upload Foto Produk (Opsional, ada default fallback)
    $imageName = 'default_cloth.svg';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $fileInfo = pathinfo($_FILES['image']['name']);
        $ext = strtolower($fileInfo['extension'] ?? '');

        if (!in_array($ext, $allowedExts)) {
            $errors['image'] = 'Format file gambar harus berupa JPG, PNG, WEBP, atau SVG.';
        } elseif ($_FILES['image']['size'] > 3 * 1024 * 1024) {
            $errors['image'] = 'Ukuran file gambar maksimal adalah 3 MB.';
        } else {
            // Beri nama unik untuk file upload
            $imageName = 'cloth_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destination = __DIR__ . '/uploads/' . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $errors['image'] = 'Gagal memindahkan file yang diunggah ke direktori server.';
            }
        }
    }

    // 5. Jika lolos seluruh validasi, jalankan INSERT query dengan PDO prepare
    if (empty($errors)) {
        // Syarat Query Rubrik: INSERT -> $pdo->prepare(...)
        $stmt = $pdo->prepare("
            INSERT INTO products (name, category, price, stock, description, image) 
            VALUES (:name, :category, :price, :stock, :description, :image)
        ");

        $stmt->execute([
            ':name'        => $name,
            ':category'    => $category,
            ':price'       => (float)$price,
            ':stock'       => (int)$stock,
            ':description' => $description,
            ':image'       => $imageName
        ]);

        // 6. PRG Pattern (Post/Redirect/Get) dengan Session Flash
        set_flash('success', 'Produk baju "' . e($name) . '" berhasil ditambahkan ke katalog!');
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="container">
  <div class="form-card">
    <div class="form-header">
      <a href="index.php" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; font-weight: 600; margin-bottom: 12px; color: var(--text-muted);">
        <i class="fas fa-arrow-left"></i> Kembali ke Katalog
      </a>
      <h1 class="form-title">
        <i class="fas fa-plus-circle" style="color: var(--primary);"></i> Tambah Produk Pakaian
      </h1>
      <p class="form-subtitle">
        Lengkapi formulir di bawah ini untuk menambahkan varian baju atau fashion baru ke dalam inventaris.
      </p>
    </div>

    <!-- Tampilkan Ringkasan Error jika ada -->
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger" style="margin-bottom: 24px;">
        <div class="alert-content">
          <i class="alert-icon fas fa-exclamation-triangle"></i>
          <div>
            <strong>Mohon perbaiki kesalahan berikut:</strong>
            <ul style="margin: 6px 0 0 18px; font-size: 0.875rem;">
              <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Form Tambah Produk dengan POST & CSRF -->
    <form action="add.php" method="POST" enctype="multipart/form-data" id="productForm" novalidate>
      <!-- Syarat Keamanan CSRF Token -->
      <?= csrf_input(); ?>

      <div class="form-grid">
        <!-- 1. Nama Produk (nama >= 3, nama unik) -->
        <div class="form-group col-span-2">
          <label for="name" class="form-label">
            <span>Nama Produk Pakaian <span class="required-star">*</span></span>
            <span class="label-hint">Syarat: Minimal 3 karakter & harus unik</span>
          </label>
          <input 
            type="text" 
            name="name" 
            id="name" 
            class="input-control <?= isset($errors['name']) ? 'is-invalid' : ''; ?>" 
            placeholder="Contoh: Heavyweight Boxy T-Shirt Vintage Black"
            value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
            required
            minlength="3"
            maxlength="150"
          >
          <?php if (isset($errors['name'])): ?>
            <span class="error-feedback"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></span>
          <?php endif; ?>
        </div>

        <!-- 2. Kategori Pakaian -->
        <div class="form-group">
          <label for="category" class="form-label">
            <span>Kategori Busana <span class="required-star">*</span></span>
          </label>
          <select 
            name="category" 
            id="category" 
            class="select-control input-control <?= isset($errors['category']) ? 'is-invalid' : ''; ?>" 
            required
          >
            <option value="">-- Pilih Kategori Pakaian --</option>
            <?php foreach (CLOTH_CATEGORIES as $cat): ?>
              <option value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>" <?= $category === $cat ? 'selected' : ''; ?>>
                <?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['category'])): ?>
            <span class="error-feedback"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['category'], ENT_QUOTES, 'UTF-8'); ?></span>
          <?php endif; ?>
        </div>

        <!-- 3. Stok Produk (stok >= 0) -->
        <div class="form-group">
          <label for="stock" class="form-label">
            <span>Jumlah Stok Fisik <span class="required-star">*</span></span>
            <span class="label-hint">Syarat: Stok &ge; 0</span>
          </label>
          <div class="input-group-addon <?= isset($errors['stock']) ? 'is-invalid' : ''; ?>">
            <span class="addon-text"><i class="fas fa-boxes"></i></span>
            <input 
              type="number" 
              name="stock" 
              id="stock" 
              class="input-control" 
              placeholder="0"
              value="<?= htmlspecialchars($stock, ENT_QUOTES, 'UTF-8'); ?>"
              min="0"
              step="1"
              required
            >
          </div>
          <?php if (isset($errors['stock'])): ?>
            <span class="error-feedback"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['stock'], ENT_QUOTES, 'UTF-8'); ?></span>
          <?php endif; ?>
        </div>

        <!-- 4. Harga Produk (harga > 0) -->
        <div class="form-group col-span-2">
          <label for="price" class="form-label">
            <span>Harga Produk (Rupiah) <span class="required-star">*</span></span>
            <span class="label-hint">Syarat: Harga nominal &gt; 0</span>
          </label>
          <div class="input-group-addon <?= isset($errors['price']) ? 'is-invalid' : ''; ?>">
            <span class="addon-text">Rp</span>
            <input 
              type="number" 
              name="price" 
              id="price" 
              class="input-control" 
              placeholder="Contoh: 185000"
              value="<?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>"
              min="1"
              step="any"
              required
            >
          </div>
          <?php if (isset($errors['price'])): ?>
            <span class="error-feedback"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['price'], ENT_QUOTES, 'UTF-8'); ?></span>
          <?php endif; ?>
        </div>

        <!-- 5. Foto / Gambar Produk -->
        <div class="form-group col-span-2">
          <label class="form-label">
            <span>Foto Produk Pakaian</span>
            <span class="label-hint">Format: JPG, PNG, WEBP (Maks 3MB)</span>
          </label>
          <div class="image-preview-container">
            <img id="imagePreviewThumb" src="assets/images/products/default_cloth.svg" alt="Preview Gambar" class="preview-thumb">
            <div class="upload-btn-wrap">
              <input type="file" name="image" id="imageFileInput" accept="image/*" class="input-control" style="background: var(--bg-surface);">
              <p style="font-size: 0.775rem; color: var(--text-dim); margin-top: 6px;">
                Jika tidak memilih file, sistem otomatis menerapkan gambar template busana elegan secara default.
              </p>
            </div>
          </div>
          <?php if (isset($errors['image'])): ?>
            <span class="error-feedback"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['image'], ENT_QUOTES, 'UTF-8'); ?></span>
          <?php endif; ?>
        </div>

        <!-- 6. Deskripsi Pakaian (Opsional) -->
        <div class="form-group col-span-2">
          <label for="description" class="form-label">
            <span>Deskripsi & Spesifikasi Produk</span>
            <span class="label-hint">Bahan, gramasi kain, siluet fitting, dll.</span>
          </label>
          <textarea 
            name="description" 
            id="description" 
            rows="4" 
            class="textarea-control" 
            placeholder="Jelaskan karakteristik material, petunjuk perawatan, ukuran fitting, dan keunggulan pakaian..."
          ><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="form-footer-actions">
        <a href="index.php" class="btn btn-secondary">
          Batal
        </a>
        <button type="submit" class="btn btn-primary btn-lg" id="submitProductBtn">
          <i class="fas fa-save"></i> Simpan Produk Baju
        </button>
      </div>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
