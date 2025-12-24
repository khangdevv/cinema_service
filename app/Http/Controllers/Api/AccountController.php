<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $account = Account::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $account->where(function ($query) use ($keyword) {
                $query->where('full_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('phone', 'LIKE', '%' . $keyword . '%');
            });
        }

        return response()->json([
                'success' => true,
                'message' => 'Account has been show Successfully',
                'data' => $account->get(),
            ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:account',
            'phone' => 'required|numeric|unique:account',
            'full_name' => 'required|string|max:255',
            'password_hash' => 'required|min:8',
            'is_active' => 'required|boolean',
            'role' => 'required|in:CUSTOMER,ADMIN',
        ]);

        try {
            $account = new Account();
            $account->email = $request->email;
            $account->phone = $request->phone;
            $account->full_name = $request->full_name;
            $account->password_hash = Hash::make($request->password_hash);
            $account->is_active = $request->is_active;
            $account->role = $request->role;
            $account->save();

            $token = $account->createToken('customer')->plainTextToken;

            return response()->json([
            'success' => true,
            'message' => 'Account has been created Successfully',
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
            'message' => 'Account has been created failed',
            'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        return response()->json([
            'success' => true,
            'data' => $account,
            'message' => 'Account has been retrieved successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $account = Account::find($id);

        if ($account == null) {
            return response()->json([
                'success' => false,
                'message' => "Account not exist",
            ], 404);
        }

        $request->validate([
            'phone' => 'numeric|unique:account',
            'full_name' => 'string|max:255',
            'password_hash' => 'string|nullable|min:8',
            'is_active' => 'boolean'
        ]);

        try {
            $account->phone = $request->phone;
            $account->full_name = $request->full_name;
            $account->password_hash = Hash::make($request->password_hash);
            $account->is_active = $request->is_active;
            $account->save();


            return response()->json([
                'success' => true,
                'message' => 'Account has been updated successfully',
                'data' => [
                    'account' => $account,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $account = Account::find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => "Account not exist",
            ],404);
        }

        $hasOrders = Order::where('account_id', $id)->exists();

        if ($hasOrders) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete account with orders",
            ], 400);
        }

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => "Account has been deleted successfully",
            'data' => $account,
        ],200);
    }
}
