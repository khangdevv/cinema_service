# API Thanh Toán QR Code - Cinema Service

## Tổng quan
Hệ thống thanh toán qua QR code sử dụng VietQR và SePay để tạo mã QR thanh toán ngân hàng tự động.

## Cấu hình

### File `.env`
```env
VIETQR_BANK_ID=970422
VIETQR_ACCOUNT_NO=0378366953
VIETQR_ACCOUNT_NAME="NGUYEN LUU BAO KHANG"
PAYMENT_WEBHOOK_SECRET=your_webhook_secret_here
```

## Flow Thanh Toán

### 1. Tạo Đơn Hàng với Thanh Toán QR
```http
POST /api/orders
Authorization: Bearer {token}
Content-Type: application/json

{
  "showtime_id": 1,
  "seats": [
    {
      "seat_id": 10,
      "unit_price": 80000
    }
  ],
  "products": [
    {
      "product_id": 1,
      "qty": 2,
      "unit_price": 50000
    }
  ],
  "payment_method": "BANK_TRANSFER"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "id": 1,
    "status": "PENDING",
    "total_amount": 180000,
    ...
  }
}
```

### 2. Tạo QR Code Thanh Toán
```http
GET /api/orders/{id}/qr
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "QR code generated successfully",
  "data": {
    "order_id": 1,
    "order_code": "ORDER000001",
    "amount": 180000,
    "bank_id": "970422",
    "account_no": "0378366953",
    "account_name": "NGUYEN LUU BAO KHANG",
    "qr_url": "https://qr.sepay.vn/img?acc=0378366953&bank=970422&amount=180000&des=ORDER000001",
    "vietqr_url": "https://img.vietqr.io/image/970422-0378366953-compact.png?amount=180000&addInfo=ORDER000001&accountName=NGUYEN+LUU+BAO+KHANG",
    "description": "ORDER000001",
    "expires_in_minutes": 15
  }
}
```

### 3. Hiển thị QR Code
Frontend hiển thị QR code từ URL `qr_url` hoặc `vietqr_url`.

Khách hàng quét mã QR bằng app ngân hàng và chuyển khoản.

### 4. Xác Nhận Thanh Toán (Manual)
```http
POST /api/orders/{id}/confirm-payment
Authorization: Bearer {token}
Content-Type: application/json

{
  "transaction_id": "optional_bank_transaction_id"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Payment confirmed successfully",
  "data": {
    "id": 1,
    "status": "CONFIRMED",
    ...
  }
}
```

### 5. Webhook (Tự động từ ngân hàng)
```http
POST /api/payment/webhook
Content-Type: application/json

{
  "transaction_id": "123456789",
  "amount": 180000,
  "description": "ORDER000001",
  "status": "success"
}
```

## Các Endpoint Khác

### Lấy Danh Sách Đơn Hàng
```http
GET /api/orders
Authorization: Bearer {token}
```

### Xem Chi Tiết Đơn Hàng
```http
GET /api/orders/{id}
Authorization: Bearer {token}
```

### Hủy Đơn Hàng
```http
POST /api/orders/{id}/cancel
Authorization: Bearer {token}
```

## Phương Thức Thanh Toán Hỗ Trợ

- `CASH` - Tiền mặt (mặc định)
- `CARD` - Thẻ tín dụng
- `MOMO` - Ví MoMo
- `VNPAY` - VNPay
- `BANK_TRANSFER` - Chuyển khoản ngân hàng qua QR

## Trạng Thái Đơn Hàng

- `PENDING` - Chờ thanh toán (cho BANK_TRANSFER)
- `CONFIRMED` - Đã xác nhận thanh toán
- `CANCELLED` - Đã hủy

## Lưu Ý Bảo Mật

1. **Webhook Secret**: Luôn validate webhook signature trong production
2. **Token Authentication**: Tất cả endpoints (trừ webhook) yêu cầu Bearer token
3. **Order Verification**: Verify order thuộc về user đang đăng nhập
4. **Amount Matching**: Kiểm tra số tiền webhook khớp với order

## Testing

### Test QR Generation
```bash
curl -X GET http://localhost:8000/api/orders/1/qr \
  -H "Authorization: Bearer {your_token}"
```

### Test Manual Confirmation
```bash
curl -X POST http://localhost:8000/api/orders/1/confirm-payment \
  -H "Authorization: Bearer {your_token}" \
  -H "Content-Type: application/json" \
  -d '{"transaction_id": "TEST123"}'
```

## Integration với Frontend

### React/Vue Example
```javascript
// 1. Tạo đơn hàng
const createOrder = async (orderData) => {
  const response = await fetch('/api/orders', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(orderData)
  });
  return response.json();
};

// 2. Lấy QR code
const getQRCode = async (orderId) => {
  const response = await fetch(`/api/orders/${orderId}/qr`, {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  return response.json();
};

// 3. Hiển thị QR
const QRPayment = ({ qrData }) => {
  return (
    <div>
      <h3>Quét mã QR để thanh toán</h3>
      <img src={qrData.qr_url} alt="QR Payment" />
      <p>Số tiền: {qrData.amount.toLocaleString()} VNĐ</p>
      <p>Nội dung: {qrData.order_code}</p>
      <p>Hết hạn sau: {qrData.expires_in_minutes} phút</p>
    </div>
  );
};

// 4. Polling để check payment status
const checkPaymentStatus = async (orderId) => {
  const response = await fetch(`/api/orders/${orderId}`, {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  const data = await response.json();
  return data.data.status === 'CONFIRMED';
};

// 5. Poll every 5 seconds
const pollPayment = (orderId, onConfirmed) => {
  const interval = setInterval(async () => {
    const isConfirmed = await checkPaymentStatus(orderId);
    if (isConfirmed) {
      clearInterval(interval);
      onConfirmed();
    }
  }, 5000);
  
  // Stop after 15 minutes
  setTimeout(() => clearInterval(interval), 15 * 60 * 1000);
};
```

## Troubleshooting

### QR không hiển thị
- Kiểm tra URL có encode đúng không
- Thử dùng `vietqr_url` thay vì `qr_url`

### Webhook không hoạt động
- Kiểm tra webhook URL đã config ở SePay/VietQR chưa
- Xem log: `storage/logs/laravel.log`

### Đơn hàng không tự động confirm
- Kiểm tra webhook endpoint có public access không
- Verify webhook signature
- Kiểm tra order_code format trong description
