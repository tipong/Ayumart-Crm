mysqldump: [Warning] Using a password on the command line interface can be insecure.
Warning: A partial dump from a server that has GTIDs will by default include the GTIDs of all transactions, even those that changed suppressed parts of the database. If you don't want to restore GTIDs, pass --set-gtid-purged=OFF. To make a complete dump, pass --all-databases --triggers --routines --events. 
Warning: A dump from a server that has GTIDs enabled will by default include the GTIDs of all transactions, even those that were executed during its extraction and might not be represented in the dumped data. This might result in an inconsistent data dump. 
In order to ensure a consistent backup of the database, pass --single-transaction or --lock-all-tables or --source-data. 
-- MySQL dump 10.13  Distrib 9.5.0, for macos15 (arm64)
--
-- Host: localhost    Database: crm_system
-- ------------------------------------------------------
-- Server version	9.5.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ 'dce61a50-ddca-11f0-addd-680c01e4728f:1-8481';

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_addresses`
--

DROP TABLE IF EXISTS `customer_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pelanggan` bigint unsigned NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat_lengkap` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kecamatan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_penerima` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telp_penerima` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_addresses_id_pelanggan_foreign` (`id_pelanggan`),
  CONSTRAINT `customer_addresses_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_pelanggan` (`id_pelanggan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_addresses`
--

LOCK TABLES `customer_addresses` WRITE;
/*!40000 ALTER TABLE `customer_addresses` DISABLE KEYS */;
INSERT INTO `customer_addresses` VALUES (1,4,'rumah','tesss1','tes1','tes1','1321','tes1','081736816331',-8.79609970,115.17155458,1,'2025-12-22 07:00:53','2025-12-22 07:00:53'),(2,4,'rumah','text','tes1','tes1','1321','tes1','081736816331',-8.79609970,115.17155458,0,'2025-12-22 07:01:03','2025-12-22 07:01:03'),(3,3,'Rumah','bali21','Denpasar','Kuta','2145','adhim','082471621831',NULL,NULL,1,'2025-12-22 18:28:08','2026-01-07 03:13:44'),(4,1,'jl. nakula no 07, br. negari sading','jl. nakula no 07, br. negari sading','Badung','Mengwi','80351','Indah Damayanti','085829295163',-8.79783519,115.16124533,0,'2025-12-28 02:24:36','2025-12-28 02:24:40'),(5,1,'jl. nakula no 07, br. negari sading','jl. nakula no 07, br. negari sading','Badung','Mengwi','80351','Indah Damayanti','085829295163',-8.79783519,115.16124533,1,'2025-12-28 02:24:40','2025-12-28 02:24:40'),(6,5,'Rumah','jl. nakula no 07, br. negarei lingkungan umahanyar kaja sading','Badung','Mengwi','80351','Ayu Setiawati','085829295163',-8.78593160,115.17882541,1,'2025-12-30 21:04:36','2025-12-30 21:04:36'),(9,3,'TES','TES','TES','TES',NULL,'TES','084624772448',NULL,NULL,0,'2026-01-07 03:16:19','2026-01-07 03:16:19');
/*!40000 ALTER TABLE `customer_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `memberships`
--

DROP TABLE IF EXISTS `memberships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `memberships` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tier` enum('bronze','silver','gold','platinum') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bronze',
  `points` int NOT NULL DEFAULT '0',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `memberships_user_id_foreign` (`user_id`),
  CONSTRAINT `memberships_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `memberships`
--

LOCK TABLES `memberships` WRITE;
/*!40000 ALTER TABLE `memberships` DISABLE KEYS */;
INSERT INTO `memberships` VALUES (1,5,'bronze',12,5.00,'2025-12-22','2026-12-22',1,'2025-12-21 22:27:41','2025-12-30 19:29:58'),(2,11,'bronze',59,5.00,'2025-12-23','2026-12-23',1,'2025-12-23 03:21:39','2025-12-30 18:59:14'),(3,9,'bronze',11,5.00,'2025-12-24','2026-12-24',1,'2025-12-23 22:17:37','2025-12-24 04:30:50'),(4,12,'bronze',21,5.00,'2025-12-31','2026-12-31',1,'2025-12-30 21:06:22','2026-01-04 22:26:58'),(5,17,'bronze',0,5.00,'2025-12-31','2026-12-31',1,'2025-12-30 21:56:16','2025-12-30 21:56:16'),(6,18,'bronze',10,5.00,'2026-01-05','2027-01-05',1,'2026-01-04 20:30:09','2026-01-05 03:27:23');
/*!40000 ALTER TABLE `memberships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000001_create_cache_table',1),(2,'0001_01_01_000002_create_jobs_table',1),(3,'2025_12_20_185412_create_tb_staff_table',1),(4,'2025_12_20_185413_create_tb_jenis_table',1),(5,'2025_12_20_185413_create_tb_pelanggan_table',1),(6,'2025_12_20_185413_create_tb_produk_table',1),(7,'2025_12_20_185414_create_tb_detail_cart_table',1),(8,'2025_12_20_185414_create_tb_gambar_produk_table',1),(9,'2025_12_20_185414_create_tb_kategori_produk_table',1),(10,'2025_12_20_185414_create_tb_tracking_newsletter_table',1),(11,'2025_12_20_185414_create_tb_wishlist_table',1),(12,'2025_12_20_185415_create_tb_membership_table',1),(13,'2025_12_20_185416_create_tb_transaksi_table',1),(14,'2025_12_20_185417_create_tb_detail_transaksi_table',1),(15,'2025_12_20_185418_create_tb_pembatalan_transaksi_table',1),(16,'2025_12_20_185419_create_tb_pengiriman_table',1),(17,'2025_12_20_185420_create_tb_review_produk_table',1),(18,'2025_12_21_022623_create_sessions_table',2),(19,'2025_12_21_032843_create_users_table',3),(20,'2025_12_21_054016_create_memberships_table',4),(21,'2024_01_15_add_category_to_products',5),(22,'2025_12_22_121410_create_tickets_table',6),(23,'2025_12_22_121501_create_ticket_messages_table',6),(24,'2025_12_23_000001_create_customer_addresses_table',7),(25,'2025_12_23_000002_update_tb_transaksi_for_shipping',7),(26,'2025_12_22_151431_update_tb_pengiriman_add_gps_and_order_fields',8),(27,'2025_12_23_000003_add_midtrans_order_id_to_tb_transaksi',9),(28,'2025_12_23_112942_add_no_resi_to_tb_transaksi_table',10),(29,'2025_12_25_071500_add_catatan_admin_to_tb_pembatalan_transaksi',11),(30,'2025_12_25_073609_remove_tgl_pembatalan_from_tb_pembatalan_transaksi_table',12),(31,'2025_12_26_103444_add_biaya_membership_to_tb_transaksi_table',13),(32,'2025_12_27_020051_create_tb_cabang_table',14),(33,'2025_12_27_020559_add_id_cabang_to_tb_transaksi_table',15),(34,'2026_01_05_033216_add_promotion_fields_to_tb_produk_table',16),(35,'2026_01_07_000001_create_tb_newsletter_table',17);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('BteJ2nCB1MAfOXCZrj8aBrGRtzerHGMkcahJzYTk',11,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Safari/605.1.15','YTo1OntzOjY6Il90b2tlbiI7czo0MDoieXByd3FYQzNXejk5VFYza3hIQWJieE5veWt4WUpNV1NVbk1VMlV1ciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvd2lzaGxpc3QvY291bnQiO3M6NToicm91dGUiO3M6MTg6ImFwaS53aXNobGlzdC5jb3VudCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjExO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc2Nzc3NTUxMDt9fQ==',1767786707),('rUTPqdoY5MVPw5GFq3jUXKKI2bUOPK38f2WhQ7nE',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Safari/605.1.15','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVmx6cVJ2RmtuR1FjbXR4QUVtQ3o5TVYzVkRFamRZeGZxT3pCaklvVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1767848966),('TU6kl02KKesS9jM4CyCQwc2XkAiwz6Se5qXg0KUh',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Safari/605.1.15','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNHhHc25BVmdOVTBzSWJrRkd4eURvSWhvaW9zR3JYcW10REtia283dyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9fQ==',1767786415);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_cabang`
--

DROP TABLE IF EXISTS `tb_cabang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_cabang` (
  `id_cabang` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_cabang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_cabang` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelurahan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kecamatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provinsi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bali',
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `google_maps_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_buka` time NOT NULL DEFAULT '08:00:00',
  `jam_tutup` time NOT NULL DEFAULT '21:00:00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_cabang`),
  UNIQUE KEY `tb_cabang_kode_cabang_unique` (`kode_cabang`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_cabang`
--

LOCK TABLES `tb_cabang` WRITE;
/*!40000 ALTER TABLE `tb_cabang` DISABLE KEYS */;
INSERT INTO `tb_cabang` VALUES (1,'Ayu Mart - Jl. Cargo Kenanga','AYM-001','Jl. Cargo Kenanga, Ubung Kaja','Ubung Kaja','Denpasar Utara','Denpasar','Bali','80116',-8.6252100,115.1924360,'https://maps.app.goo.gl/SBoaJytnFQLNRiGD9','0361-4714083','07:00:00','23:00:00',1,NULL,NULL),(2,'Ayu Mart - Jl. Gunung Guntur','AYM-002','Jl. Gunung Guntur Gang Taman Sari 1 No.7B, Padang Sambian Kelod','Padangsambian Kelod','Denpasar Barat','Denpasar','Bali','80117',-8.6547940,115.1827080,'https://maps.app.goo.gl/NfwKmJPV7sVJHvMR7','0813-5336-3083','07:00:00','23:00:00',1,NULL,NULL),(3,'Ayu Mart - Jl. Kubu Gn','AYM-003','Jl. Kubu Gn. No.103, Dalung','Dalung','Kuta Utara','Badung','Bali','80361',-8.6285970,115.1775400,'https://maps.app.goo.gl/6mQ2Bf2C6gg6VqqR6','0361-9072066','07:00:00','23:00:00',1,NULL,NULL),(4,'Ayu Mart - Jl. Kebo Iwa','AYM-004','Jl. Kebo Iwa Selatan Padangsambian','Padangsambian Kaja','Denpasar Barat','Denpasar','Bali','80116',-8.6297660,115.1855270,'https://maps.app.goo.gl/HD2XHHPGBcLUgmjH7','0361-4714437','07:00:00','23:00:00',1,NULL,NULL),(5,'Ayu Mart - Jl. Karya Makmur','AYM-005','Jl. Karya Makmur, No.03 Kargo, Ubung Kaja','Ubung Kaja','Denpasar Utara','Denpasar ','Bali','80116',-8.6245070,115.1947510,'https://maps.app.goo.gl/tfTBoo6PvN1trgih6','0361-9063893','07:00:00','23:00:00',1,NULL,NULL),(6,'Ayu Mart - Jl. Gn. Andakasa','AYM-006','Jl. Gn. Andakasa No.11, Padangsambian','Padangsambian','Denpasar Barat','Denpasar','Bali','80118',-8.6482850,115.1900040,'https://maps.app.goo.gl/e73bwFoqQt3GumXp6','0859-5520-2267','07:00:00','23:00:00',1,NULL,NULL);
/*!40000 ALTER TABLE `tb_cabang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_detail_cart`
--

DROP TABLE IF EXISTS `tb_detail_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_detail_cart` (
  `id_detail_cart` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pelanggan` bigint unsigned NOT NULL,
  `id_produk` bigint unsigned NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_cart`),
  KEY `tb_detail_cart_id_pelanggan_foreign` (`id_pelanggan`),
  KEY `tb_detail_cart_id_produk_foreign` (`id_produk`),
  CONSTRAINT `tb_detail_cart_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  CONSTRAINT `tb_detail_cart_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_detail_cart`
--

LOCK TABLES `tb_detail_cart` WRITE;
/*!40000 ALTER TABLE `tb_detail_cart` DISABLE KEYS */;
INSERT INTO `tb_detail_cart` VALUES (35,6,63,1,'2025-12-30 21:56:35','2025-12-30 21:56:35'),(42,5,21,3,'2026-01-04 23:22:03','2026-01-04 23:22:17'),(44,7,23,2,'2026-01-05 03:28:24','2026-01-05 03:28:24'),(45,3,62,1,'2026-01-05 05:40:26','2026-01-05 05:40:26'),(46,3,19,1,'2026-01-07 03:46:16','2026-01-07 03:46:16');
/*!40000 ALTER TABLE `tb_detail_cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_detail_transaksi`
--

DROP TABLE IF EXISTS `tb_detail_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_detail_transaksi` (
  `id_detail_transaksi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_transaksi` bigint unsigned NOT NULL,
  `id_produk` bigint unsigned NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `harga_item` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `diskon_item` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_transaksi`),
  KEY `tb_detail_transaksi_id_transaksi_foreign` (`id_transaksi`),
  KEY `tb_detail_transaksi_id_produk_foreign` (`id_produk`),
  CONSTRAINT `tb_detail_transaksi_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE,
  CONSTRAINT `tb_detail_transaksi_id_transaksi_foreign` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_detail_transaksi`
--

LOCK TABLES `tb_detail_transaksi` WRITE;
/*!40000 ALTER TABLE `tb_detail_transaksi` DISABLE KEYS */;
INSERT INTO `tb_detail_transaksi` VALUES (1,15,44,1,85000.00,75000.00,0.00,'2025-12-22 07:57:38','2025-12-22 07:57:38'),(2,16,45,1,15000.00,15000.00,0.00,'2025-12-22 18:28:21','2025-12-22 18:28:21'),(3,17,63,5,35000.00,150000.00,0.00,'2025-12-22 20:07:37','2025-12-22 20:07:37'),(4,18,20,1,2500.00,2500.00,0.00,'2025-12-23 22:14:09','2025-12-23 22:14:09'),(5,19,30,5,12000.00,60000.00,0.00,'2025-12-24 00:04:02','2025-12-24 00:04:02'),(6,20,44,1,85000.00,75000.00,0.00,'2025-12-24 04:26:33','2025-12-24 04:26:33'),(7,20,63,1,35000.00,30000.00,0.00,'2025-12-24 04:26:33','2025-12-24 04:26:33'),(8,20,60,1,85000.00,80000.00,0.00,'2025-12-24 04:26:33','2025-12-24 04:26:33'),(9,21,36,1,35000.00,30000.00,0.00,'2025-12-24 04:27:43','2025-12-24 04:27:43'),(10,21,44,1,85000.00,75000.00,0.00,'2025-12-24 04:27:43','2025-12-24 04:27:43'),(11,22,19,2,75000.00,150000.00,0.00,'2025-12-24 21:47:52','2025-12-24 21:47:52'),(12,23,36,4,35000.00,140000.00,0.00,'2025-12-24 23:33:34','2025-12-24 23:33:34'),(13,24,39,2,30000.00,60000.00,0.00,'2025-12-24 23:38:21','2025-12-24 23:38:21'),(14,25,42,2,65000.00,130000.00,0.00,'2025-12-24 23:54:22','2025-12-24 23:54:22'),(15,26,45,9,15000.00,135000.00,0.00,'2025-12-26 18:20:32','2025-12-26 18:20:32'),(16,27,23,2,18000.00,36000.00,0.00,'2025-12-28 02:25:43','2025-12-28 02:25:43'),(17,27,24,3,3000.00,9000.00,0.00,'2025-12-28 02:25:43','2025-12-28 02:25:43'),(18,27,21,3,2500.00,7500.00,0.00,'2025-12-28 02:25:43','2025-12-28 02:25:43'),(19,28,44,1,85000.00,85000.00,0.00,'2025-12-28 02:34:27','2025-12-28 02:34:27'),(20,29,36,3,35000.00,105000.00,0.00,'2025-12-28 04:44:14','2025-12-28 04:44:14'),(21,29,61,2,45000.00,90000.00,0.00,'2025-12-28 04:44:14','2025-12-28 04:44:14'),(22,30,36,2,35000.00,70000.00,0.00,'2025-12-29 19:41:38','2025-12-29 19:41:38'),(23,30,21,4,2500.00,10000.00,0.00,'2025-12-29 19:41:38','2025-12-29 19:41:38'),(24,30,26,2,15000.00,30000.00,0.00,'2025-12-29 19:41:38','2025-12-29 19:41:38'),(25,31,19,2,75000.00,150000.00,0.00,'2025-12-30 18:57:33','2025-12-30 18:57:33'),(26,31,42,4,65000.00,260000.00,0.00,'2025-12-30 18:57:33','2025-12-30 18:57:33'),(27,32,63,3,35000.00,105000.00,0.00,'2025-12-30 19:29:33','2025-12-30 19:29:33'),(28,33,44,1,85000.00,85000.00,0.00,'2025-12-30 21:05:50','2025-12-30 21:05:50'),(29,33,21,4,2500.00,10000.00,0.00,'2025-12-30 21:05:50','2025-12-30 21:05:50'),(30,34,44,2,85000.00,170000.00,0.00,'2025-12-30 22:02:12','2025-12-30 22:02:12'),(31,35,27,3,12000.00,36000.00,0.00,'2026-01-04 20:29:48','2026-01-04 20:29:48'),(32,35,29,5,8000.00,40000.00,0.00,'2026-01-04 20:29:48','2026-01-04 20:29:48'),(33,36,23,5,18000.00,90000.00,0.00,'2026-01-04 22:26:40','2026-01-04 22:26:40'),(34,36,26,2,15000.00,30000.00,0.00,'2026-01-04 22:26:40','2026-01-04 22:26:40'),(35,36,30,3,12000.00,36000.00,0.00,'2026-01-04 22:26:40','2026-01-04 22:26:40'),(36,37,19,2,75000.00,150000.00,0.00,'2026-01-05 03:27:04','2026-01-05 03:27:04'),(37,38,24,5,3000.00,9500.00,0.00,'2026-01-05 03:30:16','2026-01-05 03:30:16');
/*!40000 ALTER TABLE `tb_detail_transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_gambar_produk`
--

DROP TABLE IF EXISTS `tb_gambar_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_gambar_produk` (
  `id_gambar_produk` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_produk` bigint unsigned NOT NULL,
  `nama_gambar` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_gambar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` enum('1','2','3','4','5') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_gambar_produk`),
  KEY `tb_gambar_produk_id_produk_foreign` (`id_produk`),
  CONSTRAINT `tb_gambar_produk_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_gambar_produk`
--

LOCK TABLES `tb_gambar_produk` WRITE;
/*!40000 ALTER TABLE `tb_gambar_produk` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_gambar_produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_jenis`
--

DROP TABLE IF EXISTS `tb_jenis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_jenis` (
  `id_jenis` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_jenis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_jenis` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jenis`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_jenis`
--

LOCK TABLES `tb_jenis` WRITE;
/*!40000 ALTER TABLE `tb_jenis` DISABLE KEYS */;
INSERT INTO `tb_jenis` VALUES (1,'Makanan Pokok','Beras, mie, pasta, dan bahan makanan pokok lainnya','2025-12-22 04:27:32','2025-12-22 04:27:32'),(2,'Minuman','Air mineral, jus, soft drink, dan minuman kemasan','2025-12-22 04:27:32','2025-12-22 04:27:32'),(3,'Snack & Makanan Ringan','Keripik, biskuit, coklat, dan camilan','2025-12-22 04:27:32','2025-12-22 04:27:32'),(4,'Susu & Produk Olahan','Susu, yogurt, keju, dan produk dairy','2025-12-22 04:27:32','2025-12-22 04:27:32'),(5,'Buah & Sayur','Buah segar, sayuran, dan produk organik','2025-12-22 04:27:32','2025-12-22 04:27:32'),(6,'Daging & Seafood','Daging sapi, ayam, ikan, dan hasil laut','2025-12-22 04:27:32','2025-12-22 04:27:32'),(7,'Bumbu & Penyedap','Bumbu dapur, saus, kecap, dan penyedap','2025-12-22 04:27:32','2025-12-22 04:27:32'),(8,'Frozen Food','Makanan beku, nugget, sosis, dan frozen meals','2025-12-22 04:27:32','2025-12-22 04:27:32'),(9,'Perawatan Pribadi','Sabun, shampo, pasta gigi, dan toiletries','2025-12-22 04:27:32','2025-12-22 04:27:32'),(10,'Peralatan Rumah Tangga','Sabun cuci, pewangi, tissue, dan kebutuhan rumah','2025-12-22 04:27:32','2025-12-22 04:27:32'),(11,'Ibu & Bayi','Susu formula, popok, dan perlengkapan bayi','2025-12-22 04:27:32','2025-12-22 04:27:32'),(12,'Kesehatan','Vitamin, suplemen, dan produk kesehatan','2025-12-22 04:27:32','2025-12-22 04:27:32');
/*!40000 ALTER TABLE `tb_jenis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_newsletter`
--

DROP TABLE IF EXISTS `tb_newsletter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_newsletter` (
  `id_newsletter` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subjek_email` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `konten_email` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `konten_html` longtext COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','mengirim','terkirim','gagal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `tanggal_kirim` datetime DEFAULT NULL,
  `total_penerima` int NOT NULL DEFAULT '0',
  `total_terkirim` int NOT NULL DEFAULT '0',
  `total_gagal` int NOT NULL DEFAULT '0',
  `dibuat_oleh` bigint unsigned DEFAULT NULL,
  `mailchimp_campaign_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_newsletter`),
  KEY `tb_newsletter_dibuat_oleh_foreign` (`dibuat_oleh`),
  CONSTRAINT `tb_newsletter_dibuat_oleh_foreign` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_newsletter`
--

LOCK TABLES `tb_newsletter` WRITE;
/*!40000 ALTER TABLE `tb_newsletter` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_newsletter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_pelanggan`
--

DROP TABLE IF EXISTS `tb_pelanggan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_pelanggan` (
  `id_pelanggan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pelanggan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profil_pelanggan` text COLLATE utf8mb4_unicode_ci,
  `no_tlp_pelanggan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pelanggan` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pelanggan`),
  UNIQUE KEY `tb_pelanggan_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_pelanggan`
--

LOCK TABLES `tb_pelanggan` WRITE;
/*!40000 ALTER TABLE `tb_pelanggan` DISABLE KEYS */;
INSERT INTO `tb_pelanggan` VALUES (1,'Pelanggan Demo','pelanggan@crm.com','$2y$12$j5KHx0ndvuwWQbmPI4Fr/uWibGQas6qlWKzArmUND3kUXaImx7mrO',NULL,'081234567894','Jl. Contoh No. 123, Jakarta Selatan','aktif','2025-12-20 12:50:52','2025-12-20 12:50:52'),(2,'Budi Santoso','budi@gmail.com','$2y$12$3qMUSzvBn1yfBLGxX5aDXehJ/zmBBC5.k4XUn2KIi4m5P5XaxNG.e',NULL,'081234567895','Jl. Merdeka No. 45, Bandung','aktif','2025-12-20 12:50:53','2025-12-20 12:50:53'),(3,'Adhim satya nugraha','adhim@gmail.com','',NULL,'082478647110','adhim','aktif','2025-12-22 03:19:15','2025-12-22 03:19:15'),(4,'indah','indah@gmail.com','',NULL,'085829295163','hhatsvhb','aktif','2025-12-22 05:49:44','2025-12-22 05:49:44'),(5,'Indah Damayanti','indahdamayanti411@gmail.com','',NULL,'085829295163','jl. nakula no. 07 br. negari sading','aktif','2025-12-30 21:00:51','2025-12-30 21:00:51'),(6,'dini','dini@gmail.com','',NULL,'085829295163','jl jembrana','aktif','2025-12-30 21:56:35','2025-12-30 21:56:35'),(7,'adhim14','adhim14@gmail.com','',NULL,'082578613840','adhim','aktif','2026-01-04 20:28:59','2026-01-04 20:28:59');
/*!40000 ALTER TABLE `tb_pelanggan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_pembatalan_transaksi`
--

DROP TABLE IF EXISTS `tb_pembatalan_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_pembatalan_transaksi` (
  `id_pembatalan_transaksi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_transaksi` bigint unsigned NOT NULL,
  `alasan_pembatalan` text COLLATE utf8mb4_unicode_ci,
  `status_pembatalan` enum('diajukan','disetujui','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diajukan',
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `diproses_oleh` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pembatalan_transaksi`),
  KEY `tb_pembatalan_transaksi_id_transaksi_foreign` (`id_transaksi`),
  KEY `tb_pembatalan_transaksi_diproses_oleh_foreign` (`diproses_oleh`),
  CONSTRAINT `tb_pembatalan_transaksi_diproses_oleh_foreign` FOREIGN KEY (`diproses_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_pembatalan_transaksi_id_transaksi_foreign` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_pembatalan_transaksi`
--

LOCK TABLES `tb_pembatalan_transaksi` WRITE;
/*!40000 ALTER TABLE `tb_pembatalan_transaksi` DISABLE KEYS */;
INSERT INTO `tb_pembatalan_transaksi` VALUES (1,22,'Salah pembelian','disetujui',NULL,2,'2025-12-24 22:13:55','2025-12-24 23:13:44'),(2,23,'tessssskadaluarsakadaluarsakadaluarsa','diajukan',NULL,NULL,'2025-12-24 23:37:40','2025-12-24 23:37:40'),(3,24,'kadaluarsakadaluarsakadaluarsakadaluarsakadaluarsakadaluarsakadaluarsakadaluarsa','ditolak',NULL,2,'2025-12-24 23:38:31','2025-12-24 23:40:21'),(4,38,'indah jeklek','disetujui',NULL,2,'2026-01-05 03:30:35','2026-01-05 04:06:27');
/*!40000 ALTER TABLE `tb_pembatalan_transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_pengiriman`
--

DROP TABLE IF EXISTS `tb_pengiriman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_pengiriman` (
  `id_pengiriman` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pelanggan` bigint unsigned DEFAULT NULL,
  `id_transaksi` bigint unsigned NOT NULL,
  `id_staff` bigint unsigned DEFAULT NULL,
  `id_kurir` bigint unsigned DEFAULT NULL,
  `no_resi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_penerima` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat_penerima` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kecamatan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_tlp_penerima` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status_pengiriman` enum('pending','dikemas','siap_diambil','dalam_pengiriman','terkirim','gagal') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `tgl_kirim` datetime DEFAULT NULL,
  `tgl_sampai` datetime DEFAULT NULL,
  `catatan_pengiriman` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pengiriman`),
  UNIQUE KEY `tb_pengiriman_no_resi_unique` (`no_resi`),
  KEY `tb_pengiriman_id_transaksi_foreign` (`id_transaksi`),
  KEY `tb_pengiriman_id_staff_foreign` (`id_staff`),
  KEY `tb_pengiriman_id_pelanggan_foreign` (`id_pelanggan`),
  CONSTRAINT `tb_pengiriman_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  CONSTRAINT `tb_pengiriman_id_staff_foreign` FOREIGN KEY (`id_staff`) REFERENCES `tb_staff` (`id_staff`) ON DELETE SET NULL,
  CONSTRAINT `tb_pengiriman_id_transaksi_foreign` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_pengiriman`
--

LOCK TABLES `tb_pengiriman` WRITE;
/*!40000 ALTER TABLE `tb_pengiriman` DISABLE KEYS */;
INSERT INTO `tb_pengiriman` VALUES (1,4,15,NULL,4,'RESI-20251222155738-392E','tes1','tesss1','tes1','tes1','1321','081736816331',-8.79609970,115.17155458,'dalam_pengiriman','2025-12-24 06:21:18',NULL,NULL,'2025-12-22 07:57:38','2025-12-23 22:21:18'),(2,3,16,NULL,4,'RESI-20251223022821-B4DC','adhim','bali','Denpasar','Kuta','2145','082471621831',NULL,NULL,'dalam_pengiriman','2025-12-23 11:49:55',NULL,NULL,'2025-12-22 18:28:21','2025-12-23 03:49:55'),(3,3,17,NULL,4,'RESI-20251223040737-B482','adhim','bali','Denpasar','Kuta','2145','082471621831',NULL,NULL,'terkirim','2025-12-23 11:50:18','2025-12-23 11:50:28',NULL,'2025-12-22 20:07:37','2025-12-23 03:50:28'),(4,4,18,NULL,4,'RESI-20251224061410-6534','tes1','tesss1','tes1','tes1','1321','081736816331',-8.79609970,115.17155458,'terkirim','2025-12-24 08:36:23','2026-01-05 07:04:15',NULL,'2025-12-23 22:14:10','2026-01-04 23:04:15'),(5,1,32,NULL,4,'RESI-20251231032958-9364','Indah Damayanti','jl. nakula no 07, br. negari sading','Badung','Mengwi','80351','085829295163',-8.79783519,115.16124533,'terkirim','2025-12-31 03:31:28','2025-12-31 03:31:49',NULL,'2025-12-30 19:29:58','2025-12-30 19:31:49'),(6,5,33,NULL,16,'RESI-20251231050622-B1BF','Ayu Setiawati','jl. nakula no 07, br. negarei lingkungan umahanyar kaja sading','Badung','Mengwi','80351','085829295163',-8.78593160,115.17882541,'terkirim','2025-12-31 05:44:58','2025-12-31 05:45:26','Tambahkan paperbag lagi','2025-12-30 21:06:22','2025-12-30 21:45:26'),(7,5,36,NULL,4,'RESI-20260105062658-36D3','Ayu Setiawati','jl. nakula no 07, br. negarei lingkungan umahanyar kaja sading','Badung','Mengwi','80351','085829295163',-8.78593160,115.17882541,'dalam_pengiriman','2026-01-05 07:50:52',NULL,NULL,'2026-01-04 22:26:58','2026-01-04 23:50:52');
/*!40000 ALTER TABLE `tb_pengiriman` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_produk`
--

DROP TABLE IF EXISTS `tb_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_produk` (
  `id_produk` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_produk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_produk` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_produk` text COLLATE utf8mb4_unicode_ci,
  `id_jenis` bigint unsigned DEFAULT NULL,
  `harga_produk` decimal(12,2) NOT NULL,
  `harga_diskon` decimal(12,2) DEFAULT NULL,
  `persentase_diskon` decimal(5,2) DEFAULT NULL,
  `tanggal_mulai_diskon` date DEFAULT NULL,
  `tanggal_akhir_diskon` date DEFAULT NULL,
  `is_diskon_active` tinyint(1) NOT NULL DEFAULT '0',
  `stok_produk` int NOT NULL DEFAULT '0',
  `berat_produk` decimal(8,2) DEFAULT NULL,
  `foto_produk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_produk` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_produk`),
  UNIQUE KEY `tb_produk_kode_produk_unique` (`kode_produk`),
  KEY `tb_produk_id_jenis_foreign` (`id_jenis`),
  CONSTRAINT `tb_produk_id_jenis_foreign` FOREIGN KEY (`id_jenis`) REFERENCES `tb_jenis` (`id_jenis`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_produk`
--

LOCK TABLES `tb_produk` WRITE;
/*!40000 ALTER TABLE `tb_produk` DISABLE KEYS */;
INSERT INTO `tb_produk` VALUES (19,'MP001','Beras Premium 5Kg','Beras premium kualitas terbaik, pulen dan wangi',1,75000.00,NULL,NULL,NULL,NULL,0,46,5000.00,'products/gTTLH8tdFJCsNd2UHranj3OcJgRGVQCRxOIYT66G.jpg','aktif','2025-12-22 04:27:32','2026-01-05 03:27:04'),(20,'MP002','Mie Instan Goreng','Mie instan goreng rasa original',1,2500.00,NULL,NULL,NULL,NULL,0,199,85.00,'products/HdKawuVq2naSTDcBSog5SBZslcq6OrdnVKFtlOCi.png','aktif','2025-12-22 04:27:32','2025-12-26 22:01:12'),(21,'MP003','Mie Instan Kuah','Mie instan kuah rasa ayam bawang',1,2500.00,NULL,NULL,NULL,NULL,0,189,75.00,'products/B8IG0P2BnjxnjbuKz6Gtl44Ek8RjBegqs7zswmEw.jpg','aktif','2025-12-22 04:27:32','2025-12-30 21:05:50'),(22,'MP004','Tepung Terigu 1Kg','Tepung terigu serbaguna untuk membuat kue',1,12000.00,NULL,NULL,NULL,NULL,0,80,1000.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(23,'MP005','Pasta Spaghetti 500g','Pasta premium dari gandum pilihan',1,18000.00,NULL,NULL,NULL,NULL,0,53,500.00,'products/L5Mqza4yVOZuQBPlQ85qsTPtj2faJdLHGylq8kZ0.jpg','aktif','2025-12-22 04:27:32','2026-01-04 22:26:40'),(24,'MN001','Air Mineral 600ml','Air mineral murni dalam kemasan praktis',2,3000.00,1900.00,36.67,'2026-01-05','2026-01-06',1,297,600.00,'products/vjno1ikc7uQtsBlSNMmTPUjq8y991ATXXV0A4a2C.jpg','aktif','2025-12-22 04:27:32','2026-01-05 04:06:27'),(25,'MN002','Teh Botol Kemasan','Teh manis segar dalam botol',2,5000.00,NULL,NULL,NULL,NULL,0,150,350.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(26,'MN003','Jus Jeruk 1L','Jus jeruk asli tanpa pengawet',2,15000.00,NULL,NULL,NULL,NULL,0,96,1000.00,'products/zYssxodLnPr73uL2IwfXNd5NvkC1Lf6njFUcxsAW.jpg','aktif','2025-12-22 04:27:32','2026-01-04 22:26:40'),(27,'MN004','Kopi Sachet','Kopi instan 3in1 isi 10 sachet',2,12000.00,NULL,NULL,NULL,NULL,0,117,200.00,'products/9Hyy0zlyMkci38rgBSCIBDL9In8XrhZzOlGdFZkD.jpg','aktif','2025-12-22 04:27:32','2026-01-04 20:29:48'),(28,'MN005','Soft Drink 1.5L','Minuman bersoda rasa cola',2,10000.00,NULL,NULL,NULL,NULL,0,90,1500.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(29,'SN001','Keripik Kentang 100g','Keripik kentang rasa original',3,8000.00,NULL,NULL,NULL,NULL,0,145,100.00,'products/XFCWHB715yNgzPLHIViVU9Bo3Ivh1vDKGH9MJLKF.jpg','aktif','2025-12-22 04:27:32','2026-01-04 20:29:48'),(30,'SN002','Biskuit Coklat 300g','Biskuit dengan krim coklat lezat',3,12000.00,NULL,NULL,NULL,NULL,0,92,300.00,'products/XkGNJDU7rRqK8HoITiSC65cW1aTHjUeiQtOTLkjk.jpg','aktif','2025-12-22 04:27:32','2026-01-04 22:26:40'),(31,'SN003','Coklat Batang 50g','Coklat susu premium',3,7000.00,NULL,NULL,NULL,NULL,0,200,50.00,'products/RXiSclx0iTX3x5ty97Ncr155aKJWFtX6at0pBj8w.png','aktif','2025-12-22 04:27:32','2025-12-26 21:55:17'),(32,'SN004','Wafer Stick Vanilla','Wafer stick rasa vanilla isi 20',3,5000.00,NULL,NULL,NULL,NULL,0,180,150.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(33,'SN005','Kacang Goreng 200g','Kacang tanah goreng gurih',3,10000.00,NULL,NULL,NULL,NULL,0,90,200.00,'products/MKhkG88bI4FwrRnGbsSLcREL2VEDjIv5CXBjsa6H.jpg','aktif','2025-12-22 04:27:32','2025-12-26 22:06:52'),(34,'SU001','Susu UHT 1L','Susu sapi murni ultra high temperature',4,18000.00,NULL,NULL,NULL,NULL,0,100,1000.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(35,'SU002','Yogurt Drink 150ml','Yogurt rasa strawberry',4,8000.00,NULL,NULL,NULL,NULL,0,120,150.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(36,'SU003','Keju Cheddar 200g','Keju cheddar untuk sandwich',4,35000.00,NULL,NULL,NULL,NULL,0,40,200.00,'products/NBzFsD8oigoJkSNfH6gr5UMw3rkGIrxv4ixleHpX.png','aktif','2025-12-22 04:27:32','2025-12-29 19:41:38'),(37,'SU004','Susu Kental Manis 370g','Susu kental manis untuk kue dan minuman',4,12000.00,NULL,NULL,NULL,NULL,0,80,370.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(38,'BS001','Apel Fuji 1Kg','Apel fuji segar dan manis',5,35000.00,NULL,NULL,NULL,NULL,0,40,1000.00,'products/SVCFlRmMfABnuskntmB2cKIaHsRann6BnNofjI5q.jpg','aktif','2025-12-22 04:27:32','2025-12-26 21:56:04'),(39,'BS002','Jeruk Sunkist 1Kg','Jeruk sunkist segar kaya vitamin C',5,30000.00,NULL,NULL,NULL,NULL,0,48,1000.00,'products/FRUx1NUOgjKfeW6krKoJ2ovlfXrieuBPSTP7gXiG.jpg','aktif','2025-12-22 04:27:32','2025-12-26 21:56:26'),(40,'BS003','Wortel 500g','Wortel segar untuk masakan',5,8000.00,NULL,NULL,NULL,NULL,0,60,500.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(41,'BS004','Brokoli 500g','Brokoli segar organik',5,15000.00,NULL,NULL,NULL,NULL,0,30,500.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(42,'DS001','Daging Sapi 500g','Daging sapi segar pilihan',6,65000.00,NULL,NULL,NULL,NULL,0,19,500.00,'products/VBeEDOwCIxNCS1uwXM1z89xxwnYr98GmZ2Z9Kgtx.jpg','aktif','2025-12-22 04:27:32','2025-12-30 18:57:33'),(43,'DS002','Ayam Fillet 500g','Fillet ayam tanpa tulang',6,35000.00,NULL,NULL,NULL,NULL,0,1,500.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-23 02:14:51'),(44,'DS003','Ikan Salmon 300g','Salmon segar premium',6,85000.00,NULL,NULL,NULL,NULL,0,8,300.00,'products/eyXGRcx6eDw27PTBRoPxx2GidQUrcVOOfAPOUhg0.jpg','aktif','2025-12-22 04:27:32','2025-12-30 22:02:12'),(45,'BP001','Kecap Manis 600ml','Kecap manis khas Indonesia',7,15000.00,NULL,NULL,NULL,NULL,0,90,600.00,'products/VN5tQbShdC9mdKQIyVdtjfngqLDqWiizQqOgR1Kf.jpg','aktif','2025-12-22 04:27:32','2025-12-26 22:07:47'),(46,'BP002','Saus Tomat 340g','Saus tomat untuk masakan',7,12000.00,NULL,NULL,NULL,NULL,0,80,340.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(47,'BP003','Minyak Goreng 2L','Minyak goreng berkualitas',7,32000.00,NULL,NULL,NULL,NULL,0,70,2000.00,'products/UKi0sQjR6QIE95jG8vvkZM25iqbpQm3nbnOwZnPJ.jpg','aktif','2025-12-22 04:27:32','2025-12-26 22:08:29'),(48,'BP004','Garam Dapur 500g','Garam beryodium untuk masakan',7,5000.00,NULL,NULL,NULL,NULL,0,150,500.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(49,'FF001','Nugget Ayam 500g','Nugget ayam siap goreng',8,28000.00,NULL,NULL,NULL,NULL,0,60,500.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(50,'FF002','Sosis Sapi 250g','Sosis sapi premium',8,22000.00,NULL,NULL,NULL,NULL,0,70,250.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(51,'FF003','Bakso Ikan 300g','Bakso ikan siap masak',8,18000.00,NULL,NULL,NULL,NULL,0,50,300.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(52,'PP001','Sabun Mandi 85g','Sabun mandi antibakteri',9,4000.00,NULL,NULL,NULL,NULL,0,200,85.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(53,'PP002','Shampo 170ml','Shampo anti ketombe',9,18000.00,NULL,NULL,NULL,NULL,0,100,170.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(54,'PP003','Pasta Gigi 150g','Pasta gigi dengan fluoride',9,12000.00,NULL,NULL,NULL,NULL,0,120,150.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(55,'PP004','Deodorant Spray 150ml','Deodorant spray 24 jam',9,22000.00,NULL,NULL,NULL,NULL,0,80,150.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(56,'RT001','Sabun Cuci Piring 800ml','Sabun cuci piring anti lemak',10,15000.00,NULL,NULL,NULL,NULL,0,90,800.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(57,'RT002','Deterjen Bubuk 1Kg','Deterjen untuk mesin cuci',10,25000.00,NULL,NULL,NULL,NULL,0,70,1000.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(58,'RT003','Tissue Roll isi 10','Tissue toilet lembut',10,28000.00,NULL,NULL,NULL,NULL,0,100,800.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(59,'RT004','Pewangi Pakaian 900ml','Pewangi pakaian long lasting',10,20000.00,NULL,NULL,NULL,NULL,0,60,900.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(60,'IB001','Susu Formula 400g','Susu formula untuk bayi 0-6 bulan',11,85000.00,NULL,NULL,NULL,NULL,0,39,400.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-24 04:26:33'),(61,'IB002','Popok Bayi S isi 20','Popok bayi extra soft',11,45000.00,NULL,NULL,NULL,NULL,0,48,600.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-28 04:44:14'),(62,'IB003','Bubur Bayi 120g','Bubur bayi rasa apel',11,15000.00,NULL,NULL,NULL,NULL,0,70,120.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(63,'KS001','Vitamin C 1000mg','Vitamin C untuk daya tahan tubuh',12,35000.00,NULL,NULL,NULL,NULL,0,51,50.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-30 19:29:33'),(64,'KS002','Minyak Kayu Putih 30ml','Minyak kayu putih aromaterapi',12,12000.00,NULL,NULL,NULL,NULL,0,100,30.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(65,'KS003','Masker 3 Ply isi 50','Masker sekali pakai 3 lapis',12,25000.00,NULL,NULL,NULL,NULL,0,80,200.00,NULL,'aktif','2025-12-22 04:27:32','2025-12-22 04:27:32'),(67,'P89923','Susu Ultramilk','susu',4,15000.00,NULL,NULL,NULL,NULL,0,241,NULL,'products/3UXSGU3jm5VORui5VQmX7bsQ2XGquIJ83BkB7Tve.png','aktif','2026-01-04 23:36:38','2026-01-04 23:36:38');
/*!40000 ALTER TABLE `tb_produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_review_produk`
--

DROP TABLE IF EXISTS `tb_review_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_review_produk` (
  `id_review_produk` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pelanggan` bigint unsigned NOT NULL,
  `id_produk` bigint unsigned NOT NULL,
  `id_transaksi` bigint unsigned NOT NULL,
  `rating` int NOT NULL,
  `isi_review` text COLLATE utf8mb4_unicode_ci,
  `foto_review` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_review_produk`),
  KEY `tb_review_produk_id_pelanggan_foreign` (`id_pelanggan`),
  KEY `tb_review_produk_id_produk_foreign` (`id_produk`),
  KEY `tb_review_produk_id_transaksi_foreign` (`id_transaksi`),
  CONSTRAINT `tb_review_produk_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  CONSTRAINT `tb_review_produk_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE,
  CONSTRAINT `tb_review_produk_id_transaksi_foreign` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_review_produk`
--

LOCK TABLES `tb_review_produk` WRITE;
/*!40000 ALTER TABLE `tb_review_produk` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_review_produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_staff`
--

DROP TABLE IF EXISTS `tb_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_staff` (
  `id_staff` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_staff` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posisi_staff` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profil_staff` text COLLATE utf8mb4_unicode_ci,
  `no_tlp_staff` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_akun` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_staff`),
  UNIQUE KEY `tb_staff_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_staff`
--

LOCK TABLES `tb_staff` WRITE;
/*!40000 ALTER TABLE `tb_staff` DISABLE KEYS */;
INSERT INTO `tb_staff` VALUES (1,'Owner System','owner@crm.com','$2y$12$q0Q3WhBkaljPsPOjdx22j.xXli/oGYxwQzhdBcLK.3Exh8GMbqpKS','owner',NULL,'081234567890','aktif','2025-12-20 12:50:51','2025-12-20 12:50:51'),(2,'Admin System','admin@crm.com','$2y$12$wP0uMRNPYLw74T5128GsvejT6ccJFOX8eG.024iB9gTNy00umA7We','admin',NULL,'081234567891','aktif','2025-12-20 12:50:52','2025-12-20 12:50:52'),(3,'Customer Service','cs@crm.com','$2y$12$k1K48E0WBtz1C2nYE6WsZ.rnnZfxBFJR1hAo7AGZUqrkZtI0awfpq','cs',NULL,'081234567892','aktif','2025-12-20 12:50:52','2025-12-20 12:50:52'),(4,'Kurir 1','kurir@crm.com','$2y$12$WP60sYqaNBXGgnwnAYkV.eZVKxTl4nN3JCjsnlLE6iv0yOwfXpovG','kurir',NULL,'081234567893','aktif','2025-12-20 12:50:52','2025-12-20 12:50:52');
/*!40000 ALTER TABLE `tb_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_tracking_newsletter`
--

DROP TABLE IF EXISTS `tb_tracking_newsletter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_tracking_newsletter` (
  `id_tracking_newsletter` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_newsletter` bigint unsigned DEFAULT NULL,
  `id_pelanggan` bigint unsigned NOT NULL,
  `email_tujuan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `konten_email` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subjek_email` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_kirim` datetime NOT NULL,
  `status_kirim` enum('terkirim','gagal','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `mailchimp_member_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_tracking_newsletter`),
  KEY `tb_tracking_newsletter_id_pelanggan_foreign` (`id_pelanggan`),
  KEY `tb_tracking_newsletter_id_newsletter_foreign` (`id_newsletter`),
  CONSTRAINT `tb_tracking_newsletter_id_newsletter_foreign` FOREIGN KEY (`id_newsletter`) REFERENCES `tb_newsletter` (`id_newsletter`) ON DELETE CASCADE,
  CONSTRAINT `tb_tracking_newsletter_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_pelanggan` (`id_pelanggan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_tracking_newsletter`
--

LOCK TABLES `tb_tracking_newsletter` WRITE;
/*!40000 ALTER TABLE `tb_tracking_newsletter` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_tracking_newsletter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_transaksi`
--

DROP TABLE IF EXISTS `tb_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_transaksi` (
  `id_transaksi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pelanggan` bigint unsigned NOT NULL,
  `id_cabang` bigint unsigned DEFAULT NULL,
  `kode_transaksi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `midtrans_order_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_transaksi` datetime NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `total_diskon` decimal(12,2) NOT NULL DEFAULT '0.00',
  `ongkir` decimal(12,2) NOT NULL DEFAULT '0.00',
  `biaya_membership` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status_pembayaran` enum('belum_bayar','sudah_bayar','kadaluarsa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_bayar',
  `metode_pembayaran` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `snap_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `alamat_pengiriman` text COLLATE utf8mb4_unicode_ci,
  `metode_pengiriman` enum('kurir','ambil_sendiri') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kurir',
  `status_pengiriman` enum('pending','dikemas','dikirim','sampai','siap_diambil','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `address_id` bigint unsigned DEFAULT NULL,
  `no_resi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_transaksi`),
  UNIQUE KEY `tb_transaksi_kode_transaksi_unique` (`kode_transaksi`),
  KEY `tb_transaksi_id_pelanggan_foreign` (`id_pelanggan`),
  KEY `tb_transaksi_id_cabang_foreign` (`id_cabang`),
  KEY `tb_transaksi_address_id_foreign` (`address_id`),
  CONSTRAINT `tb_transaksi_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `customer_addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_transaksi_id_cabang_foreign` FOREIGN KEY (`id_cabang`) REFERENCES `tb_cabang` (`id_cabang`) ON DELETE SET NULL,
  CONSTRAINT `tb_transaksi_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_pelanggan` (`id_pelanggan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_transaksi`
--

LOCK TABLES `tb_transaksi` WRITE;
/*!40000 ALTER TABLE `tb_transaksi` DISABLE KEYS */;
INSERT INTO `tb_transaksi` VALUES (15,4,NULL,'TRX-20251222155738-21BD','TRX-20251222155738-21BD-R1766557015','2025-12-22 15:57:38',75000.00,0.00,15000.00,0.00,'sudah_bayar','qris','e76b69c5-e2d9-42b9-a925-5eb85ab9b871','qris','2025-12-24 06:17:37','tesss1, tes1, tes1, 1321','kurir','dikirim',1,'RESI-20251222155738-392E',NULL,'2025-12-22 07:57:38','2025-12-23 22:21:18'),(16,3,NULL,'TRX-20251223022821-4DEB','TRX-20251223022821-4DEB-R1766488373','2025-12-23 02:28:21',15000.00,0.00,15000.00,0.00,'sudah_bayar','credit_card','016a020d-09f4-436b-8732-1c83652a5c44','credit_card','2025-12-23 11:18:33','bali, Kuta, Denpasar, 2145','kurir','dikirim',3,'RESI-20251223022821-B4DC',NULL,'2025-12-22 18:28:21','2025-12-23 03:49:55'),(17,3,NULL,'TRX-20251223040737-B77D','TRX-20251223040737-B77D-R1766488881','2025-12-23 04:07:37',150000.00,0.00,15000.00,0.00,'sudah_bayar','bank_transfer','cec29081-b753-4564-aa28-0019ad055fda','bank_transfer','2025-12-23 11:21:39','bali, Kuta, Denpasar, 2145','kurir','sampai',3,'RESI-20251223040737-B482',NULL,'2025-12-22 20:07:37','2025-12-23 03:50:28'),(18,4,NULL,'TRX-20251224061409-0BB1','TRX-20251224061409-0BB1-R1766562954','2025-12-24 06:14:09',2500.00,0.00,15000.00,0.00,'sudah_bayar','bank_transfer','fa0df555-4600-452a-bd25-5423f447791d','bank_transfer','2025-12-24 07:56:20','tesss1, tes1, tes1, 1321','kurir','sampai',1,'RESI-20251224061410-6534',NULL,'2025-12-23 22:14:09','2026-01-04 23:04:15'),(19,4,NULL,'TRX-20251224080402-979E',NULL,'2025-12-24 08:04:02',60000.00,3000.00,15000.00,0.00,'sudah_bayar','bank_transfer','bc6d2c27-89bd-4cb8-a0ee-4faa370bf430','bank_transfer','2025-12-24 08:04:19','tesss1, tes1, tes1, 1321','kurir','dikemas',1,NULL,NULL,'2025-12-24 00:04:02','2025-12-24 00:04:19'),(20,4,NULL,'TRX-20251224122633-154E',NULL,'2025-12-24 12:26:33',185000.00,9250.00,0.00,0.00,'belum_bayar',NULL,'fc086507-8174-46cc-95b6-a9d3019be14b',NULL,NULL,'Ambil sendiri di toko','ambil_sendiri','pending',NULL,NULL,NULL,'2025-12-24 04:26:33','2025-12-24 04:26:34'),(21,4,NULL,'TRX-20251224122743-D7AF','TRX-20251224122743-D7AF-R1766579427','2025-12-24 12:27:43',105000.00,5250.00,0.00,0.00,'sudah_bayar','bank_transfer','1b5fd931-e52a-4067-b849-92980eac0386','bank_transfer','2025-12-24 12:30:50','Ambil sendiri di toko','ambil_sendiri','siap_diambil',NULL,NULL,NULL,'2025-12-24 04:27:43','2025-12-24 04:30:50'),(22,3,NULL,'TRX-20251225054752-8550',NULL,'2025-12-25 05:47:52',150000.00,7500.00,0.00,0.00,'kadaluarsa',NULL,'4287f000-065d-4a5c-b7dd-51f07e6e359c',NULL,NULL,'Ambil sendiri di toko','ambil_sendiri','pending',NULL,NULL,NULL,'2025-12-24 21:47:52','2025-12-24 23:13:44'),(23,3,NULL,'TRX-20251225073334-F64E','TRX-20251225073334-F64E-R1766649163','2025-12-25 07:33:34',140000.00,7000.00,0.00,0.00,'sudah_bayar','bank_transfer','3a22093b-818d-42e1-b69b-9028f122d447','bank_transfer','2025-12-25 07:52:59','Ambil sendiri di toko','ambil_sendiri','siap_diambil',NULL,NULL,NULL,'2025-12-24 23:33:34','2025-12-24 23:52:59'),(24,3,NULL,'TRX-20251225073821-8C9D','TRX-20251225073821-8C9D-R1766925793','2025-12-25 07:38:21',60000.00,3000.00,0.00,0.00,'sudah_bayar','qris','4b86e66f-4107-409c-bee2-68ffa656575d','qris','2025-12-28 12:43:34','Ambil sendiri di toko','ambil_sendiri','siap_diambil',NULL,NULL,NULL,'2025-12-24 23:38:21','2025-12-28 04:43:34'),(25,3,NULL,'TRX-20251225075422-B267',NULL,'2025-12-25 07:54:22',130000.00,6500.00,15000.00,0.00,'sudah_bayar','bank_transfer','d930a214-fddf-4bbd-a34f-d8bb674f4bb8','bank_transfer','2025-12-25 07:54:43','bali, Kuta, Denpasar, 2145','kurir','dikemas',3,NULL,NULL,'2025-12-24 23:54:22','2025-12-24 23:54:43'),(26,3,NULL,'TRX-20251227022032-57FB','TRX-20251227022032-57FB-R1766923180','2025-12-27 02:20:32',135000.00,6750.00,15000.00,0.00,'sudah_bayar','qris','aef3e168-4f66-4d65-9f33-5010fd5be4e4','qris','2025-12-28 12:00:00','bali, Kuta, Denpasar, 2145','kurir','dikemas',3,NULL,NULL,'2025-12-26 18:20:32','2025-12-28 04:00:00'),(27,1,2,'TRX-20251228102543-B951','TRX-20251228102543-B951-R1766917595','2025-12-28 10:25:43',52500.00,2625.00,15000.00,10000.00,'sudah_bayar','bank_transfer','ef63578f-89ec-4df8-9654-f226f2629f62','bank_transfer','2025-12-28 10:26:54','jl. nakula no 07, br. negari sading, Mengwi, Badung, 80351','kurir','dikemas',5,NULL,NULL,'2025-12-28 02:25:43','2025-12-28 02:26:54'),(28,1,2,'TRX-20251228103427-2CFA',NULL,'2025-12-28 10:34:27',85000.00,4250.00,0.00,0.00,'sudah_bayar','bank_transfer','13112c0e-e65a-4434-bd37-e97b2fe6a1e5','bank_transfer','2025-12-28 10:34:44','Jl. Gunung Guntur Gang Taman Sari 1 No.7B, Padang Sambian Kelod, Padangsambian Kelod, Denpasar Barat, Denpasar, Bali, 80117','ambil_sendiri','siap_diambil',NULL,NULL,NULL,'2025-12-28 02:34:27','2025-12-28 02:34:44'),(29,3,1,'TRX-20251228124414-747D',NULL,'2025-12-28 12:44:14',195000.00,9750.00,15000.00,0.00,'sudah_bayar','qris','9cc47aa8-61c7-44db-9b5a-b0f438c43f10','qris','2025-12-28 12:44:31','bali, Kuta, Denpasar, 2145','kurir','dikemas',3,NULL,NULL,'2025-12-28 04:44:14','2025-12-28 04:44:31'),(30,4,NULL,'TRX-20251230034138-96CE',NULL,'2025-12-30 03:41:38',110000.00,5500.00,0.00,0.00,'belum_bayar',NULL,'7c8455ce-db11-43d9-a8bb-c436ccaedbfc',NULL,NULL,'Ambil sendiri di toko','ambil_sendiri','pending',NULL,NULL,NULL,'2025-12-29 19:41:38','2025-12-29 19:41:38'),(31,3,1,'TRX-20251231025733-25FE',NULL,'2025-12-31 02:57:33',410000.00,20500.00,15000.00,0.00,'sudah_bayar','qris','65d6a47a-7ae3-4c5c-a8d2-4bf5b79d3a18','qris','2025-12-31 02:59:14','bali, Kuta, Denpasar, 2145','kurir','dikemas',3,NULL,NULL,'2025-12-30 18:57:33','2025-12-30 18:59:14'),(32,1,2,'TRX-20251231032933-B2A9',NULL,'2025-12-31 03:29:33',105000.00,5250.00,15000.00,0.00,'sudah_bayar','qris','801daa7c-5c06-46ed-b539-42e5ecb28632','qris','2025-12-31 03:29:58','jl. nakula no 07, br. negari sading, Mengwi, Badung, 80351','kurir','sampai',5,'RESI-20251231032958-9364',NULL,'2025-12-30 19:29:33','2025-12-30 19:31:49'),(33,5,2,'TRX-20251231050550-0E42',NULL,'2025-12-31 05:05:50',95000.00,0.00,15000.00,10000.00,'sudah_bayar','bank_transfer','b23d559b-175a-4d62-9e4e-12868da43f95','bank_transfer','2025-12-31 05:06:22','jl. nakula no 07, br. negarei lingkungan umahanyar kaja sading, Mengwi, Badung, 80351','kurir','sampai',6,'RESI-20251231050622-B1BF','Tambahkan paperbag lagi','2025-12-30 21:05:50','2025-12-30 21:45:26'),(34,5,2,'TRX-20251231060212-5B2F',NULL,'2025-12-31 06:02:12',170000.00,0.00,0.00,0.00,'sudah_bayar','bank_transfer','40f6e19d-4c9c-4ce9-9da7-e904b89de259','bank_transfer','2025-12-31 06:02:40','Jl. Gunung Guntur Gang Taman Sari 1 No.7B, Padang Sambian Kelod, Padangsambian Kelod, Denpasar Barat, Denpasar, Bali, 80117','ambil_sendiri','siap_diambil',NULL,NULL,NULL,'2025-12-30 22:02:12','2025-12-30 22:02:40'),(35,7,2,'TRX-20260105042948-6708',NULL,'2026-01-05 04:29:48',76000.00,0.00,0.00,10000.00,'sudah_bayar','qris','7e17d88f-533e-44f5-9feb-5233aaa0a098','qris','2026-01-05 04:30:09','Jl. Gunung Guntur Gang Taman Sari 1 No.7B, Padang Sambian Kelod, Padangsambian Kelod, Denpasar Barat, Denpasar, Bali, 80117','ambil_sendiri','siap_diambil',NULL,NULL,NULL,'2026-01-04 20:29:48','2026-01-04 20:30:09'),(36,5,2,'TRX-20260105062640-83F3',NULL,'2026-01-05 06:26:40',156000.00,7800.00,15000.00,0.00,'sudah_bayar','qris','0c10fdca-8635-4ed0-bdc1-f569a186ccab','qris','2026-01-05 06:26:58','jl. nakula no 07, br. negarei lingkungan umahanyar kaja sading, Mengwi, Badung, 80351','kurir','dikirim',6,'RESI-20260105062658-36D3',NULL,'2026-01-04 22:26:40','2026-01-04 23:50:52'),(37,7,NULL,'TRX-20260105112704-8891',NULL,'2026-01-05 11:27:04',150000.00,7500.00,0.00,0.00,'sudah_bayar','qris','94a8daf4-998a-4ce7-9590-8b6ee38725e9','qris','2026-01-05 11:27:23','Ambil sendiri di toko','ambil_sendiri','siap_diambil',NULL,NULL,NULL,'2026-01-05 03:27:04','2026-01-05 03:27:23'),(38,3,NULL,'TRX-20260105113016-6E22',NULL,'2026-01-05 11:30:16',9500.00,475.00,0.00,0.00,'kadaluarsa',NULL,'ce0497f8-b350-4355-b845-c63859b7610a',NULL,NULL,'Ambil sendiri di toko','ambil_sendiri','pending',NULL,NULL,NULL,'2026-01-05 03:30:16','2026-01-05 04:06:27');
/*!40000 ALTER TABLE `tb_transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_wishlist`
--

DROP TABLE IF EXISTS `tb_wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_wishlist` (
  `id_wishlist` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pelanggan` bigint unsigned NOT NULL,
  `id_produk` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_wishlist`),
  KEY `tb_wishlist_id_pelanggan_foreign` (`id_pelanggan`),
  KEY `tb_wishlist_id_produk_foreign` (`id_produk`),
  CONSTRAINT `tb_wishlist_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  CONSTRAINT `tb_wishlist_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_wishlist`
--

LOCK TABLES `tb_wishlist` WRITE;
/*!40000 ALTER TABLE `tb_wishlist` DISABLE KEYS */;
INSERT INTO `tb_wishlist` VALUES (4,3,45,'2025-12-22 05:44:08','2025-12-22 05:44:08'),(5,4,44,'2025-12-22 05:49:59','2025-12-22 05:49:59'),(6,4,39,'2025-12-29 19:42:18','2025-12-29 19:42:18'),(7,5,21,'2025-12-30 21:01:36','2025-12-30 21:01:36'),(8,5,19,'2025-12-30 21:01:47','2025-12-30 21:01:47');
/*!40000 ALTER TABLE `tb_wishlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_messages`
--

DROP TABLE IF EXISTS `ticket_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_messages_ticket_id_index` (`ticket_id`),
  KEY `ticket_messages_user_id_index` (`user_id`),
  CONSTRAINT `ticket_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_messages`
--

LOCK TABLES `ticket_messages` WRITE;
/*!40000 ALTER TABLE `ticket_messages` DISABLE KEYS */;
INSERT INTO `ticket_messages` VALUES (1,1,11,'Hallo',0,'2025-12-22 22:35:07','2025-12-22 22:35:07'),(2,1,3,'Alamat saya salah',0,'2025-12-22 22:35:21','2025-12-22 22:35:21'),(3,1,11,'tes selesai',0,'2025-12-22 22:35:54','2025-12-22 22:35:54'),(4,3,3,'suruh cuci aja',0,'2025-12-23 22:31:45','2025-12-23 22:31:45'),(5,3,9,'ohh gru ya kak? perlu dipakaikan pewangi? agar dia bisa membelinya di ayu mart',0,'2025-12-23 22:32:56','2025-12-23 22:32:56'),(6,3,3,'iyaaaa',0,'2025-12-23 22:33:11','2025-12-23 22:33:11'),(7,4,3,'hai',0,'2025-12-24 04:37:01','2025-12-24 04:37:01'),(8,4,9,'yashh',0,'2025-12-24 04:37:38','2025-12-24 04:37:38'),(9,5,15,'hallo kak mohon di tunggu yaa',0,'2025-12-30 21:34:52','2025-12-30 21:34:52');
/*!40000 ALTER TABLE `ticket_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_ticket_number_unique` (`ticket_number`),
  KEY `tickets_status_index` (`status`),
  KEY `tickets_priority_index` (`priority`),
  KEY `tickets_user_id_index` (`user_id`),
  KEY `tickets_assigned_to_index` (`assigned_to`),
  CONSTRAINT `tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,'TKT-ZBYECKNF',11,3,'salah memasukan alamat pengiriman','salah alamat','medium','closed',NULL,'2025-12-22 22:41:39','2025-12-22 04:22:33','2025-12-22 22:41:39'),(2,'TKT-UU1E3T83',9,NULL,'Tes ticket','tesssss','low','open','Akun',NULL,'2025-12-22 06:57:08','2025-12-22 06:57:08'),(3,'TKT-UMU0ZWLX',9,3,'jid','jinnya adhim bauk','high','resolved','Pesanan','2025-12-23 22:33:34','2025-12-23 22:29:53','2025-12-23 22:33:34'),(4,'TKT-EEOHV6XE',9,3,'hshuw','jdshbu','high','in_progress','Pembayaran',NULL,'2025-12-24 04:35:59','2025-12-24 04:37:01'),(5,'TKT-GFOB2DQ7',12,15,'Pengiriman tidak dikirim\"','tolong kirimkan barang secepatnya ya kak, karena saya butuh','medium','closed','Pengiriman','2025-12-30 21:41:32','2025-12-30 21:09:30','2025-12-30 21:41:32'),(6,'TKT-VZR1UJOB',11,NULL,'aaa','aaa','high','open','Produk',NULL,'2026-01-04 21:43:14','2026-01-04 21:43:14');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Owner','owner@crm.com',NULL,'$2y$12$SyXHMbpY6opOlQjCnALyLex/4SfrfJkHDJja6Ou3Y/8DD8j1NPL.y','081234567890',NULL,NULL,NULL,NULL,1,NULL,'2025-12-20 19:29:27','2025-12-21 22:19:05'),(2,2,'Admin','admin@crm.com',NULL,'$2y$12$O/DBQ2tHviCtawNUqxirYuSC.beJw3A4IK5b1EJ6UpjnbfHM.sZDC','081234567891',NULL,NULL,NULL,NULL,1,NULL,'2025-12-20 19:29:27','2025-12-20 19:29:27'),(3,3,'Customer Service','cs@crm.com',NULL,'$2y$12$JAKQJ8KTNt.TomumNCPf8.AFPTCqFkz5qT.fAt6NOkPIcfrEDb/xy','081234567892',NULL,NULL,NULL,NULL,1,NULL,'2025-12-20 19:29:27','2025-12-20 19:29:27'),(4,4,'Kurir','kurir@crm.com',NULL,'$2y$12$F0ShLWsd.ywl7w.MJO/qWugKmfdsfzFKKJQ2V3RztCNoAr.W2Eibq','081234567893',NULL,NULL,NULL,NULL,1,NULL,'2025-12-20 19:29:27','2025-12-20 19:29:27'),(5,5,'Pelanggan Demo','pelanggan@crm.com',NULL,'$2y$12$EVdB..ktvuzGaPk2rvSSAO4enq8Vx8yTJ5C9/vgE0aGyjRLF/IZqO','81234567890','Jl. Contoh No. 123, RT 01/RW 02, Kelurahan Test','Jakarta','12345',NULL,1,NULL,'2025-12-20 19:29:28','2025-12-21 07:59:06'),(9,5,'indah','indah@gmail.com',NULL,'$2y$12$VIVf7M/WPSVmJ5zwfTFb.ezuzGmNsJjZ1S9fEN.vy8ZZm2TWlH89G','085829295163','hhatsvhb',NULL,NULL,NULL,1,NULL,'2025-12-21 22:34:38','2025-12-21 22:34:38'),(11,5,'Adhim satya nugraha','adhim@gmail.com',NULL,'$2y$12$JaGkv4RjG9I34avvhnoXg.WP8f1M.exu9hFcNdQwBhCf70RSvR/V2','082478647110','adhim',NULL,NULL,NULL,1,NULL,'2025-12-22 01:54:58','2025-12-22 01:54:58'),(12,5,'Indah Damayanti','indahdamayanti411@gmail.com',NULL,'$2y$12$nVpC0/VOYcccw.RGUCM8Ae2geDEHPBwOQa2X58fwXDqEEl.P7pKNq','085829295163','jl. nakula no. 07 br. negari sading','Badung','80351',NULL,1,NULL,'2025-12-30 20:59:15','2025-12-30 21:12:03'),(13,1,'adhim','adhim@crm.com',NULL,'$2y$12$NdZ1dDK642FvHuxX908vfesevaAEUahRzVSqQc5ZP9gs2z15WC/a6','085829295163',NULL,NULL,NULL,NULL,1,NULL,'2025-12-30 21:26:01','2025-12-30 21:26:01'),(14,2,'indah','indah@crm.com',NULL,'$2y$12$yWO2a4geSCKfUj6MwuME3ex7GbJMzeRhyE76BAwJkGvkBJ5.GecC2','085829295163',NULL,NULL,NULL,NULL,1,NULL,'2025-12-30 21:27:03','2025-12-30 21:27:03'),(15,3,'dini','dini@crm.com',NULL,'$2y$12$tBMcGb9OTwQ79iw8uXvQYuiFGb/v7/mk.XQUS76aqPY6T9AGiN7H6','085829295163',NULL,NULL,NULL,NULL,1,NULL,'2025-12-30 21:27:36','2025-12-30 21:27:36'),(16,4,'kris','kris@crm.com',NULL,'$2y$12$buBYG5iSGqSoV05QkDXZw.OKR6s9CPHgQrvlhp1jhQl7mP8atTfqa','085829295163',NULL,NULL,NULL,NULL,1,NULL,'2025-12-30 21:28:07','2025-12-30 21:28:07'),(17,5,'dini','dini@gmail.com',NULL,'$2y$12$j1d.QZzYkFt3O3gedxDMX.7bEBlXIl4ozacU.spVorQqwUBWMuqNS','085829295163','jl jembrana',NULL,NULL,NULL,1,NULL,'2025-12-30 21:53:37','2025-12-30 21:53:37'),(18,5,'adhim14','adhim14@gmail.com',NULL,'$2y$12$VyjygSlDOp6Wxc1bLGZa/efvGOOvAyw9ixwN9S3PZ78yBBKmdtVme','082578613840','adhim',NULL,NULL,NULL,1,NULL,'2026-01-04 20:28:47','2026-01-04 20:59:04');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-08 13:19:29
