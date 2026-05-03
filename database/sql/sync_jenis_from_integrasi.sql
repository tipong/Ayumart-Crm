-- Script to sync all jenis (categories) from integrasi database to CRM database
-- This ensures all categories exist before syncing products
-- Run this BEFORE syncing products to avoid foreign key constraint errors

-- First, let's see what jenis exist in integrasi but not in CRM
SELECT
    i.id_jenis,
    i.nama_jenis,
    i.deskripsi_jenis,
    CASE
        WHEN c.id_jenis IS NULL THEN 'MISSING in CRM'
        ELSE 'EXISTS in CRM'
    END as status
FROM db_integrasi_ayu_mart.tb_jenis i
LEFT JOIN db_ayu_mart_crm.tb_jenis c ON i.id_jenis = c.id_jenis
ORDER BY i.id_jenis;

-- Insert missing jenis from integrasi to CRM
-- Note: This uses INSERT IGNORE to skip if already exists
INSERT IGNORE INTO db_ayu_mart_crm.tb_jenis (id_jenis, nama_jenis, deskripsi_jenis, created_at, updated_at)
SELECT
    i.id_jenis,
    i.nama_jenis,
    COALESCE(i.deskripsi_jenis, '') as deskripsi_jenis,
    NOW() as created_at,
    NOW() as updated_at
FROM db_integrasi_ayu_mart.tb_jenis i
WHERE NOT EXISTS (
    SELECT 1
    FROM db_ayu_mart_crm.tb_jenis c
    WHERE c.id_jenis = i.id_jenis
);

-- Verify the sync
SELECT
    'Integrasi' as source,
    COUNT(*) as total_jenis
FROM db_integrasi_ayu_mart.tb_jenis
UNION ALL
SELECT
    'CRM' as source,
    COUNT(*) as total_jenis
FROM db_ayu_mart_crm.tb_jenis;

-- Show any products in integrasi that have jenis not in CRM
SELECT DISTINCT
    p.id_jenis,
    j.nama_jenis,
    COUNT(p.id_produk) as jumlah_produk
FROM db_integrasi_ayu_mart.tb_produk p
LEFT JOIN db_ayu_mart_crm.tb_jenis j ON p.id_jenis = j.id_jenis
WHERE j.id_jenis IS NULL
    AND p.status_produk = 'aktif'
GROUP BY p.id_jenis, j.nama_jenis;
