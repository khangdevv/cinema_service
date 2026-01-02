<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $account = Account::where('email', $request->email)->first();

        if (!$account || !Hash::check($request->password, $account->password_hash)) {
            return redirect()->back()
                ->withErrors(['email' => 'Invalid email or password'])
                ->withInput();
        }

        Auth::guard('web')->login($account);

        return redirect()->route('dashboard')->with('success', 'Login successful!');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:account,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $account = Account::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password_hash' => Hash::make($request->password),
            'role' => 'CUSTOMER',
        ]);

        return redirect()->route('auth.login.form')
            ->with('success', 'Registration successful! Please login.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login.form')->with('success', 'Đăng xuất thành công!');
    }

    // Google OAuth methods
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if using Quiz app's Account model or Cinema app's Account model
            // Determine by checking referer or state
            $fromQuiz = session('google_auth_source') === 'quiz';

            if ($fromQuiz) {
                // Handle Quiz app login
                $account = \App\Models\Quiz\Account::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

                if ($account) {
                    if (!$account->google_id) {
                        $account->google_id = $googleUser->getId();
                        $account->save();
                    }
                    if ($googleUser->getAvatar() && !$account->avatar) {
                        $account->avatar = $googleUser->getAvatar();
                        $account->save();
                    }
                } else {
                    $account = \App\Models\Quiz\Account::create([
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'full_name' => $googleUser->getName(),
                        'avatar' => $googleUser->getAvatar(),
                        'role' => 'USER',
                        'is_active' => true,
                    ]);
                }

                // Create token for React app
                $token = $account->createToken('quiz_token')->plainTextToken;
                $userData = urlencode(json_encode([
                    'id' => $account->id,
                    'email' => $account->email,
                    'full_name' => $account->full_name,
                    'avatar' => $account->avatar,
                ]));

                session()->forget('google_auth_source');
                return redirect("/quiz/login?token={$token}&user={$userData}");
            }

            // Handle Cinema app login (original flow)
            $account = Account::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($account) {
                if (!$account->google_id) {
                    $account->google_id = $googleUser->getId();
                    $account->save();
                }
            } else {
                $account = Account::create([
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'full_name' => $googleUser->getName(),
                    'password_hash' => Hash::make(Str::random(16)),
                    'role' => 'CUSTOMER',
                    'is_active' => true,
                ]);
            }

            Auth::guard('web')->login($account);
            return redirect()->route('home')->with('success', 'Đăng nhập Google thành công!');

        } catch (\Exception $e) {
            if (session('google_auth_source') === 'quiz') {
                session()->forget('google_auth_source');
                return redirect('/quiz/login?error=' . urlencode('Đăng nhập Google thất bại: ' . $e->getMessage()));
            }
            return redirect()->route('auth.login.form')
                ->with('error', 'Đăng nhập Google thất bại: ' . $e->getMessage());
        }
    }
}
