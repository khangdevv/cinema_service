<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        
        // Lưu thông tin vào session để hiển thị
        session([
            'payment_qr_url' => $qrUrl,
            'payment_amount' => $amount,
            'payment_order_id' => $orderId,
            'payment_account' => $accountNo,
            'payment_bank' => $bankId
        ]);
        
        return redirect()->route('payment.checkout', $request->all());
    }

    public function confirmPayment(Request $request)
    {
        $orderId = session('payment_order_id');
        
        // Lưu order vào database với status PENDING
        // Admin sẽ verify sau
        return view('payment.result')
            ->with('success', 'Đã ghi nhận! Vui lòng chờ xác nhận từ hệ thống')
            ->with('order_id', $orderId);
    }

    public function checkout(Request $request)
    {
        // Lấy thông tin từ session hoặc request
        $showtimeId = $request->input('showtime_id');
        $seatIds = $request->input('seat_ids', []);
        
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
        
        $pricePerSeat = 100000;
        $totalAmount = count($seats) * $pricePerSeat;
        
        return view('payment.checkout', compact('showtime', 'seats', 'totalAmount'));
    }
}
