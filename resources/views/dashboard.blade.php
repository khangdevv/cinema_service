<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài Đặt - Cinema Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="text-3xl">🎬</span>
                    <h1 class="text-2xl font-bold text-gray-900">Galaxy Cinema</h1>
                </div>
                <div class="flex gap-4 items-center">
                    <a href="{{ route('booking.index') }}" class="text-gray-600 hover:text-purple-600 font-medium">Đặt Vé</a>
                    <a href="{{ route('my.bookings') }}" class="text-gray-600 hover:text-purple-600 font-medium">Vé Của Tôi</a>
                    <a href="{{ route('dashboard') }}" class="text-purple-600 hover:text-purple-700 font-semibold">Cài Đặt</a>
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

    <div class="container mx-auto py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl border border-gray-200 p-8 mb-6 shadow-lg">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">👤 Thông Tin Cá Nhân</h1>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-500 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif
                
                <div class="bg-gray-50 p-6 rounded-lg mb-6 border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-600">
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Họ và Tên</p>
                            <p class="font-semibold text-gray-900 text-lg">{{ $user->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Email</p>
                            <p class="font-semibold text-gray-900 text-lg">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Số Điện Thoại</p>
                            <p class="font-semibold text-gray-900 text-lg">{{ $user->phone ?? 'Chưa cập nhật' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Trạng Thái</p>
                            <p class="font-semibold">
                                <span class="px-3 py-1 rounded-full text-sm text-white bg-green-600">
                                    Đang Hoạt Động
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('booking.index') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white p-4 rounded-lg text-center transition-all shadow-md hover:shadow-lg font-semibold">
                        🎬 Đặt Vé Ngay
                    </a>
                    <a href="{{ route('my.bookings') }}" class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white p-4 rounded-lg text-center transition-all shadow-md hover:shadow-lg font-semibold">
                        🎟️ Xem Vé Của Tôi
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
