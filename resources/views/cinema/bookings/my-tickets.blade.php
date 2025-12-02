@extends('cinema.layouts.app')

@section('title', 'My Tickets')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">My Tickets</h1>
        <p class="text-gray-400">View and manage your movie bookings</p>
    </div>

    @if($bookings->isEmpty())
        <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-12 text-center">
            <span class="text-6xl mb-4 block">🎟️</span>
            <h3 class="text-2xl font-semibold text-white mb-2">No Bookings Yet</h3>
            <p class="text-gray-400 mb-6">You haven't booked any tickets yet</p>
            <a href="{{ route('home') }}" class="inline-block bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-8 py-3 rounded-lg font-semibold transition-all">
                Browse Movies
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($bookings as $booking)
                <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 overflow-hidden hover:border-purple-500 transition-all">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6">
                        <!-- Movie Poster -->
                        <div class="col-span-1">
                            @if($booking->showtime->movie->poster_url)
                                <img src="{{ $booking->showtime->movie->poster_url }}" alt="{{ $booking->showtime->movie->title }}" class="w-full rounded-lg shadow-lg">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-purple-900/50 to-pink-900/50 rounded-lg flex items-center justify-center">
                                    <span class="text-5xl">🎬</span>
                                </div>
                            @endif
                        </div>

                        <!-- Booking Details -->
                        <div class="col-span-2">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-2xl font-bold text-white">{{ $booking->showtime->movie->title }}</h3>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if($booking->status === 'confirmed') bg-green-600
                                    @elseif($booking->status === 'cancelled') bg-red-600
                                    @else bg-yellow-600
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>

                            <div class="space-y-2 text-sm mb-4">
                                <div class="flex items-start">
                                    <span class="text-gray-400 w-32">Booking Code:</span>
                                    <span class="text-white font-mono font-bold">{{ $booking->booking_code }}</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-400 w-32">Theater:</span>
                                    <span class="text-white">{{ $booking->showtime->room->theater->name }}</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-400 w-32">Room:</span>
                                    <span class="text-white">{{ $booking->showtime->room->name }}</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-400 w-32">Showtime:</span>
                                    <span class="text-white">
                                        {{ \Carbon\Carbon::parse($booking->showtime->start_time)->format('M d, Y - h:i A') }}
                                    </span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-400 w-32">Seats:</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($booking->bookingDetails as $detail)
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                                {{ $detail->seat->type === 'vip' ? 'bg-yellow-600' : 'bg-gray-700' }}">
                                                {{ $detail->seat->row_label }}{{ $detail->seat->seat_number }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-gray-400 w-32">Booked on:</span>
                                    <span class="text-white">{{ $booking->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>

                            @php
                                $showtime = \Carbon\Carbon::parse($booking->showtime->start_time);
                                $isPast = $showtime->isPast();
                                $isUpcoming = $showtime->isFuture();
                            @endphp

                            @if($isPast)
                                <div class="bg-gray-800/50 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-400">
                                    <span class="mr-2">⏰</span> This show has ended
                                </div>
                            @elseif($isUpcoming && $booking->status === 'confirmed')
                                <div class="bg-green-600/20 border border-green-600 rounded-lg px-3 py-2 text-sm text-green-400">
                                    <span class="mr-2">✓</span> Show in {{ $showtime->diffForHumans() }}
                                </div>
                            @endif
                        </div>

                        <!-- Price & Actions -->
                        <div class="col-span-1 flex flex-col justify-between">
                            <div class="text-right">
                                <p class="text-gray-400 text-sm mb-1">Total Amount</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ number_format($booking->total_price, 0, ',', '.') }} VNĐ
                                </p>
                                <p class="text-gray-400 text-xs mt-1">
                                    {{ $booking->bookingDetails->count() }} seat(s)
                                </p>
                            </div>

                            <div class="space-y-2 mt-4">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-center px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                                    View Details
                                </a>
                                
                                @if($booking->status === 'confirmed' && $isUpcoming)
                                    <button onclick="if(confirm('Are you sure you want to cancel this booking?')) { document.getElementById('cancel-form-{{ $booking->id }}').submit(); }" 
                                            class="block w-full bg-red-600/20 hover:bg-red-600/30 border border-red-600 text-center px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                                        Cancel Booking
                                    </button>
                                    <form id="cancel-form-{{ $booking->id }}" action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="mt-8">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
