<?php
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/../config/database.php';
}

$page_title = isset($page_title) ? $page_title . ' • Vogueora Apparel' : 'Vogueora Apparel • Product Manager';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Aplikasi Manajemen Produk Toko Baju & Fashion - Mini Project PHP MySQL PDO dengan Validasi, CSRF & PRG Pattern">
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Font Awesome 6 Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- Custom CSS Design System -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="shortcut icon" href="assets/images/logo.svg" type="image/svg+xml">
</head>
<body>

  <!-- Site Header & Sticky Navbar -->
  <header class="site-header">
    <div class="container">
      <nav class="navbar">
        <!-- Brand Logo -->
        <a href="index.php" class="brand-link" title="Beranda Katalog">
          <img src="assets/images/logo.svg" alt="Vogueora Logo" height="42">
        </a>

        <!-- Navigation Actions -->
        <div class="nav-actions">
          <span class="nav-badge-tag d-none-mobile">
            <i class="fas fa-cube"></i> Mini Project #16
          </span>

          <!-- Dark/Light Theme Toggle -->
          <button type="button" id="themeToggleBtn" class="btn-icon" title="Ganti Tema (Dark/Light)" aria-label="Toggle Theme">
            <i class="fas fa-moon"></i>
          </button>

          <!-- Add Product Action Button -->
          <a href="add.php" class="btn btn-primary" id="navAddProductBtn">
            <i class="fas fa-plus"></i>
            <span>Tambah Baju</span>
          </a>
        </div>
      </nav>
    </div>
  </header>

  <!-- Flash Messages (PRG Pattern Notifications) -->
  <?php 
  $flash = get_flash(); 
  if ($flash): 
    $iconMap = [
        'success' => 'fa-check-circle',
        'danger'  => 'fa-times-circle',
        'warning' => 'fa-exclamation-triangle',
        'info'    => 'fa-info-circle'
    ];
    $icon = $iconMap[$flash['type']] ?? 'fa-info-circle';
  ?>
  <div class="container alert-container">
    <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8'); ?>">
      <div class="alert-content">
        <i class="alert-icon fas <?= $icon; ?>"></i>
        <span><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
      <button type="button" class="alert-close" aria-label="Tutup"><i class="fas fa-times"></i></button>
    </div>
  </div>
  <?php endif; ?>
