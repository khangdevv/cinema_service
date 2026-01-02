-- =====================================================
-- DATABASE: TRACNGHIEM - He thong on tap trac nghiem
-- Tao moi hoan toan, khong anh huong cinema_service
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Tao database
CREATE DATABASE IF NOT EXISTS `tracnghiem` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tracnghiem`;

-- =====================================================
-- BANG USERS (Dang nhap Google)
-- =====================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `google_id` VARCHAR(255) NULL,
    `email` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `avatar` VARCHAR(500) NULL,
    `role` ENUM('USER', 'ADMIN') DEFAULT 'USER',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `users_google_id_unique` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BANG EXAMS (Cac de thi)
-- =====================================================
DROP TABLE IF EXISTS `exams`;
CREATE TABLE `exams` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `type` ENUM('practice', 'mixed') DEFAULT 'practice',
    `time_limit` INT NULL COMMENT 'Thoi gian lam bai (phut)',
    `total_questions` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `exams_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BANG TOPICS (Chu de ly thuyet)
-- =====================================================
DROP TABLE IF EXISTS `topics`;
CREATE TABLE `topics` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(100) NULL,
    `order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `topics_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BANG QUESTIONS (Cau hoi trac nghiem)
-- =====================================================
DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `exam_id` BIGINT UNSIGNED NULL,
    `topic_id` BIGINT UNSIGNED NULL,
    `question` TEXT NOT NULL,
    `option_a` TEXT NOT NULL,
    `option_b` TEXT NOT NULL,
    `option_c` TEXT NULL,
    `option_d` TEXT NULL,
    `correct_answer` CHAR(1) NOT NULL COMMENT 'a, b, c, d',
    `explanation` TEXT NULL COMMENT 'Giai thich dap an',
    `order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `questions_exam_id_foreign` (`exam_id`),
    KEY `questions_topic_id_foreign` (`topic_id`),
    CONSTRAINT `questions_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
    CONSTRAINT `questions_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BANG THEORIES (Ly thuyet)
-- =====================================================
DROP TABLE IF EXISTS `theories`;
CREATE TABLE `theories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `topic_id` BIGINT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `content` LONGTEXT NOT NULL,
    `order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `theories_slug_unique` (`slug`),
    KEY `theories_topic_id_foreign` (`topic_id`),
    CONSTRAINT `theories_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BANG ATTEMPTS (Ket qua thi)
-- =====================================================
DROP TABLE IF EXISTS `attempts`;
CREATE TABLE `attempts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `exam_id` BIGINT UNSIGNED NULL,
    `exam_name` VARCHAR(255) NOT NULL,
    `total_questions` INT NOT NULL,
    `correct_answers` INT DEFAULT 0,
    `wrong_answers` INT DEFAULT 0,
    `score` DECIMAL(5,2) DEFAULT 0.00,
    `time_spent` INT NULL COMMENT 'Thoi gian lam bai (giay)',
    `started_at` TIMESTAMP NULL,
    `completed_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `attempts_user_id_foreign` (`user_id`),
    KEY `attempts_exam_id_foreign` (`exam_id`),
    CONSTRAINT `attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `attempts_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BANG ATTEMPT_ANSWERS (Chi tiet cau tra loi)
-- =====================================================
DROP TABLE IF EXISTS `attempt_answers`;
CREATE TABLE `attempt_answers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `attempt_id` BIGINT UNSIGNED NOT NULL,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `user_answer` CHAR(1) NULL,
    `correct_answer` CHAR(1) NOT NULL,
    `is_correct` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `attempt_answers_attempt_id_foreign` (`attempt_id`),
    KEY `attempt_answers_question_id_foreign` (`question_id`),
    CONSTRAINT `attempt_answers_attempt_id_foreign` FOREIGN KEY (`attempt_id`) REFERENCES `attempts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `attempt_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BANG SESSIONS (Laravel Sessions)
-- =====================================================
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- INSERT CAC DE THI
-- =====================================================
INSERT INTO `exams` (`name`, `slug`, `description`, `type`, `time_limit`, `total_questions`) VALUES
('Đề Ôn Thầy Trường (Main)', 'de-on-thay-truong-main', 'Đề ôn tập chính thức từ Thầy Trường - Bao gồm các câu hỏi về Mã nguồn mở, Linux, Git, Docker, Bugzilla, Shell Script', 'practice', 60, 0),
('Trắc nghiệm Nguồn Mở (Bonus)', 'trac-nghiem-nguon-mo-bonus', 'Đề trắc nghiệm bổ sung về phần mềm mã nguồn mở, Linux, Shell Script, C programming', 'practice', 90, 0),
('Ôn tập Thầy Khuê', 'on-tap-thay-khue', 'Đề ôn tập từ Thầy Khuê - Bao gồm 7 chủ đề: Tổng quan mã nguồn mở, Linux & Shell, VS Code, LAMP Stack, CMS, Python Framework, Bugzilla', 'practice', 120, 0),
('Đề Trộn 50 Câu', 'de-tron-50-cau', 'Đề thi trộn ngẫu nhiên 50 câu từ tất cả các nguồn', 'mixed', 45, 50);

-- =====================================================
-- INSERT CAC CHU DE
-- =====================================================
INSERT INTO `topics` (`name`, `slug`, `description`, `icon`, `order`) VALUES
('Tổng quan Mã nguồn mở', 'tong-quan-ma-nguon-mo', 'Khái niệm, giấy phép, lịch sử phát triển phần mềm mã nguồn mở', 'book-open', 1),
('Giấy phép Mã nguồn mở', 'giay-phep-ma-nguon-mo', 'GPL, MIT, BSD, Apache, LGPL và các loại giấy phép khác', 'file-text', 2),
('Hệ điều hành Linux', 'he-dieu-hanh-linux', 'Kernel, cấu trúc thư mục, các lệnh cơ bản, phân quyền', 'terminal', 3),
('Shell Script', 'shell-script', 'Lập trình Shell, biến, vòng lặp, điều kiện, hàm', 'code', 4),
('Git & Version Control', 'git-version-control', 'Quản lý phiên bản, các lệnh Git, GitHub', 'git-branch', 5),
('LAMP Stack', 'lamp-stack', 'Linux, Apache, MySQL, PHP - Cài đặt và cấu hình', 'server', 6),
('Docker', 'docker', 'Container, Image, Dockerfile, Docker Compose', 'box', 7),
('Bugzilla', 'bugzilla', 'Hệ thống theo dõi lỗi, vòng đời bug, quản lý dự án', 'bug', 8),
('CMS - WordPress/Joomla', 'cms-wordpress-joomla', 'Hệ quản trị nội dung, cài đặt, cấu hình', 'layout', 9),
('Python Web Framework', 'python-web-framework', 'Django, Flask - MVC, ORM, Routing', 'code-2', 10),
('VS Code', 'vs-code', 'Cài đặt, Extension, Debug, Shortcuts', 'edit', 11);
