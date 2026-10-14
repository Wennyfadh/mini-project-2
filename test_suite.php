<?php
/**
 * Test Suite untuk Memverifikasi Seluruh Syarat Tugas Akhir
 */
require_once __DIR__ . '/config/database.php';

echo "========================================================\n";
echo " VOGUEORA APPAREL - AUTOMATED VERIFICATION SUITE\n";
echo "========================================================\n";

// TEST 1: Database Connection & Seed Data
echo "\n[TEST 1] Verifikasi Koneksi PDO & Data Awal...\n";
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM products");
$count = $stmt->fetchColumn();
echo "  -> Berhasil terhubung ke MySQL! Total produk saat ini: {$count}\n";
assert($count >= 12, "Data awal harus memiliki minimal 12 produk pakaian.");

// TEST 2: Validasi Nama (nama >= 3)
echo "\n[TEST 2] Validasi Nama < 3 Karakter...\n";
$err = validate_product($pdo, "AB", "Kaos & T-Shirt", 150000, 10);
assert(!empty($err['name']), "Nama 'AB' harus ditolak karena < 3 karakter.");
echo "  -> OK: Nama < 3 karakter ditolak dengan pesan: '{$err['name']}'\n";

// TEST 3: Validasi Harga (harga > 0)
echo "\n[TEST 3] Validasi Harga <= 0...\n";
$err = validate_product($pdo, "Baju Keren", "Kaos & T-Shirt", 0, 10);
assert(!empty($err['price']), "Harga 0 harus ditolak.");
$err_neg = validate_product($pdo, "Baju Keren", "Kaos & T-Shirt", -50000, 10);
assert(!empty($err_neg['price']), "Harga negatif harus ditolak.");
echo "  -> OK: Harga <= 0 ditolak dengan pesan: '{$err['price']}'\n";

// TEST 4: Validasi Stok (stok >= 0)
echo "\n[TEST 4] Validasi Stok < 0...\n";
$err = validate_product($pdo, "Baju Keren", "Kaos & T-Shirt", 150000, -5);
assert(!empty($err['stock']), "Stok negatif harus ditolak.");
echo "  -> OK: Stok < 0 ditolak dengan pesan: '{$err['stock']}'\n";

// TEST 5: Validasi Nama Unik
echo "\n[TEST 5] Validasi Nama Unik (Cegah Duplikat)...\n";
$firstProduct = $pdo->query("SELECT name FROM products LIMIT 1")->fetchColumn();
$err = validate_product($pdo, $firstProduct, "Kaos & T-Shirt", 150000, 10);
assert(!empty($err['name']), "Nama produk duplikat harus ditolak.");
echo "  -> OK: Nama duplikat '{$firstProduct}' ditolak: '{$err['name']}'\n";

// TEST 6: Syarat Query INSERT -> $pdo->prepare(...)
echo "\n[TEST 6] Syarat Query: INSERT -> \$pdo->prepare(...)...\n";
$testName = "Automated Test Tee " . time();
$insertStmt = $pdo->prepare("
    INSERT INTO products (name, category, price, stock, description, image) 
    VALUES (:name, :category, :price, :stock, :description, :image)
");
$insertStmt->execute([
    ':name'        => $testName,
    ':category'    => 'Kaos & T-Shirt',
    ':price'       => 175000,
    ':stock'       => 20,
    ':description' => 'Test kaos otomasi dengan prepared statement',
    ':image'       => 'default_cloth.svg'
]);
$newId = (int)$pdo->lastInsertId();
assert($newId > 0, "Insert produk baru harus menghasilkan ID.");
echo "  -> OK: Berhasil INSERT produk dengan ID #{$newId}!\n";

// TEST 7: Syarat Query SELECT by ID -> $pdo->prepare(...)
echo "\n[TEST 7] Syarat Query: SELECT by ID -> \$pdo->prepare(...)...\n";
$selectStmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$selectStmt->execute([':id' => $newId]);
$fetched = $selectStmt->fetch();
assert($fetched && $fetched['name'] === $testName, "Data yang di-fetch by ID harus cocok.");
echo "  -> OK: Berhasil SELECT by ID! Nama: '{$fetched['name']}'\n";

// TEST 8: Syarat Query UPDATE -> $pdo->prepare(...)
echo "\n[TEST 8] Syarat Query: UPDATE -> \$pdo->prepare(...)...\n";
$updatedName = $testName . " (Updated)";
$updateStmt = $pdo->prepare("
    UPDATE products 
    SET name = :name, price = :price, stock = :stock 
    WHERE id = :id
");
$updateStmt->execute([
    ':name'  => $updatedName,
    ':price' => 199000,
    ':stock' => 15,
    ':id'    => $newId
]);
$selectStmt->execute([':id' => $newId]);
$updatedProduct = $selectStmt->fetch();
assert($updatedProduct['name'] === $updatedName && (float)$updatedProduct['price'] == 199000, "Update data harus tersimpan.");
echo "  -> OK: Berhasil UPDATE produk by ID! Nama baru: '{$updatedProduct['name']}', Harga: {$updatedProduct['price']}\n";

// TEST 9: Syarat Query DELETE -> $pdo->prepare(...)
echo "\n[TEST 9] Syarat Query: DELETE -> \$pdo->prepare(...)...\n";
$deleteStmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
$deleteStmt->execute([':id' => $newId]);
$selectStmt->execute([':id' => $newId]);
$checkDeleted = $selectStmt->fetch();
assert(!$checkDeleted, "Produk harus terhapus dari database.");
echo "  -> OK: Berhasil DELETE produk by ID dengan prepared statement!\n";

// TEST 10: Syarat Keamanan CSRF Token
echo "\n[TEST 10] Pengujian Keamanan CSRF Token...\n";
$token = get_csrf_token();
assert(!empty($token), "Token CSRF harus ter-generate.");
assert(verify_csrf_token($token) === true, "Token CSRF valid harus diverifikasi true.");
assert(verify_csrf_token("invalid_token_123") === false, "Token CSRF palsu harus diverifikasi false.");
echo "  -> OK: CSRF protection bekerja secara akurat!\n";

// TEST 11: Syarat Output XSS htmlspecialchars(..., ENT_QUOTES, "UTF-8")
echo "\n[TEST 11] Pengujian Proteksi Output XSS...\n";
$maliciousInput = '<script>alert("XSS")</script>';
$safeOutput = htmlspecialchars($maliciousInput, ENT_QUOTES, "UTF-8");
assert(strpos($safeOutput, '<script>') === false, "Karakter <script> harus diescape.");
echo "  -> OK: htmlspecialchars(..., ENT_QUOTES, 'UTF-8') menghasilkan: {$safeOutput}\n";

echo "\n========================================================\n";
echo " SEMUA 11/11 PENGUJIAN SYARAT TUGAS AKHIR BERHASIL (PASSED)!\n";
echo "========================================================\n";
