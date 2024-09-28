<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Cache::remember('products', 60 * 60, function () {
            return Product::all();
        });
    
        return response()->json($products);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $product = Product::create($request->all());

        Cache::forget('products');

        return response()->json([
            'success' => true,
            'product' => $product,
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'stock' => 'sometimes|required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::findOrFail($id);
    
        $product->update($request->all());

        Cache::forget('products');

        return response()->json([
            'success' => true,
            'product' => $product,
        ], 200);
    }
}
