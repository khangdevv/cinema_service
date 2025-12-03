<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cinema Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-black-900 to-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-black/40 backdrop-blur-lg rounded-xl border border-gray-800 p-8">
            <h1 class="text-3xl font-bold text-white mb-6 text-center">Login</h1>
            
            @if($errors->any())
                <div class="bg-red-500/20 border border-red-500 text-red-100 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-500/20 border border-green-500 text-green-100 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('auth.login') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                           class="w-full px-4 py-2 rounded-lg bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-purple-500">
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-300 mb-2">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-2 rounded-lg bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-purple-500">
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-yellow-600 to-red-600 hover:from-red-700 hover:to-red-700 text-white py-3 rounded-lg font-semibold transition-all mb-4">
                    Login
                </button>
                
                <p class="text-center text-gray-400">
                    Don't have an account? <a href="{{ route('auth.register.form') }}" class="text-purple-400 hover:text-purple-300">Register</a>
                </p>
            </form>
        
        </div>
    </div>
</body>
</html>
