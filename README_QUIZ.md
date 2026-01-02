# Hệ Thống Ôn Tập Trắc Nghiệm

Hệ thống ôn tập trắc nghiệm về Mã nguồn mở, Linux, Git, Docker, Shell Script với React + Laravel.

## 🎯 Tính Năng

- ✅ **3 Đề trắc nghiệm riêng** + Đề trộn 50 câu ngẫu nhiên
- 📚 **Trang lý thuyết** tổng hợp đầy đủ kiến thức
- 📊 **Thống kê chi tiết** với biểu đồ theo dõi tiến độ
- 💡 **Giải thích lý thuyết** sau khi nộp bài
- ⏱️ **Timer** đếm ngược thời gian làm bài
- 🎨 **UI hiện đại** với glassmorphism và animations

## 📋 Yêu Cầu Hệ Thống

### Backend
- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Laravel 10.x

### Frontend
- Node.js >= 16.x
- npm hoặc yarn

## 🚀 Cài Đặt

### 1. Cài đặt Backend

```bash
# Clone dự án
git clone <repository-url>
cd cinema_service

# Cài đặt PHP dependencies
composer install

# Copy .env và cấu hình database
cp .env.example .env
php artisan key:generate

# Tạo database tracnghiem
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS tracnghiem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema
mysql -u root -p tracnghiem < tracnghiem.sql

# Chạy seeder
php artisan db:seed --class=QuizSeeder

# Khởi động Laravel server
php artisan serve
```

### 2. Cài đặt Frontend

```bash
# Chuyển vào thư mục React
cd resources/react

# Cài đặt dependencies
npm install

# Development mode
npm run dev

# Hoặc build production
npm run build
```

## 🌐 Sử Dụng

### Development

1. Khởi động Laravel server:
```bash
php artisan serve
```

2. Khởi động React dev server (terminal khác):
```bash
cd resources/react
npm run dev
```

3. Truy cập:
- React app: http://localhost:5173
- Laravel API: http://localhost:8000/api/quiz

### Production

1. Build React app:
```bash
cd resources/react
npm run build
```

2. Truy cập qua Laravel:
- http://localhost:8000/quiz

## 📱 Cấu Trúc Dự Án

```
cinema_service/
├── app/
│   ├── Http/Controllers/Quiz/    # Quiz API Controllers
│   └── Models/Quiz/               # Quiz Models
├── database/
│   └── seeders/QuizSeeder.php    # Seeder với câu hỏi mẫu
├── resources/
│   ├── react/                     # React app
│   │   ├── src/
│   │   │   ├── components/       # UI components
│   │   │   ├── pages/            # Page components
│   │   │   ├── services/         # API services
│   │   │   ├── context/          # React Context
│   │   │   └── router/           # React Router
│   │   ├── package.json
│   │   └── vite.config.js
│   └── views/quiz.blade.php      # Laravel view cho React SPA
├── routes/
│   ├── api.php                    # API routes
│   └── web.php                    # Web routes
└── tracnghiem.sql                 # Database schema
```

## 🎓 Hướng Dẫn Sử Dụng

### Đăng Ký/Đăng Nhập
1. Truy cập `/quiz`
2. Đăng ký tài khoản mới hoặc đăng nhập
3. Email/password sẽ được lưu trong database `accounts`

### Làm Bài Thi
1. Chọn một trong 4 đề thi ở trang chủ
2. Đọc kỹ câu hỏi và chọn đáp án
3. Sử dụng navigator để di chuyển giữa các câu
4. Theo dõi thời gian còn lại
5. Nộp bài khi hoàn thành

### Xem Kết Quả
- Điểm số và số câu đúng/sai được hiển thị ngay
- Xem chi tiết từng câu với giải thích lý thuyết
- Confetti animation nếu đạt ≥ 80%

### Xem Lý Thuyết
- Chọn topic từ sidebar
- Tìm kiếm nhanh với search box  
- Nội dung markdown được format đẹp

### Theo Dõi Thống Kê
- Xem tổng quan: số bài đã làm, điểm TB, cao nhất
- Biểu đồ line chart: điểm số theo thời gian
- Biểu đồ bar chart: số câu đúng/sai
- Lịch sử chi tiết từng lần thi

## 🔧 API Endpoints

### Authentication
- `POST /api/quiz/login` - Đăng nhập
- `POST /api/quiz/register` - Đăng ký
- `POST /api/quiz/logout` - Đăng xuất (auth required)
- `GET /api/quiz/me` - Thông tin user (auth required)

### Exams
- `GET /api/quiz/exams` - Danh sách đề thi
- `GET /api/quiz/exams/{slug}` - Chi tiết đề thi + câu hỏi (auth required)
- `POST /api/quiz/submit` - Nộp bài (auth required)
- `GET /api/quiz/history` - Lịch sử làm bài (auth required)
- `GET /api/quiz/attempts/{id}` - Chi tiết kết quả (auth required)

### Theory
- `GET /api/quiz/topics` - Danh sách chủ đề
- `GET /api/quiz/topics/{slug}` - Chi tiết chủ đề
- `GET /api/quiz/theories` - Tất cả lý thuyết
- `GET /api/quiz/theories/{slug}` - Chi tiết lý thuyết

### Statistics
- `GET /api/quiz/statistics/overview` - Tổng quan thống kê (auth required)
- `GET /api/quiz/statistics/progress` - Dữ liệu tiến độ (auth required)

## 🚢 Deploy trên LAMP Stack (Lightsail)

### 1. Chuẩn bị server

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Cài LAMP stack (nếu chưa có)
sudo apt install apache2 mysql-server php php-mysql php-cli php-xml php-mbstring -y

# Cài Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Cài Node.js & npm
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Clone và setup

```bash
# Clone project
cd /var/www/html
sudo git clone <repository-url> quiz_app
cd quiz_app

# Install dependencies
composer install --no-dev --optimize-autoloader
cd resources/react && npm install && npm run build
cd ../..

# Setup permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Setup .env
cp .env.example .env
php artisan key:generate
# Edit .env với database credentials
```

### 3. Cấu hình Apache

Tạo file `/etc/apache2/sites-available/quiz.conf`:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/quiz_app/public

    <Directory /var/www/html/quiz_app/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/quiz_error.log
    CustomLog ${APACHE_LOG_DIR}/quiz_access.log combined
</VirtualHost>
```

Enable site:
```bash
sudo a2enmod rewrite
sudo a2ensite quiz.conf
sudo systemctl restart apache2
```

### 4. Setup database

```bash
mysql -u root -p

CREATE DATABASE tracnghiem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'quiz_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON tracnghiem.* TO 'quiz_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema
mysql -u quiz_user -p tracnghiem < tracnghiem.sql

# Run seeder
php artisan db:seed --class=QuizSeeder
```

## 📝 Thêm Câu Hỏi

### Thủ công

Edit file `database/seeders/QuizSeeder.php` và thêm câu hỏi vào mảng tương ứng:

```php
[
    'question' => 'Câu hỏi của bạn?',
    'option_a' => 'Đáp án A',
    'option_b' => 'Đáp án B',
    'option_c' => 'Đáp án C',
    'option_d' => 'Đáp án D',
    'correct_answer' => 'a', // a, b, c, hoặc d
    'explanation' => 'Giải thích đáp án đúng',
    'topic_id' => 1 // ID của topic
]
```

Sau đó chạy:
```bash
php artisan db:seed --class=QuizSeeder --force
```

## 🎨 Tùy Chỉnh Giao Diện

### Màu sắc

Edit `resources/react/tailwind.config.js`:

```javascript
theme: {
  extend: {
    colors: {
      primary: {
        500: '#0ea5e9', // Màu chính
        // ...
      }
    }
  }
}
```

### Font

Edit `resources/react/index.html` và `resources/react/src/index.css`

## 🐛 Troubleshooting

### React app không load
- Check console browser: Ctrl+Shift+I
- Verify build output: `ls public/quiz`
- Check Laravel logs: `storage/logs/laravel.log`

### API 401 Unauthorized
- Clear browser localStorage
- Check token trong Network tab
- Verify API routes middleware

### Database connection error
- Check .env có đúng credentials không
- Test connection: `php artisan db:show`

## 📄 License

This project is open-sourced for educational purposes.

## 👨‍💻 Developer

Developed with ❤️ for students studying Open Source Software Development
