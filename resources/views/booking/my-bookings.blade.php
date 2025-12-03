<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé Của Tôi - Cinema Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('booking.index') }}" class="text-2xl font-bold text-gray-900">🎬 Galaxy Cinema</a>
                </div>
                <div class="flex gap-4 items-center">
                    <a href="{{ route('booking.index') }}" class="text-gray-600 hover:text-purple-600 font-medium">Đặt Vé</a>
                    <a href="{{ route('my.bookings') }}" class="text-purple-600 font-semibold">Vé Của Tôi</a>
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-purple-600 font-medium">Cài Đặt</a>
                    <span class="text-gray-300">|</span>
                    <span class="text-gray-700 font-medium">{{ $user->full_name }}</span>
                    <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-all shadow-md hover:shadow-lg">
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">🎟️ Vé Của Tôi</h1>
                <p class="text-gray-600">Xem và quản lý lịch sử đặt vé của bạn</p>
            </div>

            @if($orders->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center shadow-md">
                    <span class="text-6xl mb-4 block">🎫</span>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">Chưa Có Vé Nào</h3>
                    <p class="text-gray-600 mb-6">Bạn chưa đặt vé xem phim nào</p>
                    <a href="{{ route('booking.index') }}" class="inline-block bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-3 rounded-lg font-semibold transition-all shadow-md hover:shadow-lg">
                        Đặt Vé Ngay
                    </a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($orders as $order)
                        @php
                            $showtime = $order->showtime;
                            $movie = $showtime->movie;
                            $screen = $showtime->screen;
                            $seats = $order->order_lines->where('item_type', 'TICKET');
                            $products = $order->order_lines->where('item_type', 'PRODUCT');
                            $showtimeDate = \Carbon\Carbon::parse($showtime->start_at);
                            $isPast = $showtimeDate->isPast();
                            $isUpcoming = $showtimeDate->isFuture();
                        @endphp

                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:border-purple-500 transition-all shadow-md hover:shadow-lg">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6">
                                <!-- Movie Poster -->
                                <div class="col-span-1">
                                    @if($movie->poster)
                                        <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="w-full rounded-lg shadow-lg">
                                    @else
                                        <div class="w-full h-64 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg flex items-center justify-center">
                                            <span class="text-6xl">🎬</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Booking Details -->
                                <div class="col-span-2">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-2xl font-bold text-gray-900">{{ $movie->title }}</h3>
                                        @php
                                            $statusBadge = '';
                                            $statusText = '';
                                            if ($order->status === 'CANCELLED') {
                                                $statusBadge = 'bg-red-100 text-red-700';
                                                $statusText = '✗ Đã Hủy';
                                            } elseif ($isPast) {
                                                $statusBadge = 'bg-gray-100 text-gray-700';
                                                $statusText = '✓ Đã Xem';
                                            } elseif ($isUpcoming) {
                                                $statusBadge = 'bg-yellow-100 text-yellow-700';
                                                $statusText = '⏳ Chưa Tới Ngày Chiếu';
                                            } else {
                                                $statusBadge = 'bg-green-100 text-green-700';
                                                $statusText = '✓ Đã Xác Nhận';
                                            }
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>

                                    <div class="space-y-2 text-sm mb-4">
                                        <div class="flex items-start">
                                            <span class="text-gray-500 w-32">Mã Đơn:</span>
                                            <span class="text-gray-900 font-mono font-bold">ORDER{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-gray-500 w-32">Phòng Chiếu:</span>
                                            <span class="text-gray-900">{{ $screen->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-gray-500 w-32">Suất Chiếu:</span>
                                            <span class="text-gray-900">
                                                {{ $showtimeDate->format('d/m/Y - H:i') }}
                                            </span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-gray-500 w-32">Ghế:</span>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($seats as $seatLine)
                                                    @if($seatLine->seat)
                                                        <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700 text-xs font-semibold">
                                                            {{ $seatLine->seat->row_label }}{{ $seatLine->seat->seat_number }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @if($products->isNotEmpty())
                                            <div class="flex items-start">
                                                <span class="text-gray-500 w-32">Combo:</span>
                                                <div class="space-y-1">
                                                    @foreach($products as $productLine)
                                                        @if($productLine->product)
                                                            <span class="block text-gray-900">
                                                                {{ $productLine->product->name }} x{{ $productLine->qty }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        <div class="flex items-start">
                                            <span class="text-gray-500 w-32">Đặt Lúc:</span>
                                            <span class="text-gray-900">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-gray-500 w-32">Thanh Toán:</span>
                                            <span class="text-gray-900 font-medium">
                                                @if($order->payment_method === 'BANK_TRANSFER') 🏦 Chuyển Khoản
                                                @elseif($order->payment_method === 'CARD') 💳 Thẻ
                                                @elseif($order->payment_method === 'MOMO') 📱 MoMo
                                                @elseif($order->payment_method === 'VNPAY') 💰 VNPay
                                                @else 💵 Tiền Mặt
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    @if($isPast)
                                        <div class="bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-600">
                                            <span class="mr-2">⏰</span> Suất chiếu đã kết thúc
                                        </div>
                                    @elseif($isUpcoming && $order->status === 'CONFIRMED')
                                        <div class="bg-green-50 border border-green-300 rounded-lg px-3 py-2 text-sm text-green-700">
                                            <span class="mr-2">✓</span> Chiếu {{ $showtimeDate->diffForHumans() }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Price & Actions -->
                                <div class="col-span-1 flex flex-col justify-between">
                                    <div class="text-right">
                                        <p class="text-gray-500 text-sm mb-1">Tổng Tiền</p>
                                        <p class="text-3xl font-bold text-purple-600">
                                            {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                        </p>
                                        <p class="text-gray-500 text-xs mt-1">
                                            {{ $seats->count() }} ghế
                                        </p>
                                    </div>

                                    <div class="space-y-2 mt-4">
                                        @if($order->status === 'CONFIRMED' && $isUpcoming)
                                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-center">
                                                <p class="text-xs text-purple-700 font-semibold mb-1">Mã QR Vào Cửa</p>
                                                <div class="bg-white p-2 rounded">
                                                    <div class="text-2xl font-mono font-bold text-gray-900">
                                                        {{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($isPast && $order->status === 'CONFIRMED')
                                            <span class="block w-full bg-gray-100 text-gray-500 text-center px-4 py-2 rounded-lg text-sm font-semibold">
                                                ✓ Đã Xem
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="mt-8">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</body>
</html>
