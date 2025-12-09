# Hướng Dẫn Sử Dụng Admin Panel - Cinema Service

## Giới Thiệu

Admin Panel là giao diện quản trị dành cho quản lý hệ thống đặt vé xem phim. Giao diện này cho phép admin quản lý người dùng, phim và xem thống kê doanh thu.

## Truy Cập Admin Panel

### URL Truy Cập
```
http://your-domain/admin
```

### Tự Động Điều Hướng Theo Vai Trò

Khi người dùng đăng nhập thành công, hệ thống sẽ tự động điều hướng dựa trên vai trò:

- **ADMIN / STAFF**: Tự động vào giao diện Admin Panel (`/admin`)
  - Không có quyền truy cập giao diện đặt vé
  - Chỉ quản lý hệ thống

- **CUSTOMER**: Tự động vào giao diện đặt vé (`/movies`)
  - Không có quyền truy cập Admin Panel
  - Chỉ đặt vé xem phim

### Phân Quyền Truy Cập

- Admin và Staff **KHÔNG** cần truy cập giao diện đặt vé
- Customer **KHÔNG** thể truy cập Admin Panel
- Mỗi role có giao diện riêng biệt phù hợp với vai trò

## Các Chức Năng Chính

### 1. Dashboard (`/admin`)

Dashboard hiển thị tổng quan về hệ thống:

- **Tổng Người Dùng**: Số lượng tài khoản trong hệ thống
- **Tổng Số Phim**: Số lượng phim có trong database
- **Tổng Đơn Hàng**: Số lượng đơn đặt vé
- **Tổng Doanh Thu**: Tổng tiền từ các đơn hàng đã thanh toán (status = PAID)
- **Đơn Hàng Gần Đây**: Danh sách 10 đơn hàng mới nhất

### 2. Thống Kê (`/admin/statistics`)

Trang thống kê chi tiết bao gồm:

- **Tổng quan**: Doanh thu, đơn đã thanh toán, phim đang chiếu, người dùng
- **Đơn hàng theo trạng thái**: PAID (Đã thanh toán), INIT (Chờ xử lý), CANCELLED (Đã hủy)
- **Đơn hàng theo kênh**: WEB (Website), POS (Quầy bán vé)
- **Phương thức thanh toán**: CASH (Tiền mặt), CARD (Thẻ), EWALLET (Ví điện tử)
- **Người dùng theo vai trò**: ADMIN, STAFF, CUSTOMER
- **Top phim doanh thu cao nhất**: Danh sách 10 phim có doanh thu cao nhất

### 3. Quản Lý User (`/admin/users`)

#### Danh sách User
- Xem danh sách tất cả người dùng
- Hiển thị: ID, Họ tên, Email, SĐT, Vai trò, Trạng thái
- Phân trang 10 user/trang

#### Sửa User (`/admin/users/{id}/edit`)
Có thể chỉnh sửa:
- Họ tên
- Email
- Số điện thoại
- Vai trò (CUSTOMER, STAFF, ADMIN)
- Trạng thái hoạt động

#### Khóa/Mở khóa User
- Click vào icon khóa để toggle trạng thái is_active
- User bị khóa sẽ không thể đăng nhập

#### Xóa User
- Click vào icon thùng rác để xóa user
- Có confirm trước khi xóa

### 4. Quản Lý Phim (`/admin/movies`)

#### Danh sách Phim
- Xem danh sách tất cả phim
- Hiển thị: ID, Poster, Tên phim, Thời lượng, Thể loại, Rating, Trạng thái
- Phân trang 10 phim/trang

#### Thêm Phim Mới (`/admin/movies/create`)
Các trường thông tin:
- **Tên phim** (bắt buộc): Tiêu đề phim
- **Thời lượng** (bắt buộc): Số phút
- **Rating Code**: P, C13, C16, C18
- **Thể loại**: Hành động, Kinh dị, Tình cảm...
- **URL Poster**: Link hình ảnh poster
- **Đang chiếu**: Trạng thái hiển thị phim

#### Sửa Phim (`/admin/movies/{id}/edit`)
Chỉnh sửa tất cả thông tin của phim

#### Ngừng/Mở Chiếu
- Click vào icon play/pause để toggle trạng thái is_active
- Phim ngừng chiếu sẽ không hiển thị cho khách hàng

#### Xóa Phim
- Click vào icon thùng rác để xóa phim
- Có confirm trước khi xóa

## Cấu Trúc Database

### Bảng `account`
| Field | Type | Mô tả |
|-------|------|-------|
| id | bigint | Primary key |
| email | string | Email (unique) |
| phone | string(20) | Số điện thoại |
| password_hash | string | Mật khẩu đã hash |
| full_name | string | Họ tên |
| role | enum | CUSTOMER, STAFF, ADMIN |
| is_active | boolean | Trạng thái hoạt động |

### Bảng `movie`
| Field | Type | Mô tả |
|-------|------|-------|
| id | bigint | Primary key |
| title | string | Tên phim |
| duration_min | smallint | Thời lượng (phút) |
| genre | string(100) | Thể loại |
| poster | string | URL poster |
| rating_code | string(10) | P, C13, C16, C18 |
| is_active | boolean | Đang chiếu |

### Bảng `orders`
| Field | Type | Mô tả |
|-------|------|-------|
| id | bigint | Primary key |
| channel | enum | WEB, POS |
| account_id | bigint | FK -> account |
| cashier_id | bigint | FK -> account (nhân viên) |
| showtime_id | bigint | FK -> showtime |
| status | enum | INIT, PAID, CANCELLED |
| payment_method | enum | CASH, CARD, EWALLET |
| total_amount | int | Tổng tiền |

## Routes Admin

| Method | URL | Controller | Mô tả |
|--------|-----|------------|-------|
| GET | /admin | dashboard | Trang chủ admin |
| GET | /admin/statistics | statistics | Trang thống kê |
| GET | /admin/users | usersList | Danh sách user |
| GET | /admin/users/{id}/edit | userEdit | Form sửa user |
| PUT | /admin/users/{id} | userUpdate | Cập nhật user |
| DELETE | /admin/users/{id} | userDelete | Xóa user |
| PATCH | /admin/users/{id}/toggle-status | userToggleStatus | Toggle trạng thái |
| GET | /admin/movies | moviesList | Danh sách phim |
| GET | /admin/movies/create | movieCreate | Form thêm phim |
| POST | /admin/movies | movieStore | Lưu phim mới |
| GET | /admin/movies/{id}/edit | movieEdit | Form sửa phim |
| PUT | /admin/movies/{id} | movieUpdate | Cập nhật phim |
| DELETE | /admin/movies/{id} | movieDelete | Xóa phim |
| PATCH | /admin/movies/{id}/toggle-status | movieToggleStatus | Toggle trạng thái |

## Các File Đã Tạo

### Controller
- `app/Http/Controllers/Web/AdminController.php`

### Views
- `resources/views/admin/layouts/app.blade.php` - Layout chính
- `resources/views/admin/dashboard.blade.php` - Dashboard
- `resources/views/admin/statistics.blade.php` - Thống kê
- `resources/views/admin/users/list.blade.php` - Danh sách user
- `resources/views/admin/users/edit.blade.php` - Form sửa user
- `resources/views/admin/movies/list.blade.php` - Danh sách phim
- `resources/views/admin/movies/create.blade.php` - Form thêm phim
- `resources/views/admin/movies/edit.blade.php` - Form sửa phim

### Routes
- Routes được thêm vào `routes/web.php` với prefix `/admin`

## Bảo Mật & Phân Quyền

### Middleware CheckRole

Hệ thống sử dụng middleware `CheckRole` để bảo vệ routes:

- **Admin routes**: Chỉ ADMIN và STAFF mới truy cập được
- **Booking routes**: Chỉ CUSTOMER mới truy cập được
- Nếu truy cập sai quyền, hệ thống tự động redirect về trang phù hợp với role

### Cách Thức Hoạt Động

1. User đăng nhập → Hệ thống kiểm tra role trong database (bảng `account`)
2. Tự động redirect:
   - ADMIN/STAFF → `/admin`
   - CUSTOMER → `/movies`
3. Middleware bảo vệ routes:
   - CUSTOMER cố truy cập `/admin` → Redirect về `/movies` với thông báo lỗi
   - ADMIN/STAFF cố truy cập `/movies` → Redirect về `/admin` với thông báo lỗi

## Google OAuth Login

### Cài Đặt Google OAuth

Hệ thống đã tích hợp đăng nhập bằng Google cho người dùng (CUSTOMER).

#### 1. Cấu Hình Google Cloud Console

**Bước 1: Tạo OAuth 2.0 Credentials**

1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Tạo hoặc chọn một Project
3. Vào **APIs & Services** → **Credentials**
4. Click **Create Credentials** → **OAuth client ID**
5. Chọn Application type: **Web application**
6. Cấu hình:
   - **Name**: Cinema Service
   - **Authorized JavaScript origins**: `http://localhost:8000` (hoặc domain của bạn)
   - **Authorized redirect URIs**: `http://localhost:8000/auth/google/callback`
7. Click **Create** và lưu lại **Client ID** và **Client Secret**

**Bước 2: Cấu Hình Environment Variables**

Thêm các biến sau vào file `.env`:

```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

**Lưu ý**: Thay thế `your_google_client_id_here` và `your_google_client_secret_here` bằng thông tin thực tế từ Google Cloud Console.

#### 2. Cách Hoạt Động

1. User click vào nút "Đăng nhập với Google" trên trang login
2. Hệ thống redirect user đến Google OAuth consent screen
3. User đăng nhập và cấp quyền cho ứng dụng
4. Google redirect về `/auth/google/callback` với thông tin user
5. Hệ thống kiểm tra:
   - Nếu email đã tồn tại: Link tài khoản với `google_id`
   - Nếu email chưa tồn tại: Tạo tài khoản mới với role CUSTOMER
6. User được tự động đăng nhập và redirect về trang chủ

#### 3. Database Changes

Đã thêm cột `google_id` vào bảng `account`:

```php
Schema::table('account', function (Blueprint $table) {
    $table->string('google_id')->nullable()->unique()->after('email');
});
```

Chạy migration:
```bash
php artisan migrate
```

#### 4. Routes

| Method | URL | Controller | Mô tả |
|--------|-----|------------|-------|
| GET | /auth/google | redirectToGoogle | Redirect đến Google OAuth |
| GET | /auth/google/callback | handleGoogleCallback | Xử lý callback từ Google |

#### 5. Bảo Mật

- User đăng nhập bằng Google sẽ được tạo với role **CUSTOMER** mặc định
- Password tự động generate ngẫu nhiên (16 ký tự) cho các tài khoản Google
- `google_id` được lưu để xác thực user trong lần đăng nhập sau
- Email từ Google phải unique trong hệ thống

## Ghi Chú

1. **Bảo mật**: Hệ thống đã được bảo vệ bằng middleware CheckRole. Mỗi role chỉ truy cập được giao diện phù hợp.

2. **Poster**: Poster phim sử dụng URL trực tiếp, không upload file. Đảm bảo URL hợp lệ và có thể truy cập.

3. **Pagination**: Mặc định phân trang 10 items/trang. Có thể thay đổi trong controller.

4. **Responsive**: Giao diện sử dụng Tailwind CSS, hỗ trợ responsive trên các thiết bị.

5. **Phân quyền nghiêm ngặt**: Admin không thể đặt vé, Customer không thể vào admin panel.

6. **Google OAuth**: Chỉ dành cho CUSTOMER. Admin và Staff phải sử dụng email/password để đăng nhập.
