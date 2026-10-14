<?php
/**
 * Hapus Produk Pakaian (Delete Feature)
 * Mini Project: Product Manager - Penjualan Baju
 * Syarat Rubrik: POST + CSRF & DELETE -> $pdo->prepare(...)
 */

require_once __DIR__ . '/config/database.php';

// 1. Validasi Metode HTTP: Hanya Izinkan POST (Tolak Akses Langsung via URL GET)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    set_flash('danger', 'Akses ditolak: Operasi hapus produk wajib menggunakan metode POST.');
    header('Location: index.php');
    exit;
}

// 2. Syarat Rubrik: Verifikasi Token Keamanan CSRF
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    set_flash('danger', 'Keamanan terpicu: Validasi token CSRF gagal. Tindakan penghapusan dibatalkan.');
    header('Location: index.php');
    exit;
}

// 3. Validasi ID Produk
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    set_flash('danger', 'ID produk yang akan dihapus tidak valid.');
    header('Location: index.php');
    exit;
}

// 4. Cari Produk Terlebih Dahulu (Untuk Logging Pesan Flash & Penanganan Berkas)
$checkStmt = $pdo->prepare("SELECT name, image FROM products WHERE id = :id");
$checkStmt->execute([':id' => $id]);
$product = $checkStmt->fetch();

if (!$product) {
    set_flash('warning', 'Produk pakaian tersebut tidak ditemukan atau telah dihapus sebelumnya.');
    header('Location: index.php');
    exit;
}

// 5. Syarat Query Rubrik: DELETE -> $pdo->prepare(...)
$deleteStmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
$deleteStmt->execute([':id' => $id]);

// Hapus berkas foto lokal jika merupakan hasil upload khusus pengguna
if (!empty($product['image']) && strpos($product['image'], 'cloth_') === 0) {
    $filePath = __DIR__ . '/uploads/' . $product['image'];
    if (file_exists($filePath)) {
        @unlink($filePath);
    }
}

// 6. PRG Pattern (Post/Redirect/Get) dengan Session Flash Notification
set_flash('success', 'Produk busana "' . e($product['name']) . '" berhasil dihapus dari sistem inventaris.');
header('Location: index.php');
exit;
