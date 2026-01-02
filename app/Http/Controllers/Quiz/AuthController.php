<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Quiz\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // Đăng ký
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:accounts,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $account = Account::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'USER',
            'is_active' => true,
        ]);

        $token = $account->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công!',
            'data' => [
                'user' => $account,
                'token' => $token
            ]
        ]);
    }

    // Đăng nhập
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $account = Account::where('email', $request->email)->first();

        if (!$account || !Hash::check($request->password, $account->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng!'
            ], 401);
        }

        if (!$account->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản đã bị khóa!'
            ], 403);
        }

        $token = $account->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'data' => [
                'user' => $account,
                'token' => $token
            ]
        ]);
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã đăng xuất!'
        ]);
    }

    // Lấy thông tin user hiện tại
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    // Google OAuth - Redirect
    public function redirectToGoogle()
    {
        // Set session marker để Web AuthController biết request từ Quiz app
        session(['google_auth_source' => 'quiz']);

        // Redirect đến route Google chung đã được config trên Google Console
        return redirect('/auth/google');
    }

    // Google OAuth - Callback
    public function handleGoogleCallback()
    {
        try {
            $redirectUrl = config('services.google.redirect');
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->redirectUrl($redirectUrl)
                ->user();

            $account = Account::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($account) {
                if (!$account->google_id) {
                    $account->update(['google_id' => $googleUser->id]);
                }
                if ($googleUser->avatar && !$account->avatar) {
                    $account->update(['avatar' => $googleUser->avatar]);
                }
            } else {
                $account = Account::create([
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'full_name' => $googleUser->name,
                    'avatar' => $googleUser->avatar,
                    'role' => 'USER',
                    'is_active' => true,
                ]);
            }

            // Tạo token cho React app
            $token = $account->createToken('quiz_token')->plainTextToken;

            // Redirect về React app với token và user info
            $userData = urlencode(json_encode([
                'id' => $account->id,
                'email' => $account->email,
                'full_name' => $account->full_name,
                'avatar' => $account->avatar,
            ]));

            return redirect("/quiz/login?token={$token}&user={$userData}");

        } catch (\Exception $e) {
            return redirect('/quiz/login?error=' . urlencode('Đăng nhập Google thất bại: ' . $e->getMessage()));
        }
    }
}
