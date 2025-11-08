<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function login(Request $request) {

        $request->validate([
            'email' => 'required|email',
            'password_hash' => 'required',
        ]);

        $account = Account::where('email', $request->email)->first();

        if (!$account || !Hash::check($request->password_hash, $account->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Account'
            ], 401);
        }

        if (!$account->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Account'
            ], 403);
        }

        $token = $account->createToken('customer')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Successfully',
            'data' => [
                'account' => $account,
                'token' => $token,
            ],
        ]);
    }

    public function register(Request $request) {
        $request->validate([
            'email' => 'required|email|unique:account',
            'phone' => 'required|numeric|unique:account',
            'full_name' => 'required|string|max:255',
            'password_hash' => 'required|min:8',
        ]);

        try {
            $account = Account::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'full_name' => $request->full_name,
                'password_hash' => Hash::make($request->password_hash),
                'is_active' => true,
                'role' => 'CUSTOMER',
            ]);

            $token = $account->createToken('admin')->plainTextToken;

            return response()->json([
            'success' => true,
            'message' => 'Register Successfully',
            'data' => [
                'account' => $account,
                'token' => $token,
                ],
            ], 201);

        }
        catch (\Exception $e)
        {
            return response()->json([
            'success' => false,
            'message' => 'Register failed',
            'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function getInfo(Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
