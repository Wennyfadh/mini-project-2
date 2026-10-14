<?php
/**
 * Konfigurasi Database & Helper Functions
 * Mini Project: Product Manager - Toko Baju & Fashion
 * PDO (PHP Data Objects) + CSRF + Flash Messages + Sanitization
 */

// Pastikan session sudah berjalan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi Database MySQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_cloth_store');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Daftar Kategori Baju & Fashion Resmi
const CLOTH_CATEGORIES = [
    'Kaos & T-Shirt',
    'Kemeja & Flannel',
    'Hoodie & Sweater',
    'Jaket & Outerwear',
    'Celana & Jeans',
    'Aksesoris Fashion'
];

// Inisialisasi Koneksi PDO
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Tampilan ramah pengguna jika database belum diimpor atau MySQL mati
    die("
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 60px auto; padding: 30px; border-radius: 12px; background: #fff5f5; border: 1px solid #fed7d7; color: #9b2c2c;'>
            <h2 style='margin-top: 0;'>⚠️ Gagal Terhubung ke Database MySQL</h2>
            <p>Pastikan layanan <strong>MySQL di XAMPP Control Panel</strong> sudah dalam keadaan <strong>Start / Running</strong>, dan database <strong>`db_cloth_store`</strong> sudah diimpor.</p>
            <p><strong>Pesan Kesalahan:</strong> " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>
            <p>Silakan impor file <code style='background: #fff; padding: 2px 6px; border-radius: 4px;'>database/database.sql</code> melalui <strong>phpMyAdmin</strong>.</p>
        </div>
    ");
}

/**
 * ========================================================
 * KEAMANAN: CSRF (Cross-Site Request Forgery) TOKEN
 * ========================================================
 */

// Generate atau ambil token CSRF dari session
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Generate input hidden untuk form
function csrf_input() {
    $token = get_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

// Verifikasi token CSRF dari form POST
function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * ========================================================
 * KEAMANAN: OUTPUT ESCAPING (XSS PREVENTION)
 * ========================================================
 */
function e($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, "UTF-8");
}

/**
 * ========================================================
 * SESSION FLASH MESSAGES (PRG PATTERN: POST-REDIRECT-GET)
 * Menghindari data ganda saat pengguna me-refresh halaman
 * ========================================================
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, danger, warning, info
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * ========================================================
 * FORMATTER & UI HELPERS
 * ========================================================
 */

// Format angka ke format mata uang Rupiah (IDR)
function format_rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

// Status stok pakaian
function get_stock_badge($stock) {
    $stock = (int)$stock;
    if ($stock <= 0) {
        return [
            'class' => 'badge-stock-out',
            'label' => 'Habis',
            'icon' => 'fa-times-circle'
        ];
    } elseif ($stock <= 5) {
        return [
            'class' => 'badge-stock-low',
            'label' => 'Menipis (' . $stock . ')',
            'icon' => 'fa-exclamation-triangle'
        ];
    } else {
        return [
            'class' => 'badge-stock-available',
            'label' => 'Tersedia (' . $stock . ')',
            'icon' => 'fa-check-circle'
        ];
    }
}

// Icon dan styling warna per kategori pakaian
function get_category_meta($category) {
    switch ($category) {
        case 'Kaos & T-Shirt':
            return ['badge' => 'cat-tshirt', 'icon' => 'fa-tshirt'];
        case 'Kemeja & Flannel':
            return ['badge' => 'cat-shirt', 'icon' => 'fa-vest'];
        case 'Hoodie & Sweater':
            return ['badge' => 'cat-hoodie', 'icon' => 'fa-mitten'];
        case 'Jaket & Outerwear':
            return ['badge' => 'cat-jacket', 'icon' => 'fa-user-astronaut'];
        case 'Celana & Jeans':
            return ['badge' => 'cat-pants', 'icon' => 'fa-socks'];
        case 'Aksesoris Fashion':
            return ['badge' => 'cat-acc', 'icon' => 'fa-hat-cowboy'];
        default:
            return ['badge' => 'cat-default', 'icon' => 'fa-tag'];
    }
}

/**
 * ========================================================
 * VALIDASI PRODUK (SYARAT WAJIB TUGAS AKHIR)
 * - nama >= 3 karakter
 * - harga > 0
 * - stok >= 0
 * - nama unik (dicek terhadap database dengan PDO)
 * ========================================================
 */
function validate_product($pdo, $name, $category, $price, $stock, $current_id = null) {
    $errors = [];

    // Validasi Nama Produk (Panjang minimal 3 karakter)
    $clean_name = trim($name);
    if (empty($clean_name)) {
        $errors['name'] = 'Nama produk wajib diisi.';
    } elseif (mb_strlen($clean_name) < 3) {
        $errors['name'] = 'Nama produk minimal harus terdiri dari 3 karakter.';
    } elseif (mb_strlen($clean_name) > 150) {
        $errors['name'] = 'Nama produk tidak boleh melebihi 150 karakter.';
    } else {
        // Cek Keunikan Nama Produk (Nama Unik)
        if ($current_id !== null) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE LOWER(name) = LOWER(:name) AND id != :id");
            $stmt->execute([':name' => $clean_name, ':id' => $current_id]);
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE LOWER(name) = LOWER(:name)");
            $stmt->execute([':name' => $clean_name]);
        }

        if ($stmt->fetchColumn() > 0) {
            $errors['name'] = 'Nama produk sudah digunakan. Silakan gunakan nama produk yang unik.';
        }
    }

    // Validasi Kategori
    if (empty($category) || !in_array($category, CLOTH_CATEGORIES)) {
        $errors['category'] = 'Pilih salah satu kategori pakaian yang valid.';
    }

    // Validasi Harga (harga > 0)
    if (!is_numeric($price) || (float)$price <= 0) {
        $errors['price'] = 'Harga harus berupa angka nominal dan lebih besar dari 0 (harga > 0).';
    }

    // Validasi Stok (stok >= 0 dan integer)
    if (!is_numeric($stock) || (int)$stock < 0 || (int)$stock != (float)$stock) {
        $errors['stock'] = 'Stok harus berupa bilangan bulat non-negatif (stok ≥ 0).';
    }

    return $errors;
}
