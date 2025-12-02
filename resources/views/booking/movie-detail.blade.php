<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $movie->title }} - Cinema Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 min-h-screen">
    <!-- Header -->
    <nav class="bg-black/40 backdrop-blur-lg border-b border-gray-800 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('booking.index') }}" class="text-gray-300 hover:text-white text-2xl">←</a>
                    <div class="flex items-center gap-2">
                        <span class="text-3xl">🎬</span>
                        <h1 class="text-2xl font-bold text-white">Galaxy Cinema</h1>
                    </div>
                </div>
                <div class="flex gap-4 items-center">
                    <span class="text-gray-300">{{ auth()->user()->full_name }}</span>
                    <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-400 hover:text-red-300">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Movie Hero Section -->
            <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 overflow-hidden mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 p-8">
                    <!-- Poster -->
                    <div class="col-span-1">
                        @if($movie->poster)
                            <img src="{{ $movie->poster }}" 
                                 alt="{{ $movie->title }}" 
                                 class="w-full rounded-xl shadow-2xl">
                        @else
                            <div class="w-full aspect-[2/3] bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl flex items-center justify-center">
                                <span class="text-gray-600 text-8xl">🎬</span>
                            </div>
                        @endif
                    </div>

                    <!-- Movie Details -->
                    <div class="col-span-2">
                        <h1 class="text-4xl font-bold text-white mb-4">{{ $movie->title }}</h1>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            @if($movie->genre)
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">Thể loại</p>
                                    <span class="bg-purple-600 text-white px-4 py-2 rounded-full inline-block">
                                        {{ $movie->genre }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($movie->duration_minutes)
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">Thời lượng</p>
                                    <p class="text-white font-semibold text-lg">⏱️ {{ $movie->duration_minutes }} phút</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Showtimes -->
            <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-8">
                <h2 class="text-3xl font-bold text-white mb-6">⏰ Chọn Suất Chiếu</h2>

                @if($showtimesByDate->count() > 0)
                    <div class="space-y-8">
                        @foreach($showtimesByDate as $date => $showtimes)
                            <div>
                                <h3 class="text-xl font-semibold text-purple-400 mb-4 flex items-center gap-2">
                                    <span>📅</span>
                                    <span>
                                        {{ \Carbon\Carbon::parse($date)->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}
                                    </span>
                                </h3>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                    @foreach($showtimes as $showtime)
                                        <a href="{{ route('booking.seat-map', $showtime->id) }}" 
                                           class="bg-gray-800 hover:bg-purple-600 border border-gray-700 hover:border-purple-500 rounded-xl p-4 text-center transition-all group">
                                            <p class="text-3xl font-bold text-white mb-2">
                                                {{ \Carbon\Carbon::parse($showtime->start_at)->format('H:i') }}
                                            </p>
                                            <p class="text-sm text-gray-400 group-hover:text-white mb-2">
                                                🏛️ {{ $showtime->screen->name ?? 'Screen' }}
                                            </p>
                                            <p class="text-lg text-purple-400 font-bold">
                                                {{ number_format($showtime->base_price ?? 100000) }}đ
                                            </p>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <span class="text-6xl mb-4 block">😔</span>
                        <p class="text-gray-400 text-xl">Hiện tại chưa có lịch chiếu cho phim này</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
