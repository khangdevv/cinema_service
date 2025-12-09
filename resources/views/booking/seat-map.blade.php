<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn ghế - {{ $showtime->movie->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('booking.movie.detail', $showtime->movie_id) }}" class="text-gray-500 hover:text-purple-600 text-2xl transition-colors">←</a>
                    <h1 class="text-2xl font-bold text-gray-900">🎬 Cinema Service</h1>
                </div>
                <div class="flex gap-4">
                    @auth('web')
                        <a href="{{ route('booking.index') }}" class="text-gray-600 hover:text-purple-600 font-medium">Đặt Vé</a>
                        <a href="{{ route('my.bookings') }}" class="text-gray-600 hover:text-purple-600 font-medium">Vé Của Tôi</a>
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-purple-600 font-medium">Cài Đặt</a>
                    @else
                        <a href="{{ route('auth.login.form') }}" class="text-gray-600 hover:text-purple-600 font-medium">Đăng nhập</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Movie Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8 shadow-md">
                <div class="flex items-center gap-6">
                    @if($showtime->movie->poster)
                        <img src="{{ $showtime->movie->poster }}" 
                             alt="{{ $showtime->movie->title }}" 
                             class="w-24 h-36 object-cover rounded-lg shadow-sm">
                    @endif
                    
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $showtime->movie->title }}</h1>
                        <div class="text-gray-600">
                            <p class="flex items-center gap-2 mb-1">
                                <span class="text-xl">🏛️</span> {{ $showtime->screen->name ?? 'Screen' }}
                            </p>
                            <p class="flex items-center gap-2">
                                <span class="text-xl">🕐</span> {{ \Carbon\Carbon::parse($showtime->start_at)->format('H:i, d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seat Map -->
            <div class="bg-white rounded-xl border border-gray-200 p-8 mb-8 shadow-md">
                <!-- Screen -->
                <div class="mb-12">
                    <div class="bg-gradient-to-b from-purple-600 to-transparent h-3 rounded-t-3xl mb-2 opacity-50"></div>
                    <p class="text-center text-gray-400 text-sm font-medium uppercase tracking-widest">Màn hình</p>
                </div>

                <!-- Seats -->
                <div class="flex justify-center overflow-x-auto pb-4">
                    <div class="inline-block min-w-max">
                        @foreach($seatsByRow as $row => $seats)
                            <div class="flex items-center gap-2 mb-3">
                                <!-- Row Label -->
                                <div class="w-8 text-center text-gray-500 font-bold">{{ $row }}</div>
                                
                                <!-- Seats in Row -->
                                <div class="flex gap-2">
                                    @foreach($seats as $seat)
                                        @php
                                            $isBooked = in_array($seat->id, $bookedSeats);
                                            $isLocked = in_array($seat->id, $lockedSeats);
                                            $isAvailable = !$isBooked && !$isLocked;
                                            
                                            $seatClass = 'seat w-10 h-10 rounded-t-lg flex items-center justify-center text-xs font-bold transition-all cursor-pointer shadow-sm ';
                                            
                                            if ($isBooked) {
                                                $seatClass .= 'bg-red-100 text-red-400 cursor-not-allowed border border-red-200';
                                            } elseif ($isLocked) {
                                                $seatClass .= 'bg-yellow-100 text-yellow-600 cursor-not-allowed border border-yellow-200';
                                            } else {
                                                $seatClass .= 'bg-gray-200 hover:bg-purple-600 hover:text-white text-gray-600 seat-available border border-gray-300 hover:border-purple-600';
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
                                <div class="w-8 text-center text-gray-500 font-bold">{{ $row }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex justify-center gap-8 mt-12 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gray-200 rounded-t-lg border border-gray-300"></div>
                        <span class="text-gray-600 text-sm">Trống</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-purple-600 rounded-t-lg border border-purple-600"></div>
                        <span class="text-gray-600 text-sm">Đang chọn</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-red-100 rounded-t-lg border border-red-200"></div>
                        <span class="text-gray-600 text-sm">Đã đặt</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-yellow-100 rounded-t-lg border border-yellow-200"></div>
                        <span class="text-gray-600 text-sm">Đang giữ</span>
                    </div>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-md sticky bottom-4 z-40">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Ghế đã chọn</h3>
                        <p class="text-gray-600" id="selected-seats">Chưa chọn ghế nào</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500 mb-1 text-sm">Tổng tiền</p>
                        <p class="text-3xl font-bold text-purple-600"><span id="total-price">0</span> đ</p>
                    </div>
                </div>
                <button id="continue-btn" disabled 
                        class="w-full mt-6 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white py-4 rounded-lg font-bold text-lg transition-all shadow-md hover:shadow-lg">
                    Tiếp tục
                </button>
            </div>
        </div>
    </div>

    <script>
        let totalPrice=0;
        let selectedSeats = [];
        const pricePerSeat = Number('{{ $showtime->base_price }}');

        function toggleSeat(element) {
            const seatId = element.getAttribute('data-seat-id');
            const seatNumber = element.getAttribute('data-seat-number');
            const row = element.getAttribute('data-row');
            const seatLabel = row + seatNumber;

            if (element.classList.contains('bg-purple-600')) {
                // Deselect
                element.classList.remove('bg-purple-600', 'text-white', 'border-purple-600');
                element.classList.add('bg-gray-200', 'text-gray-600', 'border-gray-300');
                selectedSeats = selectedSeats.filter(s => s.id !== seatId);
            } else {
                // Select
                element.classList.remove('bg-gray-200', 'text-gray-600', 'border-gray-300');
                element.classList.add('bg-purple-600', 'text-white', 'border-purple-600');
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
                totalPriceEl.textContent = '0';
                continueBtn.disabled = true;
            } else {
                const seatLabels = selectedSeats.map(s => s.label).join(', ');
                selectedSeatsEl.textContent = seatLabels;
                
                const total = selectedSeats.length * pricePerSeat;
                totalPrice = total;
                totalPriceEl.textContent = total.toLocaleString('vi-VN');
                
                continueBtn.disabled = false;
            }
        }

        document.getElementById('continue-btn').addEventListener('click', function() {
            if (selectedSeats.length === 0) return;
            
            // Tạo form để gửi dữ liệu đến trang checkout
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("payment.checkout") }}';
            
            // Thêm showtime_id
            const showtimeInput = document.createElement('input');
            showtimeInput.type = 'hidden';
            showtimeInput.name = 'showtime_id';
            showtimeInput.value = '{{ $showtime->id }}';
            form.appendChild(showtimeInput);
            
            // Thêm seat_ids
            selectedSeats.forEach(seat => {
                const seatInput = document.createElement('input');
                seatInput.type = 'hidden';
                seatInput.name = 'seat_ids[]';
                seatInput.value = seat.id;
                form.appendChild(seatInput);
            });

            const priceInput = document.createElement('input');
            priceInput.type = "hidden";
            priceInput.name="price";
            priceInput.value = totalPrice;
            form.appendChild(priceInput);            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        });
    </script>
</body>
</html>
