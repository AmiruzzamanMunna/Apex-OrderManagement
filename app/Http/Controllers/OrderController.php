<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use Validator;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
         // Retrieve the authenticated user.
        $user = Auth::user();
        // Validate the incoming request data for placing an order.
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // If validation fails, return a 422 response with the validation errors.
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        // Initialize total amount and order items array.
        $totalAmount = 0;
        $orderItems = [];

        // Loop through each product in the request to calculate total and check stock.
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            // Check if the stock is sufficient for the requested quantity.
            if ($product->stock < $productData['quantity']) {
                return response()->json([
                    'message' => "Product {$product->name} is out of stock."
                ], 400);
            }
            // Calculate the total amount for the order.
            $totalAmount += $product->price * $productData['quantity'];
            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'price' => $product->price,
            ];
        }
        // Create a new order in the database.
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $totalAmount,
        ]);
        // Loop through the order items to create them in the database and update stock.
        foreach ($orderItems as $item) {
            OrderItems::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
            // Decrement the stock for the product.
            Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
        }
        // Return a response indicating that the order was placed successfully.
        return response()->json(['message' => 'Order placed successfully!']);
    }
    public function orderHistory(Request $request)
    {
        // Retrieve the authenticated user.
        $user = Auth::user();
        
        // Retrieve the orders with their items and associated products for the user.
        $orders = Order::with('orderItems.product')
            ->where('user_id', $user->id)
            ->get();
        // Return the order history as a JSON response.
        return response()->json($orders);
    }
}
