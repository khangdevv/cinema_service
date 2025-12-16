# 📽️ GIẢI THÍCH LUỒNG ĐI VÀ CODE CHI TIẾT - CINEMA SERVICE

## 📑 MỤC LỤC

1. [Tổng Quan Hệ Thống](#1-tổng-quan-hệ-thống)
2. [Cấu Trúc Database và Quan Hệ Giữa Các Bảng](#2-cấu-trúc-database-và-quan-hệ-giữa-các-bảng)
3. [Cấu Hình Authentication](#3-cấu-hình-authentication)
4. [Middleware và Phân Quyền](#4-middleware-và-phân-quyền)
5. [Luồng Xác Thực (Authentication Flow)](#5-luồng-xác-thực-authentication-flow)
6. [Luồng Đặt Vé (Booking Flow)](#6-luồng-đặt-vé-booking-flow)
7. [Luồng Thanh Toán (Payment Flow)](#7-luồng-thanh-toán-payment-flow)
8. [Luồng Quản Trị (Admin Flow)](#8-luồng-quản-trị-admin-flow)
9. [Cách Gọi Quan Hệ Trong Code](#9-cách-gọi-quan-hệ-trong-code)

---

## 1. TỔNG QUAN HỆ THỐNG

### 1.1 Kiến Trúc Tổng Quan

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           CINEMA SERVICE SYSTEM                              │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌─────────────┐     ┌─────────────┐     ┌─────────────┐                   │
│   │  CUSTOMER   │     │    STAFF    │     │    ADMIN    │                   │
│   │  (Website)  │     │   (Admin)   │     │   (Admin)   │                   │
│   └──────┬──────┘     └──────┬──────┘     └──────┬──────┘                   │
│          │                   │                   │                          │
│          ▼                   ▼                   ▼                          │
│   ┌─────────────────────────────────────────────────────────────────┐      │
│   │                      ROUTES (web.php)                            │      │
│   │  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │      │
│   │  │ Auth Routes │  │Booking Routes│  │    Admin Routes         │  │      │
│   │  │ (Guest)     │  │ (Customer)   │  │    (Admin/Staff)        │  │      │
│   │  └─────────────┘  └─────────────┘  └─────────────────────────┘  │      │
│   └─────────────────────────────────────────────────────────────────┘      │
│                                    │                                        │
│                                    ▼                                        │
│   ┌─────────────────────────────────────────────────────────────────┐      │
│   │                       MIDDLEWARE                                 │      │
│   │  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │      │
│   │  │  auth:web   │  │  guest:web  │  │  role:CUSTOMER/ADMIN    │  │      │
│   │  │ (Đã login)  │  │ (Chưa login)│  │  (Kiểm tra quyền)       │  │      │
│   │  └─────────────┘  └─────────────┘  └─────────────────────────┘  │      │
│   └─────────────────────────────────────────────────────────────────┘      │
│                                    │                                        │
│                                    ▼                                        │
│   ┌─────────────────────────────────────────────────────────────────┐      │
│   │                      WEB CONTROLLERS                             │      │
│   │  ┌──────────────┐  ┌────────────────┐  ┌──────────────────┐    │      │
│   │  │AuthController│  │BookingController│  │  AdminController │    │      │
│   │  └──────────────┘  └────────────────┘  └──────────────────┘    │      │
│   │  ┌────────────────┐  ┌──────────────────┐                       │      │
│   │  │PaymentController│  │DashboardController│                      │      │
│   │  └────────────────┘  └──────────────────┘                       │      │
│   └─────────────────────────────────────────────────────────────────┘      │
│                                    │                                        │
│                                    ▼                                        │
│   ┌─────────────────────────────────────────────────────────────────┐      │
│   │                         MODELS (Eloquent ORM)                    │      │
│   │  Account │ Movie │ Screen │ Seat │ Showtime │ Order │ OrderLine │      │
│   │  SeatLock │ Product                                              │      │
│   └─────────────────────────────────────────────────────────────────┘      │
│                                    │                                        │
│                                    ▼                                        │
│   ┌─────────────────────────────────────────────────────────────────┐      │
│   │                      DATABASE (MySQL)                            │      │
│   │  account │ movie │ screen │ seat │ showtime │ orders │ order_line│     │
│   │  seat_lock │ product                                             │      │
│   └─────────────────────────────────────────────────────────────────┘      │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```


### 1.2 Các Role Trong Hệ Thống

| Role | Mô tả | Quyền truy cập |
|------|-------|----------------|
| **CUSTOMER** | Khách hàng | Xem phim, đặt vé, thanh toán, xem lịch sử |
| **STAFF** | Nhân viên | Quản lý phim, xem thống kê |
| **ADMIN** | Quản trị viên | Toàn quyền: quản lý user, phim, thống kê |

---

## 2. CẤU TRÚC DATABASE VÀ QUAN HỆ GIỮA CÁC BẢNG

### 2.1 Sơ Đồ ERD (Entity Relationship Diagram)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        DATABASE SCHEMA - CINEMA SERVICE                      │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────────────┐                              ┌──────────────────┐
│     account      │                              │      movie       │
├──────────────────┤                              ├──────────────────┤
│ PK id            │                              │ PK id            │
│    email         │                              │    title         │
│    google_id     │                              │    duration_min  │
│    phone         │                              │    genre         │
│    password_hash │                              │    poster        │
│    full_name     │                              │    rating_code   │
│    role (ENUM)   │                              │    is_active     │
│    is_active     │                              └────────┬─────────┘
└────────┬─────────┘                                       │
         │                                                 │
         │ 1:N                                             │ 1:N
         │                                                 │
         ▼                                                 ▼
┌──────────────────┐         ┌──────────────────┐    ┌──────────────────┐
│     orders       │         │     screen       │    │    showtime      │
├──────────────────┤         ├──────────────────┤    ├──────────────────┤
│ PK id            │         │ PK id            │    │ PK id            │
│ FK account_id ───┼─────────│    code          │    │ FK movie_id ─────┼──► movie
│ FK showtime_id ──┼────┐    │    name          │    │ FK screen_id ────┼──► screen
│    channel       │    │    │    format        │    │    start_at      │
│    status        │    │    │    row_count     │    │    end_at        │
│    payment_method│    │    │    col_count     │    │    base_price    │
│    total_amount  │    │    │    is_active     │    │    status        │
└────────┬─────────┘    │    └────────┬─────────┘    └────────┬─────────┘
         │              │             │                       │
         │ 1:N          │             │ 1:N                   │
         │              │             │                       │
         ▼              │             ▼                       │
┌──────────────────┐    │    ┌──────────────────┐             │
│   order_line     │    │    │      seat        │             │
├──────────────────┤    │    ├──────────────────┤             │
│ PK id            │    │    │ PK id            │             │
│ FK order_id ─────┼────┼────│ FK screen_id ────┼──► screen   │
│ FK seat_id ──────┼────┼────│    row_label     │             │
│ FK product_id ───┼──┐ │    │    seat_number   │             │
│    item_type     │  │ │    │    seat_type     │             │
│    qty           │  │ │    │    is_aisle      │             │
│    unit_price    │  │ │    │    is_blocked    │             │
│    line_total    │  │ │    └────────┬─────────┘             │
└──────────────────┘  │ │             │                       │
                      │ │             │                       │
                      │ │             ▼                       │
┌──────────────────┐  │ │    ┌──────────────────┐             │
│     product      │  │ │    │    seat_lock     │             │
├──────────────────┤  │ │    ├──────────────────┤             │
│ PK id            │◄─┘ │    │ PK id            │             │
│    name          │    │    │ FK showtime_id ──┼─────────────┘
│    price         │    │    │ FK seat_id ──────┼──► seat
│    is_active     │    │    │ FK account_id ───┼──► account
└──────────────────┘    │    │    expires_at    │
                        │    └──────────────────┘
                        │
                        └──► showtime
```

### 2.2 Chi Tiết Các Bảng và Foreign Keys

#### Bảng `account` (Tài khoản người dùng)
```sql
CREATE TABLE account (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE,
    google_id VARCHAR(255),           -- ID từ Google OAuth
    phone VARCHAR(20),
    password_hash VARCHAR(255),
    full_name VARCHAR(255),
    role ENUM('CUSTOMER', 'STAFF', 'ADMIN') DEFAULT 'CUSTOMER',
    is_active BOOLEAN DEFAULT TRUE
);
```

#### Bảng `movie` (Phim)
```sql
CREATE TABLE movie (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    duration_min SMALLINT NOT NULL,   -- Thời lượng phim (phút)
    genre VARCHAR(100),               -- Thể loại
    poster VARCHAR(255),              -- URL poster
    rating_code VARCHAR(10),          -- P, C13, C16, C18
    is_active BOOLEAN DEFAULT TRUE
);
```

#### Bảng `screen` (Phòng chiếu)
```sql
CREATE TABLE screen (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE,          -- Mã phòng: SCREEN01
    name VARCHAR(100),                -- Tên: Phòng chiếu 1
    format VARCHAR(20) DEFAULT '2D',  -- 2D, 3D, IMAX
    row_count INT,                    -- Số hàng ghế
    col_count INT,                    -- Số cột ghế
    is_active BOOLEAN DEFAULT TRUE
);
```

#### Bảng `seat` (Ghế ngồi)
```sql
CREATE TABLE seat (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    screen_id BIGINT NOT NULL,        -- FK → screen.id
    row_label VARCHAR(5),             -- A, B, C, ...
    seat_number INT,                  -- 1, 2, 3, ...
    seat_type ENUM('STANDARD', 'VIP', 'COUPLE', 'ACCESSIBLE') DEFAULT 'STANDARD',
    is_aisle BOOLEAN DEFAULT FALSE,   -- Ghế lối đi
    is_blocked BOOLEAN DEFAULT FALSE, -- Ghế bị khóa
    
    UNIQUE(screen_id, row_label, seat_number),
    FOREIGN KEY (screen_id) REFERENCES screen(id) ON DELETE CASCADE
);
```

#### Bảng `showtime` (Suất chiếu)
```sql
CREATE TABLE showtime (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    movie_id BIGINT NOT NULL,         -- FK → movie.id
    screen_id BIGINT NOT NULL,        -- FK → screen.id
    start_at DATETIME NOT NULL,       -- Thời gian bắt đầu
    end_at DATETIME NOT NULL,         -- Thời gian kết thúc
    base_price INT NOT NULL,          -- Giá vé cơ bản
    status ENUM('SCHEDULED', 'OPEN', 'CLOSED', 'CANCELLED') DEFAULT 'OPEN',
    
    UNIQUE(screen_id, start_at),      -- Mỗi phòng chỉ có 1 suất tại 1 thời điểm
    FOREIGN KEY (movie_id) REFERENCES movie(id) ON DELETE RESTRICT,
    FOREIGN KEY (screen_id) REFERENCES screen(id) ON DELETE RESTRICT
);
```

#### Bảng `orders` (Đơn hàng)
```sql
CREATE TABLE orders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    channel ENUM('WEB', 'POS'),       -- Kênh đặt: Website hoặc Quầy
    account_id BIGINT,                -- FK → account.id (nullable cho khách vãng lai)
    cashier_id BIGINT,                -- FK → account.id (nhân viên bán vé)
    showtime_id BIGINT NOT NULL,      -- FK → showtime.id
    status ENUM('INIT', 'PAID', 'CANCELLED') DEFAULT 'INIT',
    payment_method ENUM('CASH', 'CARD', 'EWALLET'),
    total_amount INT DEFAULT 0,
    
    FOREIGN KEY (account_id) REFERENCES account(id) ON DELETE SET NULL,
    FOREIGN KEY (showtime_id) REFERENCES showtime(id) ON DELETE RESTRICT
);
```

#### Bảng `order_line` (Chi tiết đơn hàng)
```sql
CREATE TABLE order_line (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT NOT NULL,         -- FK → orders.id
    item_type ENUM('TICKET', 'PRODUCT'), -- Loại: Vé hoặc Sản phẩm
    seat_id BIGINT,                   -- FK → seat.id (nếu là vé)
    product_id BIGINT,                -- FK → product.id (nếu là sản phẩm)
    qty INT DEFAULT 1,
    unit_price INT NOT NULL,
    line_total INT NOT NULL,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seat(id) ON DELETE RESTRICT,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE RESTRICT
);
```

#### Bảng `seat_lock` (Khóa ghế tạm thời)
```sql
CREATE TABLE seat_lock (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    showtime_id BIGINT NOT NULL,      -- FK → showtime.id
    seat_id BIGINT NOT NULL,          -- FK → seat.id
    account_id BIGINT,                -- FK → account.id
    expires_at DATETIME NOT NULL,     -- Thời gian hết hạn khóa
    
    UNIQUE(showtime_id, seat_id),     -- Mỗi ghế chỉ bị khóa 1 lần cho 1 suất
    FOREIGN KEY (showtime_id) REFERENCES showtime(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seat(id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES account(id) ON DELETE SET NULL
);
```

#### Bảng `product` (Sản phẩm - Combo bắp nước)
```sql
CREATE TABLE product (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
);
```


---

## 3. CẤU HÌNH AUTHENTICATION

### 3.1 File config/auth.php

```php
// config/auth.php

'guards' => [
    'web' => [
        'driver' => 'session',           // Sử dụng session để lưu trạng thái đăng nhập
        'provider' => 'accounts',        // Provider là 'accounts'
    ],
    'api' => [
        'driver' => 'sanctum',           // API sử dụng Laravel Sanctum
        'provider' => 'accounts'
    ]
],

'providers' => [
    'accounts' => [
        'driver' => 'eloquent',
        'model' => App\Models\Account::class,  // Model Account thay vì User
    ],
],
```

**Giải thích:**
- `guard 'web'`: Dùng cho website, lưu session khi đăng nhập
- `guard 'api'`: Dùng cho API mobile, sử dụng token Sanctum
- `provider 'accounts'`: Chỉ định Model `Account` để xác thực

### 3.2 Model Account kế thừa Authenticatable

```php
// app/Models/Account.php

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Account extends Authenticatable  // Kế thừa Authenticatable để dùng Auth
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'account';      // Tên bảng trong database
    public $timestamps = false;        // Không dùng created_at, updated_at

    // Override method này để Laravel Auth dùng password_hash thay vì password
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
```

---

## 4. MIDDLEWARE VÀ PHÂN QUYỀN

### 4.1 Đăng ký Middleware trong bootstrap/app.php

```php
// bootstrap/app.php

use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Đăng ký alias 'role' cho CheckRole middleware
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
        
        // Redirect về /login khi chưa đăng nhập
        $middleware->redirectGuestsTo('/login');
    })
    ->create();
```

### 4.2 CheckRole Middleware

```php
// app/Http/Middleware/CheckRole.php

class CheckRole {
    public function handle(Request $request, Closure $next, ...$roles) {
        // Kiểm tra đã đăng nhập chưa
        if (!$request->user()) {
            return redirect()->route('auth.login.form');
        }

        // Kiểm tra user có role được phép không
        // $roles là mảng các role được truyền vào, VD: ['ADMIN', 'STAFF']
        if (!in_array($request->user()->role, $roles)) {
            // Redirect về trang phù hợp với role của user
            if (in_array($request->user()->role, ['ADMIN', 'STAFF'])) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Bạn không có quyền truy cập trang này');
            }
            return redirect()->route('booking.index')
                ->with('error', 'Bạn không có quyền truy cập trang này');
        }

        return $next($request);
    }
}
```

### 4.3 Cách Sử Dụng Middleware Trong Routes

```php
// routes/web.php

// 1. Routes cho Guest (chưa đăng nhập)
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm']);
    Route::post('/login', [AuthController::class, 'login']);
});

// 2. Routes cho Customer (đã đăng nhập + role CUSTOMER)
Route::middleware(['auth:web', 'role:CUSTOMER'])->group(function () {
    Route::get('/movies', [BookingController::class, 'index']);
    Route::get('/checkout', [PaymentController::class, 'checkout']);
});

// 3. Routes cho Admin/Staff (đã đăng nhập + role ADMIN hoặc STAFF)
Route::middleware(['auth:web', 'role:ADMIN,STAFF'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/users', [AdminController::class, 'usersList']);
});
```

---

## 5. LUỒNG XÁC THỰC (AUTHENTICATION FLOW)

### 5.1 Luồng Đăng Nhập

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           LUỒNG ĐĂNG NHẬP                                    │
└─────────────────────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │   User   │
    └────┬─────┘
         │
         │ 1. GET /login
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ MIDDLEWARE: guest:web                                                        │
│ Kiểm tra: User đã đăng nhập chưa?                                           │
│ - Nếu đã đăng nhập → Redirect về home                                       │
│ - Nếu chưa → Cho phép truy cập                                              │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ 2. Chưa đăng nhập → Cho phép
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: AuthController@showLoginForm()                                   │
│                                                                              │
│ return view('auth.login');                                                   │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ 3. Trả về view
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ VIEW: auth/login.blade.php                                                   │
│                                                                              │
│ ┌─────────────────────────────────────────────────────────────────────────┐ │
│ │                           LOGIN                                          │ │
│ │                                                                          │ │
│ │  Email:    [________________________]                                    │ │
│ │  Password: [________________________]                                    │ │
│ │                                                                          │ │
│ │  [        LOGIN        ]                                                 │ │
│ │                                                                          │ │
│ │  ─────────── Hoặc đăng nhập với ───────────                             │ │
│ │                                                                          │ │
│ │  [  🔵 Đăng nhập với Google  ]                                          │ │
│ │                                                                          │ │
│ │  Chưa có tài khoản? [Đăng ký]                                           │ │
│ └─────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ 4. User nhập email/password và submit
         │    POST /login
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: AuthController@login(Request $request)                           │
│                                                                              │
│ // Bước 1: Validate dữ liệu đầu vào                                         │
│ $validator = Validator::make($request->all(), [                             │
│     'email' => 'required',                                                   │
│     'password' => 'required',                                                │
│ ]);                                                                          │
│                                                                              │
│ // Bước 2: Tìm account trong database                                        │
│ $account = Account::where('email', $request->email)->first();               │
│                                                                              │
│ // Bước 3: Kiểm tra password                                                 │
│ if (!$account || !Hash::check($request->password, $account->password_hash)) │
│     return redirect()->back()->withErrors(['email' => 'Invalid...']);       │
│                                                                              │
│ // Bước 4: Đăng nhập user vào session                                        │
│ Auth::guard('web')->login($account);                                         │
│                                                                              │
│ // Bước 5: Redirect                                                          │
│ return redirect()->route('dashboard');                                       │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ 5. Đăng nhập thành công
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ DATABASE QUERY:                                                              │
│                                                                              │
│ SELECT * FROM account WHERE email = 'user@example.com' LIMIT 1;             │
│                                                                              │
│ Kết quả:                                                                     │
│ ┌────┬──────────────────┬───────────────────────┬──────────┬─────────┐      │
│ │ id │ email            │ password_hash         │ role     │is_active│      │
│ ├────┼──────────────────┼───────────────────────┼──────────┼─────────┤      │
│ │ 1  │ user@example.com │ $2y$10$xxxxx...      │ CUSTOMER │ 1       │      │
│ └────┴──────────────────┴───────────────────────┴──────────┴─────────┘      │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ 6. Redirect theo role
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ ROUTE: / (home)                                                              │
│                                                                              │
│ Route::get('/', function () {                                                │
│     if (auth()->guard('web')->check()) {                                     │
│         $user = auth()->guard('web')->user();                                │
│                                                                              │
│         // ADMIN hoặc STAFF → Admin Panel                                    │
│         if (in_array($user->role, ['ADMIN', 'STAFF'])) {                    │
│             return redirect()->route('admin.dashboard');                     │
│         }                                                                    │
│                                                                              │
│         // CUSTOMER → Trang đặt vé                                           │
│         return redirect()->route('booking.index');                           │
│     }                                                                        │
│     return redirect()->route('auth.login.form');                             │
│ });                                                                          │
└─────────────────────────────────────────────────────────────────────────────┘
```


### 5.2 Luồng Đăng Ký

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           LUỒNG ĐĂNG KÝ                                      │
└─────────────────────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │   User   │
    └────┬─────┘
         │
         │ 1. GET /register
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: AuthController@showRegisterForm()                                │
│                                                                              │
│ return view('auth.register');                                                │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ 2. User điền form và submit
         │    POST /register
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: AuthController@register(Request $request)                        │
│                                                                              │
│ // Bước 1: Validate                                                          │
│ $validator = Validator::make($request->all(), [                             │
│     'full_name' => 'required|string|max:255',                               │
│     'email' => 'required|email|unique:account,email',  // Email phải unique │
│     'phone' => 'required|string|max:20',                                    │
│     'password' => 'required|min:6|confirmed',  // Phải có password_confirmation │
│ ]);                                                                          │
│                                                                              │
│ // Bước 2: Tạo account mới                                                   │
│ $account = Account::create([                                                 │
│     'full_name' => $request->full_name,                                     │
│     'email' => $request->email,                                              │
│     'phone' => $request->phone,                                              │
│     'password_hash' => Hash::make($request->password),  // Hash password    │
│     'role' => 'CUSTOMER',  // Mặc định là CUSTOMER                          │
│ ]);                                                                          │
│                                                                              │
│ // Bước 3: Redirect về login                                                 │
│ return redirect()->route('auth.login.form')                                  │
│     ->with('success', 'Registration successful! Please login.');            │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ 3. Insert vào database
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ DATABASE QUERY:                                                              │
│                                                                              │
│ INSERT INTO account (full_name, email, phone, password_hash, role, is_active)│
│ VALUES ('Nguyen Van A', 'a@gmail.com', '0901234567', '$2y$10$xxx', 'CUSTOMER', 1);│
└─────────────────────────────────────────────────────────────────────────────┘
```

### 5.3 Luồng Đăng Nhập Google OAuth

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      LUỒNG ĐĂNG NHẬP GOOGLE OAUTH                            │
└─────────────────────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │   User   │
    └────┬─────┘
         │
         │ 1. Click "Đăng nhập với Google"
         │    GET /auth/google
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: AuthController@redirectToGoogle()                                │
│                                                                              │
│ return Socialite::driver('google')->redirect();                              │
│ // Redirect user đến trang đăng nhập Google                                  │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ 2. User đăng nhập Google và đồng ý
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                        GOOGLE OAUTH SERVER                                   │
│                                                                              │
│ User đăng nhập tài khoản Google                                              │
│ Google trả về callback với thông tin user                                    │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ 3. Google callback
         │    GET /auth/google/callback
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: AuthController@handleGoogleCallback()                            │
│                                                                              │
│ // Lấy thông tin user từ Google                                              │
│ $googleUser = Socialite::driver('google')->user();                          │
│ // $googleUser->getId()    → Google ID                                       │
│ // $googleUser->getEmail() → Email                                           │
│ // $googleUser->getName()  → Tên                                             │
│                                                                              │
│ // Tìm account theo google_id hoặc email                                     │
│ $account = Account::where('google_id', $googleUser->getId())                │
│     ->orWhere('email', $googleUser->getEmail())                              │
│     ->first();                                                               │
│                                                                              │
│ if ($account) {                                                              │
│     // Account đã tồn tại → Cập nhật google_id nếu chưa có                  │
│     if (!$account->google_id) {                                              │
│         $account->google_id = $googleUser->getId();                          │
│         $account->save();                                                    │
│     }                                                                        │
│ } else {                                                                     │
│     // Tạo account mới                                                       │
│     $account = Account::create([                                             │
│         'email' => $googleUser->getEmail(),                                  │
│         'google_id' => $googleUser->getId(),                                 │
│         'full_name' => $googleUser->getName(),                               │
│         'password_hash' => Hash::make(Str::random(16)),  // Random password │
│         'role' => 'CUSTOMER',                                                │
│         'is_active' => true,                                                 │
│     ]);                                                                      │
│ }                                                                            │
│                                                                              │
│ // Đăng nhập                                                                 │
│ Auth::guard('web')->login($account);                                         │
│                                                                              │
│ return redirect()->route('home');                                            │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 6. LUỒNG ĐẶT VÉ (BOOKING FLOW)

### 6.1 Tổng Quan Luồng Đặt Vé

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        LUỒNG ĐẶT VÉ HOÀN CHỈNH                               │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│  Bước 1  │───►│  Bước 2  │───►│  Bước 3  │───►│  Bước 4  │───►│  Bước 5  │
│ Danh sách│    │ Chi tiết │    │ Chọn ghế │    │ Thanh    │    │ Xác nhận │
│   phim   │    │   phim   │    │          │    │  toán    │    │          │
└──────────┘    └──────────┘    └──────────┘    └──────────┘    └──────────┘
  /movies      /movies/{id}   /showtimes/     /checkout      /payment/
                              {id}/seats                      confirm
```

### 6.2 Bước 1: Danh Sách Phim

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      BƯỚC 1: DANH SÁCH PHIM                                  │
└─────────────────────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │ Customer │
    └────┬─────┘
         │
         │ GET /movies
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ MIDDLEWARE: auth:web + role:CUSTOMER                                         │
│ Kiểm tra: Đã đăng nhập và có role CUSTOMER                                  │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: BookingController@index()                                        │
│                                                                              │
│ public function index()                                                      │
│ {                                                                            │
│     // Query: Lấy tất cả phim đang active                                   │
│     $movies = Movie::where('is_active', true)                               │
│         ->orderBy('title', 'asc')                                            │
│         ->get();                                                             │
│                                                                              │
│     return view('booking.index', compact('movies'));                         │
│ }                                                                            │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ Database Query
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ DATABASE QUERY:                                                              │
│                                                                              │
│ SELECT * FROM movie WHERE is_active = 1 ORDER BY title ASC;                 │
│                                                                              │
│ Kết quả:                                                                     │
│ ┌────┬─────────────────┬──────────────┬────────────┬──────────┬──────────┐  │
│ │ id │ title           │ duration_min │ genre      │ poster   │ is_active│  │
│ ├────┼─────────────────┼──────────────┼────────────┼──────────┼──────────┤  │
│ │ 1  │ Avengers        │ 150          │ Action     │ url1.jpg │ 1        │  │
│ │ 2  │ Spider-Man      │ 120          │ Action     │ url2.jpg │ 1        │  │
│ │ 3  │ Frozen          │ 100          │ Animation  │ url3.jpg │ 1        │  │
│ └────┴─────────────────┴──────────────┴────────────┴──────────┴──────────┘  │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ VIEW: booking/index.blade.php                                                │
│                                                                              │
│ @foreach($movies as $movie)                                                  │
│     <div class="movie-card">                                                 │
│         <img src="{{ $movie->poster }}">                                     │
│         <h3>{{ $movie->title }}</h3>                                         │
│         <span>{{ $movie->duration_min }} phút</span>                         │
│         <a href="{{ route('booking.movie.detail', $movie->id) }}">          │
│             Đặt vé ngay                                                      │
│         </a>                                                                 │
│     </div>                                                                   │
│ @endforeach                                                                  │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 6.3 Bước 2: Chi Tiết Phim và Chọn Suất Chiếu

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                   BƯỚC 2: CHI TIẾT PHIM + SUẤT CHIẾU                         │
└─────────────────────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │ Customer │
    └────┬─────┘
         │
         │ GET /movies/1
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: BookingController@showMovieDetail($id)                           │
│                                                                              │
│ public function showMovieDetail($id)                                         │
│ {                                                                            │
│     // Query phim với các suất chiếu                                         │
│     $movie = Movie::where('is_active', true)                                │
│         ->with(['showtimes' => function($query) {                           │
│             $query->where('status', 'OPEN')           // Chỉ lấy suất OPEN  │
│                 ->where('start_at', '>=', Carbon::now()) // Chưa qua        │
│                 ->orderBy('start_at', 'asc')                                 │
│                 ->with('screen');  // Eager load screen                      │
│         }])                                                                  │
│         ->findOrFail($id);                                                   │
│                                                                              │
│     // Group suất chiếu theo ngày                                            │
│     $showtimesByDate = $movie->showtimes->groupBy(function($showtime) {     │
│         return Carbon::parse($showtime->start_at)->format('Y-m-d');         │
│     });                                                                      │
│                                                                              │
│     return view('booking.movie-detail', compact('movie', 'showtimesByDate'));│
│ }                                                                            │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ Database Queries
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ DATABASE QUERIES:                                                            │
│                                                                              │
│ 1. Lấy thông tin phim:                                                       │
│ SELECT * FROM movie WHERE id = 1 AND is_active = 1;                         │
│                                                                              │
│ 2. Lấy các suất chiếu (Eager Loading với with()):                           │
│ SELECT showtime.*, screen.name as screen_name                                │
│ FROM showtime                                                                │
│ LEFT JOIN screen ON showtime.screen_id = screen.id                          │
│ WHERE showtime.movie_id = 1                                                  │
│   AND showtime.status = 'OPEN'                                               │
│   AND showtime.start_at >= '2025-12-13 00:00:00'                            │
│ ORDER BY showtime.start_at ASC;                                              │
│                                                                              │
│ Kết quả showtimes:                                                           │
│ ┌────┬──────────┬───────────┬─────────────────────┬────────────┬────────┐   │
│ │ id │ movie_id │ screen_id │ start_at            │ base_price │ status │   │
│ ├────┼──────────┼───────────┼─────────────────────┼────────────┼────────┤   │
│ │ 1  │ 1        │ 1         │ 2025-12-14 10:00:00 │ 100000     │ OPEN   │   │
│ │ 2  │ 1        │ 2         │ 2025-12-14 14:00:00 │ 100000     │ OPEN   │   │
│ │ 3  │ 1        │ 1         │ 2025-12-14 19:00:00 │ 120000     │ OPEN   │   │
│ │ 4  │ 1        │ 1         │ 2025-12-15 10:00:00 │ 100000     │ OPEN   │   │
│ └────┴──────────┴───────────┴─────────────────────┴────────────┴────────┘   │
└─────────────────────────────────────────────────────────────────────────────┘
```


### 6.4 Bước 3: Chọn Ghế

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         BƯỚC 3: CHỌN GHẾ                                     │
└─────────────────────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │ Customer │
    └────┬─────┘
         │
         │ GET /showtimes/1/seats
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: BookingController@seatMap($showtimeId)                           │
│                                                                              │
│ public function seatMap($showtimeId)                                         │
│ {                                                                            │
│     // 1. Lấy showtime với movie và screen (bao gồm seats)                  │
│     $showtime = Showtime::with(['movie', 'screen.seats'])                   │
│         ->where('status', 'OPEN')                                            │
│         ->findOrFail($showtimeId);                                           │
│                                                                              │
│     // 2. Lấy danh sách ghế đã được đặt (trong order đã PAID)               │
│     $bookedSeats = OrderLine::whereHas('order', function($query) use ($showtimeId) {│
│         $query->where('showtime_id', $showtimeId)                           │
│             ->whereIn('status', ['PAID']);                                   │
│     })                                                                       │
│     ->where('item_type', 'TICKET')                                           │
│     ->pluck('seat_id')                                                       │
│     ->toArray();                                                             │
│                                                                              │
│     // 3. Lấy danh sách ghế đang bị khóa tạm thời                           │
│     $lockedSeats = SeatLock::where('showtime_id', $showtimeId)              │
│         ->where('expires_at', '>', Carbon::now())                            │
│         ->pluck('seat_id')                                                   │
│         ->toArray();                                                         │
│                                                                              │
│     // 4. Group ghế theo hàng                                                │
│     $seatsByRow = $showtime->screen->seats                                   │
│         ->groupBy('row_label')                                               │
│         ->sortKeys();                                                        │
│                                                                              │
│     return view('booking.seat-map', compact(                                 │
│         'showtime', 'seatsByRow', 'bookedSeats', 'lockedSeats'              │
│     ));                                                                      │
│ }                                                                            │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ Database Queries
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ DATABASE QUERIES:                                                            │
│                                                                              │
│ 1. Lấy showtime với movie và screen:                                         │
│ SELECT * FROM showtime WHERE id = 1 AND status = 'OPEN';                    │
│ SELECT * FROM movie WHERE id = (showtime.movie_id);                         │
│ SELECT * FROM screen WHERE id = (showtime.screen_id);                       │
│ SELECT * FROM seat WHERE screen_id = (screen.id);                           │
│                                                                              │
│ 2. Lấy ghế đã đặt:                                                           │
│ SELECT order_line.seat_id                                                    │
│ FROM order_line                                                              │
│ INNER JOIN orders ON order_line.order_id = orders.id                        │
│ WHERE orders.showtime_id = 1                                                 │
│   AND orders.status IN ('PAID')                                              │
│   AND order_line.item_type = 'TICKET';                                       │
│                                                                              │
│ 3. Lấy ghế đang bị khóa:                                                     │
│ SELECT seat_id FROM seat_lock                                                │
│ WHERE showtime_id = 1                                                        │
│   AND expires_at > NOW();                                                    │
│                                                                              │
│ Kết quả seats:                                                               │
│ ┌────┬───────────┬───────────┬─────────────┬───────────┐                    │
│ │ id │ screen_id │ row_label │ seat_number │ seat_type │                    │
│ ├────┼───────────┼───────────┼─────────────┼───────────┤                    │
│ │ 1  │ 1         │ A         │ 1           │ STANDARD  │                    │
│ │ 2  │ 1         │ A         │ 2           │ STANDARD  │                    │
│ │ 3  │ 1         │ A         │ 3           │ STANDARD  │                    │
│ │ 4  │ 1         │ B         │ 1           │ STANDARD  │                    │
│ │ 5  │ 1         │ B         │ 2           │ VIP       │                    │
│ └────┴───────────┴───────────┴─────────────┴───────────┘                    │
│                                                                              │
│ bookedSeats = [3, 5]  // Ghế A3 và B2 đã được đặt                           │
│ lockedSeats = [4]     // Ghế B1 đang bị khóa bởi user khác                  │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ VIEW: booking/seat-map.blade.php                                             │
│                                                                              │
│ ┌─────────────────────────────────────────────────────────────────────────┐ │
│ │                           MÀN HÌNH                                       │ │
│ │ ═══════════════════════════════════════════════════════════════════════ │ │
│ │                                                                          │ │
│ │     A  [1][2][■][4][5][6][7][8][9][10]  A     ← Ghế A3 đã đặt (■)       │ │
│ │     B  [○][●][3][4][5][6][7][8][9][10]  B     ← B1 đang khóa (○)        │ │
│ │     C  [1][2][3][4][5][6][7][8][9][10]  C        B2 đang chọn (●)       │ │
│ │     D  [1][2][3][4][5][6][7][8][9][10]  D                               │ │
│ │                                                                          │ │
│ │  Chú thích:                                                              │ │
│ │  [ ] Trống   [●] Đang chọn   [■] Đã đặt   [○] Đang giữ                  │ │
│ │                                                                          │ │
│ │  ┌────────────────────────────────────────────────────────────────────┐ │ │
│ │  │ Ghế đã chọn: B2                                                    │ │ │
│ │  │ Tổng tiền: 100,000 đ                                               │ │ │
│ │  │ [              TIẾP TỤC              ]                             │ │ │
│ │  └────────────────────────────────────────────────────────────────────┘ │ │
│ └─────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ User chọn ghế và click "Tiếp tục"
         │ JavaScript tạo form và submit
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ JAVASCRIPT: Xử lý chọn ghế                                                   │
│                                                                              │
│ let selectedSeats = [];                                                      │
│ const pricePerSeat = 100000;  // Từ $showtime->base_price                   │
│                                                                              │
│ function toggleSeat(element) {                                               │
│     const seatId = element.getAttribute('data-seat-id');                    │
│     const seatLabel = element.getAttribute('data-row') +                    │
│                       element.getAttribute('data-seat-number');             │
│                                                                              │
│     if (element.classList.contains('selected')) {                           │
│         // Bỏ chọn                                                           │
│         selectedSeats = selectedSeats.filter(s => s.id !== seatId);         │
│     } else {                                                                 │
│         // Chọn ghế                                                          │
│         selectedSeats.push({ id: seatId, label: seatLabel });               │
│     }                                                                        │
│     updateSummary();                                                         │
│ }                                                                            │
│                                                                              │
│ // Khi click "Tiếp tục" → Submit form đến /checkout                         │
│ document.getElementById('continue-btn').addEventListener('click', () => {   │
│     const form = document.createElement('form');                            │
│     form.method = 'GET';                                                     │
│     form.action = '/checkout';                                               │
│                                                                              │
│     // Thêm showtime_id                                                      │
│     form.innerHTML = `                                                       │
│         <input type="hidden" name="showtime_id" value="1">                  │
│         <input type="hidden" name="seat_ids[]" value="5">                   │
│         <input type="hidden" name="price" value="100000">                   │
│     `;                                                                       │
│                                                                              │
│     document.body.appendChild(form);                                         │
│     form.submit();                                                           │
│ });                                                                          │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 7. LUỒNG THANH TOÁN (PAYMENT FLOW)

### 7.1 Bước 4: Trang Checkout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      BƯỚC 4: TRANG CHECKOUT                                  │
└─────────────────────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │ Customer │
    └────┬─────┘
         │
         │ GET /checkout?showtime_id=1&seat_ids[]=5&price=100000
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: PaymentController@checkout(Request $request)                     │
│                                                                              │
│ public function checkout(Request $request)                                   │
│ {                                                                            │
│     // 1. Lấy thông tin từ request                                          │
│     $showtimeId = $request->input('showtime_id');  // 1                     │
│     $seatIds = $request->input('seat_ids', []);    // [5]                   │
│     $totalAmount = $request->input('price');       // 100000                │
│                                                                              │
│     // 2. Validate                                                           │
│     if (empty($seatIds)) {                                                   │
│         return redirect()->back()                                            │
│             ->with('error', 'Vui lòng chọn ít nhất một ghế');               │
│     }                                                                        │
│                                                                              │
│     // 3. Lấy thông tin showtime với movie và screen                        │
│     $showtime = Showtime::with(['movie', 'screen'])->find($showtimeId);     │
│                                                                              │
│     // 4. Lấy thông tin các ghế đã chọn                                     │
│     $seats = Seat::whereIn('id', $seatIds)->get();                          │
│                                                                              │
│     // 5. Lưu vào session để dùng khi confirm payment                       │
│     session([                                                                │
│         'booking_showtime_id' => $showtimeId,                               │
│         'booking_seat_ids' => $seatIds,                                      │
│         'booking_total_amount' => $totalAmount                               │
│     ]);                                                                      │
│                                                                              │
│     return view('payment.checkout', compact('showtime', 'seats', 'totalAmount'));│
│ }                                                                            │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ Database Queries
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ DATABASE QUERIES:                                                            │
│                                                                              │
│ 1. Lấy showtime:                                                             │
│ SELECT * FROM showtime WHERE id = 1;                                        │
│ SELECT * FROM movie WHERE id = (showtime.movie_id);                         │
│ SELECT * FROM screen WHERE id = (showtime.screen_id);                       │
│                                                                              │
│ 2. Lấy thông tin ghế:                                                        │
│ SELECT * FROM seat WHERE id IN (5);                                         │
│                                                                              │
│ Kết quả:                                                                     │
│ showtime: { id: 1, movie_id: 1, screen_id: 1, base_price: 100000 }         │
│ movie: { id: 1, title: 'Avengers' }                                         │
│ seats: [{ id: 5, row_label: 'B', seat_number: 2 }]                          │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ VIEW: payment/checkout.blade.php                                             │
│                                                                              │
│ ┌─────────────────────────────────────────────────────────────────────────┐ │
│ │                         THANH TOÁN                                       │ │
│ │                                                                          │ │
│ │                    ┌─────────────────┐                                   │ │
│ │                    │   QR CODE       │                                   │ │
│ │                    │   (VietQR)      │                                   │ │
│ │                    └─────────────────┘                                   │ │
│ │                                                                          │ │
│ │                      100,000 đ                                           │ │
│ │                      Avengers                                            │ │
│ │                      Ghế: B2                                             │ │
│ │                      Nội dung CK: ORDER1702468800                        │ │
│ │                                                                          │ │
│ │     Ngân hàng: MB Bank (970422)                                          │ │
│ │     Số TK: 0378366953                                                    │ │
│ │     Tên TK: NGUYEN LUU BAO KHANG                                         │ │
│ │                                                                          │ │
│ │     [        ✓ ĐÃ THANH TOÁN        ]                                   │ │
│ │     [          ← Quay lại           ]                                    │ │
│ │                                                                          │ │
│ └─────────────────────────────────────────────────────────────────────────┘ │
│                                                                              │
│ // Tạo QR URL từ SePay                                                       │
│ @php                                                                         │
│     $bankId = env('VIETQR_BANK_ID', '970422');                              │
│     $accountNo = env('VIETQR_ACCOUNT_NO', '0378366953');                    │
│     $orderId = 'ORDER' . time();                                             │
│     $qrUrl = "https://qr.sepay.vn/img?acc={$accountNo}&bank={$bankId}"     │
│            . "&amount={$totalAmount}&des=" . urlencode($orderId);           │
│ @endphp                                                                      │
│                                                                              │
│ <img src="{{ $qrUrl }}" alt="QR Code">                                       │
└─────────────────────────────────────────────────────────────────────────────┘
```


### 7.2 Bước 5: Xác Nhận Thanh Toán

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                   BƯỚC 5: XÁC NHẬN THANH TOÁN                                │
└─────────────────────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │ Customer │
    └────┬─────┘
         │
         │ User quét QR, chuyển khoản xong
         │ Click "Đã thanh toán"
         │ POST /payment/confirm
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: PaymentController@confirmPayment(Request $request)               │
│                                                                              │
│ public function confirmPayment(Request $request)                             │
│ {                                                                            │
│     try {                                                                    │
│         // 1. Lấy thông tin từ session                                      │
│         $showtimeId = session('booking_showtime_id');    // 1               │
│         $seatIds = session('booking_seat_ids', []);      // [5]             │
│         $totalAmount = session('booking_total_amount');  // 100000          │
│         $account = auth()->guard('web')->user();         // Current user    │
│                                                                              │
│         // 2. Validate session data                                          │
│         if (!$showtimeId || empty($seatIds) || !$account) {                 │
│             return redirect()->route('booking.index')                        │
│                 ->with('error', 'Phiên đặt vé đã hết hạn');                 │
│         }                                                                    │
│                                                                              │
│         // 3. Bắt đầu database transaction                                   │
│         DB::beginTransaction();                                              │
│                                                                              │
│         // 4. Tạo order mới                                                  │
│         $order = Order::create([                                             │
│             'channel' => 'WEB',                                              │
│             'account_id' => $account->id,                                    │
│             'showtime_id' => $showtimeId,                                    │
│             'status' => 'PAID',                                              │
│             'payment_method' => 'CARD',                                      │
│             'total_amount' => $totalAmount,                                  │
│         ]);                                                                  │
│                                                                              │
│         // 5. Tạo order lines cho từng ghế                                   │
│         foreach ($seatIds as $seatId) {                                     │
│             OrderLine::create([                                              │
│                 'order_id' => $order->id,                                    │
│                 'item_type' => 'TICKET',                                     │
│                 'seat_id' => $seatId,                                        │
│                 'qty' => 1,                                                  │
│                 'unit_price' => $totalAmount / count($seatIds),             │
│                 'line_total' => $totalAmount / count($seatIds),             │
│             ]);                                                              │
│         }                                                                    │
│                                                                              │
│         // 6. Commit transaction                                             │
│         DB::commit();                                                        │
│                                                                              │
│         // 7. Xóa session booking                                            │
│         session()->forget([                                                  │
│             'booking_showtime_id',                                           │
│             'booking_seat_ids',                                              │
│             'booking_total_amount'                                           │
│         ]);                                                                  │
│                                                                              │
│         // 8. Redirect với thông báo thành công                              │
│         $orderCode = 'ORDER' . str_pad($order->id, 6, '0', STR_PAD_LEFT);   │
│         return redirect()->route('booking.index')                            │
│             ->with('success', "🎉 Đặt vé thành công! Mã: {$orderCode}");    │
│                                                                              │
│     } catch (\Exception $e) {                                                │
│         DB::rollBack();                                                      │
│         return redirect()->route('booking.index')                            │
│             ->with('error', 'Đặt vé thất bại: ' . $e->getMessage());        │
│     }                                                                        │
│ }                                                                            │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ Database Queries (trong Transaction)
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ DATABASE QUERIES:                                                            │
│                                                                              │
│ START TRANSACTION;                                                           │
│                                                                              │
│ 1. Insert order:                                                             │
│ INSERT INTO orders (channel, account_id, showtime_id, status, payment_method, total_amount)│
│ VALUES ('WEB', 1, 1, 'PAID', 'CARD', 100000);                               │
│ -- Trả về order.id = 1                                                       │
│                                                                              │
│ 2. Insert order_line:                                                        │
│ INSERT INTO order_line (order_id, item_type, seat_id, qty, unit_price, line_total)│
│ VALUES (1, 'TICKET', 5, 1, 100000, 100000);                                  │
│                                                                              │
│ COMMIT;                                                                      │
│                                                                              │
│ Kết quả trong database:                                                      │
│                                                                              │
│ orders:                                                                      │
│ ┌────┬─────────┬────────────┬─────────────┬────────┬────────────────┬────────────┐│
│ │ id │ channel │ account_id │ showtime_id │ status │ payment_method │ total_amount││
│ ├────┼─────────┼────────────┼─────────────┼────────┼────────────────┼────────────┤│
│ │ 1  │ WEB     │ 1          │ 1           │ PAID   │ CARD           │ 100000     ││
│ └────┴─────────┴────────────┴─────────────┴────────┴────────────────┴────────────┘│
│                                                                              │
│ order_line:                                                                  │
│ ┌────┬──────────┬───────────┬─────────┬─────┬────────────┬────────────┐      │
│ │ id │ order_id │ item_type │ seat_id │ qty │ unit_price │ line_total │      │
│ ├────┼──────────┼───────────┼─────────┼─────┼────────────┼────────────┤      │
│ │ 1  │ 1        │ TICKET    │ 5       │ 1   │ 100000     │ 100000     │      │
│ └────┴──────────┴───────────┴─────────┴─────┴────────────┴────────────┘      │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 7.3 Xem Lịch Sử Đặt Vé

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      XEM LỊCH SỬ ĐẶT VÉ                                      │
└─────────────────────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │ Customer │
    └────┬─────┘
         │
         │ GET /my-bookings
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: DashboardController@myBookings()                                 │
│                                                                              │
│ public function myBookings()                                                 │
│ {                                                                            │
│     $user = Auth::guard('web')->user();                                      │
│                                                                              │
│     // Lấy tất cả đơn hàng của user với các relationships                   │
│     $orders = Order::where('account_id', $user->id)                         │
│         ->with([                                                             │
│             'order_lines.seat',      // Thông tin ghế                       │
│             'order_lines.product',   // Thông tin sản phẩm (nếu có)         │
│             'showtime.movie',        // Thông tin phim                       │
│             'showtime.screen'        // Thông tin phòng chiếu               │
│         ])                                                                   │
│         ->orderBy('id', 'desc')      // Mới nhất trước                      │
│         ->paginate(10);              // Phân trang 10 items/page            │
│                                                                              │
│     return view('booking.my-bookings', compact('user', 'orders'));          │
│ }                                                                            │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ Database Queries (Eager Loading)
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ DATABASE QUERIES:                                                            │
│                                                                              │
│ 1. Lấy orders của user:                                                      │
│ SELECT * FROM orders WHERE account_id = 1 ORDER BY id DESC LIMIT 10;        │
│                                                                              │
│ 2. Eager load order_lines:                                                   │
│ SELECT * FROM order_line WHERE order_id IN (1, 2, 3...);                    │
│                                                                              │
│ 3. Eager load seats:                                                         │
│ SELECT * FROM seat WHERE id IN (5, 6, 7...);                                │
│                                                                              │
│ 4. Eager load showtimes:                                                     │
│ SELECT * FROM showtime WHERE id IN (1, 2...);                               │
│                                                                              │
│ 5. Eager load movies:                                                        │
│ SELECT * FROM movie WHERE id IN (1, 2...);                                  │
│                                                                              │
│ 6. Eager load screens:                                                       │
│ SELECT * FROM screen WHERE id IN (1, 2...);                                 │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 8. LUỒNG QUẢN TRỊ (ADMIN FLOW)

### 8.1 Dashboard Admin

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         ADMIN DASHBOARD                                      │
└─────────────────────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │  Admin   │
    └────┬─────┘
         │
         │ GET /admin
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ MIDDLEWARE: auth:web + role:ADMIN,STAFF                                      │
│ Kiểm tra: Đã đăng nhập và có role ADMIN hoặc STAFF                          │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ CONTROLLER: AdminController@dashboard()                                      │
│                                                                              │
│ public function dashboard()                                                  │
│ {                                                                            │
│     // Thống kê tổng quan                                                    │
│     $totalUsers = Account::count();                                          │
│     $totalMovies = Movie::count();                                           │
│     $totalOrders = Order::count();                                           │
│     $totalRevenue = Order::where('status', 'PAID')->sum('total_amount');    │
│                                                                              │
│     // Lấy 10 đơn hàng gần nhất                                              │
│     $recentOrders = Order::with(['account', 'showtime.movie'])              │
│         ->orderBy('id', 'desc')                                              │
│         ->limit(10)                                                          │
│         ->get();                                                             │
│                                                                              │
│     return view('admin.dashboard', [                                         │
│         'totalUsers' => $totalUsers,                                         │
│         'totalMovies' => $totalMovies,                                       │
│         'totalOrders' => $totalOrders,                                       │
│         'totalRevenue' => $totalRevenue,                                     │
│         'recentOrders' => $recentOrders,                                     │
│     ]);                                                                      │
│ }                                                                            │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         │ Database Queries
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ DATABASE QUERIES:                                                            │
│                                                                              │
│ 1. Đếm users:                                                                │
│ SELECT COUNT(*) FROM account;                                                │
│                                                                              │
│ 2. Đếm movies:                                                               │
│ SELECT COUNT(*) FROM movie;                                                  │
│                                                                              │
│ 3. Đếm orders:                                                               │
│ SELECT COUNT(*) FROM orders;                                                 │
│                                                                              │
│ 4. Tính tổng doanh thu:                                                      │
│ SELECT SUM(total_amount) FROM orders WHERE status = 'PAID';                 │
│                                                                              │
│ 5. Lấy 10 orders gần nhất:                                                   │
│ SELECT orders.*, account.full_name, movie.title                             │
│ FROM orders                                                                  │
│ LEFT JOIN account ON orders.account_id = account.id                         │
│ LEFT JOIN showtime ON orders.showtime_id = showtime.id                      │
│ LEFT JOIN movie ON showtime.movie_id = movie.id                             │
│ ORDER BY orders.id DESC                                                      │
│ LIMIT 10;                                                                    │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 8.2 Quản Lý Phim (CRUD)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      QUẢN LÝ PHIM - CRUD                                     │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ 1. DANH SÁCH PHIM (GET /admin/movies)                                        │
│                                                                              │
│ AdminController@moviesList()                                                 │
│ {                                                                            │
│     $movies = Movie::orderBy('id', 'desc')->paginate(10);                   │
│     return view('admin.movies.list', ['movies' => $movies]);                │
│ }                                                                            │
│                                                                              │
│ Query: SELECT * FROM movie ORDER BY id DESC LIMIT 10 OFFSET 0;              │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ 2. TẠO PHIM MỚI (POST /admin/movies)                                         │
│                                                                              │
│ AdminController@movieStore(Request $request)                                 │
│ {                                                                            │
│     $validated = $request->validate([                                        │
│         'title' => 'required|string|max:255',                               │
│         'duration_min' => 'required|integer|min:1',                         │
│         'genre' => 'nullable|string|max:100',                               │
│         'rating_code' => 'nullable|string|max:10',                          │
│         'poster' => 'nullable|string|max:255',                              │
│         'is_active' => 'boolean',                                            │
│     ]);                                                                      │
│                                                                              │
│     Movie::create($validated);                                               │
│     return redirect()->route('admin.movies.list')                            │
│         ->with('success', 'Tạo phim mới thành công');                       │
│ }                                                                            │
│                                                                              │
│ Query: INSERT INTO movie (title, duration_min, genre, poster, rating_code, is_active)│
│        VALUES ('New Movie', 120, 'Action', 'url.jpg', 'P', 1);              │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ 3. CẬP NHẬT PHIM (PUT /admin/movies/{id})                                    │
│                                                                              │
│ AdminController@movieUpdate(Request $request, $id)                           │
│ {                                                                            │
│     $movie = Movie::findOrFail($id);                                        │
│     $validated = $request->validate([...]);                                  │
│     $movie->update($validated);                                              │
│     return redirect()->route('admin.movies.list')                            │
│         ->with('success', 'Cập nhật phim thành công');                      │
│ }                                                                            │
│                                                                              │
│ Query: UPDATE movie SET title = 'Updated', duration_min = 130 WHERE id = 1; │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ 4. XÓA PHIM (DELETE /admin/movies/{id})                                      │
│                                                                              │
│ AdminController@movieDelete($id)                                             │
│ {                                                                            │
│     $movie = Movie::findOrFail($id);                                        │
│     $movie->delete();                                                        │
│     return redirect()->route('admin.movies.list')                            │
│         ->with('success', 'Xóa phim thành công');                           │
│ }                                                                            │
│                                                                              │
│ Query: DELETE FROM movie WHERE id = 1;                                       │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ 5. TOGGLE TRẠNG THÁI (PATCH /admin/movies/{id}/toggle-status)               │
│                                                                              │
│ AdminController@movieToggleStatus($id)                                       │
│ {                                                                            │
│     $movie = Movie::findOrFail($id);                                        │
│     $movie->is_active = !$movie->is_active;  // Đảo ngược trạng thái       │
│     $movie->save();                                                          │
│     return redirect()->route('admin.movies.list')                            │
│         ->with('success', 'Cập nhật trạng thái phim thành công');           │
│ }                                                                            │
│                                                                              │
│ Query: UPDATE movie SET is_active = 0 WHERE id = 1;  // Nếu đang là 1      │
└─────────────────────────────────────────────────────────────────────────────┘
```


---

## 9. CÁCH GỌI QUAN HỆ TRONG CODE

### 9.1 Định Nghĩa Quan Hệ Trong Models

```php
// ═══════════════════════════════════════════════════════════════════════════
// MODEL: Account (app/Models/Account.php)
// ═══════════════════════════════════════════════════════════════════════════

class Account extends Authenticatable
{
    protected $table = 'account';

    // 1 Account có nhiều Order (1:N)
    public function orders()
    {
        return $this->hasMany(Order::class);
        // SQL: SELECT * FROM orders WHERE account_id = {account.id}
    }

    // 1 Account có nhiều SeatLock (1:N)
    public function seat_locks()
    {
        return $this->hasMany(SeatLock::class);
        // SQL: SELECT * FROM seat_lock WHERE account_id = {account.id}
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// MODEL: Movie (app/Models/Movie.php)
// ═══════════════════════════════════════════════════════════════════════════

class Movie extends Model
{
    protected $table = 'movie';

    // 1 Movie có nhiều Showtime (1:N)
    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
        // SQL: SELECT * FROM showtime WHERE movie_id = {movie.id}
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// MODEL: Screen (app/Models/Screen.php)
// ═══════════════════════════════════════════════════════════════════════════

class Screen extends Model
{
    protected $table = 'screen';

    // 1 Screen có nhiều Seat (1:N)
    public function seats()
    {
        return $this->hasMany(Seat::class);
        // SQL: SELECT * FROM seat WHERE screen_id = {screen.id}
    }

    // 1 Screen có nhiều Showtime (1:N)
    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
        // SQL: SELECT * FROM showtime WHERE screen_id = {screen.id}
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// MODEL: Seat (app/Models/Seat.php)
// ═══════════════════════════════════════════════════════════════════════════

class Seat extends Model
{
    protected $table = 'seat';

    // 1 Seat thuộc về 1 Screen (N:1)
    public function screen()
    {
        return $this->belongsTo(Screen::class);
        // SQL: SELECT * FROM screen WHERE id = {seat.screen_id}
    }

    // 1 Seat có nhiều OrderLine (1:N)
    public function order_lines()
    {
        return $this->hasMany(OrderLine::class);
        // SQL: SELECT * FROM order_line WHERE seat_id = {seat.id}
    }

    // 1 Seat có nhiều SeatLock (1:N)
    public function seat_locks()
    {
        return $this->hasMany(SeatLock::class);
        // SQL: SELECT * FROM seat_lock WHERE seat_id = {seat.id}
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// MODEL: Showtime (app/Models/Showtime.php)
// ═══════════════════════════════════════════════════════════════════════════

class Showtime extends Model
{
    protected $table = 'showtime';

    // 1 Showtime thuộc về 1 Movie (N:1)
    public function movie()
    {
        return $this->belongsTo(Movie::class);
        // SQL: SELECT * FROM movie WHERE id = {showtime.movie_id}
    }

    // 1 Showtime thuộc về 1 Screen (N:1)
    public function screen()
    {
        return $this->belongsTo(Screen::class);
        // SQL: SELECT * FROM screen WHERE id = {showtime.screen_id}
    }

    // 1 Showtime có nhiều Order (1:N)
    public function orders()
    {
        return $this->hasMany(Order::class);
        // SQL: SELECT * FROM orders WHERE showtime_id = {showtime.id}
    }

    // 1 Showtime có nhiều SeatLock (1:N)
    public function seat_locks()
    {
        return $this->hasMany(SeatLock::class);
        // SQL: SELECT * FROM seat_lock WHERE showtime_id = {showtime.id}
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// MODEL: Order (app/Models/Order.php)
// ═══════════════════════════════════════════════════════════════════════════

class Order extends Model
{
    protected $table = 'orders';

    // 1 Order thuộc về 1 Account (N:1)
    public function account()
    {
        return $this->belongsTo(Account::class);
        // SQL: SELECT * FROM account WHERE id = {order.account_id}
    }

    // 1 Order thuộc về 1 Showtime (N:1)
    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
        // SQL: SELECT * FROM showtime WHERE id = {order.showtime_id}
    }

    // 1 Order có nhiều OrderLine (1:N)
    public function order_lines()
    {
        return $this->hasMany(OrderLine::class);
        // SQL: SELECT * FROM order_line WHERE order_id = {order.id}
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// MODEL: OrderLine (app/Models/OrderLine.php)
// ═══════════════════════════════════════════════════════════════════════════

class OrderLine extends Model
{
    protected $table = 'order_line';

    // 1 OrderLine thuộc về 1 Order (N:1)
    public function order()
    {
        return $this->belongsTo(Order::class);
        // SQL: SELECT * FROM orders WHERE id = {order_line.order_id}
    }

    // 1 OrderLine thuộc về 1 Seat (N:1) - nullable
    public function seat()
    {
        return $this->belongsTo(Seat::class);
        // SQL: SELECT * FROM seat WHERE id = {order_line.seat_id}
    }

    // 1 OrderLine thuộc về 1 Product (N:1) - nullable
    public function product()
    {
        return $this->belongsTo(Product::class);
        // SQL: SELECT * FROM product WHERE id = {order_line.product_id}
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// MODEL: SeatLock (app/Models/SeatLock.php)
// ═══════════════════════════════════════════════════════════════════════════

class SeatLock extends Model
{
    protected $table = 'seat_lock';

    // 1 SeatLock thuộc về 1 Showtime (N:1)
    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    // 1 SeatLock thuộc về 1 Seat (N:1)
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    // 1 SeatLock thuộc về 1 Account (N:1) - nullable
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// MODEL: Product (app/Models/Product.php)
// ═══════════════════════════════════════════════════════════════════════════

class Product extends Model
{
    protected $table = 'product';

    // 1 Product có nhiều OrderLine (1:N)
    public function order_lines()
    {
        return $this->hasMany(OrderLine::class);
    }
}
```

### 9.2 Cách Sử Dụng Quan Hệ Trong Controller

```php
// ═══════════════════════════════════════════════════════════════════════════
// VÍ DỤ 1: Lấy phim với các suất chiếu (Eager Loading)
// ═══════════════════════════════════════════════════════════════════════════

$movie = Movie::with('showtimes')->find(1);

// Truy cập quan hệ:
foreach ($movie->showtimes as $showtime) {
    echo $showtime->start_at;
    echo $showtime->base_price;
}

// SQL được sinh ra:
// SELECT * FROM movie WHERE id = 1;
// SELECT * FROM showtime WHERE movie_id = 1;


// ═══════════════════════════════════════════════════════════════════════════
// VÍ DỤ 2: Lấy phim với suất chiếu + screen (Nested Eager Loading)
// ═══════════════════════════════════════════════════════════════════════════

$movie = Movie::with(['showtimes' => function($query) {
    $query->where('status', 'OPEN')
          ->with('screen');  // Nested: load screen của mỗi showtime
}])->find(1);

// Truy cập:
foreach ($movie->showtimes as $showtime) {
    echo $showtime->screen->name;  // Tên phòng chiếu
}

// SQL:
// SELECT * FROM movie WHERE id = 1;
// SELECT * FROM showtime WHERE movie_id = 1 AND status = 'OPEN';
// SELECT * FROM screen WHERE id IN (1, 2, 3...);


// ═══════════════════════════════════════════════════════════════════════════
// VÍ DỤ 3: Lấy showtime với movie, screen và seats
// ═══════════════════════════════════════════════════════════════════════════

$showtime = Showtime::with(['movie', 'screen.seats'])->find(1);

// Truy cập:
echo $showtime->movie->title;           // Tên phim
echo $showtime->screen->name;           // Tên phòng
foreach ($showtime->screen->seats as $seat) {
    echo $seat->row_label . $seat->seat_number;  // A1, A2, B1...
}

// SQL:
// SELECT * FROM showtime WHERE id = 1;
// SELECT * FROM movie WHERE id = (showtime.movie_id);
// SELECT * FROM screen WHERE id = (showtime.screen_id);
// SELECT * FROM seat WHERE screen_id = (screen.id);


// ═══════════════════════════════════════════════════════════════════════════
// VÍ DỤ 4: Lấy order với tất cả thông tin liên quan
// ═══════════════════════════════════════════════════════════════════════════

$orders = Order::where('account_id', $userId)
    ->with([
        'order_lines.seat',       // Chi tiết đơn + ghế
        'order_lines.product',    // Chi tiết đơn + sản phẩm
        'showtime.movie',         // Suất chiếu + phim
        'showtime.screen'         // Suất chiếu + phòng
    ])
    ->orderBy('id', 'desc')
    ->get();

// Truy cập:
foreach ($orders as $order) {
    echo $order->showtime->movie->title;  // Tên phim
    echo $order->showtime->screen->name;  // Tên phòng
    
    foreach ($order->order_lines as $line) {
        if ($line->item_type === 'TICKET') {
            echo $line->seat->row_label . $line->seat->seat_number;  // Ghế
        } else {
            echo $line->product->name;  // Tên sản phẩm
        }
    }
}


// ═══════════════════════════════════════════════════════════════════════════
// VÍ DỤ 5: Sử dụng whereHas để filter theo quan hệ
// ═══════════════════════════════════════════════════════════════════════════

// Lấy các ghế đã được đặt cho 1 suất chiếu
$bookedSeats = OrderLine::whereHas('order', function($query) use ($showtimeId) {
    $query->where('showtime_id', $showtimeId)
          ->whereIn('status', ['PAID']);
})
->where('item_type', 'TICKET')
->pluck('seat_id')
->toArray();

// SQL:
// SELECT seat_id FROM order_line
// WHERE item_type = 'TICKET'
//   AND EXISTS (
//       SELECT 1 FROM orders
//       WHERE orders.id = order_line.order_id
//         AND orders.showtime_id = 1
//         AND orders.status IN ('PAID')
//   );


// ═══════════════════════════════════════════════════════════════════════════
// VÍ DỤ 6: Thống kê với quan hệ (Join)
// ═══════════════════════════════════════════════════════════════════════════

// Top phim có doanh thu cao nhất
$topMovies = DB::table('movie')
    ->leftJoin('showtime', 'movie.id', '=', 'showtime.movie_id')
    ->leftJoin('orders', function($join) {
        $join->on('showtime.id', '=', 'orders.showtime_id')
             ->where('orders.status', '=', 'PAID');
    })
    ->select(
        'movie.id',
        'movie.title',
        'movie.poster',
        DB::raw('COUNT(orders.id) as ticket_count'),
        DB::raw('COALESCE(SUM(orders.total_amount), 0) as revenue')
    )
    ->groupBy('movie.id', 'movie.title', 'movie.poster')
    ->orderBy('revenue', 'desc')
    ->limit(10)
    ->get();

// SQL:
// SELECT movie.id, movie.title, movie.poster,
//        COUNT(orders.id) as ticket_count,
//        COALESCE(SUM(orders.total_amount), 0) as revenue
// FROM movie
// LEFT JOIN showtime ON movie.id = showtime.movie_id
// LEFT JOIN orders ON showtime.id = orders.showtime_id AND orders.status = 'PAID'
// GROUP BY movie.id, movie.title, movie.poster
// ORDER BY revenue DESC
// LIMIT 10;
```

### 9.3 Tóm Tắt Các Loại Quan Hệ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    TÓM TẮT QUAN HỆ GIỮA CÁC BẢNG                            │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────────┬──────────────┬──────────────┬────────────────────────────────┐
│ Model        │ Quan hệ      │ Model liên kết│ Mô tả                         │
├──────────────┼──────────────┼──────────────┼────────────────────────────────┤
│ Account      │ hasMany      │ Order        │ 1 Account có nhiều Order      │
│ Account      │ hasMany      │ SeatLock     │ 1 Account có nhiều SeatLock   │
├──────────────┼──────────────┼──────────────┼────────────────────────────────┤
│ Movie        │ hasMany      │ Showtime     │ 1 Movie có nhiều Showtime     │
├──────────────┼──────────────┼──────────────┼────────────────────────────────┤
│ Screen       │ hasMany      │ Seat         │ 1 Screen có nhiều Seat        │
│ Screen       │ hasMany      │ Showtime     │ 1 Screen có nhiều Showtime    │
├──────────────┼──────────────┼──────────────┼────────────────────────────────┤
│ Seat         │ belongsTo    │ Screen       │ 1 Seat thuộc về 1 Screen      │
│ Seat         │ hasMany      │ OrderLine    │ 1 Seat có nhiều OrderLine     │
│ Seat         │ hasMany      │ SeatLock     │ 1 Seat có nhiều SeatLock      │
├──────────────┼──────────────┼──────────────┼────────────────────────────────┤
│ Showtime     │ belongsTo    │ Movie        │ 1 Showtime thuộc về 1 Movie   │
│ Showtime     │ belongsTo    │ Screen       │ 1 Showtime thuộc về 1 Screen  │
│ Showtime     │ hasMany      │ Order        │ 1 Showtime có nhiều Order     │
│ Showtime     │ hasMany      │ SeatLock     │ 1 Showtime có nhiều SeatLock  │
├──────────────┼──────────────┼──────────────┼────────────────────────────────┤
│ Order        │ belongsTo    │ Account      │ 1 Order thuộc về 1 Account    │
│ Order        │ belongsTo    │ Showtime     │ 1 Order thuộc về 1 Showtime   │
│ Order        │ hasMany      │ OrderLine    │ 1 Order có nhiều OrderLine    │
├──────────────┼──────────────┼──────────────┼────────────────────────────────┤
│ OrderLine    │ belongsTo    │ Order        │ 1 OrderLine thuộc về 1 Order  │
│ OrderLine    │ belongsTo    │ Seat         │ 1 OrderLine thuộc về 1 Seat   │
│ OrderLine    │ belongsTo    │ Product      │ 1 OrderLine thuộc về 1 Product│
├──────────────┼──────────────┼──────────────┼────────────────────────────────┤
│ SeatLock     │ belongsTo    │ Showtime     │ 1 SeatLock thuộc về 1 Showtime│
│ SeatLock     │ belongsTo    │ Seat         │ 1 SeatLock thuộc về 1 Seat    │
│ SeatLock     │ belongsTo    │ Account      │ 1 SeatLock thuộc về 1 Account │
├──────────────┼──────────────┼──────────────┼────────────────────────────────┤
│ Product      │ hasMany      │ OrderLine    │ 1 Product có nhiều OrderLine  │
└──────────────┴──────────────┴──────────────┴────────────────────────────────┘
```

---

## 📝 KẾT LUẬN

Tài liệu này đã giải thích chi tiết:

1. **Cấu trúc Database**: 9 bảng với các foreign keys và quan hệ rõ ràng
2. **Authentication**: Sử dụng Laravel Auth với Model Account thay vì User
3. **Middleware**: Phân quyền theo role (CUSTOMER, STAFF, ADMIN)
4. **Luồng đặt vé**: 5 bước từ xem phim → chọn suất → chọn ghế → thanh toán → xác nhận
5. **Luồng Admin**: Dashboard, quản lý user, quản lý phim, thống kê
6. **Cách gọi quan hệ**: Sử dụng Eloquent ORM với hasMany, belongsTo, with(), whereHas()

Mỗi luồng đều được giải thích từ:
- **Request từ browser** → **Middleware kiểm tra** → **Controller xử lý** → **Database query** → **View hiển thị**
