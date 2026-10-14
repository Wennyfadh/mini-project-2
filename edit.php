<?php
/**
 * Edit Produk Baju (Update Feature)
 * Mini Project: Product Manager - Penjualan Baju
 * SELECT by ID -> $pdo->prepare(...) + UPDATE -> $pdo->prepare(...) + Validasi + PRG
 */

require_once __DIR__ . '/config/database.php';

// 1. Ambil ID dari URL atau POST
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($id <= 0) {
    set_flash('danger', 'ID produk tidak valid.');
    header('Location: index.php');
    exit;
}

// 2. Syarat Query Rubrik: SELECT by ID -> $pdo->prepare(...)
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('danger', 'Produk pakaian dengan ID #' . $id . ' tidak ditemukan dalam database.');
    header('Location: index.php');
    exit;
}

$page_title = 'Edit Produk: ' . $product['name'];
$errors = [];

// Nilai awal dari database
$name = $product['name'];
$category = $product['category'];
$price = $product['price'];
$stock = $product['stock'];
$description = $product['description'];
$imageName = $product['image'];

// 3. Proses Formulir UPDATE (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifikasi Token Keamanan CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Validasi token CSRF gagal. Silakan muat ulang formulir.');
        header('Location: edit.php?id=' . $id);
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validasi Wajib Rubrik (nama >= 3, harga > 0, stok >= 0, nama unik kecuali dirinya sendiri)
    $errors = validate_product($pdo, $name, $category, $price, $stock, $id);

    // Proses Penggantian Foto (Opsional)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $fileInfo = pathinfo($_FILES['image']['name']);
        $ext = strtolower($fileInfo['extension'] ?? '');

        if (!in_array($ext, $allowedExts)) {
            $errors['image'] = 'Format file gambar harus berupa JPG, PNG, WEBP, atau SVG.';
        } elseif ($_FILES['image']['size'] > 3 * 1024 * 1024) {
            $errors['image'] = 'Ukuran file gambar maksimal adalah 3 MB.';
        } else {
            $newImageName = 'cloth_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destination = __DIR__ . '/uploads/' . $newImageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                // Hapus gambar lama jika merupakan custom upload (bukan gambar default)
                if (!empty($product['image']) && strpos($product['image'], 'cloth_') === 0) {
                    $oldFilePath = __DIR__ . '/uploads/' . $product['image'];
                    if (file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }
                $imageName = $newImageName;
            } else {
                $errors['image'] = 'Gagal memindahkan file foto yang baru.';
            }
        }
    }

    // 4. Jika validasi lolos, jalankan UPDATE dengan PDO prepare
    if (empty($errors)) {
        // Syarat Query Rubrik: UPDATE -> $pdo->prepare(...)
        $updateStmt = $pdo->prepare("
            UPDATE products 
            SET name = :name, 
                category = :category, 
                price = :price, 
                stock = :stock, 
                description = :description, 
                image = :image 
            WHERE id = :id
        ");

        $updateStmt->execute([
            ':name'        => $name,
            ':category'    => $category,
            ':price'       => (float)$price,
            ':stock'       => (int)$stock,
            ':description' => $description,
            ':image'       => $imageName,
            ':id'          => $id
        ]);

        // 5. PRG Pattern (Post/Redirect/Get) dengan Session Flash
        set_flash('success', 'Data produk "' . e($name) . '" berhasil diperbarui!');
        header('Location: index.php');
        exit;
    }
}

// Resolve preview gambar
$currentImagePath = 'uploads/' . $imageName;
if (!file_exists(__DIR__ . '/' . $currentImagePath) || empty($imageName)) {
    $currentImagePath = 'assets/images/products/default_cloth.svg';
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
        <i class="fas fa-edit" style="color: #0284C7;"></i> Edit Data Produk Pakaian
      </h1>
      <p class="form-subtitle">
        Memperbarui informasi katalog untuk <strong>#<?= (int)$product['id']; ?> - <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></strong>.
      </p>
    </div>

    <!-- Tampilkan Error jika ada -->
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

    <!-- Form Edit Produk dengan POST & CSRF -->
    <form action="edit.php?id=<?= (int)$id; ?>" method="POST" enctype="multipart/form-data" id="productForm" novalidate>
      <!-- Syarat Keamanan CSRF Token & Hidden ID -->
      <?= csrf_input(); ?>
      <input type="hidden" name="id" value="<?= (int)$id; ?>">

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
            <span>Foto Produk Pakaian Saat Ini & Penggantian</span>
            <span class="label-hint">Format: JPG, PNG, WEBP (Maks 3MB)</span>
          </label>
          <div class="image-preview-container">
            <img id="imagePreviewThumb" src="<?= htmlspecialchars($currentImagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto Produk" class="preview-thumb">
            <div class="upload-btn-wrap">
              <input type="file" name="image" id="imageFileInput" accept="image/*" class="input-control" style="background: var(--bg-surface);">
              <p style="font-size: 0.775rem; color: var(--text-dim); margin-top: 6px;">
                Biarkan kosong jika tidak ingin mengubah foto produk pakaian saat ini.
              </p>
            </div>
          </div>
          <?php if (isset($errors['image'])): ?>
            <span class="error-feedback"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['image'], ENT_QUOTES, 'UTF-8'); ?></span>
          <?php endif; ?>
        </div>

        <!-- 6. Deskripsi Pakaian -->
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
          ><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="form-footer-actions">
        <a href="index.php" class="btn btn-secondary">
          Batal
        </a>
        <button type="submit" class="btn btn-primary btn-lg" id="submitProductBtn">
          <i class="fas fa-save"></i> Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
