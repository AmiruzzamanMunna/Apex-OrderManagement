<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\AdminMiddleware;



Route::post('/register', [UserLoginController::class, 'register']);
Route::post('/login', [UserLoginController::class, 'login']);
Route::post('/logout', [UserLoginController::class, 'logout']);

//product list publicly
Route::get('/products', [ProductController::class, 'index']);
//product list end

Route::middleware(['jwt.auth'])->group(function () {

    // token refresh
    Route::post('/refresh-token', [UserLoginController::class, 'refreshToken']);
    // token refresh end

    // user order route
    Route::get('/orderHistory', [OrderController::class, 'orderHistory']);
    Route::post('/orders', [OrderController::class, 'placeOrder']);
    // user order route end

    // Admin Route
    Route::middleware('admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::post('/products/{id}', [ProductController::class, 'update']);
    });
    // Admin Route
});

