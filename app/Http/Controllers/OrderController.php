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
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $totalAmount = 0;
        $orderItems = [];

        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            if ($product->stock < $productData['quantity']) {
                return response()->json([
                    'message' => "Product {$product->name} is out of stock."
                ], 400);
            }

            $totalAmount += $product->price * $productData['quantity'];
            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'price' => $product->price,
            ];
        }
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $totalAmount,
        ]);
        
        foreach ($orderItems as $item) {
            OrderItems::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
        }

        return response()->json(['message' => 'Order placed successfully!']);
    }
    public function orderHistory(Request $request)
    {
        $user = Auth::user();
        
        $orders = Order::with('orderItems.product')
            ->where('user_id', $user->id)
            ->get();
       
        return response()->json($orders);
    }
}
