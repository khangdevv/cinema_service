# Eloquent Query Examples - Cinema Service

## Lấy suất chiếu của một phim

### Cách 1: Sử dụng Relationship (Recommended)

```php
// Route
Route::get('/movies/{id}/showtimes', [MovieController::class, 'getShowtimes']);

// Controller
public function getShowtimes($id)
{
    $movie = Movie::findOrFail($id);
    $showtimes = $movie->showtimes;
    
    return response()->json([
        'success' => true,
        'data' => [
            'movie' => $movie,
            'showtimes' => $showtimes
        ]
    ]);
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "movie": {
            "id": 1,
            "title": "Avengers: Endgame",
            "duration_min": 181,
            "genre": "Action",
            "rating_code": "PG-13"
        },
        "showtimes": [
            {
                "id": 1,
                "movie_id": 1,
                "screen_id": 1,
                "start_at": "2025-12-16 14:00:00",
                "end_at": "2025-12-16 17:01:00",
                "base_price": 80000,
                "status": "OPEN"
            },
            {
                "id": 2,
                "movie_id": 1,
                "screen_id": 2,
                "start_at": "2025-12-16 18:00:00",
                "end_at": "2025-12-16 21:01:00",
                "base_price": 100000,
                "status": "OPEN"
            }
        ]
    }
}
```

---

### Cách 2: Eager Loading (Tối ưu hơn)

```php
// Route
Route::get('/movies/{id}/showtimes', [MovieController::class, 'getShowtimes']);

// Controller
public function getShowtimes($id)
{
    $movie = Movie::with(['showtimes.screen'])->findOrFail($id);
    
    return response()->json([
        'success' => true,
        'data' => $movie
    ]);
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Avengers: Endgame",
        "duration_min": 181,
        "genre": "Action",
        "rating_code": "PG-13",
        "showtimes": [
            {
                "id": 1,
                "movie_id": 1,
                "screen_id": 1,
                "start_at": "2025-12-16 14:00:00",
                "end_at": "2025-12-16 17:01:00",
                "base_price": 80000,
                "status": "OPEN",
                "screen": {
                    "id": 1,
                    "code": "SCR001",
                    "name": "Phòng 1",
                    "format": "2D"
                }
            }
        ]
    }
}
```

---

### Cách 3: Query trực tiếp từ Showtime

```php
// Route
Route::get('/movies/{id}/showtimes', [MovieController::class, 'getShowtimes']);

// Controller
public function getShowtimes($id)
{
    $showtimes = Showtime::where('movie_id', $id)
        ->with('screen')
        ->get();
    
    return response()->json([
        'success' => true,
        'data' => $showtimes
    ]);
}
```

---

### Cách 4: Lọc theo điều kiện

```php
// Route
Route::get('/movies/{id}/showtimes', [MovieController::class, 'getShowtimes']);

// Controller
public function getShowtimes(Request $request, $id)
{
    $movie = Movie::findOrFail($id);
    
    $showtimes = $movie->showtimes()
        ->where('status', 'OPEN')
        ->where('start_at', '>', now())
        ->orderBy('start_at', 'asc')
        ->get();
    
    return response()->json([
        'success' => true,
        'data' => [
            'movie' => $movie,
            'showtimes' => $showtimes
        ]
    ]);
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "movie": {
            "id": 1,
            "title": "Avengers: Endgame"
        },
        "showtimes": [
            {
                "id": 3,
                "start_at": "2025-12-16 20:00:00",
                "status": "OPEN"
            }
        ]
    }
}
```

---

## So sánh các cách

| Cách | Số Query | Ưu điểm | Nhược điểm |
|------|----------|---------|------------|
| **Cách 1** | 2 queries | Đơn giản, dễ hiểu | N+1 nếu load thêm screen |
| **Cách 2** | 3 queries | Tối ưu, load sẵn screen | Phức tạp hơn |
| **Cách 3** | 2 queries | Linh hoạt | Không có thông tin movie |
| **Cách 4** | 2 queries | Có filter | Cần validate điều kiện |

---

## Các ví dụ khác

### 1. Lấy tất cả phim với số lượng suất chiếu

```php
Route::get('/movies', [MovieController::class, 'index']);

public function index()
{
    $movies = Movie::withCount('showtimes')
        ->where('is_active', true)
        ->get();
    
    return response()->json([
        'success' => true,
        'data' => $movies
    ]);
}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Avengers: Endgame",
            "showtimes_count": 5
        },
        {
            "id": 2,
            "title": "Spider-Man",
            "showtimes_count": 3
        }
    ]
}
```

---

### 2. Lấy suất chiếu theo ngày

```php
Route::get('/movies/{id}/showtimes/date/{date}', [MovieController::class, 'getShowtimesByDate']);

public function getShowtimesByDate($id, $date)
{
    $movie = Movie::findOrFail($id);
    
    $showtimes = $movie->showtimes()
        ->whereDate('start_at', $date)
        ->with('screen')
        ->orderBy('start_at')
        ->get();
    
    return response()->json([
        'success' => true,
        'data' => [
            'movie' => $movie,
            'date' => $date,
            'showtimes' => $showtimes
        ]
    ]);
}
```

**Request:**
```
GET /api/movies/1/showtimes/date/2025-12-16
```

**Response:**
```json
{
    "success": true,
    "data": {
        "movie": {
            "id": 1,
            "title": "Avengers: Endgame"
        },
        "date": "2025-12-16",
        "showtimes": [
            {
                "id": 1,
                "start_at": "2025-12-16 14:00:00",
                "screen": {
                    "name": "Phòng 1"
                }
            },
            {
                "id": 2,
                "start_at": "2025-12-16 18:00:00",
                "screen": {
                    "name": "Phòng 2"
                }
            }
        ]
    }
}
```

---

### 3. Lấy suất chiếu với thông tin ghế trống

```php
Route::get('/movies/{id}/showtimes/available', [MovieController::class, 'getAvailableShowtimes']);

public function getAvailableShowtimes($id)
{
    $movie = Movie::with(['showtimes' => function($query) {
        $query->where('status', 'OPEN')
              ->where('start_at', '>', now())
              ->with(['screen', 'orders']);
    }])->findOrFail($id);
    
    // Tính số ghế còn trống cho mỗi suất
    $movie->showtimes->each(function($showtime) {
        $totalSeats = $showtime->screen->row_count * $showtime->screen->col_count;
        $bookedSeats = $showtime->orders()
            ->where('status', 'PAID')
            ->withCount('order_lines')
            ->get()
            ->sum('order_lines_count');
        
        $showtime->available_seats = $totalSeats - $bookedSeats;
    });
    
    return response()->json([
        'success' => true,
        'data' => $movie
    ]);
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Avengers: Endgame",
        "showtimes": [
            {
                "id": 1,
                "start_at": "2025-12-16 14:00:00",
                "screen": {
                    "name": "Phòng 1",
                    "row_count": 10,
                    "col_count": 12
                },
                "available_seats": 95
            }
        ]
    }
}
```

---

### 4. Lấy suất chiếu theo phòng chiếu

```php
Route::get('/screens/{screenId}/showtimes', [ShowtimeController::class, 'getByScreen']);

public function getByScreen($screenId)
{
    $screen = Screen::with(['showtimes.movie'])->findOrFail($screenId);
    
    return response()->json([
        'success' => true,
        'data' => $screen
    ]);
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Phòng 1",
        "showtimes": [
            {
                "id": 1,
                "start_at": "2025-12-16 14:00:00",
                "movie": {
                    "id": 1,
                    "title": "Avengers: Endgame"
                }
            },
            {
                "id": 5,
                "start_at": "2025-12-16 20:00:00",
                "movie": {
                    "id": 2,
                    "title": "Spider-Man"
                }
            }
        ]
    }
}
```

---

## Best Practices

### ✅ Nên làm

```php
// 1. Sử dụng Eager Loading
$movies = Movie::with('showtimes')->get();

// 2. Sử dụng findOrFail để tự động trả về 404
$movie = Movie::findOrFail($id);

// 3. Sử dụng when() cho điều kiện động
$showtimes = Showtime::query()
    ->when($request->date, fn($q, $date) => $q->whereDate('start_at', $date))
    ->get();
```

### ❌ Không nên làm

```php
// 1. N+1 Query Problem
$movies = Movie::all();
foreach ($movies as $movie) {
    echo $movie->showtimes; // Query mỗi lần loop!
}

// 2. Không kiểm tra tồn tại
$movie = Movie::find($id);
$showtimes = $movie->showtimes; // Lỗi nếu $movie = null

// 3. Select * không cần thiết
$movies = Movie::all(); // Lấy tất cả columns
```

---

## Testing với Postman/cURL

```bash
# Lấy suất chiếu của phim ID = 1
curl http://localhost:8000/api/movies/1/showtimes

# Lấy suất chiếu theo ngày
curl http://localhost:8000/api/movies/1/showtimes/date/2025-12-16

# Lấy suất chiếu còn ghế trống
curl http://localhost:8000/api/movies/1/showtimes/available
```
