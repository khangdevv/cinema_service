# Database ERD - Cinema Service

## Sơ đồ quan hệ Entity-Relationship

```mermaid
erDiagram
    ACCOUNT {
        bigint id PK
        varchar email UK
        varchar google_id
        varchar phone
        varchar password_hash
        varchar full_name
        enum role "CUSTOMER|STAFF|ADMIN"
        boolean is_active
    }

    MOVIE {
        bigint id PK
        varchar title
        smallint duration_min
        varchar rating_code
        varchar genre
        varchar poster
        boolean is_active
    }

    SCREEN {
        bigint id PK
        varchar code UK
        varchar name
        varchar format "2D|3D|IMAX"
        int row_count
        int col_count
        boolean is_active
    }

    SEAT {
        bigint id PK
        bigint screen_id FK
        varchar row_label
        int seat_number
        enum seat_type "STANDARD|VIP|COUPLE|ACCESSIBLE"
        boolean is_aisle
        boolean is_blocked
    }

    SHOWTIME {
        bigint id PK
        bigint movie_id FK
        bigint screen_id FK
        datetime start_at
        datetime end_at
        int base_price
        enum status "SCHEDULED|OPEN|CLOSED|CANCELLED"
    }

    ORDERS {
        bigint id PK
        enum channel "WEB|POS"
        bigint account_id FK
        bigint cashier_id FK
        bigint showtime_id FK
        enum status "INIT|PAID|CANCELLED"
        enum payment_method "CASH|CARD|EWALLET"
        int total_amount
    }

    ORDER_LINE {
        bigint id PK
        bigint order_id FK
        enum item_type "TICKET|PRODUCT"
        bigint seat_id FK
        bigint product_id FK
        int qty
        int unit_price
        int line_total
    }

    PRODUCT {
        bigint id PK
        varchar name
        int price
        boolean is_active
    }

    SEAT_LOCK {
        bigint id PK
        bigint showtime_id FK
        bigint seat_id FK
        bigint account_id FK
        datetime expires_at
    }

    %% Relationships
    MOVIE ||--o{ SHOWTIME : "has many"
    SCREEN ||--o{ SHOWTIME : "has many"
    SCREEN ||--o{ SEAT : "has many"
    
    SHOWTIME ||--o{ ORDERS : "has many"
    SHOWTIME ||--o{ SEAT_LOCK : "has many"
    
    ACCOUNT ||--o{ ORDERS : "has many"
    ACCOUNT ||--o{ SEAT_LOCK : "has many"
    
    ORDERS ||--o{ ORDER_LINE : "has many"
    
    SEAT ||--o{ ORDER_LINE : "has many"
    SEAT ||--o{ SEAT_LOCK : "has many"
    
    PRODUCT ||--o{ ORDER_LINE : "has many"
```

---

## Chi tiết Foreign Keys

| Table | Column | References | On Update | On Delete |
|-------|--------|------------|-----------|-----------|
| `showtime` | `movie_id` | `movie.id` | CASCADE | RESTRICT |
| `showtime` | `screen_id` | `screen.id` | CASCADE | RESTRICT |
| `seat` | `screen_id` | `screen.id` | CASCADE | CASCADE |
| `orders` | `account_id` | `account.id` | CASCADE | SET NULL |
| `orders` | `showtime_id` | `showtime.id` | CASCADE | RESTRICT |
| `order_line` | `order_id` | `orders.id` | CASCADE | CASCADE |
| `order_line` | `product_id` | `product.id` | CASCADE | RESTRICT |
| `order_line` | `seat_id` | `seat.id` | CASCADE | RESTRICT |
| `seat_lock` | `seat_id` | `seat.id` | CASCADE | CASCADE |
| `seat_lock` | `showtime_id` | `showtime.id` | CASCADE | CASCADE |
| `seat_lock` | `account_id` | `account.id` | CASCADE | SET NULL |

---

## Giải thích On Delete

| Action | Ý nghĩa |
|--------|---------|
| `CASCADE` | Xóa parent → Tự động xóa tất cả children |
| `RESTRICT` | Không cho xóa parent nếu còn children |
| `SET NULL` | Xóa parent → Set foreign key = NULL |

---

## Logic nghiệp vụ

### 1. Movie → Showtime (1:N)
```
Một phim có nhiều suất chiếu
Không thể xóa phim nếu còn suất chiếu (RESTRICT)
```

### 2. Screen → Seat (1:N)
```
Một phòng chiếu có nhiều ghế
Xóa phòng chiếu → Xóa tất cả ghế (CASCADE)
```

### 3. Screen → Showtime (1:N)
```
Một phòng có nhiều suất chiếu
Không thể xóa phòng nếu còn suất chiếu (RESTRICT)
```

### 4. Showtime → Order (1:N)
```
Một suất chiếu có nhiều đơn hàng
Không thể xóa suất chiếu nếu còn đơn hàng (RESTRICT)
```

### 5. Order → OrderLine (1:N)
```
Một đơn hàng có nhiều dòng chi tiết
Xóa đơn hàng → Xóa tất cả chi tiết (CASCADE)
```

### 6. Account → Order (1:N)
```
Một tài khoản có nhiều đơn hàng
Xóa tài khoản → Order.account_id = NULL (SET NULL)
```

### 7. Showtime + Seat → SeatLock
```
Khóa ghế tạm thời khi user đang chọn ghế
Unique: (showtime_id, seat_id) - Mỗi ghế chỉ khóa 1 lần cho 1 suất
```

---

## Flow đặt vé

```mermaid
sequenceDiagram
    participant U as User
    participant API as Backend
    participant DB as Database

    U->>API: 1. Chọn phim
    API->>DB: SELECT * FROM movie WHERE is_active = true
    DB-->>API: Danh sách phim

    U->>API: 2. Chọn suất chiếu
    API->>DB: SELECT * FROM showtime WHERE movie_id = ?
    DB-->>API: Danh sách suất chiếu

    U->>API: 3. Xem ghế trống
    API->>DB: SELECT seats, seat_locks, order_lines
    DB-->>API: Trạng thái ghế

    U->>API: 4. Chọn ghế A1, A2
    API->>DB: INSERT INTO seat_lock (expires_at = now + 10min)
    DB-->>API: Lock thành công

    U->>API: 5. Thanh toán
    API->>DB: INSERT INTO orders, order_line
    API->>DB: DELETE FROM seat_lock
    DB-->>API: Đặt vé thành công
```
