/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: crud_facturas
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
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
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `razonSocial` varchar(255) NOT NULL,
  `nombreComercial` varchar(255) DEFAULT NULL,
  `rfc` varchar(13) DEFAULT NULL,
  `correoElectronico` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccionFiscal` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_rfc_unique` (`rfc`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES
(1,'Comercializadora del Centro SA de CV','Comercial Centro','CCE120101AB1','compras@comercialcentro.mx','5550011001','Av. Reforma 120, Col. Centro, Ciudad de Mexico',1,'2026-03-03 01:34:24','2026-03-03 01:34:24'),
(2,'Servicios Industriales del Norte SA de CV','SINorte','SIN150505CD2','facturacion@sinorte.mx','8180012002','Blvd. Industria 450, Parque Industrial, Monterrey',1,'2026-03-03 01:34:24','2026-03-03 01:34:24');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura_items`
--

DROP TABLE IF EXISTS `factura_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `facturaId` bigint(20) unsigned NOT NULL,
  `productoId` bigint(20) unsigned DEFAULT NULL,
  `orden` int(10) unsigned NOT NULL DEFAULT 1,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` decimal(12,3) NOT NULL DEFAULT 1.000,
  `precioUnitario` decimal(12,2) NOT NULL,
  `porcentajeImpuesto` decimal(5,2) NOT NULL DEFAULT 0.00,
  `porcentajeDescuento` decimal(5,2) NOT NULL DEFAULT 0.00,
  `totalLinea` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `factura_items_facturaid_foreign` (`facturaId`),
  KEY `factura_items_orden_index` (`orden`),
  KEY `factura_items_productoid_index` (`productoId`),
  CONSTRAINT `factura_items_facturaid_foreign` FOREIGN KEY (`facturaId`) REFERENCES `facturas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_items`
--

LOCK TABLES `factura_items` WRITE;
/*!40000 ALTER TABLE `factura_items` DISABLE KEYS */;
INSERT INTO `factura_items` VALUES
(2,1,4,1,'Curso de capacitacion para usuarios finales',1.002,4500.00,16.00,0.00,5230.44,'2026-03-03 01:51:09','2026-03-03 01:51:09'),
(3,2,3,1,'Mantenimiento preventivo para equipo de computo',1.000,1250.00,16.00,0.00,1450.00,'2026-03-03 02:00:43','2026-03-03 02:00:43'),
(4,3,2,1,'Licencia anual de uso de software administrativo',1.000,3200.00,16.00,0.00,3712.00,'2026-03-03 02:49:04','2026-03-03 02:49:04'),
(5,4,4,1,'Curso de capacitacion para usuarios finales',1.000,4500.00,16.00,0.00,5220.00,'2026-03-03 02:50:27','2026-03-03 02:50:27'),
(6,4,1,2,'Servicio de consultoria tecnica por hora',1.000,850.00,16.00,0.00,986.00,'2026-03-03 02:50:27','2026-03-03 02:50:27'),
(7,5,1,1,'Consultoria tecnica por hora',2.000,850.00,16.00,0.00,1972.00,'2026-03-03 03:02:28','2026-03-03 03:02:28'),
(8,5,2,2,'Licencia anual',1.000,3200.00,16.00,5.00,3526.40,'2026-03-03 03:02:28','2026-03-03 03:02:28');
/*!40000 ALTER TABLE `factura_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `facturas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `clienteId` bigint(20) unsigned NOT NULL,
  `usuarioId` bigint(20) unsigned NOT NULL,
  `folio` varchar(255) NOT NULL,
  `fechaEmision` date NOT NULL,
  `fechaVencimiento` date DEFAULT NULL,
  `moneda` varchar(3) NOT NULL DEFAULT 'MXN',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `impuesto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descuento` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `estado` enum('borrador','emitida','pagada','cancelada') NOT NULL DEFAULT 'borrador',
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facturas_folio_unique` (`folio`),
  KEY `facturas_clienteid_foreign` (`clienteId`),
  KEY `facturas_usuarioid_foreign` (`usuarioId`),
  KEY `facturas_fechaemision_index` (`fechaEmision`),
  KEY `facturas_estado_index` (`estado`),
  CONSTRAINT `facturas_clienteid_foreign` FOREIGN KEY (`clienteId`) REFERENCES `clientes` (`id`),
  CONSTRAINT `facturas_usuarioid_foreign` FOREIGN KEY (`usuarioId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
INSERT INTO `facturas` VALUES
(1,1,1,'FAC-000001','2026-03-02','2026-03-09','MXN',4509.00,721.44,0.00,5230.44,'cancelada','Alguna nota1','2026-03-03 01:51:03','2026-03-03 01:51:17'),
(2,2,1,'FAC-000002','2026-03-02','2026-03-02','MXN',1250.00,200.00,0.00,1450.00,'emitida',NULL,'2026-03-03 02:00:43','2026-03-03 03:00:31'),
(3,2,1,'FAC-000003','2026-03-02',NULL,'MXN',3200.00,512.00,0.00,3712.00,'cancelada',NULL,'2026-03-03 02:49:04','2026-03-03 03:00:36'),
(4,2,1,'FAC-000004','2026-03-02',NULL,'MXN',5350.00,856.00,0.00,6206.00,'cancelada',NULL,'2026-03-03 02:50:27','2026-06-10 11:17:04'),
(5,1,1,'FAC-000005','2026-03-02','2026-03-15','MXN',4900.00,758.40,160.00,5498.40,'borrador','Factura de prueba desde curl','2026-03-03 03:02:28','2026-03-03 03:02:28');
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
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
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_03_02_191341_create_clientes_table',1),
(5,'2026_03_02_191342_create_facturas_table',1),
(6,'2026_03_02_191343_create_factura_items_table',1),
(7,'2026_03_02_191636_create_productos_table',1),
(8,'2026_03_02_201510_create_personal_access_tokens_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES
(1,'App\\Models\\User',1,'dispositivo-api','0bd86621d37fce35d7c4de5d6b3f5ad72b83f3322eeffa9978652c503e477c99','[\"*\"]','2026-03-03 03:02:28',NULL,'2026-03-03 02:15:42','2026-03-03 03:02:28'),
(2,'App\\Models\\User',1,'dispositivo-api','c3bbd9ebb23287205ec0efc04a3c1b97926037f77f8158ef749c4b824dfbb5e7','[\"*\"]',NULL,NULL,'2026-06-10 10:39:57','2026-06-10 10:39:57');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `unidadMedida` varchar(20) NOT NULL DEFAULT 'pieza',
  `precioBase` decimal(12,2) NOT NULL,
  `porcentajeImpuesto` decimal(5,2) NOT NULL DEFAULT 0.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_codigo_unique` (`codigo`),
  KEY `productos_nombre_index` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES
(1,'PROD-001','Consultoria Tecnica','Servicio de consultoria tecnica por hora','hora',850.00,16.00,1,'2026-03-03 01:34:24','2026-03-03 01:34:24'),
(2,'PROD-002','Licencia de Software','Licencia anual de uso de software administrativo','licencia',3200.00,16.00,1,'2026-03-03 01:34:24','2026-03-03 01:34:24'),
(3,'PROD-003','Mantenimiento Preventivo','Mantenimiento preventivo para equipo de computo','servicio',1250.00,16.00,1,'2026-03-03 01:34:24','2026-03-03 01:34:24'),
(4,'PROD-004','Capacitacion de Personal','Curso de capacitacion para usuarios finales','curso',4500.00,16.00,1,'2026-03-03 01:34:24','2026-03-03 01:34:24'),
(5,'PROD-005','Soporte Remoto','Soporte remoto para incidencias tecnicas','evento',600.00,16.00,1,'2026-03-03 01:34:24','2026-03-03 01:34:24');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
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
INSERT INTO `sessions` VALUES
('g1qK8rlB9sDk4kCjJqSl0RNqgQXRMmNVcIM6mUOw',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSEFCVGRmdjluc0V5WnRnWUNTREd5YTRNR3k1dnB1VmhLQ3VUbUU4SSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4NSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1781068634),
('QpjN1k4NCbYjvQz2etO3HhqwXFQGl96ubKYMKI2G',NULL,'127.0.0.1','curl/8.5.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSWk3M20zUElGQ0l2eW5JbUNCRXRVSDJtVjBaR1NSNEl2QlZoYm9NZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4NSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1781066397),
('wva354miscBR8GwSHVwMvOeMqwG9QZtAMBKGCA6v',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoibEk5bkNRbEkxNzBja0pQN3RENmx0SkFRNGd2MFhPemtvakFCc3lCNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1772500277),
('zNsZ1W2TXRl1X8bOvS5Hq8N5wcgMd8KgvG5Uremv',NULL,'127.0.0.1','curl/8.5.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVjRFR2RmMGxPZzNqWGJLYjA4UDlYMW1wcXlCQ253YXplUGNtUHF0MiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4NS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1781066397),
('ZV0xagHfGOvVvulfx9oZlLKXfl0Kl9rpZv2JtvXC',NULL,'127.0.0.1','curl/8.5.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRzZ0REl5cWRvZ1I4N2o4aHBBTGd2Z2NvSXZWWU9jNmpYaEJqRWF0VSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4NS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1781067697);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Administrador','admin@facturas.local','2026-03-03 01:34:24','$2y$12$gardhynCSVp40KSUqLHaHuRq83snuxtRT0CgQE4oM9x.t.Q9NNuGC',NULL,'2026-03-03 01:34:24','2026-03-03 01:34:24');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'crud_facturas'
--

--
-- Dumping routines for database 'crud_facturas'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-09 23:19:49
