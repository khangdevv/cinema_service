-- =====================================================
-- DATABASE: TRẮC NGHIỆM MÃ NGUỒN MỞ
-- Tạo bởi: Claude AI
-- Mục đích: Ôn tập trắc nghiệm phần mềm mã nguồn mở
-- =====================================================

-- Tạo database
CREATE DATABASE IF NOT EXISTS tracnghiem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tracnghiem;

-- =====================================================
-- BẢNG ACCOUNTS (Người dùng)
-- =====================================================
CREATE TABLE IF NOT EXISTS accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    google_id VARCHAR(255) UNIQUE NULL,
    phone VARCHAR(20) NULL,
    password VARCHAR(255) NULL,
    full_name VARCHAR(255) NULL,
    avatar VARCHAR(500) NULL,
    role ENUM('USER', 'ADMIN') DEFAULT 'USER',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- BẢNG QUIZ_EXAMS (Đề thi)
-- =====================================================
CREATE TABLE IF NOT EXISTS quiz_exams (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    type ENUM('practice', 'mixed') DEFAULT 'practice',
    time_limit INT NULL COMMENT 'Thời gian làm bài (phút)',
    total_questions INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- BẢNG QUIZ_TOPICS (Chủ đề)
-- =====================================================
CREATE TABLE IF NOT EXISTS quiz_topics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- BẢNG QUIZ_QUESTIONS (Câu hỏi trắc nghiệm)
-- =====================================================
CREATE TABLE IF NOT EXISTS quiz_questions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exam_id BIGINT UNSIGNED NULL,
    topic_id BIGINT UNSIGNED NULL,
    question TEXT NOT NULL,
    option_a TEXT NOT NULL,
    option_b TEXT NOT NULL,
    option_c TEXT NULL,
    option_d TEXT NULL,
    correct_answer CHAR(1) NOT NULL COMMENT 'a, b, c, d',
    explanation TEXT NULL COMMENT 'Giải thích đáp án',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_id) REFERENCES quiz_exams(id) ON DELETE CASCADE,
    FOREIGN KEY (topic_id) REFERENCES quiz_topics(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- BẢNG QUIZ_THEORIES (Lý thuyết)
-- =====================================================
CREATE TABLE IF NOT EXISTS quiz_theories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    topic_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES quiz_topics(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- BẢNG QUIZ_ATTEMPTS (Lịch sử làm bài)
-- =====================================================
CREATE TABLE IF NOT EXISTS quiz_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_id BIGINT UNSIGNED NOT NULL,
    exam_id BIGINT UNSIGNED NULL,
    exam_name VARCHAR(255) NOT NULL,
    total_questions INT NOT NULL,
    correct_answers INT NOT NULL DEFAULT 0,
    wrong_answers INT NOT NULL DEFAULT 0,
    score DECIMAL(5,2) NOT NULL DEFAULT 0,
    time_spent INT NULL COMMENT 'Thời gian làm bài (giây)',
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES quiz_exams(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- BẢNG QUIZ_ATTEMPT_ANSWERS (Chi tiết câu trả lời)
-- =====================================================
CREATE TABLE IF NOT EXISTS quiz_attempt_answers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attempt_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    user_answer CHAR(1) NULL,
    correct_answer CHAR(1) NOT NULL,
    is_correct BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- BẢNG SESSIONS (Phiên đăng nhập)
-- =====================================================
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
) ENGINE=InnoDB;

-- =====================================================
-- INDEX TỐI ƯU HIỆU NĂNG
-- =====================================================
CREATE INDEX idx_questions_exam ON quiz_questions(exam_id);
CREATE INDEX idx_questions_topic ON quiz_questions(topic_id);
CREATE INDEX idx_attempts_account ON quiz_attempts(account_id);
CREATE INDEX idx_attempts_exam ON quiz_attempts(exam_id);
CREATE INDEX idx_attempts_score ON quiz_attempts(score);
CREATE INDEX idx_attempt_answers_attempt ON quiz_attempt_answers(attempt_id);

-- =====================================================
-- THÊM DỮ LIỆU MẪU - ĐỀ THI
-- =====================================================
INSERT INTO quiz_exams (name, slug, description, type, time_limit, is_active) VALUES
('Đề Ôn Thầy Trường (CK)', 'de-on-thay-truong', 'Đề ôn tập cuối kỳ của thầy Trường - Bao gồm các chủ đề: Mã nguồn mở, Linux, Shell Script, Git, Docker, Bugzilla', 'practice', 60, TRUE),
('Trắc nghiệm nguồn mở (Bonus)', 'trac-nghiem-nguon-mo-bonus', 'Đề trắc nghiệm bổ sung về phần mềm mã nguồn mở - Chương 1,2,3,4 và các chủ đề nâng cao', 'practice', 90, TRUE),
('Ôn tập Thầy Khuê', 'on-tap-thay-khue', 'Đề ôn tập của thầy Khuê - 7 chủ đề: Tổng quan OSS, Linux Shell, VS Code, LAMP, CMS, Python Framework, Bugzilla', 'practice', 90, TRUE),
('Đề Trộn 50 Câu', 'de-tron-50-cau', 'Đề thi trộn ngẫu nhiên 50 câu từ tất cả các đề để luyện tập toàn diện', 'mixed', 45, TRUE);

-- =====================================================
-- THÊM DỮ LIỆU MẪU - CHỦ ĐỀ
-- =====================================================
INSERT INTO quiz_topics (name, slug, description, sort_order) VALUES
('Tổng quan Mã nguồn mở & Giấy phép', 'tong-quan-ma-nguon-mo', 'Khái niệm OSS, các loại giấy phép: GPL, MIT, BSD, Apache, LGPL, MPL', 1),
('Hệ điều hành Linux', 'linux', 'Kernel Linux, cấu trúc thư mục, các lệnh cơ bản, phân quyền', 2),
('Shell Script', 'shell-script', 'Cú pháp Bash, biến, vòng lặp, điều kiện, hàm', 3),
('Git & GitHub', 'git-github', 'Quản lý phiên bản, các lệnh git, workflow', 4),
('Docker', 'docker', 'Container, Image, Docker Compose, Docker Hub', 5),
('Bugzilla', 'bugzilla', 'Hệ thống theo dõi lỗi, trạng thái bug, vòng đời bug', 6),
('LAMP Stack', 'lamp-stack', 'Linux, Apache, MySQL, PHP - Cài đặt và cấu hình', 7),
('VS Code', 'vscode', 'Phím tắt, Extension, Debug, Cấu hình', 8),
('CMS (WordPress, Joomla, OpenCart)', 'cms', 'Hệ quản trị nội dung, Theme, Plugin', 9),
('Python Web Framework', 'python-framework', 'Django, Flask, MVT, ORM, CRUD', 10);

-- =====================================================
-- TÀI KHOẢN ADMIN MẪU
-- Password: admin123 (đã hash bcrypt)
-- =====================================================
INSERT INTO accounts (email, password, full_name, role, is_active) VALUES
('admin@tracnghiem.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4tC.kzJYtCEXKBYy', 'Admin Trắc Nghiệm', 'ADMIN', TRUE);

SELECT 'Database tracnghiem đã được tạo thành công!' as message;
