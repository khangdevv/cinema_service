<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $product = Product::query();

        if ($request->has('name')) {
            $product->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('price')) {
            $product->where('price', $request->price);
        }

        if ($request->has('is_active')) {
            $product->where('is_active', $request->is_active);
        }

        return $product->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        try {
            $product = new Product();
            $product->name = $request->name;
            $product->price = $request->price;
            $product->is_active = $request->is_active;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Product has been created Successfully',
                'data' => [
                    'product' => $product,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product has been created failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product has been retrieved successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if ($product == null) {
            return response()->json([
                'success' => false,
                'message' => "Product not exist",
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        try {
            $product->name = $request->name;
            $product->price = $request->price;
            $product->is_active = $request->is_active;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Product has been updated successfully',
                'data' => [
                    'product' => $product,
                ],
            ], 201);

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
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => "Product not exist",
            ], 404);
        }

        $product->delete();
        return response()->json([
            'success' => true,
            'message' => "Product has been deleted successfully",
            'data' => $product,
        ], 200);
    }
}
