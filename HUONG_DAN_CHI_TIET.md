# 📚 HƯỚNG DẪN CHI TIẾT HỆ THỐNG ĐẶT VÉ XEM PHIM

## 📖 MỤC LỤC
1. [Tổng Quan Hệ Thống](#1-tổng-quan-hệ-thống)
2. [Cấu Trúc Database](#2-cấu-trúc-database)
3. [Luồng Đăng Nhập (Login)](#3-luồng-đăng-nhập-login)
4. [Sinh Phim & Xuất Chiếu](#4-sinh-phim--xuất-chiếu)
5. [Hiển Thị Danh Sách Phim](#5-hiển-thị-danh-sách-phim)
6. [Chọn Ghế & Đặt Vé](#6-chọn-ghế--đặt-vé)
7. [Thanh Toán](#7-thanh-toán)
8. [Xem Lại Vé Đã Đặt](#8-xem-lại-vé-đã-đặt)
9. [Flow Chart Tổng Thể](#9-flow-chart-tổng-thể)

---

## 1. TỔNG QUAN HỆ THỐNG

### Kiến Trúc
```
Frontend (Blade Templates) 
    ↕
Routes (web.php, api.php)
    ↕
Controllers (AuthController, BookingController, PaymentController)
    ↕
Models (Movie, Showtime, Seat, Order, OrderLine)
    ↕
Database (MySQL)
```

### Tech Stack
- **Backend**: Laravel 11
- **Frontend**: Blade Templates + TailwindCSS
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (API) + Web Guard (Session)

---

## 2. CẤU TRÚC DATABASE

### Bảng `movie` - Quản lý phim
```sql
CREATE TABLE `movie` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,              -- Tên phim
    `duration_min` INT NOT NULL,                -- Thời lượng (phút)
    `genre` VARCHAR(255),                       -- Thể loại
    `poster` TEXT,                              -- URL poster
    `rating_code` VARCHAR(10),                  -- Phân loại độ tuổi
    `is_active` BOOLEAN DEFAULT TRUE            -- Đang chiếu hay không
);
```

### Bảng `screen` - Quản lý phòng chiếu
```sql
CREATE TABLE `screen` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,               -- Tên phòng (Screen 1, 2...)
    `is_active` BOOLEAN DEFAULT TRUE,
    `total_seats` INT                           -- Tổng số ghế
);
```

### Bảng `showtime` - Quản lý xuất chiếu
```sql
CREATE TABLE `showtime` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `movie_id` BIGINT,                          -- ID phim
    `screen_id` BIGINT,                         -- ID phòng chiếu
    `start_at` DATETIME NOT NULL,               -- Giờ bắt đầu
    `end_at` DATETIME NOT NULL,                 -- Giờ kết thúc
    `base_price` INT NOT NULL,                  -- Giá vé cơ bản
    `status` ENUM('OPEN', 'FULL', 'CLOSED'),   -- Trạng thái
    FOREIGN KEY (`movie_id`) REFERENCES `movie`(`id`),
    FOREIGN KEY (`screen_id`) REFERENCES `screen`(`id`)
);
```

### Bảng `seat` - Quản lý ghế ngồi
```sql
CREATE TABLE `seat` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `screen_id` BIGINT,                         -- Thuộc phòng nào
    `row_label` VARCHAR(5) NOT NULL,            -- Hàng (A, B, C...)
    `seat_number` INT NOT NULL,                 -- Số ghế (1, 2, 3...)
    `seat_type` ENUM('NORMAL', 'VIP'),         -- Loại ghế
    `is_aisle` BOOLEAN DEFAULT FALSE,           -- Ghế lối đi
    `is_blocked` BOOLEAN DEFAULT FALSE,         -- Ghế bị chặn
    FOREIGN KEY (`screen_id`) REFERENCES `screen`(`id`)
);
```

### Bảng `account` - Quản lý tài khoản
```sql
CREATE TABLE `account` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `role` ENUM('ADMIN', 'CUSTOMER') DEFAULT 'CUSTOMER',
    `is_active` BOOLEAN DEFAULT TRUE
);
```

### Bảng `orders` - Quản lý đơn hàng
```sql
CREATE TABLE `orders` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `channel` VARCHAR(50),                      -- WEB, MOBILE, POS
    `account_id` BIGINT,                        -- Người đặt
    `showtime_id` BIGINT,                       -- Xuất chiếu
    `status` ENUM('PENDING', 'CONFIRMED', 'CANCELLED'),
    `payment_method` VARCHAR(50),               -- CASH, CARD, BANK_TRANSFER...
    `total_amount` INT NOT NULL,                -- Tổng tiền
    FOREIGN KEY (`account_id`) REFERENCES `account`(`id`),
    FOREIGN KEY (`showtime_id`) REFERENCES `showtime`(`id`)
);
```

### Bảng `order_line` - Chi tiết đơn hàng
```sql
CREATE TABLE `order_line` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `order_id` BIGINT,                          -- Thuộc đơn hàng nào
    `item_type` ENUM('TICKET', 'PRODUCT'),     -- Loại: Vé hoặc Sản phẩm
    `seat_id` BIGINT NULL,                      -- ID ghế (nếu là vé)
    `product_id` BIGINT NULL,                   -- ID sản phẩm (nếu là combo)
    `qty` INT DEFAULT 1,                        -- Số lượng
    `unit_price` INT,                           -- Đơn giá
    `line_total` INT,                           -- Thành tiền
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`),
    FOREIGN KEY (`seat_id`) REFERENCES `seat`(`id`),
    FOREIGN KEY (`product_id`) REFERENCES `product`(`id`)
);
```

### Mối Quan Hệ Giữa Các Bảng
```
Movie (1) ----< (N) Showtime (N) >---- (1) Screen
                      |
                      | (1)
                      |
                      ↓ (N)
                    Orders
                      |
                      | (1)
                      |
                      ↓ (N)
                  Order_Line (N) >---- (1) Seat
```

---

## 3. LUỒNG ĐĂNG NHẬP (LOGIN)

### 3.1. Route Đăng Nhập
**File: `routes/web.php`**
```php
// Authentication Routes (Guest only)
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
});

// Logout (Authenticated only)
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:web');
```

### 3.2. AuthController - Xử Lý Đăng Nhập
**File: `app/Http/Controllers/Web/AuthController.php`**

```php
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Account;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        // Bước 1: Validate dữ liệu
        $request->validate([
            'email' => 'required|email',
            'password_hash' => 'required',
        ]);

        // Bước 2: Tìm user theo email
        $account = Account::where('email', $request->email)->first();

        // Bước 3: Kiểm tra password
        if (!$account || !Hash::check($request->password_hash, $account->password_hash)) {
            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng.',
            ])->withInput();
        }

        // Bước 4: Kiểm tra tài khoản có active không
        if (!$account->is_active) {
            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa.',
            ])->withInput();
        }

        // Bước 5: Đăng nhập (tạo session)
        Auth::guard('web')->login($account);

        // Bước 6: Redirect về trang đặt vé
        return redirect()->route('booking.index')->with('success', 'Đăng nhập thành công!');
    }

    /**
     * Xử lý đăng ký
     */
    public function register(Request $request)
    {
        // Bước 1: Validate
        $request->validate([
            'email' => 'required|email|unique:account',
            'phone' => 'required|numeric|unique:account',
            'full_name' => 'required|string|max:255',
            'password_hash' => 'required|min:8|confirmed',
        ]);

        try {
            // Bước 2: Tạo tài khoản mới
            $account = Account::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'full_name' => $request->full_name,
                'password_hash' => Hash::make($request->password_hash),
                'is_active' => true,
                'role' => 'CUSTOMER', // Mặc định là khách hàng
            ]);

            // Bước 3: Tự động đăng nhập
            Auth::guard('web')->login($account);

            // Bước 4: Redirect
            return redirect()->route('booking.index')->with('success', 'Đăng ký thành công!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Đăng ký thất bại. Vui lòng thử lại.',
            ])->withInput();
        }
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('auth.login.form')->with('success', 'Đã đăng xuất!');
    }
}
```

### 3.3. View Login
**File: `resources/views/auth/login.blade.php`**
```blade
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-96">
            <h2 class="text-2xl font-bold mb-6 text-center">🎬 Đăng Nhập</h2>
            
            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-4 py-2 border rounded-lg" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 mb-2">Mật Khẩu</label>
                    <input type="password" name="password_hash" 
                           class="w-full px-4 py-2 border rounded-lg" required>
                </div>

                <button type="submit" 
                        class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700">
                    Đăng Nhập
                </button>

                <p class="mt-4 text-center">
                    Chưa có tài khoản? 
                    <a href="{{ route('auth.register.form') }}" class="text-purple-600">Đăng ký</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
```

---

## 4. SINH PHIM & XUẤT CHIẾU

### 4.1. Tạo Phim Thủ Công
**Endpoint: POST /api/movies** (Yêu cầu ADMIN role)

```php
// MovieController.php
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'duration_min' => 'required|numeric',
        'genre' => 'nullable|string',
        'poster' => 'nullable|url',
        'rating_code' => 'nullable|string',
    ]);

    $movie = Movie::create([
        'title' => $request->title,
        'duration_min' => $request->duration_min,
        'genre' => $request->genre,
        'poster' => $request->poster,
        'rating_code' => $request->rating_code,
        'is_active' => true,
    ]);

    return response()->json(['success' => true, 'data' => $movie], 201);
}
```

### 4.2. Sinh Phim + Xuất Chiếu Tự Động
**Endpoint: POST /api/movies/generateSchedule**

```php
// MovieController.php
public function generateSchedule(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'duration_min' => 'required|numeric',
        'genre' => 'nullable|string',
        'poster' => 'nullable|url',
        'rating_code' => 'nullable|string',
        'days' => 'required|numeric|min:1|max:3',           // Số ngày chiếu
        'screens_count' => 'required|numeric|min:1|max:8',  // Số phòng chiếu
        'base_price' => 'required|numeric|min:0',           // Giá vé
    ]);

    try {
        DB::beginTransaction();

        // BƯỚC 1: Tạo phim mới
        $movie = Movie::create([
            'title' => $request->title,
            'duration_min' => $request->duration_min,
            'genre' => $request->genre,
            'poster' => $request->poster,
            'rating_code' => $request->rating_code,
            'is_active' => true,
        ]);

        // BƯỚC 2: Lấy danh sách phòng chiếu đang hoạt động
        $allScreens = Screen::where('is_active', true)->get();

        if ($allScreens->count() == 0) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Không có phòng chiếu'], 400);
        }

        // BƯỚC 3: Chọn ngẫu nhiên N phòng chiếu
        $screenIds = $allScreens->pluck('id')->toArray();
        $selectedCount = min($request->screens_count, count($screenIds));
        $randomKeys = array_rand($screenIds, $selectedCount);
        
        if (!is_array($randomKeys)) {
            $randomKeys = [$randomKeys];
        }

        $selectedScreenIds = array_map(fn($key) => $screenIds[$key], $randomKeys);
        $screens = Screen::whereIn('id', $selectedScreenIds)->get();

        // BƯỚC 4: Tạo xuất chiếu cho mỗi phòng, mỗi ngày
        $startDate = Carbon::now()->addDay(); // Bắt đầu từ ngày mai
        $createdCount = 0;

        for ($day = 0; $day < $request->days; $day++) {
            foreach ($screens as $screen) {
                // Tạo giờ chiếu ngẫu nhiên trong ngày
                $startAt = Carbon::parse($startDate->copy()
                    ->addDays($day)
                    ->setHour(rand(8, 20))                      // Giờ từ 8h-20h
                    ->setMinute(fake()->randomElement([0, 15, 30, 45]))  // Phút: 0, 15, 30, 45
                    ->setSecond(0));

                // Tính giờ kết thúc = giờ bắt đầu + thời lượng phim
                $endAt = $startAt->copy()->addMinutes($movie->duration_min);

                // Tạo xuất chiếu
                Showtime::create([
                    'movie_id' => $movie->id,
                    'screen_id' => $screen->id,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'base_price' => $request->base_price,
                    'status' => 'OPEN',
                ]);

                $createdCount++;
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Tạo phim và xuất chiếu thành công',
            'data' => [
                'movie' => $movie,
                'showtimes_created' => $createdCount,
            ],
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
    }
}
```

### 4.3. Ví Dụ Cụ Thể

**Input:**
```json
{
    "title": "Avengers: Endgame",
    "duration_min": 181,
    "genre": "Action, Adventure, Sci-Fi",
    "poster": "https://example.com/poster.jpg",
    "rating_code": "13+",
    "days": 3,
    "screens_count": 2,
    "base_price": 80000
}
```

**Kết quả:**
- 1 phim: Avengers: Endgame
- 2 phòng được chọn ngẫu nhiên: Screen 1, Screen 3
- 3 ngày chiếu: 04/12/2025, 05/12/2025, 06/12/2025
- **Tổng: 6 xuất chiếu** (2 phòng × 3 ngày)

**Các xuất chiếu được tạo:**
```
1. Screen 1 - 04/12/2025 10:00 - 13:01
2. Screen 3 - 04/12/2025 14:30 - 17:31
3. Screen 1 - 05/12/2025 16:15 - 19:16
4. Screen 3 - 05/12/2025 09:45 - 12:46
5. Screen 1 - 06/12/2025 19:00 - 22:01
6. Screen 3 - 06/12/2025 11:30 - 14:31
```

---

## 5. HIỂN THỊ DANH SÁCH PHIM

### 5.1. Route
```php
Route::middleware('auth:web')->group(function () {
    Route::get('/movies', [BookingController::class, 'index'])->name('booking.index');
});
```

### 5.2. BookingController - Lấy Danh Sách Phim
**File: `app/Http/Controllers/Web/BookingController.php`**

```php
public function index()
{
    // Lấy tất cả phim đang active
    $movies = Movie::where('is_active', true)->get();
    
    return view('booking.index', compact('movies'));
}
```

### 5.3. View - Danh Sách Phim
**File: `resources/views/booking/index.blade.php`**

```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach($movies as $movie)
        <div class="bg-white rounded-xl border overflow-hidden hover:shadow-xl transition-all">
            <a href="{{ route('booking.movie.detail', $movie->id) }}">
                <!-- Poster -->
                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" 
                     class="w-full h-96 object-cover">
                
                <!-- Thông tin phim -->
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2">{{ $movie->title }}</h3>
                    <p class="text-gray-600 text-sm mb-2">
                        <span class="mr-2">⏱️ {{ $movie->duration_min }} phút</span>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">
                            {{ $movie->rating_code }}
                        </span>
                    </p>
                    <p class="text-gray-500 text-sm">{{ $movie->genre }}</p>
                </div>
            </a>
        </div>
    @endforeach
</div>
```

---

## 6. CHỌN GHẾ & ĐẶT VÉ

### 6.1. Xem Chi Tiết Phim & Chọn Xuất Chiếu
**Route:**
```php
Route::get('/movies/{id}', [BookingController::class, 'showMovieDetail'])->name('booking.movie.detail');
```

**Controller:**
```php
public function showMovieDetail($id)
{
    $movie = Movie::findOrFail($id);
    
    // Lấy các xuất chiếu của phim này (chỉ lấy xuất chiếu trong tương lai)
    $showtimes = Showtime::where('movie_id', $id)
        ->where('start_at', '>', now())
        ->where('status', 'OPEN')
        ->with('screen')
        ->orderBy('start_at')
        ->get();
    
    return view('booking.movie-detail', compact('movie', 'showtimes'));
}
```

### 6.2. Chọn Ghế
**Route:**
```php
Route::get('/showtimes/{id}/seats', [BookingController::class, 'seatMap'])->name('booking.seat-map');
```

**Controller:**
```php
public function seatMap($id)
{
    $showtime = Showtime::with(['movie', 'screen'])->findOrFail($id);
    
    // BƯỚC 1: Lấy tất cả ghế của phòng chiếu
    $seats = Seat::where('screen_id', $showtime->screen_id)
        ->orderBy('row_label')
        ->orderBy('seat_number')
        ->get();
    
    // BƯỚC 2: Nhóm ghế theo hàng (row)
    $seatsByRow = $seats->groupBy('row_label');
    
    // BƯỚC 3: Lấy danh sách ghế đã được đặt cho xuất chiếu này
    $bookedSeats = OrderLine::whereHas('order', function($query) use ($id) {
            $query->where('showtime_id', $id)
                  ->where('status', 'CONFIRMED');
        })
        ->where('item_type', 'TICKET')
        ->pluck('seat_id')
        ->toArray();
    
    // BƯỚC 4: Lấy danh sách ghế đang bị lock tạm thời (nếu có)
    $lockedSeats = SeatLock::where('showtime_id', $id)
        ->where('expires_at', '>', now())
        ->pluck('seat_id')
        ->toArray();
    
    return view('booking.seat-map', compact('showtime', 'seatsByRow', 'bookedSeats', 'lockedSeats'));
}
```

### 6.3. View - Sơ Đồ Ghế
**File: `resources/views/booking/seat-map.blade.php`**

```blade
<!-- Màn Hình -->
<div class="text-center mb-8">
    <div class="bg-gradient-to-b from-purple-600 to-transparent h-3 rounded-t-3xl"></div>
    <p class="text-gray-400 text-sm mt-2">MÀN HÌNH</p>
</div>

<!-- Sơ Đồ Ghế -->
<div class="flex justify-center">
    @foreach($seatsByRow as $row => $seats)
        <div class="flex items-center gap-2 mb-3">
            <!-- Nhãn hàng -->
            <div class="w-8 text-center font-bold">{{ $row }}</div>
            
            <!-- Ghế trong hàng -->
            @foreach($seats as $seat)
                @php
                    $isBooked = in_array($seat->id, $bookedSeats);
                    $isLocked = in_array($seat->id, $lockedSeats);
                    $isAvailable = !$isBooked && !$isLocked && !$seat->is_blocked;
                    
                    $seatClass = 'w-10 h-10 rounded-t-lg flex items-center justify-center text-xs font-bold ';
                    
                    if ($isBooked) {
                        $seatClass .= 'bg-red-100 text-red-400 cursor-not-allowed';
                    } elseif ($isLocked) {
                        $seatClass .= 'bg-yellow-100 text-yellow-600 cursor-not-allowed';
                    } elseif ($seat->is_blocked) {
                        $seatClass .= 'bg-gray-300 cursor-not-allowed';
                    } else {
                        $seatClass .= 'bg-gray-200 hover:bg-purple-600 hover:text-white cursor-pointer seat-available';
                    }
                @endphp
                
                <div class="{{ $seatClass }}" 
                     data-seat-id="{{ $seat->id }}"
                     data-row="{{ $row }}"
                     data-seat-number="{{ $seat->seat_number }}"
                     @if($isAvailable) onclick="toggleSeat(this)" @endif>
                    {{ $seat->seat_number }}
                </div>
            @endforeach
        </div>
    @endforeach
</div>

<!-- JavaScript Chọn Ghế -->
<script>
let selectedSeats = [];
const pricePerSeat = {{ $showtime->base_price }};

function toggleSeat(element) {
    const seatId = element.getAttribute('data-seat-id');
    const seatNumber = element.getAttribute('data-seat-number');
    const row = element.getAttribute('data-row');
    const seatLabel = row + seatNumber;

    if (element.classList.contains('bg-purple-600')) {
        // Bỏ chọn
        element.classList.remove('bg-purple-600', 'text-white');
        element.classList.add('bg-gray-200');
        selectedSeats = selectedSeats.filter(s => s.id !== seatId);
    } else {
        // Chọn
        element.classList.remove('bg-gray-200');
        element.classList.add('bg-purple-600', 'text-white');
        selectedSeats.push({ id: seatId, label: seatLabel });
    }

    updateSummary();
}

function updateSummary() {
    const total = selectedSeats.length * pricePerSeat;
    document.getElementById('total-price').textContent = total.toLocaleString('vi-VN');
    document.getElementById('selected-seats').textContent = 
        selectedSeats.length > 0 
            ? selectedSeats.map(s => s.label).join(', ') 
            : 'Chưa chọn ghế nào';
    
    document.getElementById('continue-btn').disabled = selectedSeats.length === 0;
}
</script>
```

### 6.4. Xử Lý Đặt Vé & Thanh Toán
**Route:**
```php
Route::get('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::post('/payment/confirm', [PaymentController::class, 'confirmPayment'])->name('payment.confirm');
```

**Controller:**
```php
// PaymentController.php

public function checkout(Request $request)
{
    $showtimeId = $request->input('showtime_id');
    $seatIds = $request->input('seat_ids', []);
    $totalAmount = $request->input('price');
    
    // Lấy thông tin
    $showtime = Showtime::with(['movie', 'screen'])->findOrFail($showtimeId);
    $seats = Seat::whereIn('id', $seatIds)->get();
    
    // Lưu vào session
    session([
        'booking_showtime_id' => $showtimeId,
        'booking_seat_ids' => $seatIds,
        'booking_total_amount' => $totalAmount
    ]);
    
    return view('payment.checkout', compact('showtime', 'seats', 'totalAmount'));
}

public function confirmPayment(Request $request)
{
    try {
        // Lấy thông tin từ session
        $showtimeId = session('booking_showtime_id');
        $seatIds = session('booking_seat_ids', []);
        $totalAmount = session('booking_total_amount');
        $account = auth()->guard('web')->user();
        
        DB::beginTransaction();
        
        // BƯỚC 1: Tạo Order
        $order = Order::create([
            'channel' => 'WEB',
            'account_id' => $account->id,
            'showtime_id' => $showtimeId,
            'status' => 'CONFIRMED',              // Giả định thanh toán thành công ngay
            'payment_method' => 'BANK_TRANSFER',
            'total_amount' => $totalAmount,
        ]);
        
        // BƯỚC 2: Tạo Order Lines cho từng ghế
        foreach ($seatIds as $seatId) {
            OrderLine::create([
                'order_id' => $order->id,
                'item_type' => 'TICKET',
                'seat_id' => $seatId,
                'qty' => 1,
                'unit_price' => $totalAmount / count($seatIds),
                'line_total' => $totalAmount / count($seatIds),
            ]);
        }
        
        DB::commit();
        
        // BƯỚC 3: Xóa session
        session()->forget(['booking_showtime_id', 'booking_seat_ids', 'booking_total_amount']);
        
        // BƯỚC 4: Redirect về trang chính
        return redirect()->route('booking.index')
            ->with('success', '🎉 Đặt vé thành công! Mã: ORDER' . str_pad($order->id, 6, '0', STR_PAD_LEFT));
            
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('booking.index')
            ->with('error', 'Đặt vé thất bại: ' . $e->getMessage());
    }
}
```

---

## 7. THANH TOÁN

### 7.1. View Thanh Toán với QR Code
**File: `resources/views/payment/checkout.blade.php`**

```blade
<div class="bg-white rounded-xl p-8 text-center">
    <h2 class="text-3xl font-bold mb-6">Thanh Toán</h2>
    
    @php
        $bankId = env('VIETQR_BANK_ID', '970422');
        $accountNo = env('VIETQR_ACCOUNT_NO', '0378366953');
        $accountName = env('VIETQR_ACCOUNT_NAME', 'NGUYEN LUU BAO KHANG');
        $orderId = 'ORDER' . time();
        
        // Tạo QR SePay
        $qrUrl = "https://qr.sepay.vn/img?acc={$accountNo}&bank={$bankId}&amount={$totalAmount}&des=" . urlencode($orderId);
    @endphp

    <!-- QR Code -->
    <div class="bg-white p-4 rounded-xl inline-block mb-6">
        <img src="{{ $qrUrl }}" alt="QR Code" class="w-64 h-64">
    </div>

    <!-- Thông tin -->
    <p class="text-5xl font-bold mb-2">{{ number_format($totalAmount, 0, ',', '.') }} đ</p>
    <p class="text-gray-600">{{ $showtime->movie->title }}</p>
    <p class="text-sm text-gray-500">Nội dung CK: {{ $orderId }}</p>

    <!-- Nút xác nhận -->
    <form method="POST" action="{{ route('payment.confirm') }}">
        @csrf
        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-lg font-bold mt-6">
            ✓ Đã Thanh Toán
        </button>
    </form>
</div>
```

---

## 8. XEM LẠI VÉ ĐÃ ĐẶT

### 8.1. Route
```php
Route::get('/my-bookings', [DashboardController::class, 'myBookings'])->name('my.bookings');
```

### 8.2. Controller
```php
// DashboardController.php

public function myBookings()
{
    $user = Auth::guard('web')->user();
    
    // Lấy tất cả đơn hàng của user
    $orders = Order::where('account_id', $user->id)
        ->with(['order_lines.seat', 'order_lines.product', 'showtime.movie', 'showtime.screen'])
        ->orderBy('id', 'desc')
        ->paginate(10);

    return view('booking.my-bookings', compact('user', 'orders'));
}
```

### 8.3. View - Danh Sách Vé
**File: `resources/views/booking/my-bookings.blade.php`**

```blade
@foreach($orders as $order)
    @php
        $showtime = $order->showtime;
        $movie = $showtime->movie;
        $seats = $order->order_lines->where('item_type', 'TICKET');
        $showtimeDate = \Carbon\Carbon::parse($showtime->start_at);
        $isPast = $showtimeDate->isPast();
        $isUpcoming = $showtimeDate->isFuture();
    @endphp

    <div class="bg-white rounded-xl border p-6">
        <div class="grid grid-cols-4 gap-6">
            <!-- Poster -->
            <div>
                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="rounded-lg">
            </div>

            <!-- Thông tin -->
            <div class="col-span-2">
                <h3 class="text-2xl font-bold mb-3">{{ $movie->title }}</h3>
                
                <!-- Trạng thái -->
                @if($isPast)
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">✓ Đã Xem</span>
                @elseif($isUpcoming)
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">⏳ Chưa Tới Ngày Chiếu</span>
                @endif

                <div class="mt-4 space-y-2 text-sm">
                    <p><strong>Mã Đơn:</strong> ORDER{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                    <p><strong>Phòng:</strong> {{ $showtime->screen->name }}</p>
                    <p><strong>Suất Chiếu:</strong> {{ $showtimeDate->format('d/m/Y - H:i') }}</p>
                    <p><strong>Ghế:</strong> 
                        @foreach($seats as $seatLine)
                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs">
                                {{ $seatLine->seat->row_label }}{{ $seatLine->seat->seat_number }}
                            </span>
                        @endforeach
                    </p>
                    <p><strong>Đặt Lúc:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Giá -->
            <div class="text-right">
                <p class="text-gray-500 text-sm">Tổng Tiền</p>
                <p class="text-3xl font-bold text-purple-600">
                    {{ number_format($order->total_amount, 0, ',', '.') }}đ
                </p>
                <p class="text-gray-500 text-xs mt-1">{{ $seats->count() }} ghế</p>
            </div>
        </div>
    </div>
@endforeach
```

---

## 9. FLOW CHART TỔNG THỂ

```
┌─────────────┐
│   START     │
└──────┬──────┘
       │
       ▼
┌─────────────────────┐
│  User truy cập web  │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐      Chưa đăng nhập
│  Check Login?       ├──────────────────────┐
└──────┬──────────────┘                      │
       │ Đã đăng nhập                        │
       │                                     ▼
       │                          ┌──────────────────┐
       │                          │  Login Form      │
       │                          └────┬─────────────┘
       │                               │
       │                               ▼
       │                          ┌──────────────────┐
       │                          │ AuthController   │
       │                          │   ->login()      │
       │                          └────┬─────────────┘
       │                               │ Success
       │◄──────────────────────────────┘
       │
       ▼
┌─────────────────────┐
│  Trang Đặt Vé       │ ← GET /movies
│  (Danh sách phim)   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Chọn 1 phim        │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Chi tiết phim      │ ← GET /movies/{id}
│  + Danh sách xuất   │
│    chiếu            │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Chọn 1 xuất chiếu  │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Sơ đồ ghế          │ ← GET /showtimes/{id}/seats
│  - Lấy tất cả ghế   │
│  - Check ghế booked │
│  - Check ghế locked │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  User chọn ghế      │
│  (JavaScript)       │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Click "Tiếp tục"   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Trang Thanh Toán   │ ← GET /checkout
│  - Hiển thị QR      │
│  - Thông tin đơn    │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Click "Đã Thanh     │
│       Toán"         │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ PaymentController   │ ← POST /payment/confirm
│   ->confirmPayment()│
│                     │
│ BƯỚC 1: Tạo Order   │
│ BƯỚC 2: Tạo         │
│         OrderLines  │
│ BƯỚC 3: Commit DB   │
└──────┬──────────────┘
       │ Success
       ▼
┌─────────────────────┐
│ Redirect về trang   │
│ chính với thông     │
│ báo thành công      │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ User có thể:        │
│ - Đặt vé tiếp       │
│ - Xem "Vé Của Tôi"  │ ← GET /my-bookings
└─────────────────────┘
```

---

## 10. TỔNG KẾT

### Các Bước Chính:
1. ✅ **Đăng nhập** → AuthController xác thực
2. ✅ **Xem phim** → Lấy danh sách Movie
3. ✅ **Chọn xuất chiếu** → Lấy Showtimes theo movie_id
4. ✅ **Chọn ghế** → Lấy Seats theo screen_id, check booked
5. ✅ **Thanh toán** → Tạo Order + OrderLine
6. ✅ **Xem vé** → Lấy Orders theo account_id

### Database Flow:
```
Movie → Showtime → Screen → Seat
                ↓
              Order → OrderLine → Seat
```

### File Quan Trọng:
- **Routes**: `routes/web.php`, `routes/api.php`
- **Controllers**: `AuthController`, `BookingController`, `PaymentController`, `DashboardController`
- **Models**: `Movie`, `Showtime`, `Screen`, `Seat`, `Order`, `OrderLine`, `Account`
- **Views**: `auth/login.blade.php`, `booking/index.blade.php`, `booking/seat-map.blade.php`, `payment/checkout.blade.php`, `booking/my-bookings.blade.php`

---

**🎉 HẾT - Chúc bạn code thành công!**
