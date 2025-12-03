<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Cinema Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl border border-gray-200 p-8 mb-6 shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">🎬 Dashboard</h1>
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-all shadow-md hover:shadow-lg">
                            Logout
                        </button>
                    </form>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-500 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif
                
                <div class="bg-gray-50 p-6 rounded-lg mb-6 border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Welcome, {{ $user->full_name }}!</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600">
                        <div>
                            <p class="text-gray-500 text-sm">Email:</p>
                            <p class="font-semibold text-gray-900">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Phone:</p>
                            <p class="font-semibold text-gray-900">{{ $user->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Role:</p>
                            <p class="font-semibold">
                                <span class="px-3 py-1 rounded-full text-sm text-white {{ $user->role === 'ADMIN' ? 'bg-purple-600' : 'bg-blue-600' }}">
                                    {{ $user->role }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Quick Access</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('booking.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg text-center transition-all shadow-md hover:shadow-lg font-semibold">
                            🎬 Xem Phim
                        </a>
                        <a href="{{ route('booking.index') }}" class="bg-pink-600 hover:bg-pink-700 text-white p-4 rounded-lg text-center transition-all shadow-md hover:shadow-lg font-semibold">
                            🎬 Đặt Vé
                        </a>
                        <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-lg text-center transition-all shadow-md hover:shadow-lg font-semibold">
                            🎟️ My Bookings
                        </a>
                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition-all shadow-md hover:shadow-lg font-semibold">
                            👤 Profile Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
