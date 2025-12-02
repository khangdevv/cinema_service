@extends('cinema.layouts.app')

@section('title', 'Home - Cinema Booking')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="text-center mb-12">
        <h1 class="text-5xl font-bold mb-4 bg-gradient-to-r from-purple-400 to-pink-600 bg-clip-text text-transparent">
            Book Your Movie Experience
        </h1>
        <p class="text-xl text-gray-300">
            Choose from the latest releases and upcoming blockbusters
        </p>
    </div>

    <!-- Now Showing -->
    <section class="mb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-white">Now Showing</h2>
            <div class="h-1 flex-1 bg-gradient-to-r from-purple-600 to-transparent ml-6"></div>
        </div>
        
        @if($nowShowing->isEmpty())
            <p class="text-gray-400 text-center py-8">No movies currently showing</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($nowShowing as $movie)
                    <div class="bg-black/40 backdrop-blur-lg rounded-xl overflow-hidden border border-gray-800 hover:border-purple-500 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/50">
                        <div class="relative h-96">
                            @if($movie->poster_url)
                                <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-purple-900/50 to-pink-900/50 flex items-center justify-center">
                                    <span class="text-6xl">🎬</span>
                                </div>
                            @endif
                            <div class="absolute top-4 right-4 bg-yellow-500 text-black px-3 py-1 rounded-full font-bold">
                                ⭐ {{ number_format($movie->rating, 1) }}
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-2xl font-bold mb-2 text-white">{{ $movie->title }}</h3>
                            <p class="text-gray-400 mb-2">{{ $movie->duration }} phút</p>
                            <p class="text-gray-300 mb-4 line-clamp-2">{{ $movie->description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-purple-400">{{ $movie->genre }}</span>
                                <a href="{{ route('movies.show', $movie->id) }}" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-6 py-2 rounded-lg font-semibold transition-all">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Coming Soon -->
    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-white">Coming Soon</h2>
            <div class="h-1 flex-1 bg-gradient-to-r from-pink-600 to-transparent ml-6"></div>
        </div>
        
        @if($comingSoon->isEmpty())
            <p class="text-gray-400 text-center py-8">No upcoming movies</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($comingSoon as $movie)
                    <div class="bg-black/40 backdrop-blur-lg rounded-xl overflow-hidden border border-gray-800 hover:border-pink-500 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-pink-500/50">
                        <div class="relative h-96">
                            @if($movie->poster_url)
                                <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-pink-900/50 to-purple-900/50 flex items-center justify-center">
                                    <span class="text-6xl">🎬</span>
                                </div>
                            @endif
                            <div class="absolute top-4 left-4 bg-pink-600 text-white px-3 py-1 rounded-full font-bold">
                                Coming Soon
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-2xl font-bold mb-2 text-white">{{ $movie->title }}</h3>
                            <p class="text-gray-400 mb-2">{{ $movie->duration }} phút</p>
                            <p class="text-gray-300 mb-4 line-clamp-2">{{ $movie->description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-pink-400">{{ $movie->genre }}</span>
                                <span class="text-gray-500 px-6 py-2 rounded-lg font-semibold border border-gray-700">
                                    Not Available
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
