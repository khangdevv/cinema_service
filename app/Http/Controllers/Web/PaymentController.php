<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function generateVietQR(Request $request)
    {
        // Lấy thông tin từ request
        $amount = $request->input('amount');
        $orderId = 'DV' . time(); // Mã đơn hàng
        
        // Thông tin tài khoản ngân hàng 
        $bankId = env('VIETQR_BANK_ID', '970422'); // MB Bank
        $accountNo = env('VIETQR_ACCOUNT_NO', '0378366953');
        $accountName = env('VIETQR_ACCOUNT_NAME', 'NGUYEN LUU BAO KHANG');
        
        // Tạo nội dung chuyển khoản
        $description = $orderId;
        
        // Template QR
        $template = 'compact';
        
        // URL API VietQR
        $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?"
            . "amount={$amount}"
            . "&addInfo=" . urlencode($description)
            . "&accountName=" . urlencode($accountName);

        $sepay = "https://qr.sepay.vn/img?acc=0378366953&bank=970422&amount={$amount}&des={$description}";
        
        // Lưu thông tin vào session để hiển thị
        session([
            'payment_qr_url' => $sepay,
            'payment_amount' => $amount,
            'payment_order_id' => $orderId,
            'payment_account' => $accountNo,
            'payment_bank' => $bankId
        ]);
        
        return redirect()->route('payment.checkout', $request->all());
    }

    public function confirmPayment(Request $request)
    {
        try {
            // Lấy thông tin từ session
            $showtimeId = session('booking_showtime_id');
            $seatIds = session('booking_seat_ids', []);
            $totalAmount = session('booking_total_amount');
            $account = auth()->guard('web')->user();
            
            if (!$showtimeId || empty($seatIds) || !$account) {
                return redirect()->route('booking.index')->with('error', 'Phiên đặt vé đã hết hạn');
            }
            
            DB::beginTransaction();
            
            // Tạo order
            $order = \App\Models\Order::create([
                'channel' => 'WEB',
                'account_id' => $account->id,
                'showtime_id' => $showtimeId,
                'status' => 'PAID',
                'payment_method' => 'CARD',
                'total_amount' => $totalAmount,
            ]);
            
            // Tạo order lines cho từng ghế
            foreach ($seatIds as $seatId) {
                \App\Models\OrderLine::create([
                    'order_id' => $order->id,
                    'item_type' => 'TICKET',
                    'seat_id' => $seatId,
                    'qty' => 1,
                    'unit_price' => $totalAmount / count($seatIds),
                    'line_total' => $totalAmount / count($seatIds),
                ]);
            }
            
            DB::commit();
            
            // Xóa session
            session()->forget(['booking_showtime_id', 'booking_seat_ids', 'booking_total_amount']);
            
            // Redirect về trang chính với thông báo thành công
            return redirect()->route('booking.index')->with('success', '🎉 Đặt vé thành công! Mã đơn hàng: ORDER' . str_pad($order->id, 6, '0', STR_PAD_LEFT));
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('booking.index')->with('error', 'Đặt vé thất bại: ' . $e->getMessage());
        }
    }

    public function checkout(Request $request)
    {
        // Lấy thông tin từ session hoặc request
        $showtimeId = $request->input('showtime_id');
        $seatIds = $request->input('seat_ids', []);
        $totalAmount = $request->input('price');
        
        // Nếu seat_ids là JSON string, decode nó
        if (is_string($seatIds)) {
            $seatIds = json_decode($seatIds, true) ?? [];
        }
        
        if (empty($seatIds)) {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một ghế');
        }

        if (!$showtimeId) {
            return redirect()->route('booking.index')->with('error', 'Không tìm thấy thông tin suất chiếu');
        }

        // Lấy thông tin showtime
        $showtime = \App\Models\Showtime::with(['movie', 'screen'])->find($showtimeId);
        
        if (!$showtime) {
            return redirect()->route('booking.index')->with('error', 'Suất chiếu không tồn tại');
        }
        
        // Lấy thông tin ghế
        $seats = \App\Models\Seat::whereIn('id', $seatIds)->get();
        
        if ($seats->isEmpty()) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin ghế');
        }
        
        // Lưu vào session để dùng khi confirm
        session([
            'booking_showtime_id' => $showtimeId,
            'booking_seat_ids' => $seatIds,
            'booking_total_amount' => $totalAmount
        ]);
        
        return view('payment.checkout', compact('showtime', 'seats', 'totalAmount'));
    }
}
