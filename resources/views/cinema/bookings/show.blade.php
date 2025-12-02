@extends('cinema.layouts.app')

@section('title', 'Booking Confirmation')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Success Message -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-500/20 rounded-full mb-4">
            <span class="text-5xl">✓</span>
        </div>
        <h1 class="text-4xl font-bold text-white mb-2">Booking Successful!</h1>
        <p class="text-gray-400">Your tickets have been confirmed</p>
    </div>

    <!-- Booking Details -->
    <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 overflow-hidden mb-8">
        <!-- Booking Code Banner -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 text-center">
            <p class="text-sm text-white/80 mb-2">Booking Code</p>
            <p class="text-3xl font-bold text-white tracking-wider">{{ $booking->booking_code }}</p>
        </div>

        <!-- Movie Information -->
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    @if($booking->showtime->movie->poster_url)
                        <img src="{{ $booking->showtime->movie->poster_url }}" alt="{{ $booking->showtime->movie->title }}" class="w-full rounded-lg shadow-lg">
                    @else
                        <div class="w-full h-64 bg-gradient-to-br from-purple-900/50 to-pink-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-6xl">🎬</span>
                        </div>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <h2 class="text-2xl font-bold text-white mb-4">{{ $booking->showtime->movie->title }}</h2>
                    
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <span class="text-gray-400 w-32">Theater:</span>
                            <span class="text-white font-semibold">{{ $booking->showtime->room->theater->name }}</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-gray-400 w-32">Room:</span>
                            <span class="text-white font-semibold">{{ $booking->showtime->room->name }}</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-gray-400 w-32">Date & Time:</span>
                            <span class="text-white font-semibold">
                                {{ \Carbon\Carbon::parse($booking->showtime->start_time)->format('l, F d, Y - h:i A') }}
                            </span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-gray-400 w-32">Seats:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($booking->bookingDetails as $detail)
                                    <span class="px-3 py-1 rounded-lg font-semibold
                                        {{ $detail->seat->type === 'vip' ? 'bg-yellow-600' : 'bg-gray-700' }}">
                                        {{ $detail->seat->row_label }}{{ $detail->seat->seat_number }}
                                        @if($detail->seat->type === 'vip')
                                            <span class="text-xs ml-1">(VIP)</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="border-t border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-white mb-4">Payment Summary</h3>
                <div class="space-y-2 mb-4">
                    @php
                        $standardSeats = $booking->bookingDetails->where('seat.type', 'standard');
                        $vipSeats = $booking->bookingDetails->where('seat.type', 'vip');
                    @endphp
                    
                    @if($standardSeats->count() > 0)
                        <div class="flex justify-between text-gray-300">
                            <span>Standard Seats ({{ $standardSeats->count() }})</span>
                            <span>{{ number_format($standardSeats->sum('price'), 0, ',', '.') }} VNĐ</span>
                        </div>
                    @endif
                    
                    @if($vipSeats->count() > 0)
                        <div class="flex justify-between text-yellow-400">
                            <span>VIP Seats ({{ $vipSeats->count() }})</span>
                            <span>{{ number_format($vipSeats->sum('price'), 0, ',', '.') }} VNĐ</span>
                        </div>
                    @endif
                </div>
                
                <div class="border-t border-gray-700 pt-4 flex justify-between text-white font-bold text-xl">
                    <span>Total Amount</span>
                    <span>{{ number_format($booking->total_price, 0, ',', '.') }} VNĐ</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Information -->
    <div class="bg-yellow-500/10 border border-yellow-500/50 rounded-lg p-6 mb-8">
        <h3 class="text-yellow-500 font-semibold mb-3 flex items-center">
            <span class="text-2xl mr-2">⚠️</span>
            Important Information
        </h3>
        <ul class="text-gray-300 space-y-2 ml-8">
            <li>Please arrive at least 15 minutes before showtime</li>
            <li>Present your booking code at the counter to collect tickets</li>
            <li>This booking code: <strong class="text-white">{{ $booking->booking_code }}</strong></li>
            <li>Save this page or take a screenshot for reference</li>
        </ul>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('bookings.my-tickets') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-8 py-3 rounded-lg font-semibold text-center transition-all">
            View My Tickets
        </a>
        <a href="{{ route('home') }}" class="border border-gray-700 hover:bg-gray-800 px-8 py-3 rounded-lg font-semibold text-center transition-all">
            Back to Home
        </a>
    </div>
</div>
@endsection
