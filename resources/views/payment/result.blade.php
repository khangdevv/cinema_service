<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 min-h-screen">
    <nav class="bg-black/40 backdrop-blur-lg border-b border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <h1 class="text-2xl font-bold text-white">🎬 Cinema</h1>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-xl mx-auto text-center">
            
            @if(session('success'))
                <div class="mb-8">
                    <div class="w-24 h-24 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h1 class="text-4xl font-bold text-white mb-4">Đã ghi nhận!</h1>
                    <p class="text-gray-300 mb-8">Chúng tôi sẽ xác nhận đơn hàng của bạn trong giây lát</p>

                    @if(session('order_id'))
                        <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-6 mb-6">
                            <p class="text-gray-400 text-sm mb-2">Mã đơn hàng</p>
                            <p class="text-white font-mono font-bold text-xl">{{ session('order_id') }}</p>
                        </div>
                    @endif

                    <a href="{{ route('booking.index') }}" 
                       class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-bold">
                        Về trang chủ
                    </a>
                </div>

            @else
                <div class="mb-8">
                    <div class="w-24 h-24 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>

                    <h1 class="text-4xl font-bold text-white mb-4">Có lỗi xảy ra</h1>
                    <p class="text-gray-300 mb-8">{{ session('error') ?? 'Vui lòng thử lại' }}</p>

                    <a href="{{ route('booking.index') }}" 
                       class="inline-block bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-bold">
                        Thử lại
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
