-- SQL Commands untuk Verifikasi Fitur Cabang Terdekat

-- 1. Lihat semua cabang yang aktif
SELECT
    id_cabang,
    nama_cabang,
    kode_cabang,
    CONCAT(alamat, ', ', kecamatan, ', ', kota) as alamat_lengkap,
    no_telepon,
    CONCAT(jam_buka, ' - ', jam_tutup) as jam_operasional,
    CONCAT(latitude, ', ', longitude) as koordinat,
    is_active
FROM tb_cabang
WHERE is_active = 1
ORDER BY kode_cabang;

-- 2. Lihat transaksi dengan cabang yang ditugaskan
SELECT
    t.id_transaksi,
    t.kode_transaksi,
    t.tgl_transaksi,
    t.metode_pengiriman,
    c.nama_cabang,
    c.kode_cabang,
    t.status_pembayaran,
    t.status_pengiriman,
    t.total_harga
FROM tb_transaksi t
LEFT JOIN tb_cabang c ON t.id_cabang = c.id_cabang
ORDER BY t.tgl_transaksi DESC
LIMIT 20;

-- 3. Statistik transaksi per cabang
SELECT
    c.nama_cabang,
    c.kode_cabang,
    COUNT(t.id_transaksi) as total_transaksi,
    SUM(CASE WHEN t.metode_pengiriman = 'ambil_sendiri' THEN 1 ELSE 0 END) as pickup_count,
    SUM(CASE WHEN t.metode_pengiriman = 'kurir' THEN 1 ELSE 0 END) as delivery_count,
    SUM(CASE WHEN t.status_pembayaran = 'sudah_bayar' THEN t.total_harga ELSE 0 END) as total_revenue
FROM tb_cabang c
LEFT JOIN tb_transaksi t ON c.id_cabang = t.id_cabang
GROUP BY c.id_cabang, c.nama_cabang, c.kode_cabang
ORDER BY total_transaksi DESC;

-- 4. Lihat pengiriman dengan informasi cabang
SELECT
    p.no_resi,
    t.kode_transaksi,
    c.nama_cabang as cabang_pengirim,
    p.nama_penerima,
    p.alamat_penerima,
    CONCAT(p.kota, ', ', p.kecamatan) as tujuan,
    p.status_pengiriman,
    k.nama as nama_kurir
FROM tb_pengiriman p
JOIN tb_transaksi t ON p.id_transaksi = t.id_transaksi
LEFT JOIN tb_cabang c ON t.id_cabang = c.id_cabang
LEFT JOIN users k ON p.id_kurir = k.id
ORDER BY p.created_at DESC
LIMIT 20;

-- 5. Cek alamat pelanggan yang memiliki koordinat GPS
SELECT
    ca.id,
    ca.label,
    ca.nama_penerima,
    CONCAT(ca.alamat_lengkap, ', ', ca.kota) as alamat,
    ca.latitude,
    ca.longitude,
    ca.is_default,
    p.nama as nama_pelanggan
FROM customer_addresses ca
JOIN tb_pelanggan p ON ca.id_pelanggan = p.id_pelanggan
WHERE ca.latitude IS NOT NULL AND ca.longitude IS NOT NULL
ORDER BY ca.created_at DESC
LIMIT 20;

-- 6. Update contoh: Mengaktifkan/Menonaktifkan cabang
-- UPDATE tb_cabang SET is_active = 0 WHERE kode_cabang = 'AYM-001';
-- UPDATE tb_cabang SET is_active = 1 WHERE kode_cabang = 'AYM-001';

-- 7. Insert contoh cabang baru (jika diperlukan)
/*
INSERT INTO tb_cabang (
    nama_cabang, kode_cabang, alamat, kelurahan, kecamatan,
    kota, provinsi, kode_pos, latitude, longitude,
    google_maps_url, no_telepon, jam_buka, jam_tutup, is_active
) VALUES (
    'AyuMart Cabang 6 - Jl. Magelang',
    'AYM-006',
    'Jl. Magelang KM 5',
    'Sinduadi',
    'Mlati',
    'Sleman',
    'D.I. Yogyakarta',
    '55284',
    -7.752778,
    110.373889,
    'https://maps.app.goo.gl/example6',
    '0274-678901',
    '08:00:00',
    '21:00:00',
    1
);
*/

-- 8. Perhitungan jarak manual (contoh - tidak akurat, hanya estimasi)
-- Untuk perhitungan akurat, gunakan fungsi Haversine di aplikasi
SELECT
    nama_cabang,
    latitude,
    longitude,
    -- Contoh perhitungan sederhana untuk titik referensi (-7.755, 110.405)
    SQRT(POW((latitude - (-7.755)) * 111, 2) + POW((longitude - 110.405) * 111, 2)) as estimated_distance_km
FROM tb_cabang
WHERE is_active = 1
ORDER BY estimated_distance_km;

-- 9. Hapus semua data cabang (HATI-HATI!)
-- DELETE FROM tb_cabang;
-- ATAU reset auto increment:
-- ALTER TABLE tb_cabang AUTO_INCREMENT = 1;

-- 10. Export data cabang untuk backup
/*
SELECT
    nama_cabang,
    kode_cabang,
    alamat,
    kelurahan,
    kecamatan,
    kota,
    provinsi,
    kode_pos,
    latitude,
    longitude,
    google_maps_url,
    no_telepon,
    jam_buka,
    jam_tutup,
    is_active
FROM tb_cabang
INTO OUTFILE '/tmp/backup_cabang.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
*/
