# 📚 HƯỚNG DẪN API ĐẶT VÉ XEM PHIM

## 🎯 MỤC TIÊU

1. **Tạo phim mới** → Tự động tạo suất chiếu random
2. **Xem sơ đồ ghế** → Biết ghế nào đã đặt, chưa đặt

---

## 📝 PHẦN 1: TẠO PHIM + AUTO GENERATE SHOWTIMES

### **API Endpoint:**
```
POST /api/movies/create-with-schedule
```

### **Request JSON:**
```json
{
    "title": "Avengers: Endgame",
    "duration_min": 181,
    "genre": "Action",
    "poster": "https://example.com/poster.jpg",
    "rating_code": "T13",
    "auto_schedule": {
        "days": 7,
        "screens_count": 3,
        "base_price": 70000
    }
}
```

### **Response:**
```json
{
    "success": true,
    "message": "Movie created with showtimes successfully",
    "data": {
        "movie": {
            "id": 101,
            "title": "Avengers: Endgame",
            "duration_min": 181
        },
        "showtimes_created": 84,
        "screens_used": ["Screen 1", "Screen 2", "Screen 3"]
    }
}
```

### **Code Implementation:**

Thêm vào `MovieController.php`:

```php
/**
 * Create movie with auto-generated showtimes
 */
public function createWithSchedule(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'duration_min' => 'required|numeric',
        'genre' => 'nullable|string',
        'poster' => 'nullable|url',
        'rating_code' => 'nullable|string',
        'auto_schedule.days' => 'required|numeric|min:1|max:30',
        'auto_schedule.screens_count' => 'required|numeric|min:1|max:10',
        'auto_schedule.base_price' => 'required|numeric|min:0',
    ]);

    try {
        // BƯỚC 1: Tạo phim
        $movie = Movie::create([
            'title' => $request->title,
            'duration_min' => $request->duration_min,
            'genre' => $request->genre,
            'poster' => $request->poster,
            'rating_code' => $request->rating_code,
            'is_active' => true,
        ]);

        // BƯỚC 2: Lấy ngẫu nhiên các phòng chiếu
        $screens = \App\Models\Screen::where('is_active', true)
            ->inRandomOrder()
            ->limit($request->auto_schedule['screens_count'])
            ->get();

        if ($screens->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active screens available',
            ], 400);
        }

        // BƯỚC 3: Tạo suất chiếu tự động
        $startDate = \Carbon\Carbon::now()->addDay(); // Bắt đầu từ ngày mai
        $days = $request->auto_schedule['days'];
        $basePrice = $request->auto_schedule['base_price'];
        
        $timeSlots = [
            ['hour' => 9, 'minute' => 0],
            ['hour' => 13, 'minute' => 30],
            ['hour' => 17, 'minute' => 0],
            ['hour' => 20, 'minute' => 30],
        ];

        $createdShowtimes = [];

        for ($day = 0; $day < $days; $day++) {
            $currentDate = $startDate->copy()->addDays($day);
            
            foreach ($screens as $screen) {
                foreach ($timeSlots as $slot) {
                    $startAt = $currentDate->copy()
                        ->setHour($slot['hour'])
                        ->setMinute($slot['minute'])
                        ->setSecond(0);
                    
                    $endAt = $startAt->copy()->addMinutes($movie->duration_min);

                    // Kiểm tra conflict
                    $conflict = \App\Models\Showtime::where('screen_id', $screen->id)
                        ->where(function ($query) use ($startAt, $endAt) {
                            $query->whereBetween('start_at', [$startAt, $endAt])
                                ->orWhereBetween('end_at', [$startAt, $endAt])
                                ->orWhere(function ($q) use ($startAt, $endAt) {
                                    $q->where('start_at', '<=', $startAt)
                                      ->where('end_at', '>=', $endAt);
                                });
                        })
                        ->exists();

                    if (!$conflict) {
                        $showtime = \App\Models\Showtime::create([
                            'movie_id' => $movie->id,
                            'screen_id' => $screen->id,
                            'start_at' => $startAt,
                            'end_at' => $endAt,
                            'base_price' => $basePrice,
                            'status' => 'OPEN',
                        ]);
                        
                        $createdShowtimes[] = $showtime;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Movie created with showtimes successfully',
            'data' => [
                'movie' => $movie,
                'showtimes_created' => count($createdShowtimes),
                'screens_used' => $screens->pluck('name'),
            ],
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create movie with schedule',
            'error' => $e->getMessage(),
        ], 400);
    }
}
```

---

## 🪑 PHẦN 2: API XEM SƠ ĐỒ GHẾ

### **API Endpoint:**
```
GET /api/showtimes/{showtime_id}/seat-map
```

### **Response:**
```json
{
    "success": true,
    "data": {
        "showtime": {
            "id": 1,
            "movie": "Avengers: Endgame",
            "screen": "Screen 1",
            "start_at": "2025-12-01 14:00:00",
            "base_price": 70000
        },
        "screen_layout": {
            "rows": 10,
            "cols": 12,
            "total_seats": 120
        },
        "seats": [
            {
                "id": 1,
                "row": "A",
                "number": 1,
                "type": "STANDARD",
                "status": "AVAILABLE",
                "price": 70000
            },
            {
                "id": 2,
                "row": "A",
                "number": 2,
                "type": "STANDARD",
                "status": "BOOKED",
                "price": 70000
            },
            {
                "id": 3,
                "row": "A",
                "number": 3,
                "type": "VIP",
                "status": "LOCKED",
                "price": 90000
            }
        ],
        "summary": {
            "available": 100,
            "booked": 15,
            "locked": 5
        }
    }
}
```

### **Code Implementation:**

Thêm vào `ShowtimeController.php`:

```php
/**
 * Get seat map for a showtime (for Android app)
 */
public function getSeatMap(string $showtimeId)
{
    $showtime = Showtime::with(['movie', 'screen'])->find($showtimeId);

    if (!$showtime) {
        return response()->json([
            'success' => false,
            'message' => 'Showtime not found',
        ], 404);
    }

    // Lấy tất cả ghế của phòng chiếu
    $seats = \App\Models\Seat::where('screen_id', $showtime->screen_id)
        ->orderBy('row_label')
        ->orderBy('seat_number')
        ->get();

    // Lấy ghế đã đặt (từ orders đã thanh toán)
    $bookedSeatIds = \App\Models\OrderLine::whereHas('order', function($q) use ($showtimeId) {
        $q->where('showtime_id', $showtimeId)
          ->where('status', 'PAID');
    })
    ->where('item_type', 'TICKET')
    ->pluck('seat_id')
    ->toArray();

    // Lấy ghế đang bị lock (giữ tạm thời)
    $lockedSeatIds = \App\Models\SeatLock::where('showtime_id', $showtimeId)
        ->where('expires_at', '>', \Carbon\Carbon::now())
        ->pluck('seat_id')
        ->toArray();

    // Map trạng thái cho từng ghế
    $seatMap = $seats->map(function($seat) use ($bookedSeatIds, $lockedSeatIds, $showtime) {
        $status = 'AVAILABLE';
        
        if (in_array($seat->id, $bookedSeatIds)) {
            $status = 'BOOKED';
        } elseif (in_array($seat->id, $lockedSeatIds)) {
            $status = 'LOCKED';
        } elseif ($seat->is_blocked) {
            $status = 'BLOCKED';
        }

        // Tính giá theo loại ghế
        $price = $showtime->base_price;
        if ($seat->seat_type == 'VIP') {
            $price = $showtime->base_price * 1.5;
        } elseif ($seat->seat_type == 'COUPLE') {
            $price = $showtime->base_price * 2;
        }

        return [
            'id' => $seat->id,
            'row' => $seat->row_label,
            'number' => $seat->seat_number,
            'type' => $seat->seat_type,
            'status' => $status,
            'price' => $price,
        ];
    });

    // Thống kê
    $summary = [
        'available' => $seatMap->where('status', 'AVAILABLE')->count(),
        'booked' => $seatMap->where('status', 'BOOKED')->count(),
        'locked' => $seatMap->where('status', 'LOCKED')->count(),
        'blocked' => $seatMap->where('status', 'BLOCKED')->count(),
    ];

    return response()->json([
        'success' => true,
        'data' => [
            'showtime' => [
                'id' => $showtime->id,
                'movie' => $showtime->movie->title,
                'screen' => $showtime->screen->name,
                'start_at' => $showtime->start_at,
                'base_price' => $showtime->base_price,
            ],
            'screen_layout' => [
                'rows' => $showtime->screen->row_count,
                'cols' => $showtime->screen->col_count,
                'total_seats' => $seats->count(),
            ],
            'seats' => $seatMap,
            'summary' => $summary,
        ],
    ], 200);
}
```

---

## 📱 PHẦN 3: HƯỚNG DẪN ANDROID NATIVE

### **1. Gọi API lấy sơ đồ ghế:**

```kotlin
// Retrofit Interface
interface ShowtimeApi {
    @GET("showtimes/{id}/seat-map")
    suspend fun getSeatMap(@Path("id") showtimeId: Int): Response<SeatMapResponse>
}

// Data Classes
data class SeatMapResponse(
    val success: Boolean,
    val data: SeatMapData
)

data class SeatMapData(
    val showtime: ShowtimeInfo,
    val screen_layout: ScreenLayout,
    val seats: List<Seat>,
    val summary: Summary
)

data class Seat(
    val id: Int,
    val row: String,
    val number: Int,
    val type: String,
    val status: String, // AVAILABLE, BOOKED, LOCKED, BLOCKED
    val price: Int
)
```

### **2. Hiển thị sơ đồ ghế:**

```kotlin
// RecyclerView Adapter
class SeatAdapter(private val seats: List<Seat>) : RecyclerView.Adapter<SeatViewHolder>() {
    
    override fun onBindViewHolder(holder: SeatViewHolder, position: Int) {
        val seat = seats[position]
        
        // Đổi màu theo trạng thái
        when (seat.status) {
            "AVAILABLE" -> holder.seatView.setBackgroundColor(Color.GREEN)
            "BOOKED" -> holder.seatView.setBackgroundColor(Color.RED)
            "LOCKED" -> holder.seatView.setBackgroundColor(Color.YELLOW)
            "BLOCKED" -> holder.seatView.setBackgroundColor(Color.GRAY)
        }
        
        // Hiển thị số ghế
        holder.seatText.text = "${seat.row}${seat.number}"
        
        // Click listener
        holder.itemView.setOnClickListener {
            if (seat.status == "AVAILABLE") {
                // Cho phép chọn ghế
                onSeatSelected(seat)
            } else {
                // Hiển thị thông báo ghế không khả dụng
                Toast.makeText(context, "Ghế đã được đặt", Toast.LENGTH_SHORT).show()
            }
        }
    }
}
```

### **3. Layout sơ đồ ghế (GridLayout):**

```xml
<androidx.recyclerview.widget.RecyclerView
    android:id="@+id/seatMapRecyclerView"
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    app:layoutManager="androidx.recyclerview.widget.GridLayoutManager"
    app:spanCount="12" />
```

### **4. Màu sắc gợi ý:**

```kotlin
// Chú thích màu
val legend = mapOf(
    "Ghế trống" to Color.GREEN,
    "Đã đặt" to Color.RED,
    "Đang giữ" to Color.YELLOW,
    "Không khả dụng" to Color.GRAY
)
```

---

## 🔗 ROUTES CẦN THÊM

Thêm vào `routes/api.php`:

```php
// Tạo phim với lịch chiếu tự động
Route::post('/movies/create-with-schedule', [MovieController::class, 'createWithSchedule']);

// Xem sơ đồ ghế
Route::get('/showtimes/{showtime}/seat-map', [ShowtimeController::class, 'getSeatMap']);
```

---

## 📊 LUỒNG HOẠT ĐỘNG

```
1. Admin tạo phim mới
   ↓
2. Hệ thống tự động:
   - Chọn ngẫu nhiên 3 phòng chiếu
   - Tạo 4 suất/ngày x 7 ngày = 28 suất/phòng
   - Tổng: 84 suất chiếu
   ↓
3. User Android:
   - Xem danh sách phim
   - Chọn phim → Xem suất chiếu
   - Chọn suất → Gọi API seat-map
   - Hiển thị sơ đồ ghế màu sắc
   - Chọn ghế trống → Đặt vé
```

---

## ✅ CHECKLIST

- [ ] Thêm method `createWithSchedule()` vào MovieController
- [ ] Thêm method `getSeatMap()` vào ShowtimeController
- [ ] Thêm routes mới
- [ ] Test API với Postman
- [ ] Implement Android UI
- [ ] Test luồng đặt vé hoàn chỉnh

---

Bạn muốn tôi implement code vào controller luôn không?
