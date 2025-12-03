<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 min-h-screen">
    <nav class="bg-black/40 backdrop-blur-lg border-b border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('booking.index') }}"><h1 class="text-2xl font-bold text-white">🎬 Cinema</h1></a>
                <div class="flex gap-4">
                    <a href="{{ route('booking.index') }}" class="text-gray-300 hover:text-white font-medium">Đặt Vé</a>
                    <a href="{{ route('my.bookings') }}" class="text-gray-300 hover:text-white font-medium">Vé Của Tôi</a>
                    <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white font-medium">Cài Đặt</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            
            @if(session('error'))
                <div class="bg-red-500/20 border border-red-500 rounded-lg p-4 mb-6">
                    <p class="text-red-200">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-8 text-center">
                <h2 class="text-3xl font-bold text-white mb-6">Thanh toán</h2>
                
                @php
                    $bankId = env('VIETQR_BANK_ID', '970422');
                    $accountNo = env('VIETQR_ACCOUNT_NO', '0378366953');
                    $accountName = env('VIETQR_ACCOUNT_NAME', 'NGUYEN LUU BAO KHANG');
                    $orderId = 'ORDER' . time();
                    
                    // Sử dụng SePay QR
                    $qrUrl = "https://qr.sepay.vn/img?acc={$accountNo}&bank={$bankId}&amount={$totalAmount}&des=" . urlencode($orderId);
                @endphp

                <div class="bg-white p-4 rounded-xl inline-block mb-6">
                    <img src="{{ $qrUrl }}" alt="QR Code" class="w-64 h-64 object-contain">
                </div>

                <div class="mb-6">
                    <p class="text-5xl font-bold text-white mb-2">{{ number_format($totalAmount, 0, ',', '.') }} đ</p>
                    <p class="text-gray-300 text-lg mb-1">{{ $showtime->movie->title }}</p>
                    <p class="text-gray-400 text-sm">Nội dung CK: {{ $orderId }}</p>
                </div>

                <div class="bg-gray-800/50 rounded-lg p-4 mb-6 text-left">
                    <p class="text-gray-300 text-sm mb-2">📱 <strong>Ngân hàng:</strong> MB Bank ({{ $bankId }})</p>
                    <p class="text-gray-300 text-sm mb-2">💳 <strong>Số TK:</strong> {{ $accountNo }}</p>
                    <p class="text-gray-300 text-sm">👤 <strong>Tên TK:</strong> {{ $accountName }}</p>
                </div>

                <form method="POST" action="{{ route('payment.confirm') }}">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $orderId }}">
                    <input type="hidden" name="amount" value="{{ $totalAmount }}">
                    
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white py-4 rounded-lg font-bold text-lg shadow-lg hover:shadow-xl transition-all">
                        ✓ Đã thanh toán
                    </button>
                    
                    <a href="{{ route('booking.index') }}" 
                       class="block w-full mt-3 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg font-medium transition-all">
                        ← Quay lại
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>