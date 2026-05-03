-- ============================================================================
-- INSERT DATA DUMMY: tb_stok_cabang
-- Database: db_integrasi_ayu_mart
-- Tanggal: 14 Januari 2026
-- ============================================================================
--
-- Struktur:
-- - 20 produk (id_produk: 1-20)
-- - 6 cabang aktif (id_cabang: 1-6)
-- - Total: 120 records (20 x 6)
--
-- Stok Random:
-- - Produk populer: 30-100 unit
-- - Produk normal: 15-50 unit
-- - Produk slow moving: 5-20 unit
-- ============================================================================

USE db_integrasi_ayu_mart;

-- Hapus data lama jika ada
TRUNCATE TABLE tb_stok_cabang;

-- ============================================================================
-- CABANG 1: Ayu Mart - Jl. Cargo Kenanga (Denpasar Utara)
-- ============================================================================
INSERT INTO tb_stok_cabang (id_stok_cabang, id_produk, id_cabang, total_stok, stok_minimum, created_at, updated_at) VALUES
-- Produk populer (stok tinggi)
(1, 1, 1, 85, 30, NOW(), NOW()),  -- Beras Premium 5kg
(2, 2, 1, 92, 30, NOW(), NOW()),  -- Minyak Goreng Filma 2L
(3, 3, 1, 78, 25, NOW(), NOW()),  -- Gula Pasir Gulaku 1kg
(4, 4, 1, 65, 20, NOW(), NOW()),  -- Tepung Terigu Segitiga Biru 1kg
(5, 5, 1, 88, 30, NOW(), NOW()),  -- Susu Ultra 1L
-- Produk normal
(6, 6, 1, 45, 15, NOW(), NOW()),  -- Mie Instan Indomie (Isi 5)
(7, 7, 1, 52, 15, NOW(), NOW()),  -- Kopi Kapal Api 165g
(8, 8, 1, 38, 12, NOW(), NOW()),  -- Teh Celup Sariwangi 25s
(9, 9, 1, 42, 15, NOW(), NOW()),  -- Sabun Mandi Lifebuoy
(10, 10, 1, 35, 10, NOW(), NOW()), -- Shampo Clear 170ml
(11, 11, 1, 48, 15, NOW(), NOW()), -- Pasta Gigi Pepsodent 190g
(12, 12, 1, 55, 18, NOW(), NOW()), -- Detergen Rinso 900g
(13, 13, 1, 40, 12, NOW(), NOW()), -- Tissue Paseo 250s
(14, 14, 1, 32, 10, NOW(), NOW()), -- Sikat Gigi Formula
-- Produk slow moving
(15, 15, 1, 18, 8, NOW(), NOW()),  -- Vitamin C 1000mg
(16, 16, 1, 15, 5, NOW(), NOW()),  -- Madu Murni 500ml
(17, 17, 1, 12, 5, NOW(), NOW()),  -- Ikan Salmon 300g
(18, 18, 1, 20, 8, NOW(), NOW()),  -- Keju Cheddar 200g
(19, 19, 1, 14, 5, NOW(), NOW()),  -- Yogurt Plain 150ml
(20, 20, 1, 16, 6, NOW(), NOW());  -- Es Krim Vanilla 1L

-- ============================================================================
-- CABANG 2: Ayu Mart - Jl. Kebo Iwa (Denpasar Barat)
-- ============================================================================
INSERT INTO tb_stok_cabang (id_stok_cabang, id_produk, id_cabang, total_stok, stok_minimum, created_at, updated_at) VALUES
(21, 1, 2, 72, 30, NOW(), NOW()),
(22, 2, 2, 80, 30, NOW(), NOW()),
(23, 3, 2, 65, 25, NOW(), NOW()),
(24, 4, 2, 58, 20, NOW(), NOW()),
(25, 5, 2, 75, 30, NOW(), NOW()),
(26, 6, 2, 40, 15, NOW(), NOW()),
(27, 7, 2, 48, 15, NOW(), NOW()),
(28, 8, 2, 35, 12, NOW(), NOW()),
(29, 9, 2, 38, 15, NOW(), NOW()),
(30, 10, 2, 30, 10, NOW(), NOW()),
(31, 11, 2, 42, 15, NOW(), NOW()),
(32, 12, 2, 50, 18, NOW(), NOW()),
(33, 13, 2, 36, 12, NOW(), NOW()),
(34, 14, 2, 28, 10, NOW(), NOW()),
(35, 15, 2, 15, 8, NOW(), NOW()),
(36, 16, 2, 12, 5, NOW(), NOW()),
(37, 17, 2, 10, 5, NOW(), NOW()),
(38, 18, 2, 18, 8, NOW(), NOW()),
(39, 19, 2, 12, 5, NOW(), NOW()),
(40, 20, 2, 14, 6, NOW(), NOW());

-- ============================================================================
-- CABANG 3: Ayu Mart - Jl. Sunset Road (Kuta)
-- ============================================================================
INSERT INTO tb_stok_cabang (id_stok_cabang, id_produk, id_cabang, total_stok, stok_minimum, created_at, updated_at) VALUES
(41, 1, 3, 95, 30, NOW(), NOW()),
(42, 2, 3, 88, 30, NOW(), NOW()),
(43, 3, 3, 82, 25, NOW(), NOW()),
(44, 4, 3, 70, 20, NOW(), NOW()),
(45, 5, 3, 92, 30, NOW(), NOW()),
(46, 6, 3, 50, 15, NOW(), NOW()),
(47, 7, 3, 55, 15, NOW(), NOW()),
(48, 8, 3, 42, 12, NOW(), NOW()),
(49, 9, 3, 45, 15, NOW(), NOW()),
(50, 10, 3, 38, 10, NOW(), NOW()),
(51, 11, 3, 52, 15, NOW(), NOW()),
(52, 12, 3, 60, 18, NOW(), NOW()),
(53, 13, 3, 45, 12, NOW(), NOW()),
(54, 14, 3, 35, 10, NOW(), NOW()),
(55, 15, 3, 20, 8, NOW(), NOW()),
(56, 16, 3, 16, 5, NOW(), NOW()),
(57, 17, 3, 14, 5, NOW(), NOW()),
(58, 18, 3, 22, 8, NOW(), NOW()),
(59, 19, 3, 16, 5, NOW(), NOW()),
(60, 20, 3, 18, 6, NOW(), NOW());

-- ============================================================================
-- CABANG 4: Ayu Mart - Jl. Gatot Subroto (Denpasar Timur)
-- ============================================================================
INSERT INTO tb_stok_cabang (id_stok_cabang, id_produk, id_cabang, total_stok, stok_minimum, created_at, updated_at) VALUES
(61, 1, 4, 68, 30, NOW(), NOW()),
(62, 2, 4, 75, 30, NOW(), NOW()),
(63, 3, 4, 62, 25, NOW(), NOW()),
(64, 4, 4, 55, 20, NOW(), NOW()),
(65, 5, 4, 70, 30, NOW(), NOW()),
(66, 6, 4, 38, 15, NOW(), NOW()),
(67, 7, 4, 45, 15, NOW(), NOW()),
(68, 8, 4, 32, 12, NOW(), NOW()),
(69, 9, 4, 35, 15, NOW(), NOW()),
(70, 10, 4, 28, 10, NOW(), NOW()),
(71, 11, 4, 40, 15, NOW(), NOW()),
(72, 12, 4, 48, 18, NOW(), NOW()),
(73, 13, 4, 34, 12, NOW(), NOW()),
(74, 14, 4, 26, 10, NOW(), NOW()),
(75, 15, 4, 14, 8, NOW(), NOW()),
(76, 16, 4, 11, 5, NOW(), NOW()),
(77, 17, 4, 9, 5, NOW(), NOW()),
(78, 18, 4, 16, 8, NOW(), NOW()),
(79, 19, 4, 11, 5, NOW(), NOW()),
(80, 20, 4, 13, 6, NOW(), NOW());

-- ============================================================================
-- CABANG 5: Ayu Mart - Jl. Sanur (Denpasar Selatan)
-- ============================================================================
INSERT INTO tb_stok_cabang (id_stok_cabang, id_produk, id_cabang, total_stok, stok_minimum, created_at, updated_at) VALUES
(81, 1, 5, 90, 30, NOW(), NOW()),
(82, 2, 5, 85, 30, NOW(), NOW()),
(83, 3, 5, 78, 25, NOW(), NOW()),
(84, 4, 5, 65, 20, NOW(), NOW()),
(85, 5, 5, 88, 30, NOW(), NOW()),
(86, 6, 5, 48, 15, NOW(), NOW()),
(87, 7, 5, 52, 15, NOW(), NOW()),
(88, 8, 5, 40, 12, NOW(), NOW()),
(89, 9, 5, 42, 15, NOW(), NOW()),
(90, 10, 5, 35, 10, NOW(), NOW()),
(91, 11, 5, 50, 15, NOW(), NOW()),
(92, 12, 5, 58, 18, NOW(), NOW()),
(93, 13, 5, 42, 12, NOW(), NOW()),
(94, 14, 5, 32, 10, NOW(), NOW()),
(95, 15, 5, 18, 8, NOW(), NOW()),
(96, 16, 5, 14, 5, NOW(), NOW()),
(97, 17, 5, 12, 5, NOW(), NOW()),
(98, 18, 5, 20, 8, NOW(), NOW()),
(99, 19, 5, 14, 5, NOW(), NOW()),
(100, 20, 5, 16, 6, NOW(), NOW());

-- ============================================================================
-- CABANG 6: Ayu Mart - Jl. Bypass Ngurah Rai (Badung)
-- ============================================================================
INSERT INTO tb_stok_cabang (id_stok_cabang, id_produk, id_cabang, total_stok, stok_minimum, created_at, updated_at) VALUES
(101, 1, 6, 100, 30, NOW(), NOW()),
(102, 2, 6, 95, 30, NOW(), NOW()),
(103, 3, 6, 88, 25, NOW(), NOW()),
(104, 4, 6, 75, 20, NOW(), NOW()),
(105, 5, 6, 98, 30, NOW(), NOW()),
(106, 6, 6, 55, 15, NOW(), NOW()),
(107, 7, 6, 60, 15, NOW(), NOW()),
(108, 8, 6, 45, 12, NOW(), NOW()),
(109, 9, 6, 48, 15, NOW(), NOW()),
(110, 10, 6, 40, 10, NOW(), NOW()),
(111, 11, 6, 55, 15, NOW(), NOW()),
(112, 12, 6, 65, 18, NOW(), NOW()),
(113, 13, 6, 48, 12, NOW(), NOW()),
(114, 14, 6, 38, 10, NOW(), NOW()),
(115, 15, 6, 22, 8, NOW(), NOW()),
(116, 16, 6, 18, 5, NOW(), NOW()),
(117, 17, 6, 15, 5, NOW(), NOW()),
(118, 18, 6, 24, 8, NOW(), NOW()),
(119, 19, 6, 18, 5, NOW(), NOW()),
(120, 20, 6, 20, 6, NOW(), NOW());

-- ============================================================================
-- SUMMARY DATA DUMMY
-- ============================================================================
-- Total Records: 120 (20 produk x 6 cabang)
--
-- Distribusi Stok per Kategori:
-- - Produk Populer (ID 1-5): 65-100 unit per cabang
-- - Produk Normal (ID 6-14): 26-65 unit per cabang
-- - Produk Slow Moving (ID 15-20): 9-24 unit per cabang
--
-- Cabang dengan Stok Tertinggi: Cabang 6 (Bypass Ngurah Rai)
-- Cabang dengan Stok Terendah: Cabang 4 (Gatot Subroto)
-- ============================================================================

SELECT 'Data dummy tb_stok_cabang berhasil ditambahkan!' as status;
SELECT COUNT(*) as total_records FROM tb_stok_cabang;
