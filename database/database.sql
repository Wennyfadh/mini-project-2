-- =====================================================
-- DATABASE: db_cloth_store
-- Tugas Akhir: Product Manager (Tema: Penjualan Baju)
-- Mini Project PHP - MySQL (PDO)
-- =====================================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS `db_cloth_store` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `db_cloth_store`;

-- -----------------------------------------------------
-- Struktur Tabel `products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL UNIQUE,
  `category` VARCHAR(50) NOT NULL,
  `price` DECIMAL(12, 2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `description` TEXT NULL,
  `image` VARCHAR(255) DEFAULT 'default_cloth.svg',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Data Awal (Sample Clothing Products)
-- -----------------------------------------------------
INSERT INTO `products` (`name`, `category`, `price`, `stock`, `description`, `image`) VALUES
(
  'Oversized Heavyweight Cotton T-Shirt',
  'Kaos & T-Shirt',
  149000.00,
  24,
  'Kaos oversized dengan bahan 100% cotton combed 20s premium gramasi tinggi 220 GSM. Potongan boxy modern, kerah rib tebal anti melar, nyaman dipakai seharian.',
  'tshirt_oversize.jpg'
),
(
  'Oxford Slim-Fit Long Sleeve Shirt',
  'Kemeja & Flannel',
  229000.00,
  15,
  'Kemeja lengan panjang bahan Oxford cotton woven kualitas ekspor. Desain button-down collar yang rapi dan elegan, cocok untuk gaya formal maupun casual smart.',
  'shirt_oxford.jpg'
),
(
  'Vintage Wash Denim Trucker Jacket',
  'Jaket & Outerwear',
  389000.00,
  8,
  'Jaket denim trucker klasik dengan finishing vintage stone-wash. Menggunakan material denim katun 13.5 oz durable dengan kancing kuningan timbul.',
  'jacket_denim.jpg'
),
(
  'Essential Pullover Fleece Hoodie',
  'Hoodie & Sweater',
  279000.00,
  12,
  'Hoodie pullover bahan cotton fleece 330 GSM dengan tekstur dalam lembut dan hangat. Kantong model kangguru dan tali serut round tip metal.',
  'hoodie_fleece.jpg'
),
(
  'Relaxed Pleated Chino Pants',
  'Celana & Jeans',
  249000.00,
  18,
  'Celana chino model loose-fit dengan aksen lipit depan (pleated). Bahan cotton twill elastis yang fleksibel dan breathable untuk mobilitas tinggi.',
  'pants_chino.jpg'
),
(
  'Minimalist Corduroy Overshirt Khaki',
  'Kemeja & Flannel',
  269000.00,
  4,
  'Overshirt bahan corduroy 16 wale bertekstur lembut. Dapat dikenakan sebagai kemeja santai maupun jaket luaran (layering outfit).',
  'shirt_corduroy.jpg'
),
(
  'Heavy Knitted Crewneck Sweater',
  'Hoodie & Sweater',
  310000.00,
  6,
  'Sweater rajut rajut jacquard tebal dengan siluet santai. Menghangatkan tanpa terasa gatal, jahitan rapi pada rib leher dan pergelangan.',
  'sweater_knit.jpg'
),
(
  'Techwear Windbreaker Waterproof Jacket',
  'Jaket & Outerwear',
  345000.00,
  3,
  'Jaket windbreaker tahan angin dan cipratan air dengan resleting waterproof. Dilengkapi ventilasi tersembunyi dan saku multifungsi.',
  'jacket_windbreaker.jpg'
),
(
  'Japanese Raw Selvedge Denim 14oz',
  'Celana & Jeans',
  450000.00,
  0,
  'Celana jeans denim selvedge asli dengan aksen red-line ID. Bahan raw kaku yang akan membentuk karakter fade unik seiring pemakaian.',
  'jeans_selvedge.jpg'
),
(
  'Acid Wash Vintage Graphic Tee',
  'Kaos & T-Shirt',
  165000.00,
  30,
  'T-Shirt vintage dengan teknik acid wash aesthetic dan sablon plastisol high-density. Potongan relaxed fit bergaya streetwear 90s.',
  'tshirt_acid.jpg'
),
(
  'Classic Heavy Canvas Daily Tote Bag',
  'Aksesoris Fashion',
  89000.00,
  25,
  'Tas jinjing kanvas tebal 14oz dengan kompartemen laptop 14 inch dan resleting YKK utama. Jahitan bartack penguat di setiap sudut beban.',
  'totebag_canvas.jpg'
),
(
  'Ribbed Fisherman Beanie Hat',
  'Aksesoris Fashion',
  75000.00,
  10,
  'Kupluk rajut model pendek (fisherman style) dengan benang acrylic wol lembut. Elastis, tidak mudah kendor, dan cocok untuk segala bentuk kepala.',
  'beanie_hat.jpg'
);