# 🎬 Cơ chế lấy danh sách phim và trả về JSON

## Tổng quan

Tài liệu này giải thích chi tiết cách ứng dụng Mobile gọi API để lấy danh sách phim từ Laravel backend.

---

## 📊 Sơ đồ luồng hoạt động

```
┌─────────────┐     GET /api/movies      ┌──────────────┐
│  Mobile App │ ──────────────────────▶  │   Laravel    │
└─────────────┘                          │   Router     │
                                         │ (api.php:15) │
                                         └──────┬───────┘
                                                │
                                                ▼
                                         ┌──────────────────┐
                                         │ MovieController  │
                                         │    index()       │
                                         └──────┬───────────┘
                                                │
                                                ▼
                                         ┌──────────────────┐
                                         │   Movie Model    │
                                         │   (Eloquent)     │
                                         └──────┬───────────┘
                                                │
                                                ▼
                                         ┌──────────────────┐
                                         │    Database      │
                                         │   (movie table)  │
                                         └──────┬───────────┘
                                                │
                                                ▼
┌─────────────┐      JSON Response       ┌──────────────────┐
│  Mobile App │ ◀────────────────────────│  response()      │
└─────────────┘                          │    ->json()      │
                                         └──────────────────┘
```

---

## Bước 1: Request từ Mobile App

Khi ứng dụng mobile gọi đến:

```http
GET /api/movies
```

Có thể thêm query parameters để filter:

```http
GET /api/movies?title=Avengers&genre=Action
```

---

## Bước 2: Route nhận request

**File:** `routes/api.php` - dòng 15

```php
Route::get('/movies', [MovieController::class, 'index']);
```

### Giải thích:

| Phần | Ý nghĩa |
|------|---------|
| `Route::get('/movies', ...)` | Định nghĩa endpoint **GET /api/movies** (Laravel tự thêm prefix `/api`) |
| `[MovieController::class, 'index']` | Khi có request, gọi method `index()` trong `MovieController` |

> **Lưu ý:** Route này **không nằm trong middleware `auth:sanctum`**, nên **KHÔNG cần đăng nhập** để xem danh sách phim.

---

## Bước 3: Controller xử lý

**File:** `app/Http/Controllers/Api/MovieController.php` - dòng 20-46

```php
public function index(Request $request)
{
    // Bước 3.1: Tạo Query Builder
    $movie = Movie::query();

    // Bước 3.2: Mặc định chỉ lấy phim active
    if (!$request->has('show_all')) {
        $movie->where('is_active', true);
    }

    // Bước 3.3: Filter theo title (nếu có)
    if ($request->has('title')) {
        $movie->where('title', 'LIKE', '%' . $request->title . '%');
    }

    // Bước 3.4: Filter theo genre (nếu có)
    if ($request->has('genre')) {
        $movie->where('genre', 'LIKE', '%' . $request->genre . '%');
    }

    // Bước 3.5: Filter theo rating_code (nếu có)
    if ($request->has('rating_code')) {
        $movie->where('rating_code', 'LIKE', '%' . $request->rating_code . '%');
    }

    // Bước 3.6: Thực thi query và trả về JSON
    return response()->json([
        'success' => true,
        'message' => 'Movies retrieved successfully',
        'data' => $movie->get()  // ← Thực thi SQL query
    ], 200);
}
```

### Chi tiết từng bước:

| Bước | Code | Mô tả |
|------|------|-------|
| 3.1 | `Movie::query()` | Khởi tạo query builder từ Model |
| 3.2 | `where('is_active', true)` | Mặc định chỉ lấy phim đang hoạt động |
| 3.3 | `where('title', 'LIKE', ...)` | Tìm kiếm theo tên phim (nếu có param) |
| 3.4 | `where('genre', 'LIKE', ...)` | Lọc theo thể loại (nếu có param) |
| 3.5 | `where('rating_code', ...)` | Lọc theo mã rating (nếu có param) |
| 3.6 | `$movie->get()` | Thực thi SQL và lấy kết quả |

---

## Bước 4: Model ánh xạ Database

**File:** `app/Models/Movie.php`

```php
class Movie extends Model
{
    protected $table = 'movie';          // Tên bảng trong DB
    public $timestamps = false;          // Không dùng created_at, updated_at
    
    protected $fillable = [              // Các cột cho phép mass assignment
        'title', 
        'duration_min', 
        'genre', 
        'poster', 
        'rating_code', 
        'is_active'
    ];
}
```

### SQL được sinh ra:

Khi gọi `$movie->get()`, Laravel Eloquent tự động build câu SQL:

```sql
SELECT * FROM movie 
WHERE is_active = 1 
AND title LIKE '%Avengers%' 
AND genre LIKE '%Action%'
```

---

## Bước 5: Response JSON trả về Mobile

```json
{
    "success": true,
    "message": "Movies retrieved successfully",
    "data": [
        {
            "id": 1,
            "title": "Avengers: Endgame",
            "duration_min": 181,
            "genre": "Action, Sci-Fi",
            "poster": "https://example.com/poster.jpg",
            "rating_code": "PG-13",
            "is_active": true
        },
        {
            "id": 2,
            "title": "Spider-Man: No Way Home",
            "duration_min": 148,
            "genre": "Action, Adventure",
            "poster": "https://example.com/spiderman.jpg",
            "rating_code": "PG-13",
            "is_active": true
        }
    ]
}
```

---

## 📌 Tổng kết

| Thành phần | File | Vai trò |
|------------|------|---------|
| **Route** | `routes/api.php:15` | Định nghĩa endpoint `/api/movies` |
| **Controller** | `MovieController.php:20-46` | Xử lý logic, build query, filter |
| **Model** | `Movie.php` | Ánh xạ với bảng `movie` trong DB |
| **Response** | `response()->json()` | Format dữ liệu thành JSON |

---

## 🔗 Các endpoint liên quan

| Method | Endpoint | Mô tả | Auth |
|--------|----------|-------|------|
| GET | `/api/movies` | Lấy danh sách phim | ❌ Không cần |
| GET | `/api/movies/{id}` | Lấy chi tiết 1 phim | ❌ Không cần |
| POST | `/api/movies` | Tạo phim mới | ✅ Admin only |
| PUT | `/api/movies/{id}` | Cập nhật phim | ✅ Admin only |
| DELETE | `/api/movies/{id}` | Xóa phim | ✅ Admin only |
