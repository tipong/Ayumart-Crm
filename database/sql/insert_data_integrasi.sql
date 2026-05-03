-- Insert Data Dummy untuk Database db_integrasi_ayu_mart
-- Tanggal: 14 Januari 2026

USE db_integrasi_ayu_mart;

-- 1. Insert Data Detail Cabang (sesuai dengan data di CRM)
INSERT INTO tb_detail_cabang (id_detail_cabang, nama_cabang, alamat) VALUES
(1, 'Ayu Mart - Jl. Cargo Kenanga', 'Jl. Cargo Kenanga, Ubung Kaja'),
(2, 'Ayu Mart - Jl. Gunung Guntur', 'Jl. Gunung Guntur Gang Taman Sari 1 No.7B, Padang Sambian Kelod'),
(3, 'Ayu Mart - Jl. Kubu Gn', 'Jl. Kubu Gn. No.103, Dalung'),
(4, 'Ayu Mart - Jl. Kebo Iwa', 'Jl. Kebo Iwa Selatan Padangsambian'),
(5, 'Ayu Mart - Jl. Karya Makmur', 'Jl. Karya Makmur, No.03 Kargo, Ubung Kaja');

-- 2. Insert Data Jenis Produk
INSERT INTO tb_jenis (id_jenis, nama_jenis, deskripsi_jenis) VALUES
(1, 'Makanan & Minuman', 'Produk makanan dan minuman'),
(2, 'Sembako', 'Kebutuhan pokok sehari-hari'),
(3, 'Peralatan Rumah Tangga', 'Alat-alat keperluan rumah tangga'),
(4, 'Elektronik', 'Peralatan elektronik'),
(5, 'Kesehatan & Kecantikan', 'Produk kesehatan dan kecantikan'),
(6, 'Perlengkapan Bayi', 'Produk untuk bayi dan anak'),
(7, 'Snack & Camilan', 'Makanan ringan dan camilan'),
(8, 'Bumbu Dapur', 'Bumbu dan rempah-rempah'),
(9, 'Minuman Kemasan', 'Minuman dalam kemasan'),
(10, 'Produk Susu', 'Susu dan produk turunannya');

-- 3. Insert Data Produk
INSERT INTO tb_produk (id_produk, kode_produk, nama_produk, deskripsi_produk, id_jenis, harga_produk, harga_diskon, tanggal_mulai_diskon, tanggal_akhir_diskon, is_diskon_active, berat_produk, foto_produk, status_produk, satuan, harga_beli) VALUES
-- Sembako
(1, 'PRD001', 'Beras Premium 5kg', 'Beras berkualitas premium untuk keluarga', 2, 75000, 70000, '2026-01-01', '2026-01-31', 1, 5000, 'beras-premium.jpg', 'aktif', 'kg', 65000),
(2, 'PRD002', 'Minyak Goreng 2L', 'Minyak goreng berkualitas', 2, 35000, NULL, NULL, NULL, 0, 2000, 'minyak-goreng.jpg', 'aktif', 'liter', 30000),
(3, 'PRD003', 'Gula Pasir 1kg', 'Gula pasir putih bersih', 2, 15000, NULL, NULL, NULL, 0, 1000, 'gula-pasir.jpg', 'aktif', 'kg', 12000),
(4, 'PRD004', 'Telur Ayam (10 butir)', 'Telur ayam segar', 2, 25000, 23000, '2026-01-10', '2026-01-20', 1, 600, 'telur-ayam.jpg', 'aktif', 'pack', 20000),
(5, 'PRD005', 'Tepung Terigu 1kg', 'Tepung terigu serbaguna', 2, 12000, NULL, NULL, NULL, 0, 1000, 'tepung-terigu.jpg', 'aktif', 'kg', 9000),

-- Minuman Kemasan
(6, 'PRD006', 'Air Mineral 600ml (1 dus)', 'Air mineral kemasan 24 botol', 9, 35000, NULL, NULL, NULL, 0, 15000, 'air-mineral.jpg', 'aktif', 'dus', 28000),
(7, 'PRD007', 'Teh Botol Sosro (1 dus)', 'Teh botol kemasan 24 botol', 9, 48000, 45000, '2026-01-01', '2026-02-28', 1, 12000, 'teh-botol.jpg', 'aktif', 'dus', 40000),
(8, 'PRD008', 'Coca Cola 1.5L', 'Minuman bersoda Coca Cola', 9, 12000, NULL, NULL, NULL, 0, 1500, 'coca-cola.jpg', 'aktif', 'botol', 9000),

-- Snack & Camilan
(9, 'PRD009', 'Chitato 68gr', 'Keripik kentang rasa original', 7, 10000, NULL, NULL, NULL, 0, 68, 'chitato.jpg', 'aktif', 'pcs', 7500),
(10, 'PRD010', 'Oreo Original 137gr', 'Biskuit sandwich coklat', 7, 12500, NULL, NULL, NULL, 0, 137, 'oreo.jpg', 'aktif', 'pcs', 9500),
(11, 'PRD011', 'Indomie Goreng (1 dus)', 'Mie instan goreng 40 bungkus', 1, 120000, 115000, '2026-01-05', '2026-01-25', 1, 3000, 'indomie.jpg', 'aktif', 'dus', 100000),

-- Produk Susu
(12, 'PRD012', 'Susu UHT Indomilk 1L', 'Susu UHT full cream', 10, 18000, NULL, NULL, NULL, 0, 1000, 'susu-indomilk.jpg', 'aktif', 'pcs', 15000),
(13, 'PRD013', 'Susu Dancow 800gr', 'Susu bubuk untuk keluarga', 10, 75000, 72000, '2026-01-01', '2026-01-31', 1, 800, 'susu-dancow.jpg', 'aktif', 'box', 65000),

-- Bumbu Dapur
(14, 'PRD014', 'Royco Ayam (1 box)', 'Penyedap rasa ayam 100 sachet', 8, 45000, NULL, NULL, NULL, 0, 800, 'royco.jpg', 'aktif', 'box', 38000),
(15, 'PRD015', 'Kecap Bango 220ml', 'Kecap manis berkualitas', 8, 15000, NULL, NULL, NULL, 0, 350, 'kecap-bango.jpg', 'aktif', 'botol', 12000),

-- Peralatan Rumah Tangga
(16, 'PRD016', 'Sapu Lantai', 'Sapu lidi untuk lantai', 3, 15000, NULL, NULL, NULL, 0, 300, 'sapu.jpg', 'aktif', 'pcs', 10000),
(17, 'PRD017', 'Pel Lantai + Tongkat', 'Pel kain microfiber dengan tongkat', 3, 35000, 32000, '2026-01-10', '2026-01-30', 1, 800, 'pel.jpg', 'aktif', 'set', 25000),

-- Kesehatan & Kecantikan
(18, 'PRD018', 'Sabun Mandi Lifebuoy', 'Sabun mandi batangan', 5, 5000, NULL, NULL, NULL, 0, 85, 'sabun-lifebuoy.jpg', 'aktif', 'pcs', 3500),
(19, 'PRD019', 'Pasta Gigi Pepsodent 190gr', 'Pasta gigi keluarga', 5, 12000, NULL, NULL, NULL, 0, 190, 'pasta-gigi.jpg', 'aktif', 'pcs', 9000),
(20, 'PRD020', 'Shampo Pantene 170ml', 'Shampo anti ketombe', 5, 18000, 16000, '2026-01-01', '2026-01-31', 1, 170, 'shampo-pantene.jpg', 'aktif', 'pcs', 13000);

-- 4. Insert Data Stok Cabang
-- Cabang 1: Jl. Cargo Kenanga
INSERT INTO tb_stok_cabang (id_stok_cabang, id_produk, id_detail_cabang, total_stok, stok_minimum) VALUES
(1, 1, 1, 150, 20),
(2, 2, 1, 200, 30),
(3, 3, 1, 180, 25),
(4, 4, 1, 100, 15),
(5, 5, 1, 120, 20),
(6, 6, 1, 80, 10),
(7, 7, 1, 90, 12),
(8, 8, 1, 150, 20),
(9, 9, 1, 200, 30),
(10, 10, 1, 180, 25),
(11, 11, 1, 100, 15),
(12, 12, 1, 120, 18),
(13, 13, 1, 80, 10),
(14, 14, 1, 60, 8),
(15, 15, 1, 150, 20),
(16, 16, 1, 100, 15),
(17, 17, 1, 80, 12),
(18, 18, 1, 300, 50),
(19, 19, 1, 200, 30),
(20, 20, 1, 150, 25);

-- Cabang 2: Jl. Gunung Guntur
INSERT INTO tb_stok_cabang (id_stok_cabang, id_produk, id_detail_cabang, total_stok, stok_minimum) VALUES
(21, 1, 2, 120, 20),
(22, 2, 2, 180, 30),
(23, 3, 2, 160, 25),
(24, 4, 2, 90, 15),
(25, 5, 2, 100, 20),
(26, 6, 2, 70, 10),
(27, 7, 2, 85, 12),
(28, 8, 2, 130, 20),
(29, 9, 2, 180, 30),
(30, 10, 2, 160, 25),
(31, 11, 2, 90, 15),
(32, 12, 2, 100, 18),
(33, 13, 2, 70, 10),
(34, 14, 2, 50, 8),
(35, 15, 2, 130, 20),
(36, 16, 2, 90, 15),
(37, 17, 2, 70, 12),
(38, 18, 2, 250, 50),
(39, 19, 2, 180, 30),
(40, 20, 2, 130, 25);

-- Cabang 3: Jl. Kubu Gn
INSERT INTO tb_stok_cabang (id_stok_cabang, id_produk, id_detail_cabang, total_stok, stok_minimum) VALUES
(41, 1, 3, 140, 20),
(42, 2, 3, 190, 30),
(43, 3, 3, 170, 25),
(44, 4, 3, 95, 15),
(45, 5, 3, 110, 20),
(46, 6, 3, 75, 10),
(47, 7, 3, 88, 12),
(48, 8, 3, 140, 20),
(49, 9, 3, 190, 30),
(50, 10, 3, 170, 25),
(51, 11, 3, 95, 15),
(52, 12, 3, 110, 18),
(53, 13, 3, 75, 10),
(54, 14, 3, 55, 8),
(55, 15, 3, 140, 20),
(56, 16, 3, 95, 15),
(57, 17, 3, 75, 12),
(58, 18, 3, 280, 50),
(59, 19, 3, 190, 30),
(60, 20, 3, 140, 25);

-- Cabang 4: Jl. Kebo Iwa
INSERT INTO tb_stok_cabang (id_stok_cabang, id_produk, id_detail_cabang, total_stok, stok_minimum) VALUES
(61, 1, 4, 130, 20),
(62, 2, 4, 170, 30),
(63, 3, 4, 150, 25),
(64, 4, 4, 85, 15),
(65, 5, 4, 95, 20),
(66, 6, 4, 65, 10),
(67, 7, 4, 80, 12),
(68, 8, 4, 120, 20),
(69, 9, 4, 170, 30),
(70, 10, 4, 150, 25),
(71, 11, 4, 85, 15),
(72, 12, 4, 95, 18),
(73, 13, 4, 65, 10),
(74, 14, 4, 45, 8),
(75, 15, 4, 120, 20),
(76, 16, 4, 85, 15),
(77, 17, 4, 65, 12),
(78, 18, 4, 230, 50),
(79, 19, 4, 170, 30),
(80, 20, 4, 120, 25);

-- Cabang 5: Jl. Karya Makmur
INSERT INTO tb_stok_cabang (id_stok_cabang, id_produk, id_detail_cabang, total_stok, stok_minimum) VALUES
(81, 1, 5, 160, 20),
(82, 2, 5, 210, 30),
(83, 3, 5, 190, 25),
(84, 4, 5, 110, 15),
(85, 5, 5, 130, 20),
(86, 6, 5, 85, 10),
(87, 7, 5, 95, 12),
(88, 8, 5, 160, 20),
(89, 9, 5, 210, 30),
(90, 10, 5, 190, 25),
(91, 11, 5, 110, 15),
(92, 12, 5, 130, 18),
(93, 13, 5, 85, 10),
(94, 14, 5, 65, 8),
(95, 15, 5, 160, 20),
(96, 16, 5, 110, 15),
(97, 17, 5, 85, 12),
(98, 18, 5, 320, 50),
(99, 19, 5, 210, 30),
(100, 20, 5, 160, 25);

-- Selesai
SELECT 'Data dummy berhasil diinsert!' AS Status;
