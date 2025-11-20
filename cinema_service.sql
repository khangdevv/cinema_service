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


-- Đang kết xuất đổ cấu trúc cơ sở dữ liệu cho cinema_service
CREATE DATABASE IF NOT EXISTS `cinema_service` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `cinema_service`;

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.account
CREATE TABLE IF NOT EXISTS `account` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role` enum('CUSTOMER','STAFF','ADMIN') NOT NULL DEFAULT 'CUSTOMER',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.account: ~11 rows (xấp xỉ)
INSERT INTO `account` (`id`, `email`, `phone`, `password_hash`, `full_name`, `role`, `is_active`) VALUES
	(1, 'admin@cinema.com', '0123456789', '$2y$12$tgcwNsgT43G578Z4DF0xQ.lkzsKtHRyJX4gAq8uTJ7TMkMF6MkZ12', 'Admin User', 'ADMIN', 1),
	(2, 'sandrine45@example.com', '845.957.5430', '$2y$12$0ipMyofEND5ssmIYm/yn3eOOgC0SnjSeasOcsV9vMyQMNGG7tt0nK', 'Darius Emmerich', 'STAFF', 1),
	(3, 'jmiller@example.com', '1-520-676-8777', '$2y$12$clCQ8T4KJ9fXki2chwdhReJn6PHJ0u.jtqBtT65RJXMXBwR9OVmMi', 'Prof. Vivien Baumbach', 'STAFF', 1),
	(4, 'verla85@example.org', '+1-330-201-1830', '$2y$12$8QQibJDJEuRpfAjdqFpAJOLyAeugzdsejqRzDoM2yZGmgrkC24Nqu', 'Prof. Westley Grimes I', 'CUSTOMER', 1),
	(5, 'jarrell59@example.net', '307-231-0731', '$2y$12$mN5KF11.riGYMa3uvasjJeDM8YnoM9nkbKlqgdX2l3QAzfXnViaDu', 'Delphia Williamson', 'STAFF', 1),
	(6, 'wilson49@example.org', '1-475-740-7630', '$2y$12$EMDhs/if8XzTnT11y3xdHegMy./2l.AX1TouU4PpimyCpQNQ60qgq', 'Mrs. Annabelle Hirthe DVM', 'CUSTOMER', 1),
	(7, 'virginia91@example.net', '+1 (775) 215-9845', '$2y$12$e7RMXrDYvSTkPJY33dC.PuQInQe8m2VTmujtaRiNLLjLcfsQ6KA9y', 'Rhea Christiansen Jr.', 'CUSTOMER', 1),
	(8, 'rodger.braun@example.com', '+1.303.592.9644', '$2y$12$AqmHnaWTANgRuiQvR2VZ9.5uXuvRz/kfYe.eNJD3z8O8xAlVAqSR2', 'Roderick Bartell', 'STAFF', 1),
	(9, 'ryleigh51@example.org', '920.722.8174', '$2y$12$ihNt/c1vzqexET5WZZfaM.q5beaHV1ZVkB6fHb6adCiezm1HEo.Ta', 'Lempi Kuhic', 'CUSTOMER', 1),
	(10, 'icruickshank@example.net', '+1-603-684-7622', '$2y$12$YzmwJgchcpoy4H1ACYcBU.hfuBRaxfnweDLIpgQU0L28Nzh1pZtJO', 'Kali Schneider', 'STAFF', 1),
	(11, 'abshire.frankie@example.org', '405.904.4209', '$2y$12$RqW61NDIHpvqi2z/dtxe7uMjZzRqO7EvTzDp7cZIXHgWaAeFbj5qm', 'Darryl White', 'CUSTOMER', 0),
	(12, 'user@example.com', '0123456799', '$2y$12$ZBi2STrWwb73SQfkPdUREOM84UTi2r0UiFB8A.sKvgSVpcLdZO6cG', 'Nguyen Van A', 'CUSTOMER', 1);

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.cache: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.cache_locks: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.failed_jobs
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

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.failed_jobs: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.job_batches
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

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.job_batches: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.jobs
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

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.jobs: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.migrations: ~19 rows (xấp xỉ)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(37, '0001_01_01_000000_create_users_table', 1),
	(38, '0001_01_01_000001_create_cache_table', 1),
	(39, '0001_01_01_000002_create_jobs_table', 1),
	(40, '2025_11_06_143152_create_account_table', 1),
	(41, '2025_11_06_143152_create_movie_table', 1),
	(42, '2025_11_06_143152_create_order_line_table', 1),
	(43, '2025_11_06_143152_create_orders_table', 1),
	(44, '2025_11_06_143152_create_product_table', 1),
	(45, '2025_11_06_143152_create_screen_table', 1),
	(46, '2025_11_06_143152_create_seat_lock_table', 1),
	(47, '2025_11_06_143152_create_seat_table', 1),
	(48, '2025_11_06_143152_create_showtime_table', 1),
	(49, '2025_11_06_143155_add_foreign_keys_to_order_line_table', 1),
	(50, '2025_11_06_143155_add_foreign_keys_to_orders_table', 1),
	(51, '2025_11_06_143155_add_foreign_keys_to_seat_lock_table', 1),
	(52, '2025_11_06_143155_add_foreign_keys_to_seat_table', 1),
	(53, '2025_11_06_143155_add_foreign_keys_to_showtime_table', 1),
	(54, '2025_11_06_144023_create_personal_access_tokens_table', 1),
	(55, '2025_11_18_061957_add_colums_poster_genre_into_table_movie', 1);

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.movie
CREATE TABLE IF NOT EXISTS `movie` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `duration_min` smallint NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `rating_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movie_title_index` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.movie: ~50 rows (xấp xỉ)
INSERT INTO `movie` (`id`, `title`, `duration_min`, `genre`, `poster`, `rating_code`) VALUES
	(51, 'Odit molestiae impedit quidem sed aut explicabo.', 105, 'Sci-Fi', 'https://picsum.photos/500/750?random=1103', 'T16'),
	(52, 'Quia deserunt sit sapiente.', 147, 'Action', 'https://picsum.photos/500/750?random=4400', 'T18'),
	(53, 'Sed neque voluptatem qui corporis consequuntur optio.', 117, 'Action', 'https://picsum.photos/500/750?random=5283', 'T18'),
	(54, 'Nostrum fugit accusamus voluptatem ab.', 105, 'Honor', 'https://picsum.photos/500/750?random=4388', 'T13'),
	(55, 'Hic reprehenderit quidem omnis voluptatem commodi odio.', 145, 'Action', 'https://picsum.photos/500/750?random=6538', 'T13'),
	(56, 'Maiores numquam sint soluta et optio omnis.', 107, 'Honor', 'https://picsum.photos/500/750?random=5081', 'T13'),
	(57, 'Eveniet recusandae voluptatem dolor voluptatem.', 167, 'Adventur', 'https://picsum.photos/500/750?random=6463', 'T13'),
	(58, 'Ut neque est delectus perferendis.', 142, 'Honor', 'https://picsum.photos/500/750?random=8502', 'T18'),
	(59, 'Quo et earum magni eveniet quis.', 126, 'Honor', 'https://picsum.photos/500/750?random=7282', 'T18'),
	(60, 'Natus ut vitae nihil dolor.', 114, 'Adventur', 'https://picsum.photos/500/750?random=1298', 'T16'),
	(61, 'Itaque vitae sit dolor.', 108, 'Adventur', 'https://picsum.photos/500/750?random=3228', 'T18'),
	(62, 'Quaerat sunt et ut dolore animi consequuntur.', 149, 'Action', 'https://picsum.photos/500/750?random=560', 'T18'),
	(63, 'Odit dolorem eius temporibus officia voluptas.', 165, 'Adventur', 'https://picsum.photos/500/750?random=5185', 'T18'),
	(64, 'Consequatur in totam nobis.', 155, 'Action', 'https://picsum.photos/500/750?random=7609', 'T18'),
	(65, 'Adipisci nemo ut eveniet.', 178, 'Sci-Fi', 'https://picsum.photos/500/750?random=3726', 'T16'),
	(66, 'Delectus ullam dolores ex est est ipsa.', 126, 'Adventur', 'https://picsum.photos/500/750?random=6711', 'T18'),
	(67, 'Cumque similique porro qui facere.', 168, 'Sci-Fi', 'https://picsum.photos/500/750?random=5906', 'T16'),
	(68, 'Molestiae illum mollitia ad.', 178, 'Sci-Fi', 'https://picsum.photos/500/750?random=3070', 'T16'),
	(69, 'Qui cum voluptatem et doloribus.', 97, 'Sci-Fi', 'https://picsum.photos/500/750?random=8873', 'T13'),
	(70, 'Ad et consectetur quos error.', 152, 'Honor', 'https://picsum.photos/500/750?random=5869', 'T18'),
	(71, 'Id sed quas saepe dolorum beatae.', 92, 'Adventur', 'https://picsum.photos/500/750?random=7352', 'T18'),
	(72, 'Vel quis est et expedita est voluptas.', 118, 'Honor', 'https://picsum.photos/500/750?random=2948', 'T18'),
	(73, 'Sunt eaque amet consequatur.', 105, 'Honor', 'https://picsum.photos/500/750?random=1543', 'T13'),
	(74, 'Sed sint minima ullam est saepe.', 133, 'Honor', 'https://picsum.photos/500/750?random=3134', 'T13'),
	(75, 'Et maxime non qui.', 151, 'Sci-Fi', 'https://picsum.photos/500/750?random=7170', 'T13'),
	(76, 'Commodi fuga dolor molestiae aut velit eum.', 164, 'Honor', 'https://picsum.photos/500/750?random=5233', 'T18'),
	(77, 'Et libero doloribus nihil sint eos.', 165, 'Action', 'https://picsum.photos/500/750?random=9699', 'T16'),
	(78, 'Nam ea accusamus in qui.', 136, 'Adventur', 'https://picsum.photos/500/750?random=2598', 'T16'),
	(79, 'Nostrum corporis maiores dolore.', 119, 'Sci-Fi', 'https://picsum.photos/500/750?random=7493', 'T18'),
	(80, 'Velit incidunt laudantium sint.', 161, 'Honor', 'https://picsum.photos/500/750?random=3970', 'T16'),
	(81, 'Sed dolores enim odio.', 106, 'Honor', 'https://picsum.photos/500/750?random=2709', 'T13'),
	(82, 'Qui minima iusto natus optio molestias saepe.', 165, 'Action', 'https://picsum.photos/500/750?random=7796', 'T13'),
	(83, 'Aspernatur cumque molestiae consequatur ut iusto.', 121, 'Action', 'https://picsum.photos/500/750?random=4609', 'T18'),
	(84, 'Enim consequatur corrupti sit soluta deserunt repellat.', 168, 'Honor', 'https://picsum.photos/500/750?random=242', 'T18'),
	(85, 'Debitis praesentium tempora eum dolor consequatur.', 171, 'Sci-Fi', 'https://picsum.photos/500/750?random=2072', 'T18'),
	(86, 'Aut voluptatibus eum a ut.', 172, 'Action', 'https://picsum.photos/500/750?random=6542', 'T18'),
	(87, 'Possimus sequi esse accusamus harum.', 152, 'Adventur', 'https://picsum.photos/500/750?random=6580', 'T18'),
	(88, 'Qui et et quidem ut.', 154, 'Sci-Fi', 'https://picsum.photos/500/750?random=9618', 'T18'),
	(89, 'Consequuntur et molestiae iusto ratione.', 147, 'Adventur', 'https://picsum.photos/500/750?random=5194', 'T18'),
	(90, 'Nobis laboriosam quia dicta.', 111, 'Adventur', 'https://picsum.photos/500/750?random=9264', 'T13'),
	(91, 'Hic fuga aut ipsam eos.', 125, 'Action', 'https://picsum.photos/500/750?random=6760', 'T16'),
	(92, 'Delectus molestias distinctio sit.', 170, 'Adventur', 'https://picsum.photos/500/750?random=9380', 'T16'),
	(93, 'Consequatur aliquid qui blanditiis quae.', 142, 'Sci-Fi', 'https://picsum.photos/500/750?random=1167', 'T13'),
	(94, 'Doloribus explicabo delectus quas.', 166, 'Action', 'https://picsum.photos/500/750?random=6430', 'T13'),
	(95, 'Ut repellat ducimus laborum aspernatur eos.', 129, 'Sci-Fi', 'https://picsum.photos/500/750?random=3435', 'T13'),
	(96, 'Assumenda sequi ipsa tempore aliquid error non.', 158, 'Action', 'https://picsum.photos/500/750?random=5082', 'T13'),
	(97, 'Et doloribus aliquid consequuntur incidunt.', 128, 'Action', 'https://picsum.photos/500/750?random=2847', 'T16'),
	(98, 'Voluptas repellat beatae incidunt numquam.', 107, 'Action', 'https://picsum.photos/500/750?random=8419', 'T18'),
	(99, 'Odio qui optio ipsam eius vel quia.', 179, 'Action', 'https://picsum.photos/500/750?random=2795', 'T16'),
	(100, 'Et expedita alias rerum sint odit occaecati.', 107, 'Sci-Fi', 'https://picsum.photos/500/750?random=3823', 'T16');

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.order_line
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
  CONSTRAINT `order_line_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `order_line_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seat` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.order_line: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.orders
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
  CONSTRAINT `orders_showtime_id_foreign` FOREIGN KEY (`showtime_id`) REFERENCES `showtime` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.orders: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.password_reset_tokens: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.personal_access_tokens
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.personal_access_tokens: ~0 rows (xấp xỉ)
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(2, 'App\\Models\\Account', 1, 'admin', '39f3d3ece28dbaaf2c5caa0f6709ac7dd6e6c1e37f2e58b919653acf42b815fc', '["*"]', '2025-11-20 02:24:44', NULL, '2025-11-20 02:18:32', '2025-11-20 02:24:44'),
	(3, 'App\\Models\\Account', 12, 'admin', '89e8fffcfc08ae6e7bed1e86e1a4b695b6b0b1b3349d05c17929db23f588accd', '["*"]', '2025-11-20 02:29:29', NULL, '2025-11-20 02:27:57', '2025-11-20 02:29:29');

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.product
CREATE TABLE IF NOT EXISTS `product` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` int NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.product: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.screen
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.screen: ~0 rows (xấp xỉ)
INSERT INTO `screen` (`id`, `code`, `name`, `format`, `row_count`, `col_count`, `is_active`) VALUES
	(1, 'SCR-885', 'Screen 1', 'ScreenX', 14, 15, 1),
	(2, 'SCR-974', 'Screen 2', 'IMAX', 10, 12, 1),
	(3, 'SCR-418', 'Screen 4', 'IMAX', 13, 8, 1),
	(5, 'SCR-895', 'Screen 2', '2D', 12, 10, 1),
	(6, 'SCR-5', 'Screen 6', '3D', 14, 13, 1),
	(7, 'SCR-876', 'Screen 1', '2D', 12, 11, 1),
	(8, 'SCR-1', 'Screen 1 - VIP', 'IMAX', 12, 15, 1);

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.seat
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.seat: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.seat_lock
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

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.seat_lock: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.sessions
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

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.sessions: ~0 rows (xấp xỉ)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('ejWymJAGPTRzntGSFIbWK55t46FaNFRFmHa9hLUr', NULL, '127.0.0.1', 'PostmanRuntime/7.49.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDY5OWkzMWVFZGd6YnQyaFhxYktxaW1FUTdsZVhicWhBQVRxTWwydSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1763605627);

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.showtime
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
  CONSTRAINT `showtime_movie_id_foreign` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `showtime_screen_id_foreign` FOREIGN KEY (`screen_id`) REFERENCES `screen` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.showtime: ~0 rows (xấp xỉ)

-- Đang kết xuất đổ cấu trúc cho bảng cinema_service.users
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

-- Đang kết xuất đổ dữ liệu cho bảng cinema_service.users: ~0 rows (xấp xỉ)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
