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
            <h1 class="text-2xl font-bold text-white">🎬 Cinema</h1>
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
                    $accountNo = env('VIETQR_ACCOUNT_NO', '0123456789');
                    $accountName = env('VIETQR_ACCOUNT_NAME', 'NGUYEN VAN A');
                    $orderId = 'DV' . time();
                    $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$totalAmount}&addInfo=" . urlencode($orderId) . "&accountName=" . urlencode($accountName);
                @endphp

                <div class="bg-white p-4 rounded-xl inline-block mb-6">
                    <img src="{{ $qrUrl }}" alt="QR" class="w-64 h-64">
                </div>

                <p class="text-5xl font-bold text-white mb-2">{{ number_format($totalAmount, 0, ',', '.') }} đ</p>
                <p class="text-gray-400 text-sm mb-8">{{ $showtime->movie->title }}</p>

                <form method="POST" action="{{ route('payment.confirm') }}">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $orderId }}">
                    <input type="hidden" name="amount" value="{{ $totalAmount }}">
                    
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white py-4 rounded-lg font-bold text-lg">
                        ✓ Đã thanh toán
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>