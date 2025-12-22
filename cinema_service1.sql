-- --------------------------------------------------------
-- Máy chủ:                      127.0.0.1
-- Phiên bản máy chủ:            8.0.43 - MySQL Community Server - GPL
-- HĐH máy chủ:                  Linux
-- HeidiSQL Phiên bản:           12.13.0.7147
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Đang kết xuất đổ cấu trúc cơ sở dữ liệu cho cinema_service1
CREATE DATABASE IF NOT EXISTS `cinema_service1` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `cinema_service1`;

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.account
CREATE TABLE IF NOT EXISTS `account` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role` enum('CUSTOMER','STAFF','ADMIN') NOT NULL DEFAULT 'CUSTOMER',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_email_unique` (`email`),
  UNIQUE KEY `account_google_id_unique` (`google_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.account: ~8 rows (xấp xỉ)
INSERT INTO `account` (`id`, `email`, `google_id`, `phone`, `password_hash`, `full_name`, `role`, `is_active`) VALUES
	(1, 'admin@cinema.com', NULL, '0901234567', '$2y$12$LQv3c1yycLj9LZUQPzKdeeqFJF.RaU6i/eBGE.qT5r6ZKF5qIVXXu', 'Administrator', 'ADMIN', 1),
	(2, 'staff1@cinema.com', NULL, '0902345678', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', 'STAFF', 1),
	(3, 'staff2@cinema.com', NULL, '0903456789', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', 'STAFF', 1),
	(4, 'customer1@gmail.com', NULL, '0904567890', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn C', 'CUSTOMER', 1),
	(5, 'customer2@gmail.com', NULL, '0905678901', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị D', 'CUSTOMER', 1),
	(6, 'customer3@gmail.com', 'google_123456', '0906789012', NULL, 'Hoàng Văn E', 'CUSTOMER', 1),
	(7, 'customer4@gmail.com', 'google_789012', '0907890123', NULL, 'Vũ Thị F', 'CUSTOMER', 1),
	(8, 'customer5@gmail.com', NULL, '0908901234', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Đặng Văn G', 'CUSTOMER', 1);

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.cache: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.cache_locks: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.failed_jobs: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.job_batches: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.jobs: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.migrations: ~21 rows (xấp xỉ)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_11_06_143152_create_account_table', 1),
	(5, '2025_11_06_143152_create_movie_table', 1),
	(6, '2025_11_06_143152_create_order_line_table', 1),
	(7, '2025_11_06_143152_create_orders_table', 1),
	(8, '2025_11_06_143152_create_product_table', 1),
	(9, '2025_11_06_143152_create_screen_table', 1),
	(10, '2025_11_06_143152_create_seat_lock_table', 1),
	(11, '2025_11_06_143152_create_seat_table', 1),
	(12, '2025_11_06_143152_create_showtime_table', 1),
	(13, '2025_11_06_143155_add_foreign_keys_to_order_line_table', 1),
	(14, '2025_11_06_143155_add_foreign_keys_to_orders_table', 1),
	(15, '2025_11_06_143155_add_foreign_keys_to_seat_lock_table', 1),
	(16, '2025_11_06_143155_add_foreign_keys_to_seat_table', 1),
	(17, '2025_11_06_143155_add_foreign_keys_to_showtime_table', 1),
	(18, '2025_11_06_144023_create_personal_access_tokens_table', 1),
	(19, '2025_11_18_061957_add_colums_poster_genre_into_table_movie', 1),
	(20, '2025_11_27_153742_add_is_active_to_movie_table', 1),
	(57, '2025_12_09_024545_add_google_id_to_account_table', 2);

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.movie
CREATE TABLE IF NOT EXISTS `movie` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `duration_min` smallint NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `rating_code` varchar(10) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `movie_title_index` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.movie: ~15 rows (xấp xỉ)
INSERT INTO `movie` (`id`, `title`, `duration_min`, `genre`, `poster`, `rating_code`, `is_active`) VALUES
	(1, 'Avatar: The Way of Water', 192, 'Sci-Fi, Adventure', 'https://image.tmdb.org/t/p/w500/94xxm5701CzOdJdUEdIuwqZaowx.jpg', 'T13', 1),
	(2, 'Top Gun: Maverick', 131, 'Action, Drama', 'https://image.tmdb.org/t/p/w500/62HCnUTziyWcpDaBO2i1DX17ljH.jpg', 'T13', 1),
	(3, 'Avengers: Endgame', 181, 'Action, Sci-Fi', 'https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg', 'T13', 1),
	(4, 'The Batman', 176, 'Action, Crime', 'https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg', 'T16', 1),
	(5, 'Spider-Man: No Way Home', 148, 'Action, Adventure', 'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg', 'T13', 1),
	(6, 'Frozen II', 103, 'Animation, Family', 'https://image.tmdb.org/t/p/w500/pjeMs3yqRmFL3giJy4PMXWZTTPa.jpg', 'P', 1),
	(7, 'Dune: Part Two', 166, 'Sci-Fi, Adventure', 'https://image.tmdb.org/t/p/w500/8b8R8l88Qje9dn9OE8PY05Nxl1X.jpg', 'T13', 1),
	(8, 'Oppenheimer', 180, 'Biography, Drama', 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg', 'T16', 1),
	(9, 'Barbie', 114, 'Comedy, Adventure', 'https://image.tmdb.org/t/p/w500/iuFNMS8U5cb6xfzi51Dbkovj7vM.jpg', 'T13', 1),
	(10, 'The Super Mario Bros. Movie', 92, 'Animation, Family', 'https://image.tmdb.org/t/p/w500/qNBAXBIQlnOThrVvA6mA2B5ggV6.jpg', 'P', 1),
	(11, 'Guardians of the Galaxy Vol. 3', 150, 'Action, Sci-Fi', 'https://image.tmdb.org/t/p/w500/r2J02Z2OpNTctfOSN1Ydgii51I3.jpg', 'T13', 1),
	(12, 'Fast X', 141, 'Action, Crime', 'https://image.tmdb.org/t/p/w500/fiVW06jE7z9YnO4trhaMEdclSiC.jpg', 'T16', 1),
	(13, 'The Little Mermaid', 135, 'Fantasy, Musical', 'https://image.tmdb.org/t/p/w500/ym1dxyOk4jFcSl4Q2zmRrA5BEEN.jpg', 'P', 1),
	(14, 'Mission: Impossible 7', 163, 'Action, Thriller', 'https://image.tmdb.org/t/p/w500/NNxYkU70HPurnNCSiCjYAmacwm.jpg', 'T13', 1),
	(15, 'Elemental', 109, 'Animation, Family', 'https://image.tmdb.org/t/p/w500/4Y1WNkd88JXmGfhtWR7dmDAo1T2.jpg', 'P', 1);

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.order_line
CREATE TABLE IF NOT EXISTS `order_line` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` bigint NOT NULL,
  `item_type` enum('TICKET','PRODUCT') NOT NULL,
  `seat_id` bigint DEFAULT NULL,
  `product_id` bigint DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `unit_price` int NOT NULL,
  `line_total` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_line_order_id_item_type_index` (`order_id`,`item_type`),
  KEY `order_line_seat_id_index` (`seat_id`),
  KEY `order_line_product_id_index` (`product_id`),
  CONSTRAINT `order_line_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_line_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `order_line_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seat` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.order_line: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `channel` enum('WEB','POS') NOT NULL,
  `account_id` bigint DEFAULT NULL,
  `cashier_id` bigint DEFAULT NULL,
  `showtime_id` bigint NOT NULL,
  `status` enum('INIT','PAID','CANCELLED') NOT NULL DEFAULT 'INIT',
  `payment_method` enum('CASH','CARD','EWALLET') DEFAULT NULL,
  `total_amount` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `orders_showtime_id_status_index` (`showtime_id`,`status`),
  KEY `orders_channel_index` (`channel`),
  KEY `orders_account_id_index` (`account_id`),
  KEY `orders_cashier_id_index` (`cashier_id`),
  CONSTRAINT `orders_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `orders_showtime_id_foreign` FOREIGN KEY (`showtime_id`) REFERENCES `showtime` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.orders: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.password_reset_tokens: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.personal_access_tokens: ~1 rows (xấp xỉ)
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(60, 'App\\Models\\Account', 14, 'admin', '504c85b217621d1426193134949522e95ea23a5c27afb190f81ce56019fb9f6d', '["*"]', '2025-12-09 10:08:03', NULL, '2025-12-09 10:00:54', '2025-12-09 10:08:03');

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.product
CREATE TABLE IF NOT EXISTS `product` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` int NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.product: ~10 rows (xấp xỉ)
INSERT INTO `product` (`id`, `name`, `price`, `is_active`) VALUES
	(1, 'Combo 1 (Bắp + Nước)', 80000, 1),
	(2, 'Combo 2 (Bắp lớn + 2 Nước)', 120000, 1),
	(3, 'Bắp rang bơ (M)', 50000, 1),
	(4, 'Bắp rang bơ (L)', 65000, 1),
	(5, 'Nước ngọt (M)', 35000, 1),
	(6, 'Nước ngọt (L)', 45000, 1),
	(7, 'Combo Couple (2 Bắp + 2 Nước)', 150000, 1),
	(8, 'Combo Gia đình (3 Bắp + 4 Nước)', 250000, 1),
	(9, 'Snack', 25000, 1),
	(10, 'Kẹo', 20000, 1);

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.screen
CREATE TABLE IF NOT EXISTS `screen` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `format` varchar(20) DEFAULT '2D',
  `row_count` int NOT NULL,
  `col_count` int NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `screen_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.screen: ~5 rows (xấp xỉ)
INSERT INTO `screen` (`id`, `code`, `name`, `format`, `row_count`, `col_count`, `is_active`) VALUES
	(1, 'S01', 'Screen 1', '2D', 8, 12, 1),
	(2, 'S02', 'Screen 2', '3D', 10, 14, 1),
	(3, 'S03', 'Screen 3', 'IMAX', 12, 16, 1),
	(4, 'S04', 'Screen 4', '2D', 8, 10, 1),
	(5, 'S05', 'Screen 5', '4DX', 6, 10, 1);

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.seat
CREATE TABLE IF NOT EXISTS `seat` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `screen_id` bigint NOT NULL,
  `row_label` varchar(5) NOT NULL,
  `seat_number` int NOT NULL,
  `seat_type` enum('STANDARD','VIP','COUPLE','ACCESSIBLE') NOT NULL DEFAULT 'STANDARD',
  `is_aisle` tinyint(1) NOT NULL DEFAULT '0',
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `seat_screen_id_row_label_seat_number_unique` (`screen_id`,`row_label`,`seat_number`),
  KEY `seat_screen_id_index` (`screen_id`),
  CONSTRAINT `seat_screen_id_foreign` FOREIGN KEY (`screen_id`) REFERENCES `screen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.seat: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.seat_lock
CREATE TABLE IF NOT EXISTS `seat_lock` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `showtime_id` bigint NOT NULL,
  `seat_id` bigint NOT NULL,
  `account_id` bigint DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seat_lock_showtime_id_seat_id_unique` (`showtime_id`,`seat_id`),
  KEY `seat_lock_seat_id_index` (`seat_id`),
  KEY `seat_lock_account_id_index` (`account_id`),
  KEY `seat_lock_expires_at_index` (`expires_at`),
  CONSTRAINT `seat_lock_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `seat_lock_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `seat_lock_showtime_id_foreign` FOREIGN KEY (`showtime_id`) REFERENCES `showtime` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.seat_lock: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.sessions: ~3 rows (xấp xỉ)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('7mNQqNCcyF7eiDmNOOZOOGoHCuA7905BiIOTVCAa', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicnlzSUJzRk84U2Q2dWRWaTJrQlR4bTh4cGNLcmVBYTVqOUJGUnZnSCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hdXRoL2dvb2dsZSI7fXM6NToic3RhdGUiO3M6NDA6ImdTYlB2eTFvYVJtZzEwVU11VXBKekNvWDRTSkRjSEFEaTB3bkJRV1YiO30=', 1765250223),
	('OikmHs6d8CilI3yOFxvuqUdh8gdxwub0xDUitkJh', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDFaRkJodjRpSUVqdHlNOUh0dTBJOXdkeVdFQ3BHT09Kd3pFS0F6bCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7fX0=', 1765251257),
	('XcBktbvEfR0BvtvGoM2j3sBXn56rX4mmvvOLb9ZO', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSTBsOThsc1ZnY1Fsd0p5b09aVTlhVGVCcjRiSXhSOEdzU1JUQnhUZiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hdXRoL2dvb2dsZSI7fXM6NToic3RhdGUiO3M6NDA6IlhOOXBQUWlQZ2pxa2RWQUg0c2tjR3dMaG12SFUyZXp2SFZtMG9IeXEiO30=', 1765251252);

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.showtime
CREATE TABLE IF NOT EXISTS `showtime` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `movie_id` bigint NOT NULL,
  `screen_id` bigint NOT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `base_price` int NOT NULL,
  `status` enum('SCHEDULED','OPEN','CLOSED','CANCELLED') NOT NULL DEFAULT 'OPEN',
  PRIMARY KEY (`id`),
  UNIQUE KEY `showtime_screen_id_start_at_unique` (`screen_id`,`start_at`),
  KEY `showtime_movie_id_start_at_index` (`movie_id`,`start_at`),
  CONSTRAINT `showtime_movie_id_foreign` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `showtime_screen_id_foreign` FOREIGN KEY (`screen_id`) REFERENCES `screen` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.showtime: ~112 rows (xấp xỉ)
INSERT INTO `showtime` (`id`, `movie_id`, `screen_id`, `start_at`, `end_at`, `base_price`, `status`) VALUES
	(164, 1, 3, '2024-12-16 09:00:00', '2024-12-16 12:12:00', 150000, 'OPEN'),
	(165, 1, 3, '2024-12-16 14:00:00', '2024-12-16 17:12:00', 150000, 'OPEN'),
	(166, 1, 3, '2024-12-16 19:30:00', '2024-12-16 22:42:00', 180000, 'OPEN'),
	(167, 1, 3, '2024-12-17 09:00:00', '2024-12-17 12:12:00', 150000, 'SCHEDULED'),
	(168, 1, 3, '2024-12-17 14:00:00', '2024-12-17 17:12:00', 150000, 'SCHEDULED'),
	(169, 1, 3, '2024-12-17 19:30:00', '2024-12-17 22:42:00', 180000, 'SCHEDULED'),
	(170, 1, 3, '2024-12-18 14:00:00', '2024-12-18 17:12:00', 150000, 'SCHEDULED'),
	(171, 1, 3, '2024-12-19 09:00:00', '2024-12-19 12:12:00', 150000, 'SCHEDULED'),
	(172, 1, 3, '2024-12-20 19:30:00', '2024-12-20 22:42:00', 180000, 'SCHEDULED'),
	(173, 1, 3, '2024-12-21 14:00:00', '2024-12-21 17:12:00', 150000, 'SCHEDULED'),
	(174, 2, 2, '2024-12-16 10:00:00', '2024-12-16 12:11:00', 120000, 'OPEN'),
	(175, 2, 2, '2024-12-16 13:00:00', '2024-12-16 15:11:00', 120000, 'OPEN'),
	(176, 2, 2, '2024-12-16 20:00:00', '2024-12-16 22:11:00', 140000, 'OPEN'),
	(177, 2, 2, '2024-12-18 10:00:00', '2024-12-18 12:11:00', 120000, 'SCHEDULED'),
	(178, 2, 2, '2024-12-19 13:00:00', '2024-12-19 15:11:00', 120000, 'SCHEDULED'),
	(179, 2, 2, '2024-12-20 10:00:00', '2024-12-20 12:11:00', 120000, 'SCHEDULED'),
	(180, 2, 2, '2024-12-21 20:00:00', '2024-12-21 22:11:00', 140000, 'SCHEDULED'),
	(181, 2, 2, '2024-12-22 13:00:00', '2024-12-22 15:11:00', 120000, 'SCHEDULED'),
	(182, 3, 1, '2024-12-16 09:30:00', '2024-12-16 12:31:00', 100000, 'OPEN'),
	(183, 3, 1, '2024-12-16 14:00:00', '2024-12-16 17:01:00', 100000, 'OPEN'),
	(184, 3, 1, '2024-12-16 18:30:00', '2024-12-16 21:31:00', 120000, 'OPEN'),
	(185, 3, 1, '2024-12-19 09:30:00', '2024-12-19 12:31:00', 100000, 'SCHEDULED'),
	(186, 3, 1, '2024-12-20 14:00:00', '2024-12-20 17:01:00', 100000, 'SCHEDULED'),
	(187, 3, 1, '2024-12-21 09:30:00', '2024-12-21 12:31:00', 100000, 'SCHEDULED'),
	(188, 3, 1, '2024-12-22 18:30:00', '2024-12-22 21:31:00', 120000, 'SCHEDULED'),
	(189, 3, 1, '2024-12-23 14:00:00', '2024-12-23 17:01:00', 100000, 'SCHEDULED'),
	(190, 4, 4, '2024-12-16 11:00:00', '2024-12-16 13:56:00', 100000, 'OPEN'),
	(191, 4, 4, '2024-12-16 15:00:00', '2024-12-16 17:56:00', 100000, 'OPEN'),
	(192, 4, 4, '2024-12-16 19:30:00', '2024-12-16 22:26:00', 120000, 'OPEN'),
	(193, 4, 4, '2024-12-17 11:00:00', '2024-12-17 13:56:00', 100000, 'SCHEDULED'),
	(194, 4, 4, '2024-12-18 19:30:00', '2024-12-18 22:26:00', 120000, 'SCHEDULED'),
	(195, 4, 4, '2024-12-21 15:00:00', '2024-12-21 17:56:00', 100000, 'SCHEDULED'),
	(196, 4, 4, '2024-12-23 11:00:00', '2024-12-23 13:56:00', 100000, 'SCHEDULED'),
	(197, 4, 4, '2024-12-24 19:30:00', '2024-12-24 22:26:00', 120000, 'SCHEDULED'),
	(198, 5, 5, '2024-12-16 10:30:00', '2024-12-16 12:58:00', 200000, 'OPEN'),
	(199, 5, 5, '2024-12-16 14:30:00', '2024-12-16 16:58:00', 200000, 'OPEN'),
	(200, 5, 5, '2024-12-16 19:00:00', '2024-12-16 21:28:00', 250000, 'OPEN'),
	(201, 5, 5, '2024-12-17 10:30:00', '2024-12-17 12:58:00', 200000, 'SCHEDULED'),
	(202, 5, 5, '2024-12-18 14:30:00', '2024-12-18 16:58:00', 200000, 'SCHEDULED'),
	(203, 5, 5, '2024-12-19 19:00:00', '2024-12-19 21:28:00', 250000, 'SCHEDULED'),
	(204, 5, 5, '2024-12-22 10:30:00', '2024-12-22 12:58:00', 200000, 'SCHEDULED'),
	(205, 5, 5, '2024-12-24 14:30:00', '2024-12-24 16:58:00', 200000, 'SCHEDULED'),
	(206, 6, 1, '2024-12-17 09:00:00', '2024-12-17 10:43:00', 80000, 'SCHEDULED'),
	(207, 6, 1, '2024-12-17 11:00:00', '2024-12-17 12:43:00', 80000, 'SCHEDULED'),
	(208, 6, 1, '2024-12-18 09:00:00', '2024-12-18 10:43:00', 80000, 'SCHEDULED'),
	(209, 6, 1, '2024-12-22 09:30:00', '2024-12-22 11:13:00', 80000, 'SCHEDULED'),
	(210, 6, 1, '2024-12-24 09:00:00', '2024-12-24 10:43:00', 80000, 'SCHEDULED'),
	(211, 6, 1, '2024-12-25 11:00:00', '2024-12-25 12:43:00', 80000, 'SCHEDULED'),
	(212, 6, 1, '2024-12-26 09:00:00', '2024-12-26 10:43:00', 80000, 'SCHEDULED'),
	(213, 7, 3, '2024-12-18 09:00:00', '2024-12-18 11:46:00', 150000, 'SCHEDULED'),
	(214, 7, 3, '2024-12-18 19:30:00', '2024-12-18 22:16:00', 180000, 'SCHEDULED'),
	(215, 7, 3, '2024-12-22 09:00:00', '2024-12-22 11:46:00', 150000, 'SCHEDULED'),
	(216, 7, 3, '2024-12-22 14:00:00', '2024-12-22 16:46:00', 150000, 'SCHEDULED'),
	(217, 7, 3, '2024-12-23 19:30:00', '2024-12-23 22:16:00', 180000, 'SCHEDULED'),
	(218, 7, 3, '2024-12-25 14:00:00', '2024-12-25 16:46:00', 150000, 'SCHEDULED'),
	(219, 7, 3, '2024-12-27 09:00:00', '2024-12-27 11:46:00', 150000, 'SCHEDULED'),
	(220, 8, 2, '2024-12-17 10:00:00', '2024-12-17 13:00:00', 130000, 'SCHEDULED'),
	(221, 8, 2, '2024-12-17 14:30:00', '2024-12-17 17:30:00', 130000, 'SCHEDULED'),
	(222, 8, 2, '2024-12-17 19:00:00', '2024-12-17 22:00:00', 150000, 'SCHEDULED'),
	(223, 8, 2, '2024-12-20 14:30:00', '2024-12-20 17:30:00', 130000, 'SCHEDULED'),
	(224, 8, 2, '2024-12-23 10:00:00', '2024-12-23 13:00:00', 130000, 'SCHEDULED'),
	(225, 8, 2, '2024-12-25 19:00:00', '2024-12-25 22:00:00', 150000, 'SCHEDULED'),
	(226, 8, 2, '2024-12-28 14:30:00', '2024-12-28 17:30:00', 130000, 'SCHEDULED'),
	(227, 9, 4, '2024-12-17 09:00:00', '2024-12-17 10:54:00', 90000, 'SCHEDULED'),
	(228, 9, 4, '2024-12-18 09:00:00', '2024-12-18 10:54:00', 90000, 'SCHEDULED'),
	(229, 9, 4, '2024-12-19 11:00:00', '2024-12-19 12:54:00', 90000, 'SCHEDULED'),
	(230, 9, 4, '2024-12-22 09:00:00', '2024-12-22 10:54:00', 90000, 'SCHEDULED'),
	(231, 9, 4, '2024-12-25 09:00:00', '2024-12-25 10:54:00', 90000, 'SCHEDULED'),
	(232, 9, 4, '2024-12-27 11:00:00', '2024-12-27 12:54:00', 90000, 'SCHEDULED'),
	(233, 9, 4, '2024-12-29 09:00:00', '2024-12-29 10:54:00', 90000, 'SCHEDULED'),
	(234, 10, 1, '2024-12-21 11:00:00', '2024-12-21 12:32:00', 80000, 'SCHEDULED'),
	(235, 10, 1, '2024-12-25 09:00:00', '2024-12-25 10:32:00', 80000, 'SCHEDULED'),
	(236, 10, 1, '2024-12-26 11:00:00', '2024-12-26 12:32:00', 80000, 'SCHEDULED'),
	(237, 10, 1, '2024-12-28 09:00:00', '2024-12-28 10:32:00', 80000, 'SCHEDULED'),
	(238, 10, 1, '2024-12-29 11:00:00', '2024-12-29 12:32:00', 80000, 'SCHEDULED'),
	(239, 10, 1, '2024-12-31 09:00:00', '2024-12-31 10:32:00', 80000, 'SCHEDULED'),
	(240, 10, 1, '2025-01-01 11:00:00', '2025-01-01 12:32:00', 80000, 'SCHEDULED'),
	(241, 11, 2, '2024-12-19 10:00:00', '2024-12-19 12:30:00', 120000, 'SCHEDULED'),
	(242, 11, 2, '2024-12-19 14:00:00', '2024-12-19 16:30:00', 120000, 'SCHEDULED'),
	(243, 11, 2, '2024-12-19 19:00:00', '2024-12-19 21:30:00', 140000, 'SCHEDULED'),
	(244, 11, 2, '2024-12-24 10:00:00', '2024-12-24 12:30:00', 120000, 'SCHEDULED'),
	(245, 11, 2, '2024-12-26 14:00:00', '2024-12-26 16:30:00', 120000, 'SCHEDULED'),
	(246, 11, 2, '2024-12-29 19:00:00', '2024-12-29 21:30:00', 140000, 'SCHEDULED'),
	(247, 11, 2, '2024-12-31 14:00:00', '2024-12-31 16:30:00', 120000, 'SCHEDULED'),
	(248, 12, 5, '2024-12-20 10:30:00', '2024-12-20 12:51:00', 200000, 'SCHEDULED'),
	(249, 12, 5, '2024-12-20 14:30:00', '2024-12-20 16:51:00', 200000, 'SCHEDULED'),
	(250, 12, 5, '2024-12-20 19:00:00', '2024-12-20 21:21:00', 250000, 'SCHEDULED'),
	(251, 12, 5, '2024-12-23 10:30:00', '2024-12-23 12:51:00', 200000, 'SCHEDULED'),
	(252, 12, 5, '2024-12-25 14:30:00', '2024-12-25 16:51:00', 200000, 'SCHEDULED'),
	(253, 12, 5, '2024-12-27 19:00:00', '2024-12-27 21:21:00', 250000, 'SCHEDULED'),
	(254, 12, 5, '2024-12-30 14:30:00', '2024-12-30 16:51:00', 200000, 'SCHEDULED'),
	(255, 13, 4, '2024-12-20 09:00:00', '2024-12-20 11:15:00', 85000, 'SCHEDULED'),
	(256, 13, 4, '2024-12-22 11:00:00', '2024-12-22 13:15:00', 85000, 'SCHEDULED'),
	(257, 13, 4, '2024-12-26 09:00:00', '2024-12-26 11:15:00', 85000, 'SCHEDULED'),
	(258, 13, 4, '2024-12-28 11:00:00', '2024-12-28 13:15:00', 85000, 'SCHEDULED'),
	(259, 13, 4, '2024-12-30 09:00:00', '2024-12-30 11:15:00', 85000, 'SCHEDULED'),
	(260, 13, 4, '2025-01-01 09:00:00', '2025-01-01 11:15:00', 85000, 'SCHEDULED'),
	(261, 13, 4, '2025-01-02 11:00:00', '2025-01-02 13:15:00', 85000, 'SCHEDULED'),
	(262, 14, 3, '2024-12-24 09:00:00', '2024-12-24 11:43:00', 150000, 'SCHEDULED'),
	(263, 14, 3, '2024-12-24 14:00:00', '2024-12-24 16:43:00', 150000, 'SCHEDULED'),
	(264, 14, 3, '2024-12-26 09:00:00', '2024-12-26 11:43:00', 150000, 'SCHEDULED'),
	(265, 14, 3, '2024-12-26 19:30:00', '2024-12-26 22:13:00', 180000, 'SCHEDULED'),
	(266, 14, 3, '2024-12-28 09:00:00', '2024-12-28 11:43:00', 150000, 'SCHEDULED'),
	(267, 14, 3, '2024-12-30 14:00:00', '2024-12-30 16:43:00', 150000, 'SCHEDULED'),
	(268, 14, 3, '2025-01-01 19:30:00', '2025-01-01 22:13:00', 180000, 'SCHEDULED'),
	(269, 15, 1, '2024-12-27 09:00:00', '2024-12-27 10:49:00', 80000, 'SCHEDULED'),
	(270, 15, 1, '2024-12-27 11:00:00', '2024-12-27 12:49:00', 80000, 'SCHEDULED'),
	(271, 15, 1, '2024-12-29 09:00:00', '2024-12-29 10:49:00', 80000, 'SCHEDULED'),
	(272, 15, 1, '2024-12-30 11:00:00', '2024-12-30 12:49:00', 80000, 'SCHEDULED'),
	(273, 15, 1, '2025-01-02 09:00:00', '2025-01-02 10:49:00', 80000, 'SCHEDULED'),
	(274, 15, 1, '2025-01-03 11:00:00', '2025-01-03 12:49:00', 80000, 'SCHEDULED'),
	(275, 15, 1, '2025-01-04 09:00:00', '2025-01-04 10:49:00', 80000, 'SCHEDULED');

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service1.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service1.users: ~0 rows (xấp xỉ)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
