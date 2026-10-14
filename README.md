# 👕 VOGUEORA APPAREL &bull; Product Management System
> **Tugas Akhir: Mini Project #16 — Product Manager (Tema: Penjualan Pakaian & Fashion)**  
> Aplikasi Web Berbasis **PHP Data Objects (PDO)** & **MySQL** dengan Keamanan Input Dasar, Proteksi CSRF, Anti-Double Submit (PRG Pattern), Live Search & Filter, serta Antarmuka Responsif Modern.

---

## 📌 Daftar Isi
1. [Tentang Aplikasi](#-tentang-aplikasi)
2. [Fitur Utama & Pemenuhan Rubrik Tugas](#-fitur-utama--pemenuhan-rubrik-tugas)
3. [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
4. [Struktur Direktori Proyek](#-struktur-direktori-proyek)
5. [Panduan Instalasi & Penggunaan di XAMPP](#-panduan-instalasi--penggunaan-di-xampp)
6. [Dokumentasi & Tangkapan Layar Sistem (Screenshots)](#-dokumentasi--tangkapan-layar-sistem-screenshots)
7. [Panduan Penamaan File Foto Screenshot](#-panduan-penamaan-file-foto-screenshot)
8. [Keamanan & Arsitektur Kode](#-keamanan--arsitektur-kode)

---

## 📖 Tentang Aplikasi
**Vogueora Apparel Product Manager** adalah aplikasi web manajemen inventaris dan katalog pakaian yang dirancang khusus untuk toko busana modern. Aplikasi ini memungkinkan pengelola toko untuk memantau stok fisik baju, memperbarui harga, mengelompokkan pakaian berdasarkan kategori (Kaos, Kemeja, Jaket, Hoodie, Celana, Aksesoris), serta melakukan pencarian dan penyaringan data secara instan.

Sistem dibangun dengan standar keamanan tinggi dan pengalaman pengguna (UX) yang nyaman:
- Menggunakan **Prepared Statements PDO** untuk mencegah SQL Injection pada seluruh operasi CRUD.
- Menerapkan **Pola Post/Redirect/Get (PRG)** dengan session flash notification untuk mencegah data ganda saat pengguna me-refresh halaman web.
- Dilengkapi **Token CSRF (Cross-Site Request Forgery)** pada form penghapusan dan pengubahan data.
- Menjalankan **Escape Output XSS** menggunakan `htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8")`.
- Desain antarmuka responsif dengan dukungan **Mode Gelap (Dark Mode)** dan **Mode Terang (Light Mode)**.

---

## 🎯 Fitur Utama & Pemenuhan Rubrik Tugas

Sistem ini memenuhi seluruh kriteria dan spesifikasi wajib tugas akhir:

| Kriteria Tugas | Implementasi pada Sistem | Status |
| :--- | :--- | :---: |
| **Tema Pilihan** | Penjualan Baju & Fashion Apparel (Vogueora Collection) | ✅ Terpenuhi |
| **Create** | Formulir tambah baju lengkap: nama, kategori, harga, stok, foto, dan deskripsi | ✅ Terpenuhi |
| **Read** | Tampilan kartu responsif (*Responsive Card Grid*) & alternatif *Table View* dengan status stok | ✅ Terpenuhi |
| **Update** | Edit data pakaian berdasarkan ID (`edit.php?id=...`) dengan validasi ketat | ✅ Terpenuhi |
| **Delete** | Penghapusan data via metode **POST** disertai validasi **Token CSRF** | ✅ Terpenuhi |
| **Validasi Nama** | `nama >= 3` karakter & wajib unik (tidak boleh ada nama baju ganda di database) | ✅ Terpenuhi |
| **Validasi Harga** | `harga > 0` (harus angka positif valid) | ✅ Terpenuhi |
| **Validasi Stok** | `stok >= 0` (bilangan bulat non-negatif) | ✅ Terpenuhi |
| **Cegah Dobel Input** | Pola **PRG (Post/Redirect/Get)** menggunakan Session Flash Messages | ✅ Terpenuhi |
| **Syarat Query INSERT** | `$pdo->prepare("INSERT INTO products ...")` | ✅ Terpenuhi |
| **Syarat Query SELECT by ID** | `$pdo->prepare("SELECT * FROM products WHERE id = :id")` | ✅ Terpenuhi |
| **Syarat Query UPDATE** | `$pdo->prepare("UPDATE products SET ... WHERE id = :id")` | ✅ Terpenuhi |
| **Syarat Query DELETE** | `$pdo->prepare("DELETE FROM products WHERE id = :id")` | ✅ Terpenuhi |
| **Syarat Output XSS** | `htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8")` pada setiap output dinamis | ✅ Terpenuhi |
| **Fitur Pencarian & Filter** | Live Search instan + filter per kategori baju + filter status stok + pengurutan harga | ✅ Terpenuhi |

---

## 💻 Teknologi yang Digunakan
- **Backend:** PHP 8.x (PHP Data Objects / PDO Extension)
- **Database:** MySQL / MariaDB (Database Engine InnoDB)
- **Frontend Styling:** Custom Modern Vanilla CSS3 (CSS Variables, Flexbox, CSS Grid, Glassmorphism, Micro-animations)
- **Frontend Interactivity:** Vanilla JavaScript (ES6+), FileReader API, LocalStorage untuk Preferensi Tema
- **Tipografi & Ikon:** Google Fonts (*Plus Jakarta Sans*), Font Awesome 6 CDN
- **Server Lingkungan:** XAMPP (Apache & MySQL)

---

## 📁 Struktur Direktori Proyek

```text
mini_projectwnny/
├── assets/
│   ├── css/
│   │   └── style.css            # Desain kustom responsif, dark mode & animasi
│   ├── js/
│   │   └── main.js              # Script interaksi, live filter, modal & preview foto
│   ├── images/
│   │   ├── logo.svg             # Logo vektor resmi Vogueora Apparel
│   │   └── products/            # Gambar vektor cadangan koleksi pakaian
│   └── screenshots/             # Tempat penyimpanan screenshot untuk GitHub
│       └── README.md            # Panduan ringkas daftar nama foto
├── config/
│   └── database.php             # Koneksi PDO, proteksi CSRF, validasi & flash messages
├── database/
│   └── database.sql             # Skrip SQL lengkap (siap diimpor ke phpMyAdmin)
├── includes/
│   ├── header.php               # Navigasi atas, switch mode, dan alert flash
│   └── footer.php               # Footer, script imports, modal CSRF & quick view
├── uploads/                     # Direktori berkas foto pakaian yang diunggah
├── add.php                      # Halaman Tambah Baju (Create)
├── edit.php                     # Halaman Edit Baju berdasarkan ID (Update)
├── delete.php                   # Controller Hapus Baju (POST + CSRF)
├── index.php                    # Dashboard & Katalog Busana Utama (Read + Filter)
├── view.php                     # Halaman Rincian Spesifikasi Pakaian
├── test_suite.php               # Skrip verifikasi otomasi 11/11 kriteria tugas
└── README.md                    # Dokumentasi utama proyek GitHub
```

---

## 🚀 Panduan Instalasi & Penggunaan di XAMPP

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di komputer lokal:

### Langkah 1: Pastikan Folder Terletak di `htdocs`
Folder proyek ini harus berada di dalam direktori `htdocs` XAMPP Anda:
```text
C:\xampp\htdocs\mini_projectwnny
```

### Langkah 2: Nyalakan Layanan Apache & MySQL
1. Buka aplikasi **XAMPP Control Panel**.
2. Klik tombol **Start** pada modul **Apache**.
3. Klik tombol **Start** pada modul **MySQL**.
4. Pastikan kedua indikator modul berwarna hijau.

### Langkah 3: Impor Database MySQL melalui phpMyAdmin
1. Buka browser dan buka alamat: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Klik menu **Import** pada bilah navigasi atas.
3. Klik tombol **Choose File** (Pilih Berkas), lalu arahkan ke file:
   ```text
   C:\xampp\htdocs\mini_projectwnny\database\database.sql
   ```
4. Gulir ke bawah dan klik tombol **Import** (atau **Go**).
5. Database `db_cloth_store` beserta tabel `products` dan 12 data pakaian awal akan otomatis dibuat dan siap digunakan.

> **Tips:** Skrip SQL sudah dilengkapi perintah `CREATE DATABASE IF NOT EXISTS db_cloth_store;`, sehingga Anda tidak perlu membuat database kosong terlebih dahulu secara manual.

### Langkah 4: Buka Aplikasi Web di Browser
Buka tab baru pada peramban web Anda dan akses tautan berikut:
```text
http://localhost/mini_projectwnny
```

---

## 📸 Dokumentasi & Tangkapan Layar Sistem (Screenshots)

Berikut adalah tangkapan layar antarmuka sistem manajemen produk pakaian **Vogueora Apparel**:

### 1. Dashboard Utama & Katalog Busana (Read: Responsive Cards)
Tampilan katalog utama menampilkan ringkasan metrik statistik (Total Produk, Total Stok, Nilai Aset Stok, Perlu Restok), bilah pencarian cerdas, filter chip kategori, serta susunan kartu pakaian responsif.
![Dashboard Katalog Utama](assets/screenshots/01_dashboard_katalog.png)

---

### 2. Metode Pencarian & Filter Kategori (Search & Filtering)
Pencarian instan *live search* berdasarkan kata kunci nama busana, filter kategori baju (*Kaos, Kemeja, Hoodie, Jaket, Celana, Aksesoris*), dan pengurutan harga (*termurah/termahal*).
![Pencarian dan Filter](assets/screenshots/02_live_search_filter.png)

---

### 3. Formulir Tambah Produk Baju (Create Feature)
Formulir penambahan pakaian baru dengan input nama baju, kategori, harga nominal, stok fisik, deskripsi, dan upload foto produk dengan pratinjau instan (*live image preview*).
![Form Tambah Produk](assets/screenshots/03_tambah_produk.png)

---

### 4. Sistem Validasi & Pesan Kesalahan (Input Validation)
Pengujian validasi ketat sesuai ketentuan tugas: nama wajib minimal 3 karakter (`nama >= 3`), nama unik tidak boleh kembar, harga harus positif (`harga > 0`), dan stok tidak boleh negatif (`stok >= 0`).
![Validasi Form](assets/screenshots/04_validasi_error.png)

---

### 5. Formulir Edit Produk Berdasarkan ID (Update Feature)
Halaman pengubahan data pakaian yang diambil menggunakan query `$pdo->prepare("SELECT * FROM products WHERE id = :id")`, menjaga keunikan nama saat diperbarui.
![Edit Produk](assets/screenshots/05_edit_produk.png)

---

### 6. Modal Quick View / Rincian Produk (Modal Detail)
Popup modal modern untuk melihat detail spesifikasi busana, stok terkini, kategori, tanggal pencatatan, serta tombol navigasi cepat.
![Modal Detail Produk](assets/screenshots/06_modal_detail_produk.png)

---

### 7. Keamanan Hapus Data dengan Modal CSRF (Delete: POST + CSRF)
Operasi penghapusan data tidak menggunakan tautan GET langsung, melainkan formulir terenkapsulasi dengan metode **POST** dan verifikasi **Token CSRF** untuk menjamin keamanan dari serangan *Cross-Site Request Forgery*.
![Konfirmasi Hapus CSRF](assets/screenshots/08_konfirmasi_hapus_csrf.png)

---

### 8. Tampilan Alternatif Tabel (Table View Switcher)
Pengguna dapat beralih antara tampilan grid kartu (*Card Grid*) dan tampilan tabel ringkas (*Data Table*) hanya dengan satu klik.
![Tampilan Tabel](assets/screenshots/07_tampilan_tabel.png)

---

### 9. Dukungan Mode Gelap (Dark Mode Aesthetic)
Antarmuka elegan dengan tema gelap (*Dark Mode*) untuk kenyamanan visual saat mengelola katalog di malam hari.
![Mode Gelap](assets/screenshots/09_dark_mode.png)

---

## 🏷️ Panduan Penamaan File Foto Screenshot
Untuk mengisi foto di atas sebelum melakukan `git commit` ke GitHub, silakan ambil tangkapan layar sistem Anda di browser dan simpan di dalam folder **`assets/screenshots/`** dengan nama file berikut:

| No | Nama File yang Wajib Di-rename | Halaman / Bagian yang Ditangkap Layar |
| :---: | :--- | :--- |
| 1 | `01_dashboard_katalog.png` | Buka `http://localhost/mini_projectwnny/index.php` (Tampilan utama katalog kartu produk). |
| 2 | `02_live_search_filter.png` | Ketik kata kunci di kolom pencarian atau klik salah satu chip kategori busana. |
| 3 | `03_tambah_produk.png` | Buka `http://localhost/mini_projectwnny/add.php` (Formulir tambah produk pakaian). |
| 4 | `04_validasi_error.png` | Kosongkan salah satu input wajib atau isi nama < 3 karakter lalu klik simpan untuk memunculkan pesan merah validasi. |
| 5 | `05_edit_produk.png` | Buka `http://localhost/mini_projectwnny/edit.php?id=1` (Formulir edit baju). |
| 6 | `06_modal_detail_produk.png` | Klik tombol **"Detail"** pada salah satu kartu baju untuk membuka modal popup. |
| 7 | `07_tampilan_tabel.png` | Klik tombol **"Tabel"** pada switcher tampilan di kanan atas katalog. |
| 8 | `08_konfirmasi_hapus_csrf.png` | Klik ikon tempat sampah merah pada produk untuk memunculkan dialog konfirmasi hapus. |
| 9 | `09_dark_mode.png` | Klik ikon bulan/matahari di navbar untuk mengaktifkan tema gelap. |

> **Catatan:** Setelah gambar disimpan di `assets/screenshots/`, seluruh foto akan langsung tampil secara otomatis pada halaman README di GitHub Anda!

---

## 🔒 Keamanan & Arsitektur Kode

### 1. Pencegahan SQL Injection (Prepared Statements)
Seluruh query yang melibatkan parameter eksternal diproses menggunakan PDO Prepared Statements:
```php
// SELECT by ID
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);

// INSERT
$stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, description, image) VALUES (:name, :category, :price, :stock, :description, :image)");
$stmt->execute([...]);

// UPDATE
$stmt = $pdo->prepare("UPDATE products SET name = :name, category = :category, price = :price, stock = :stock WHERE id = :id");
$stmt->execute([...]);

// DELETE
$stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
```

### 2. Pencegahan Serangan CSRF (Cross-Site Request Forgery)
Setiap form yang memodifikasi data menyertakan token acak kriptografis yang diverifikasi menggunakan `hash_equals()`:
```php
// Validasi token pada controller delete.php
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die("Akses ditolak: Token CSRF tidak valid.");
}
```

### 3. Pencegahan Dobel Submit / Dobel Data Saat Refresh (Pola PRG)
Setelah proses Create, Update, atau Delete berhasil, aplikasi tidak langsung merender tampilan, melainkan melakukan pengalihan HTTP (*302 Redirect*) ke halaman GET disertai pesan notifikasi di sesi:
```php
// Simpan notifikasi ke session
set_flash('success', 'Produk baju berhasil ditambahkan!');

// Redirect HTTP GET (PRG Pattern)
header('Location: index.php');
exit;
```

### 4. Pencegahan XSS (Cross-Site Scripting)
Sesuai syarat eksplisit rubrik tugas akhir:
```php
echo htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8");
```

---

## 👨‍💻 Kontributor & Lisensi
- **Tema Proyek:** Manajemen Produk Toko Busana (*Clothing / Apparel Product Manager*)
- **Mata Kuliah / Modul:** Mini Project Web Development #16
- **Lisensi:** Open-source untuk keperluan edukasi dan penilaian akademik.
