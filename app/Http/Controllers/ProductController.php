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
         // Attempt to retrieve the products from the cache, or fetch from the database if not cached.
        $products = Cache::remember('products', 60 * 60, function () {
            return Product::all();
        });
        // Return the products as a JSON response.
        return response()->json($products);
    }
    public function store(Request $request)
    {
        // Validate the incoming request data for creating a product.
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);
        // If validation fails, return a 422 response with the validation errors.
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        // Create a new product in the database.
        $product = Product::create($request->all());

        // Clear the products cache to ensure the latest product data is fetched next time.
        Cache::forget('products');
        // Return a 201 response indicating the product was created successfully.
        return response()->json([
            'success' => true,
            'product' => $product,
        ], 201);
    }
    public function update(Request $request, $id)
    {
        // Validate the incoming request data for updating a product.
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'stock' => 'sometimes|required|integer',
        ]);

        // If validation fails, return a 422 response with the validation errors.
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        // Find the product by ID or fail if not found.
        $product = Product::findOrFail($id);
        // Update the product with the request data.
        $product->update($request->all());
        
        // Clear the products cache to ensure the latest product data is fetched next time.
        Cache::forget('products');
        // Return a 200 response indicating the product was updated successfully.
        return response()->json([
            'success' => true,
            'product' => $product,
        ], 200);
    }
}
