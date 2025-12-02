<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Service - Đặt vé xem phim</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 min-h-screen">
    <!-- Header -->
    <nav class="bg-black/40 backdrop-blur-lg border-b border-gray-800 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="text-3xl">🎬</span>
                    <h1 class="text-2xl font-bold text-white">Galaxy Cinema</h1>
                </div>
                <div class="flex gap-4 items-center">
                    <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white">Dashboard</a>
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-300">{{ auth()->user()->full_name }}</span>
                    <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-all">
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-bold text-white mb-2">🎬 Phim Đang Chiếu</h2>
            <p class="text-gray-400 mb-8">Chọn phim và đặt vé ngay hôm nay!</p>

            @if($movies->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($movies as $movie)
                        <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 overflow-hidden hover:border-purple-500 hover:scale-105 transition-all group">
                            <a href="{{ route('booking.movie.detail', $movie->id) }}">
                                <!-- Movie Poster -->
                                <div class="relative overflow-hidden">
                                    @if($movie->poster)
                                        <img src="{{ $movie->poster }}" 
                                             alt="{{ $movie->title }}" 
                                             class="w-full h-96 object-cover group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-96 bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                                            <span class="text-gray-600 text-6xl">🎬</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Genre Badge -->
                                    @if($movie->genre)
                                        <div class="absolute top-3 left-3">
                                            <span class="bg-purple-600 text-white text-xs px-3 py-1 rounded-full font-semibold">
                                                {{ $movie->genre }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Movie Info -->
                                <div class="p-4">
                                    <h3 class="text-xl font-bold text-white mb-2 line-clamp-2 group-hover:text-purple-400 transition-colors">
                                        {{ $movie->title }}
                                    </h3>
                                    
                                    <div class="text-gray-400 text-sm mb-3">
                                        @if($movie->duration_min)
                                            <p>⏱️ {{ $movie->duration_min }} phút</p>
                                        @endif
                                    </div>

                                    <!-- Quick Showtimes -->
                                    @if($movie->showtimes && $movie->showtimes->count() > 0)
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            @foreach($movie->showtimes->take(3) as $showtime)
                                                <span class="text-xs bg-gray-800 text-purple-300 px-2 py-1 rounded border border-gray-700">
                                                    {{ \Carbon\Carbon::parse($showtime->start_at)->format('H:i') }}
                                                </span>
                                            @endforeach
                                            @if($movie->showtimes->count() > 3)
                                                <span class="text-xs text-gray-500">+{{ $movie->showtimes->count() - 3 }} suất</span>
                                            @endif
                                        </div>
                                    @endif

                                    <button class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-2 rounded-lg font-semibold transition-all">
                                        Đặt vé ngay
                                    </button>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-16 text-center">
                    <span class="text-6xl mb-4 block">🎬</span>
                    <p class="text-gray-400 text-xl">Hiện tại chưa có phim nào đang chiếu</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
