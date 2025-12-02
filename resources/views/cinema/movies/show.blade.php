@extends('cinema.layouts.app')

@section('title', $movie->title . ' - Cinema Booking')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Movie Details -->
    <div class="bg-black/40 backdrop-blur-lg rounded-xl overflow-hidden border border-gray-800 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 p-8">
            <!-- Poster -->
            <div class="col-span-1">
                @if($movie->poster_url)
                    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="w-full rounded-lg shadow-2xl">
                @else
                    <div class="w-full h-96 bg-gradient-to-br from-purple-900/50 to-pink-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-9xl">🎬</span>
                    </div>
                @endif
            </div>

            <!-- Information -->
            <div class="col-span-2">
                <h1 class="text-4xl font-bold mb-4 text-white">{{ $movie->title }}</h1>
                
                <div class="flex items-center space-x-6 mb-6">
                    <div class="flex items-center">
                        <span class="text-yellow-500 text-2xl mr-2">⭐</span>
                        <span class="text-xl font-bold text-white">{{ number_format($movie->rating, 1) }}</span>
                    </div>
                    <div class="text-gray-300">
                        <span class="font-semibold">Duration:</span> {{ $movie->duration }} phút
                    </div>
                    <div class="text-gray-300">
                        <span class="font-semibold">Genre:</span> {{ $movie->genre }}
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-2 text-white">Release Date</h3>
                    <p class="text-gray-300">{{ \Carbon\Carbon::parse($movie->release_date)->format('F d, Y') }}</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-2 text-white">Synopsis</h3>
                    <p class="text-gray-300 leading-relaxed">{{ $movie->description }}</p>
                </div>

                @if($movie->trailer_url)
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold mb-2 text-white">Trailer</h3>
                        <div class="aspect-video rounded-lg overflow-hidden">
                            <iframe 
                                width="100%" 
                                height="100%" 
                                src="{{ $movie->trailer_url }}" 
                                title="Movie Trailer" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                class="w-full h-full"
                            ></iframe>
                        </div>
                    </div>
                @endif

                <div class="flex items-center space-x-4">
                    <span class="px-4 py-2 rounded-lg text-sm font-semibold
                        {{ $movie->status === 'now_showing' ? 'bg-green-600' : 'bg-yellow-600' }}">
                        {{ $movie->status === 'now_showing' ? 'Now Showing' : 'Coming Soon' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Showtimes -->
    @if($movie->status === 'now_showing')
        <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-8">
            <h2 class="text-3xl font-bold mb-6 text-white">Select Showtime</h2>
            
            @if($showtimesByDate->isEmpty())
                <p class="text-gray-400 text-center py-8">No showtimes available at the moment</p>
            @else
                @foreach($showtimesByDate as $date => $showtimes)
                    <div class="mb-8 last:mb-0">
                        <h3 class="text-xl font-semibold mb-4 text-purple-400">
                            {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}
                        </h3>
                        
                        <div class="space-y-4">
                            @foreach($showtimes as $showtime)
                                <div class="bg-gray-900/50 rounded-lg p-4 border border-gray-700 hover:border-purple-500 transition-all">
                                    <div class="flex items-center justify-between flex-wrap gap-4">
                                        <div>
                                            <p class="text-white font-semibold text-lg">
                                                {{ \Carbon\Carbon::parse($showtime->start_time)->format('h:i A') }}
                                            </p>
                                            <p class="text-gray-400 text-sm">
                                                {{ $showtime->room->theater->name }} - {{ $showtime->room->name }}
                                            </p>
                                        </div>
                                        
                                        <div class="flex items-center space-x-6">
                                            <div class="text-right">
                                                <p class="text-gray-400 text-sm">Standard</p>
                                                <p class="text-white font-bold">{{ number_format($showtime->price, 0, ',', '.') }} VNĐ</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-yellow-400 text-sm">VIP</p>
                                                <p class="text-white font-bold">{{ number_format($showtime->vip_price, 0, ',', '.') }} VNĐ</p>
                                            </div>
                                            
                                            @auth
                                                <a href="{{ route('bookings.select-seats', $showtime->id) }}" 
                                                   class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-6 py-2 rounded-lg font-semibold transition-all">
                                                    Book Seats
                                                </a>
                                            @else
                                                <a href="{{ route('login') }}" 
                                                   class="bg-gray-700 hover:bg-gray-600 px-6 py-2 rounded-lg font-semibold transition-all">
                                                    Login to Book
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @else
        <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-8 text-center">
            <p class="text-gray-400 text-xl">This movie is coming soon. Showtimes will be available closer to the release date.</p>
        </div>
    @endif
</div>
@endsection
