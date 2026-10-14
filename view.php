<?php
/**
 * Detail Spesifikasi Produk Pakaian (View Feature)
 * Mini Project: Product Manager - Penjualan Baju
 * SELECT by ID -> $pdo->prepare(...)
 */

require_once __DIR__ . '/config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    set_flash('danger', 'ID produk tidak valid.');
    header('Location: index.php');
    exit;
}

// Syarat Query Rubrik: SELECT by ID -> $pdo->prepare(...)
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('danger', 'Produk pakaian tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$page_title = $product['name'] . ' • Detail Produk';

$badge = get_stock_badge($product['stock']);
$imagePath = 'uploads/' . $product['image'];
if (!file_exists(__DIR__ . '/' . $imagePath) || empty($product['image'])) {
    $imagePath = 'assets/images/products/default_cloth.svg';
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="container" style="padding-top: 30px; padding-bottom: 50px;">
  <div style="margin-bottom: 20px;">
    <a href="index.php" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; color: var(--text-muted);">
      <i class="fas fa-arrow-left"></i> Kembali ke Katalog Produk
    </a>
  </div>

  <div class="form-card" style="max-width: 960px; margin: 0 auto;">
    <div class="quick-view-grid" style="grid-template-columns: 1fr 1.2fr; gap: 36px;">
      <!-- Gambar Produk -->
      <div>
        <div style="border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-md); aspect-ratio: 4/3; background: var(--bg-alt);">
          <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
      </div>

      <!-- Informasi Produk -->
      <div>
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
          <span class="category-chip active" style="font-size: 0.8rem;">
            <?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?>
          </span>
          <span class="badge-stock <?= $badge['class']; ?>" style="position: static;">
            <i class="fas <?= $badge['icon']; ?>"></i> <?= $badge['label']; ?>
          </span>
        </div>

        <!-- Syarat Rubrik: htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8") -->
        <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 14px; line-height: 1.3;">
          <?= htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8"); ?>
        </h1>

        <div style="font-size: 2rem; font-weight: 800; color: var(--primary); margin-bottom: 20px;">
          <?= format_rupiah($product['price']); ?>
        </div>

        <div style="background: var(--bg-alt); padding: 18px; border-radius: var(--radius-md); margin-bottom: 22px; border: 1px solid var(--border-subtle);">
          <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--text-dim); margin-bottom: 8px; letter-spacing: 0.5px;">
            Deskripsi & Karakteristik Busana
          </h4>
          <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.7; white-space: pre-line;">
            <?= !empty($product['description']) ? htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') : 'Tidak ada catatan deskripsi tambahan untuk produk ini.'; ?>
          </p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 28px;">
          <div><i class="fas fa-barcode"></i> SKU / ID Produk: <strong>#<?= (int)$product['id']; ?></strong></div>
          <div><i class="fas fa-boxes"></i> Jumlah Stok: <strong><?= (int)$product['stock']; ?> pcs</strong></div>
          <div><i class="far fa-calendar-check"></i> Ditambahkan pada: <?= date('d F Y, H:i', strtotime($product['created_at'])); ?></div>
          <div><i class="fas fa-sync-alt"></i> Pembaruan terakhir: <?= date('d F Y, H:i', strtotime($product['updated_at'])); ?></div>
        </div>

        <div style="display: flex; gap: 12px; border-top: 1px solid var(--border-subtle); padding-top: 20px;">
          <a href="edit.php?id=<?= (int)$product['id']; ?>" class="btn btn-primary">
            <i class="fas fa-pen"></i> Edit Produk
          </a>
          <button 
            type="button" 
            class="btn btn-danger btn-trigger-delete" 
            data-id="<?= (int)$product['id']; ?>" 
            data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
          >
            <i class="fas fa-trash-alt"></i> Hapus Produk
          </button>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
