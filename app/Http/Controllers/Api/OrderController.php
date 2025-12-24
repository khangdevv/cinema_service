<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Seat;
use App\Models\SeatLock;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Tạo đơn hàng mới và đặt ghế
     */
    public function store(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtime,id',
            'seats' => 'required|array|min:1',
            'seats.*.seat_id' => 'required|exists:seat,id',
            'seats.*.unit_price' => 'required|numeric|min:0',
            'products' => 'nullable|array',
            'products.*.product_id' => 'required|exists:product,id',
            'products.*.qty' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|in:CASH,CARD,MOMO,VNPAY,BANK_TRANSFER',
        ]);

        try {
            DB::beginTransaction();

            $showtime = Showtime::findOrFail($request->showtime_id);
            $account = $request->user();

            // Kiểm tra các ghế có sẵn không
            foreach ($request->seats as $seatData) {
                $seat = Seat::findOrFail($seatData['seat_id']);

                // Kiểm tra ghế có bị block không
                if ($seat->is_blocked) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Seat {$seat->row_label}{$seat->seat_number} is blocked",
                    ], 400);
                }

                // Kiểm tra ghế đã được đặt chưa
                $existingOrder = OrderLine::whereHas('order', function ($query) use ($request) {
                    $query->where('showtime_id', $request->showtime_id)
                        ->where('status', '!=', 'CANCELLED');
                })->where('seat_id', $seatData['seat_id'])->exists();

                if ($existingOrder) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Seat {$seat->row_label}{$seat->seat_number} is already booked",
                    ], 400);
                }

                // Kiểm tra ghế có đang bị lock bởi người khác không
                $seatLock = SeatLock::where('showtime_id', $request->showtime_id)
                    ->where('seat_id', $seatData['seat_id'])
                    ->where('expires_at', '>', Carbon::now())
                    ->where('account_id', '!=', $account->id)
                    ->first();

                if ($seatLock) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Seat {$seat->row_label}{$seat->seat_number} is locked by another user",
                    ], 400);
                }
            }

            // Tính tổng tiền
            $totalAmount = 0;

            // Tính tiền ghế
            foreach ($request->seats as $seatData) {
                $totalAmount += $seatData['unit_price'];
            }

            // Tính tiền sản phẩm (nếu có)
            if ($request->has('products')) {
                foreach ($request->products as $productData) {
                    $totalAmount += $productData['unit_price'] * $productData['qty'];
                }
            }

            // Tạo đơn hàng - Giả định thanh toán thành công ngay lập tức
            $order = Order::create([
                'channel' => 'WEB',
                'account_id' => $account->id,
                'showtime_id' => $request->showtime_id,
                'status' => 'CONFIRMED',
                'payment_method' => $request->payment_method ?? 'CASH',
                'total_amount' => $totalAmount,
            ]);

            // Tạo order line cho ghế
            foreach ($request->seats as $seatData) {
                OrderLine::create([
                    'order_id' => $order->id,
                    'item_type' => 'TICKET',
                    'seat_id' => $seatData['seat_id'],
                    'qty' => 1,
                    'unit_price' => $seatData['unit_price'],
                    'line_total' => $seatData['unit_price'],
                ]);
            }

            // Tạo order line cho sản phẩm (nếu có)
            if ($request->has('products')) {
                foreach ($request->products as $productData) {
                    OrderLine::create([
                        'order_id' => $order->id,
                        'item_type' => 'PRODUCT',
                        'product_id' => $productData['product_id'],
                        'qty' => $productData['qty'],
                        'unit_price' => $productData['unit_price'],
                        'line_total' => $productData['unit_price'] * $productData['qty'],
                    ]);
                }
            }

            // Xóa các seat lock của user này cho showtime này
            SeatLock::where('showtime_id', $request->showtime_id)
                ->where('account_id', $account->id)
                ->delete();

            DB::commit();

            // Load relationships
            $order->load(['order_lines.seat', 'order_lines.product', 'showtime.movie', 'showtime.screen']);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Lấy danh sách đơn hàng của user
     */
    public function index(Request $request)
    {
        $account = $request->user();

        $orders = Order::where('account_id', $account->id)
            ->with(['account', 'order_lines.seat', 'order_lines.product', 'showtime.movie', 'showtime.screen'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders,
        ]);
    }

    public function getall(Request $request)
    {
        if ($request->has('status')) {
            $orders = Order::where('status', 'LIKE', '%' . $request->status . '%')
                ->with(['account', 'order_lines.seat', 'order_lines.product', 'showtime.movie', 'showtime.screen'])
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $orders = Order::with(['account', 'order_lines.seat', 'order_lines.product', 'showtime.movie', 'showtime.screen'])
                ->orderBy('id', 'desc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders,
        ]);
    }

    public function updateStatusPayment(Request $request, $id)
    {
        $order = Order::find($id);

        if ($order == null) {
            return response()->json([
                'success' => false,
                'message' => 'Order not exist',
            ]);
        }

        $request->validate([
            'status' => 'nullable|in:INIT,PAID,CANCELLED',
        ]);

        try {
            $order->status = $request->status;
            $order->save();

            return response()->json([
                'success'=> true,
                'message'=> 'Order has been updated successfully',
                'data'=> $order,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Lấy chi tiết đơn hàng
     */
    public function show(Request $request, $id)
    {
        $account = $request->user();

        $order = Order::where('id', $id)
            ->where('account_id', $account->id)
            ->with(['account', 'order_lines.seat', 'order_lines.product', 'showtime.movie', 'showtime.screen'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully',
            'data' => $order,
        ]);
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel(Request $request, $id)
    {
        $account = $request->user();

        $order = Order::where('id', $id)
            ->where('account_id', $account->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->status === 'CANCELLED') {
            return response()->json([
                'success' => false,
                'message' => 'Order already cancelled',
            ], 400);
        }

        // Kiểm tra thời gian showtime, không cho hủy nếu quá gần giờ chiếu
        $showtime = $order->showtime;
        $hoursBeforeShowtime = Carbon::now()->diffInHours($showtime->start_at, false);

        if ($hoursBeforeShowtime < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel order less than 2 hours before showtime',
            ], 400);
        }

        $order->status = 'CANCELLED';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order,
        ]);
    }

    /**
     * Lock ghế tạm thời (để giữ ghế trong khi user chọn)
     */
    public function lockSeats(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtime,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'required|exists:seat,id',
        ]);

        try {
            $account = $request->user();
            $expiresAt = Carbon::now()->addMinutes(10); // Lock trong 10 phút

            // Xóa các lock cũ của user này
            SeatLock::where('showtime_id', $request->showtime_id)
                ->where('account_id', $account->id)
                ->delete();

            // Xóa các lock đã hết hạn
            SeatLock::where('expires_at', '<', Carbon::now())->delete();

            $lockedSeats = [];

            foreach ($request->seat_ids as $seatId) {
                $seat = Seat::findOrFail($seatId);

                // Kiểm tra ghế có bị block không
                if ($seat->is_blocked) {
                    return response()->json([
                        'success' => false,
                        'message' => "Seat {$seat->row_label}{$seat->seat_number} is blocked",
                    ], 400);
                }

                // Kiểm tra ghế đã được đặt chưa
                $existingOrder = OrderLine::whereHas('order', function ($query) use ($request) {
                    $query->where('showtime_id', $request->showtime_id)
                        ->where('status', '!=', 'CANCELLED');
                })->where('seat_id', $seatId)->exists();

                if ($existingOrder) {
                    return response()->json([
                        'success' => false,
                        'message' => "Seat {$seat->row_label}{$seat->seat_number} is already booked",
                    ], 400);
                }

                // Kiểm tra ghế có đang bị lock bởi người khác không
                $existingLock = SeatLock::where('showtime_id', $request->showtime_id)
                    ->where('seat_id', $seatId)
                    ->where('expires_at', '>', Carbon::now())
                    ->where('account_id', '!=', $account->id)
                    ->first();

                if ($existingLock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Seat {$seat->row_label}{$seat->seat_number} is locked by another user",
                    ], 400);
                }

                // Tạo lock mới
                $lock = SeatLock::create([
                    'showtime_id' => $request->showtime_id,
                    'seat_id' => $seatId,
                    'account_id' => $account->id,
                    'expires_at' => $expiresAt,
                ]);

                $lockedSeats[] = $lock;
            }

            return response()->json([
                'success' => true,
                'message' => 'Seats locked successfully',
                'data' => [
                        'locked_seats' => $lockedSeats,
                        'expires_at' => $expiresAt,
                    ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to lock seats',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Unlock ghế (hủy lock)
     */
    public function unlockSeats(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtime,id',
            'seat_ids' => 'nullable|array',
            'seat_ids.*' => 'exists:seat,id',
        ]);

        $account = $request->user();

        // Nếu có seat_ids thì chỉ unlock những ghế đó, không thì unlock tất cả ghế của user
        $query = SeatLock::where('showtime_id', $request->showtime_id)
            ->where('account_id', $account->id);

        if ($request->has('seat_ids')) {
            $query->whereIn('seat_id', $request->seat_ids);
        }

        $deleted = $query->delete();

        return response()->json([
            'success' => true,
            'message' => 'Seats unlocked successfully',
            'data' => [
                    'unlocked_count' => $deleted,
                ],
        ]);
    }

    /**
     * Tạo QR code thanh toán cho đơn hàng
     */
    public function generateQR(Request $request, $id)
    {
        $account = $request->user();

        $order = Order::where('id', $id)
            ->where('account_id', $account->id)
            ->with(['showtime.movie'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->status === 'CONFIRMED') {
            return response()->json([
                'success' => false,
                'message' => 'Order already confirmed',
            ], 400);
        }

        if ($order->status === 'CANCELLED') {
            return response()->json([
                'success' => false,
                'message' => 'Order already cancelled',
            ], 400);
        }

        // Thông tin tài khoản ngân hàng từ env
        $bankId = env('VIETQR_BANK_ID', '970422'); // MB Bank
        $accountNo = env('VIETQR_ACCOUNT_NO', '0378366953');
        $accountName = env('VIETQR_ACCOUNT_NAME', 'NGUYEN LUU BAO KHANG');

        // Mã đơn hàng để tracking
        $orderCode = 'ORDER' . str_pad($order->id, 6, '0', STR_PAD_LEFT);

        // Tạo URL QR VietQR
        $vietQRUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?"
            . "amount={$order->total_amount}"
            . "&addInfo=" . urlencode($orderCode)
            . "&accountName=" . urlencode($accountName);

        // Tạo URL QR SePay (alternative)
        $sepayUrl = "https://qr.sepay.vn/img?acc={$accountNo}&bank={$bankId}"
            . "&amount={$order->total_amount}"
            . "&des=" . urlencode($orderCode);

        return response()->json([
            'success' => true,
            'message' => 'QR code generated successfully',
            'data' => [
                    'order_id' => $order->id,
                    'order_code' => $orderCode,
                    'amount' => $order->total_amount,
                    'bank_id' => $bankId,
                    'account_no' => $accountNo,
                    'account_name' => $accountName,
                    'qr_url' => $sepayUrl, // Sử dụng SePay vì ổn định hơn
                    'vietqr_url' => $vietQRUrl, // Backup option
                    'description' => $orderCode,
                    'expires_in_minutes' => 15, // QR hết hạn sau 15 phút
                ],
        ]);
    }

    /**
     * Xác nhận thanh toán đã hoàn tất (webhook hoặc manual confirm)
     */
    public function confirmPayment(Request $request, $id)
    {
        $request->validate([
            'transaction_id' => 'nullable|string', // Mã giao dịch từ ngân hàng (nếu có)
        ]);

        $account = $request->user();

        $order = Order::where('id', $id)
            ->where('account_id', $account->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->status === 'CONFIRMED') {
            return response()->json([
                'success' => true,
                'message' => 'Order already confirmed',
                'data' => $order,
            ]);
        }

        if ($order->status === 'CANCELLED') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot confirm cancelled order',
            ], 400);
        }

        // Cập nhật trạng thái đơn hàng
        $order->status = 'CONFIRMED';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed successfully',
            'data' => $order,
        ]);
    }

    /**
     * Webhook nhận thông báo từ ngân hàng (SePay/VietQR)
     * Endpoint này không cần auth vì được gọi từ bên thứ 3
     */
    public function webhook(Request $request)
    {
        // Validate webhook signature nếu có
        $webhookSecret = env('PAYMENT_WEBHOOK_SECRET');

        // Log webhook data để debug
        Log::info('Payment webhook received', $request->all());

        // Parse thông tin từ webhook
        // Format có thể khác nhau tùy provider
        $transactionId = $request->input('transaction_id');
        $amount = $request->input('amount');
        $description = $request->input('description');

        // Extract order ID từ description (ORDER000001 -> 1)
        if (preg_match('/ORDER(\d+)/', $description, $matches)) {
            $orderId = (int) $matches[1];

            $order = Order::find($orderId);

            if ($order && $order->total_amount == $amount && $order->status === 'PENDING') {
                $order->status = 'CONFIRMED';
                $order->save();

                // Xóa các seat lock liên quan
                SeatLock::where('showtime_id', $order->showtime_id)
                    ->whereHas('order_lines', function ($query) use ($order) {
                        $query->where('order_id', $order->id);
                    })
                    ->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed',
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid webhook data',
        ], 400);
    }
}
