<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Service - Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 min-h-screen">
    <div class="container mx-auto py-16">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-6xl font-bold text-white mb-6">
                🎬 Cinema Service
            </h1>
            <p class="text-2xl text-gray-300 mb-12">
                Welcome to Cinema Booking System
            </p>

            <div class="flex justify-center gap-4 mb-12">
                <a href="{{ route('auth.login.form') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-3 rounded-lg font-semibold transition-all">
                    Login
                </a>
                <a href="{{ route('auth.register.form') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-3 rounded-lg font-semibold transition-all">
                    Register
                </a>
            </div>
            
            <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-8 mb-8">
                <h2 class="text-3xl font-bold text-white mb-6">API Endpoints</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                    <div class="bg-gray-900 p-4 rounded-lg">
                        <h3 class="text-purple-400 font-semibold mb-2">Authentication</h3>
                        <ul class="text-gray-300 text-sm space-y-1">
                            <li>POST /api/login</li>
                            <li>POST /api/register</li>
                            <li>POST /api/logout</li>
                        </ul>
                    </div>
                    <div class="bg-gray-900 p-4 rounded-lg">
                        <h3 class="text-purple-400 font-semibold mb-2">Movies</h3>
                        <ul class="text-gray-300 text-sm space-y-1">
                            <li>GET /api/movies</li>
                            <li>GET /api/movies/{id}</li>
                        </ul>
                    </div>
                    <div class="bg-gray-900 p-4 rounded-lg">
                        <h3 class="text-purple-400 font-semibold mb-2">Screens</h3>
                        <ul class="text-gray-300 text-sm space-y-1">
                            <li>GET /api/screens</li>
                            <li>GET /api/screens/{id}</li>
                        </ul>
                    </div>
                    <div class="bg-gray-900 p-4 rounded-lg">
                        <h3 class="text-purple-400 font-semibold mb-2">Showtimes</h3>
                        <ul class="text-gray-300 text-sm space-y-1">
                            <li>GET /api/showtimes</li>
                            <li>GET /api/showtimes/{id}/seat-map</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 rounded-xl">
                <p class="text-white text-lg mb-4">
                    ✅ Application is running successfully!
                </p>
                <div class="flex gap-4 justify-center">
                    <a href="{{ route('auth.login.form') }}" class="bg-white text-purple-600 px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transition-all">
                        Login
                    </a>
                    <a href="{{ route('auth.register.form') }}" class="bg-purple-800 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-900 transition-all">
                        Register
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
