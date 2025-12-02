@extends('cinema.layouts.app')

@section('title', 'Select Seats - ' . $showtime->movie->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="seatSelection()">
    <!-- Movie Info Bar -->
    <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-6 mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white mb-2">{{ $showtime->movie->title }}</h1>
                <p class="text-gray-400">
                    {{ $showtime->room->theater->name }} - {{ $showtime->room->name }} |
                    {{ \Carbon\Carbon::parse($showtime->start_time)->format('l, F d, Y - h:i A') }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-gray-400 text-sm">Total Amount</p>
                <p class="text-3xl font-bold text-white" x-text="formatPrice(totalPrice)">0 VNĐ</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Seat Map -->
        <div class="lg:col-span-2">
            <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-8">
                <!-- Screen -->
                <div class="mb-8">
                    <div class="bg-gradient-to-b from-gray-300 to-gray-500 h-2 rounded-t-full mb-2"></div>
                    <p class="text-center text-gray-400 text-sm">SCREEN</p>
                </div>

                <!-- Seat Grid -->
                <div class="flex justify-center mb-8">
                    <div class="inline-block">
                        @foreach($seats as $row => $rowSeats)
                            <div class="flex items-center mb-2">
                                <!-- Row Label -->
                                <div class="w-8 text-center text-gray-400 font-bold mr-4">{{ $row }}</div>
                                
                                <!-- Seats -->
                                <div class="flex space-x-2">
                                    @foreach($rowSeats as $seat)
                                        @php
                                            $isBooked = in_array($seat->id, $bookedSeatIds);
                                            $seatClass = 'w-10 h-10 rounded-t-lg cursor-pointer transition-all transform hover:scale-110 flex items-center justify-center text-xs font-bold';
                                            
                                            if ($isBooked) {
                                                $seatClass .= ' bg-red-600 cursor-not-allowed opacity-50';
                                            } elseif ($seat->type === 'vip') {
                                                $seatClass .= ' bg-yellow-600 hover:bg-yellow-500';
                                            } else {
                                                $seatClass .= ' bg-gray-600 hover:bg-gray-500';
                                            }
                                        @endphp
                                        
                                        <button
                                            type="button"
                                            @if(!$isBooked)
                                                @click="toggleSeat({{ $seat->id }}, '{{ $seat->type }}', {{ $seat->type === 'vip' ? $showtime->vip_price : $showtime->price }})"
                                                :class="selectedSeats.includes({{ $seat->id }}) ? 'bg-green-500 hover:bg-green-400 scale-110' : '{{ $seat->type === 'vip' ? 'bg-yellow-600 hover:bg-yellow-500' : 'bg-gray-600 hover:bg-gray-500' }}'"
                                            @endif
                                            class="{{ $seatClass }}"
                                            {{ $isBooked ? 'disabled' : '' }}
                                            title="{{ $isBooked ? 'Booked' : ($seat->type === 'vip' ? 'VIP - ' . number_format($showtime->vip_price, 0, ',', '.') . ' VNĐ' : 'Standard - ' . number_format($showtime->price, 0, ',', '.') . ' VNĐ') }}"
                                        >
                                            {{ $seat->seat_number }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex justify-center space-x-6 text-sm">
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-gray-600 rounded-t-lg mr-2"></div>
                        <span class="text-gray-300">Available</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-yellow-600 rounded-t-lg mr-2"></div>
                        <span class="text-gray-300">VIP</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-green-500 rounded-t-lg mr-2"></div>
                        <span class="text-gray-300">Selected</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-red-600 rounded-t-lg mr-2 opacity-50"></div>
                        <span class="text-gray-300">Booked</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="lg:col-span-1">
            <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-6 sticky top-8">
                <h2 class="text-xl font-bold text-white mb-6">Booking Summary</h2>

                <!-- Selected Seats -->
                <div class="mb-6">
                    <h3 class="text-gray-400 text-sm mb-2">Selected Seats</h3>
                    <div class="bg-gray-900/50 rounded-lg p-4 min-h-[100px]">
                        <template x-if="selectedSeatsDetails.length === 0">
                            <p class="text-gray-500 text-center">No seats selected</p>
                        </template>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="seat in selectedSeatsDetails" :key="seat.id">
                                <div class="flex items-center space-x-2 bg-gray-800 px-3 py-1 rounded-lg">
                                    <span class="text-white font-semibold" x-text="getSeatLabel(seat.id)"></span>
                                    <span class="text-xs" :class="seat.type === 'vip' ? 'text-yellow-400' : 'text-gray-400'" x-text="seat.type === 'vip' ? 'VIP' : 'STD'"></span>
                                    <button @click="toggleSeat(seat.id, seat.type, seat.price)" class="text-red-400 hover:text-red-300">✕</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="mb-6 space-y-2">
                    <div class="flex justify-between text-gray-300">
                        <span>Standard Seats (<span x-text="selectedSeatsDetails.filter(s => s.type === 'standard').length">0</span>)</span>
                        <span x-text="formatPrice(selectedSeatsDetails.filter(s => s.type === 'standard').reduce((sum, s) => sum + s.price, 0))">0 VNĐ</span>
                    </div>
                    <div class="flex justify-between text-yellow-400">
                        <span>VIP Seats (<span x-text="selectedSeatsDetails.filter(s => s.type === 'vip').length">0</span>)</span>
                        <span x-text="formatPrice(selectedSeatsDetails.filter(s => s.type === 'vip').reduce((sum, s) => sum + s.price, 0))">0 VNĐ</span>
                    </div>
                    <div class="border-t border-gray-700 pt-2 flex justify-between text-white font-bold text-lg">
                        <span>Total</span>
                        <span x-text="formatPrice(totalPrice)">0 VNĐ</span>
                    </div>
                </div>

                <!-- Booking Form -->
                <form method="POST" action="{{ route('bookings.store') }}" @submit="return validateForm()">
                    @csrf
                    <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">
                    <input type="hidden" name="seat_ids" x-model="selectedSeatsInput">
                    
                    <button type="submit" 
                            :disabled="selectedSeats.length === 0"
                            :class="selectedSeats.length === 0 ? 'bg-gray-700 cursor-not-allowed' : 'bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700'"
                            class="w-full py-3 rounded-lg font-semibold transition-all mb-4">
                        Confirm Booking (<span x-text="selectedSeats.length">0</span> seats)
                    </button>
                </form>

                <a href="{{ route('movies.show', $showtime->movie->id) }}" class="block w-full text-center py-3 border border-gray-700 rounded-lg font-semibold hover:bg-gray-800 transition-all">
                    Back to Movie
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function seatSelection() {
    return {
        selectedSeats: [],
        selectedSeatsDetails: [],
        seatMap: {!! json_encode(collect($seats)->flatten(1)->keyBy('id')->map(function($seat) {
            return [
                'id' => $seat->id,
                'row' => $seat->row_label,
                'number' => $seat->seat_number,
                'type' => $seat->type
            ];
        })) !!},
        
        get totalPrice() {
            return this.selectedSeatsDetails.reduce((sum, seat) => sum + seat.price, 0);
        },
        
        get selectedSeatsInput() {
            return JSON.stringify(this.selectedSeats);
        },
        
        toggleSeat(seatId, type, price) {
            const index = this.selectedSeats.indexOf(seatId);
            
            if (index > -1) {
                // Remove seat
                this.selectedSeats.splice(index, 1);
                this.selectedSeatsDetails.splice(index, 1);
            } else {
                // Add seat
                this.selectedSeats.push(seatId);
                this.selectedSeatsDetails.push({
                    id: seatId,
                    type: type,
                    price: price
                });
            }
        },
        
        getSeatLabel(seatId) {
            const seat = this.seatMap[seatId];
            return seat ? `${seat.row}${seat.number}` : '';
        },
        
        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ';
        },
        
        validateForm() {
            if (this.selectedSeats.length === 0) {
                alert('Please select at least one seat');
                return false;
            }
            return true;
        }
    }
}
</script>
@endpush
@endsection
