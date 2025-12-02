<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn ghế - {{ $showtime->movie->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 min-h-screen">
    <!-- Header -->
    <nav class="bg-black/40 backdrop-blur-lg border-b border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('booking.movie.detail', $showtime->movie_id) }}" class="text-gray-300 hover:text-white">← Quay lại</a>
                    <h1 class="text-2xl font-bold text-white">🎬 Cinema Service</h1>
                </div>
                <div class="flex gap-4">
                    @auth('web')
                        <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white">Dashboard</a>
                    @else
                        <a href="{{ route('auth.login.form') }}" class="text-gray-300 hover:text-white">Đăng nhập</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Movie Info -->
            <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-6 mb-8">
                <div class="flex items-center gap-6">
                    @if($showtime->movie->poster)
                        <img src="{{ $showtime->movie->poster }}" 
                             alt="{{ $showtime->movie->title }}" 
                             class="w-24 h-36 object-cover rounded-lg">
                    @endif
                    
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $showtime->movie->title }}</h1>
                        <div class="text-gray-300">
                            <p>🏛️ {{ $showtime->screen->name ?? 'Screen' }}</p>
                            <p>🕐 {{ \Carbon\Carbon::parse($showtime->start_at)->format('H:i, d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seat Map -->
            <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-8 mb-8">
                <!-- Screen -->
                <div class="mb-12">
                    <div class="bg-gradient-to-b from-purple-600 to-transparent h-3 rounded-t-3xl mb-2"></div>
                    <p class="text-center text-gray-400 text-sm">Màn hình</p>
                </div>

                <!-- Seats -->
                <div class="flex justify-center">
                    <div class="inline-block">
                        @foreach($seatsByRow as $row => $seats)
                            <div class="flex items-center gap-2 mb-3">
                                <!-- Row Label -->
                                <div class="w-8 text-center text-white font-bold">{{ $row }}</div>
                                
                                <!-- Seats in Row -->
                                <div class="flex gap-2">
                                    @foreach($seats as $seat)
                                        @php
                                            $isBooked = in_array($seat->id, $bookedSeats);
                                            $isLocked = in_array($seat->id, $lockedSeats);
                                            $isAvailable = !$isBooked && !$isLocked;
                                            
                                            $seatClass = 'seat w-10 h-10 rounded-t-lg flex items-center justify-center text-xs font-bold transition-all cursor-pointer ';
                                            
                                            if ($isBooked) {
                                                $seatClass .= 'bg-red-600 text-white cursor-not-allowed opacity-50';
                                            } elseif ($isLocked) {
                                                $seatClass .= 'bg-yellow-600 text-white cursor-not-allowed opacity-50';
                                            } else {
                                                $seatClass .= 'bg-gray-700 hover:bg-purple-600 text-white seat-available';
                                            }
                                        @endphp
                                        
                                        <div class="{{ $seatClass }}" 
                                             data-seat-id="{{ $seat->id }}"
                                             data-seat-number="{{ $seat->seat_number }}"
                                             data-row="{{ $row }}"
                                             @if($isAvailable)
                                                onclick="toggleSeat(this)"
                                             @endif>
                                            {{ $seat->seat_number }}
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Row Label (right side) -->
                                <div class="w-8 text-center text-white font-bold">{{ $row }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex justify-center gap-8 mt-12">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gray-700 rounded-t-lg"></div>
                        <span class="text-gray-300">Trống</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-purple-600 rounded-t-lg"></div>
                        <span class="text-gray-300">Đang chọn</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-red-600 rounded-t-lg opacity-50"></div>
                        <span class="text-gray-300">Đã đặt</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-yellow-600 rounded-t-lg opacity-50"></div>
                        <span class="text-gray-300">Đang giữ</span>
                    </div>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">Ghế đã chọn</h3>
                        <p class="text-gray-300" id="selected-seats">Chưa chọn ghế nào</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-400 mb-2">Tổng tiền</p>
                        <p class="text-3xl font-bold text-purple-400" id="total-price">0 đ</p>
                    </div>
                </div>
                <button id="continue-btn" disabled 
                        class="w-full mt-6 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 disabled:from-gray-600 disabled:to-gray-700 disabled:cursor-not-allowed text-white py-4 rounded-lg font-bold text-lg transition-all">
                    Tiếp tục
                </button>
            </div>
        </div>
    </div>

    <script>
        let selectedSeats = [];
        const pricePerSeat = 100000; // 100k VND per seat

        function toggleSeat(element) {
            const seatId = element.getAttribute('data-seat-id');
            const seatNumber = element.getAttribute('data-seat-number');
            const row = element.getAttribute('data-row');
            const seatLabel = row + seatNumber;

            if (element.classList.contains('bg-purple-600')) {
                // Deselect
                element.classList.remove('bg-purple-600');
                element.classList.add('bg-gray-700');
                selectedSeats = selectedSeats.filter(s => s.id !== seatId);
            } else {
                // Select
                element.classList.remove('bg-gray-700');
                element.classList.add('bg-purple-600');
                selectedSeats.push({
                    id: seatId,
                    label: seatLabel
                });
            }

            updateSummary();
        }

        function updateSummary() {
            const continueBtn = document.getElementById('continue-btn');
            const selectedSeatsEl = document.getElementById('selected-seats');
            const totalPriceEl = document.getElementById('total-price');

            if (selectedSeats.length === 0) {
                selectedSeatsEl.textContent = 'Chưa chọn ghế nào';
                totalPriceEl.textContent = '0 đ';
                continueBtn.disabled = true;
            } else {
                const seatLabels = selectedSeats.map(s => s.label).join(', ');
                selectedSeatsEl.textContent = seatLabels;
                
                const total = selectedSeats.length * pricePerSeat;
                totalPriceEl.textContent = total.toLocaleString('vi-VN') + ' đ';
                
                continueBtn.disabled = false;
            }
        }

        document.getElementById('continue-btn').addEventListener('click', function() {
            if (selectedSeats.length === 0) return;
            
            alert('Chức năng thanh toán đang được phát triển!\nGhế đã chọn: ' + selectedSeats.map(s => s.label).join(', '));
        });
    </script>
</body>
</html>
