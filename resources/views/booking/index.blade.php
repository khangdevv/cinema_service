<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Service - Đặt vé xem phim</title>
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
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-purple-600 font-medium">Dashboard</a>
                    <span class="text-gray-300">|</span>
                    <span class="text-gray-700 font-medium">{{ auth()->user()->full_name }}</span>
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
            <h2 class="text-4xl font-bold text-gray-900 mb-2">🎬 Phim Đang Chiếu</h2>
            <p class="text-gray-600 mb-8">Chọn phim và đặt vé ngay hôm nay!</p>

            @if($movies->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($movies as $movie)
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:border-purple-500 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group shadow-md">
                            <a href="{{ route('booking.movie.detail', $movie->id) }}">
                                <!-- Movie Poster -->
                                <div class="relative overflow-hidden">
                                    @if($movie->poster)
                                        <img src="{{ $movie->poster }}" 
                                             alt="{{ $movie->title }}" 
                                             class="w-full h-96 object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-400 text-6xl">🎬</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Genre Badge -->
                                    @if($movie->genre)
                                        <div class="absolute top-3 left-3">
                                            <span class="bg-purple-600/90 backdrop-blur-sm text-white text-xs px-3 py-1 rounded-full font-semibold shadow-sm">
                                                {{ $movie->genre }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Movie Info -->
                                <div class="p-5">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-purple-600 transition-colors">
                                        {{ $movie->title }}
                                    </h3>
                                    
                                    <div class="text-gray-500 text-sm mb-4 flex items-center gap-2">
                                        @if($movie->duration_min)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $movie->duration_min }} phút
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Quick Showtimes -->
                                    @if($movie->showtimes && $movie->showtimes->count() > 0)
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            @foreach($movie->showtimes->take(3) as $showtime)
                                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded border border-gray-200 font-medium">
                                                    {{ \Carbon\Carbon::parse($showtime->start_at)->format('H:i') }}
                                                </span>
                                            @endforeach
                                            @if($movie->showtimes->count() > 3)
                                                <span class="text-xs text-gray-500 flex items-center">+{{ $movie->showtimes->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2.5 rounded-lg font-semibold transition-all shadow-md hover:shadow-lg">
                                        Đặt vé ngay
                                    </button>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-200 p-16 text-center shadow-sm">
                    <span class="text-6xl mb-4 block">🎬</span>
                    <p class="text-gray-500 text-xl">Hiện tại chưa có phim nào đang chiếu</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
