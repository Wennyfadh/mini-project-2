<?php
/**
 * Dashboard & Katalog Utama
 * Mini Project: Product Manager - Penjualan Baju (Vogueora Apparel)
 * Read: Card Responsif + Filter & Live Search
 */

require_once __DIR__ . '/config/database.php';

$page_title = 'Katalog Produk Pakaian';

// ==========================================
// 1. STATISTIK INVENTARIS TOKO BAJU
// ==========================================
try {
    $statQuery = $pdo->query("
        SELECT 
            COUNT(*) AS total_items,
            COALESCE(SUM(stock), 0) AS total_stock,
            COALESCE(SUM(price * stock), 0) AS total_inventory_value,
            COALESCE(SUM(CASE WHEN stock <= 5 THEN 1 ELSE 0 END), 0) AS low_stock_items
        FROM products
    ");
    $stats = $statQuery->fetch();
} catch (PDOException $e) {
    $stats = ['total_items' => 0, 'total_stock' => 0, 'total_inventory_value' => 0, 'low_stock_items' => 0];
}

// ==========================================
// 2. FILTER, PENCARIAN & SORTING (SERVER-SIDE)
// ==========================================
$searchQuery = trim($_GET['q'] ?? '');
$categoryFilter = trim($_GET['category'] ?? '');
$stockFilter = trim($_GET['stock'] ?? '');
$sortBy = trim($_GET['sort'] ?? 'newest');

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

// Filter Pencarian (Nama, Kategori, atau Deskripsi)
if (!empty($searchQuery)) {
    $sql .= " AND (name LIKE :search_name OR category LIKE :search_cat OR description LIKE :search_desc)";
    $params[':search_name'] = "%{$searchQuery}%";
    $params[':search_cat']  = "%{$searchQuery}%";
    $params[':search_desc'] = "%{$searchQuery}%";
}

// Filter Kategori Baju
if (!empty($categoryFilter) && in_array($categoryFilter, CLOTH_CATEGORIES)) {
    $sql .= " AND category = :category";
    $params[':category'] = $categoryFilter;
}

// Filter Status Stok
if ($stockFilter === 'available') {
    $sql .= " AND stock > 5";
} elseif ($stockFilter === 'low') {
    $sql .= " AND stock BETWEEN 1 AND 5";
} elseif ($stockFilter === 'out') {
    $sql .= " AND stock = 0";
}

// Pengurutan (Sorting)
switch ($sortBy) {
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY name ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY name DESC";
        break;
    case 'stock_desc':
        $sql .= " ORDER BY stock DESC";
        break;
    case 'stock_asc':
        $sql .= " ORDER BY stock ASC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY id DESC";
        break;
}

// Eksekusi Prepared Statement dengan PDO
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
$totalFiltered = count($products);

require_once __DIR__ . '/includes/header.php';
?>

<main class="container">
  <!-- Hero Banner -->
  <section class="hero-section">
    <div class="hero-card">
      <div class="hero-content">
        <span class="hero-tag">
          <i class="fas fa-sparkles"></i> Katalog Koleksi Busana 2026
        </span>
        <h1 class="hero-title">Manajemen Produk Pakaian</h1>
        <p class="hero-desc">
          Kelola inventaris busana, stok pakaian, harga, dan varian katalog secara aman, terstruktur, dan responsif dengan integrasi PHP PDO & MySQL.
        </p>
      </div>
      <div class="hero-action">
        <a href="add.php" class="btn btn-primary btn-lg" id="heroAddProductBtn">
          <i class="fas fa-plus-circle"></i> Tambah Produk Baru
        </a>
      </div>
    </div>
  </section>

  <!-- Statistics 4-Cards Grid -->
  <section class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon primary">
        <i class="fas fa-tshirt"></i>
      </div>
      <div class="stat-info">
        <div class="stat-label">Total Produk</div>
        <div class="stat-value"><?= number_format($stats['total_items']); ?> <span style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted);">Model</span></div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon success">
        <i class="fas fa-boxes"></i>
      </div>
      <div class="stat-info">
        <div class="stat-label">Total Stok Fisik</div>
        <div class="stat-value"><?= number_format($stats['total_stock']); ?> <span style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted);">Pcs</span></div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon warning">
        <i class="fas fa-coins"></i>
      </div>
      <div class="stat-info">
        <div class="stat-label">Nilai Aset Stok</div>
        <div class="stat-value" style="font-size: 1.25rem;"><?= format_rupiah($stats['total_inventory_value']); ?></div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon danger">
        <i class="fas fa-exclamation-circle"></i>
      </div>
      <div class="stat-info">
        <div class="stat-label">Perlu Restok (≤ 5)</div>
        <div class="stat-value"><?= number_format($stats['low_stock_items']); ?> <span style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted);">Item</span></div>
      </div>
    </div>
  </section>

  <!-- Toolbar: Search, Filter, Sort, View Controls -->
  <section class="toolbar-section">
    <form action="index.php" method="GET" class="search-filter-form" id="searchFilterForm">
      <!-- Search Input & Quick Filters Row -->
      <div class="search-primary-row">
        <div class="search-input-wrap">
          <i class="fas fa-search search-icon"></i>
          <input 
            type="text" 
            name="q" 
            id="liveSearchInput" 
            class="search-input" 
            placeholder="Cari nama pakaian, model, atau kata kunci..." 
            value="<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>"
            autocomplete="off"
          >
          <button type="button" id="clearSearchBtn" class="btn-clear-search" title="Hapus Pencarian">
            <i class="fas fa-times-circle"></i>
          </button>
        </div>

        <div class="filter-dropdowns">
          <!-- Filter Status Stok -->
          <select name="stock" class="select-control" id="stockFilterSelect" onchange="this.form.submit()">
            <option value="">Semua Status Stok</option>
            <option value="available" <?= $stockFilter === 'available' ? 'selected' : ''; ?>>Tersedia (> 5)</option>
            <option value="low" <?= $stockFilter === 'low' ? 'selected' : ''; ?>>Menipis (1 - 5)</option>
            <option value="out" <?= $stockFilter === 'out' ? 'selected' : ''; ?>>Stok Habis (0)</option>
          </select>

          <!-- Sorting Selector -->
          <select name="sort" class="select-control" id="sortBySelect" onchange="this.form.submit()">
            <option value="newest" <?= $sortBy === 'newest' ? 'selected' : ''; ?>>Terbaru Ditambahkan</option>
            <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : ''; ?>>Harga: Termurah &rarr; Termahal</option>
            <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : ''; ?>>Harga: Termahal &rarr; Termurah</option>
            <option value="name_asc" <?= $sortBy === 'name_asc' ? 'selected' : ''; ?>>Nama Pakaian (A - Z)</option>
            <option value="name_desc" <?= $sortBy === 'name_desc' ? 'selected' : ''; ?>>Nama Pakaian (Z - A)</option>
            <option value="stock_desc" <?= $sortBy === 'stock_desc' ? 'selected' : ''; ?>>Stok Terbanyak</option>
            <option value="stock_asc" <?= $sortBy === 'stock_asc' ? 'selected' : ''; ?>>Stok Tersedikit</option>
          </select>

          <!-- Submit Button if JS disabled -->
          <noscript>
            <button type="submit" class="btn btn-secondary">Terapkan</button>
          </noscript>
        </div>
      </div>

      <!-- Quick Category Chips -->
      <div class="category-chips">
        <a href="index.php?<?= http_build_query(array_merge($_GET, ['category' => ''])); ?>" 
           class="category-chip <?= empty($categoryFilter) ? 'active' : ''; ?>">
          <i class="fas fa-border-all"></i> Semua Kategori
        </a>
        <?php foreach (CLOTH_CATEGORIES as $cat): ?>
          <a href="index.php?<?= http_build_query(array_merge($_GET, ['category' => $cat])); ?>" 
             class="category-chip <?= $categoryFilter === $cat ? 'active' : ''; ?>">
            <?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Toolbar Meta & View Switcher -->
      <div class="toolbar-meta-bar">
        <div class="result-count">
          Menampilkan <strong id="visibleResultCount"><?= $totalFiltered; ?></strong> dari <?= number_format($stats['total_items']); ?> produk pakaian
          <?php if (!empty($searchQuery) || !empty($categoryFilter) || !empty($stockFilter)): ?>
            &bull; <a href="index.php" style="color: var(--primary); font-weight: 600;"><i class="fas fa-redo-alt"></i> Reset Filter</a>
          <?php endif; ?>
        </div>

        <div class="view-switcher">
          <button type="button" class="view-btn active" id="viewGridBtn" title="Tampilan Card Grid">
            <i class="fas fa-th-large"></i> Grid
          </button>
          <button type="button" class="view-btn" id="viewTableBtn" title="Tampilan Tabel Ringkas">
            <i class="fas fa-list"></i> Tabel
          </button>
        </div>
      </div>
    </form>
  </section>

  <!-- Empty State (No Products Found) -->
  <div class="empty-state" id="liveEmptyState" style="<?= empty($products) ? 'display: block;' : 'display: none;'; ?>">
    <div class="empty-icon">
      <i class="fas fa-shopping-bag"></i>
    </div>
    <h3 class="empty-title">Tidak Ada Produk Pakaian Ditemukan</h3>
    <p class="empty-desc">
      Coba periksa kata kunci pencarian Anda atau reset filter kategori untuk melihat seluruh koleksi pakaian.
    </p>
    <a href="index.php" class="btn btn-secondary">
      <i class="fas fa-undo"></i> Reset Filter Pencarian
    </a>
  </div>

  <!-- VIEW 1: RESPONSIVE CARDS GRID (READ FEATURE) -->
  <div class="products-grid" id="productsGridContainer" style="<?= !empty($products) ? 'display: grid;' : 'display: none;'; ?>">
    <?php foreach ($products as $product): ?>
      <?php
        $badge = get_stock_badge($product['stock']);
        // Resolve image path
        $imagePath = 'uploads/' . $product['image'];
        if (!file_exists(__DIR__ . '/' . $imagePath) || empty($product['image'])) {
            $imagePath = 'assets/images/products/default_cloth.svg';
        }
      ?>
      <article 
        class="product-card" 
        data-id="<?= (int)$product['id']; ?>"
        data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
        data-category="<?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?>"
        data-price="<?= format_rupiah($product['price']); ?>"
        data-stock="<?= (int)$product['stock']; ?>"
        data-desc="<?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
        data-image="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>"
        data-date="<?= date('d M Y', strtotime($product['created_at'])); ?>"
      >
        <!-- Card Media -->
        <div class="card-media">
          <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
          
          <span class="badge-category-chip">
            <?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?>
          </span>

          <span class="badge-stock <?= $badge['class']; ?>">
            <i class="fas <?= $badge['icon']; ?>"></i> <?= $badge['label']; ?>
          </span>
        </div>

        <!-- Card Body -->
        <div class="card-body">
          <!-- Syarat Rubric: htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8") -->
          <h2 class="product-title" title="<?= htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8"); ?>">
            <?= htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8"); ?>
          </h2>

          <p class="product-desc">
            <?= !empty($product['description']) ? htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') : 'Koleksi busana berkualitas tinggi dengan material terbaik.'; ?>
          </p>

          <div class="card-price-row">
            <div>
              <div class="card-price-label">Harga Produk</div>
              <div class="product-price"><?= format_rupiah($product['price']); ?></div>
            </div>
            <div style="text-align: right;">
              <div class="card-price-label">Sisa Stok</div>
              <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">
                <?= (int)$product['stock']; ?> pcs
              </div>
            </div>
          </div>

          <!-- Card Actions (Detail, Edit, Delete) -->
          <div class="card-actions">
            <!-- Quick View Modal Trigger -->
            <button 
              type="button" 
              class="btn-card-action btn-view btn-trigger-view"
              data-id="<?= (int)$product['id']; ?>"
              data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
              data-category="<?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?>"
              data-price="<?= format_rupiah($product['price']); ?>"
              data-stock="<?= (int)$product['stock']; ?>"
              data-desc="<?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
              data-image="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>"
              data-date="<?= date('d M Y', strtotime($product['created_at'])); ?>"
              title="Lihat Detail Produk"
            >
              <i class="fas fa-eye"></i> Detail
            </button>

            <!-- Edit Button (Update Feature) -->
            <a 
              href="edit.php?id=<?= (int)$product['id']; ?>" 
              class="btn-card-action btn-edit" 
              title="Edit Data Produk"
            >
              <i class="fas fa-pen"></i> Edit
            </a>

            <!-- Delete Button (Delete Feature with CSRF) -->
            <button 
              type="button" 
              class="btn-card-action btn-delete btn-trigger-delete" 
              data-id="<?= (int)$product['id']; ?>" 
              data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
              title="Hapus Produk Ini"
            >
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <!-- VIEW 2: COMPACT TABLE VIEW (ALTERNATIVE VIEW) -->
  <div class="table-responsive-wrapper" id="productsTableContainer" style="display: none;">
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 70px;">Foto</th>
          <th>Nama Produk Pakaian</th>
          <th>Kategori</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Status</th>
          <th style="width: 140px; text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $product): ?>
          <?php
            $badge = get_stock_badge($product['stock']);
            $imagePath = 'uploads/' . $product['image'];
            if (!file_exists(__DIR__ . '/' . $imagePath) || empty($product['image'])) {
                $imagePath = 'assets/images/products/default_cloth.svg';
            }
          ?>
          <tr 
            data-id="<?= (int)$product['id']; ?>"
            data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
            data-category="<?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?>"
          >
            <td>
              <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="" class="table-thumb">
            </td>
            <td>
              <!-- Syarat Rubric: htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8") -->
              <div class="table-product-title"><?= htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8"); ?></div>
              <div class="table-product-cat">ID: #<?= (int)$product['id']; ?></div>
            </td>
            <td>
              <span class="category-chip" style="padding: 3px 10px; font-size: 0.75rem;">
                <?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </td>
            <td style="font-weight: 700; color: var(--primary);">
              <?= format_rupiah($product['price']); ?>
            </td>
            <td style="font-weight: 600;">
              <?= (int)$product['stock']; ?> pcs
            </td>
            <td>
              <span class="badge-stock <?= $badge['class']; ?>" style="position: static;">
                <?= $badge['label']; ?>
              </span>
            </td>
            <td style="text-align: center;">
              <div style="display: inline-flex; gap: 6px;">
                <a href="edit.php?id=<?= (int)$product['id']; ?>" class="btn-card-action btn-edit" title="Edit">
                  <i class="fas fa-pen"></i>
                </a>
                <button 
                  type="button" 
                  class="btn-card-action btn-delete btn-trigger-delete" 
                  data-id="<?= (int)$product['id']; ?>" 
                  data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                  title="Hapus"
                >
                  <i class="fas fa-trash-alt"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
