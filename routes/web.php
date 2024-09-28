<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () {
    return view('welcomenew');
});

 

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('event:clear');
    Artisan::call('optimize:clear');
    
    return "All caches cleared!";
});
Route::get('/table-migrate', function () {
    Artisan::call('migrate');
    Artisan::call('migrate:refresh');
    return "All table migrated!";
});
Route::get('/table-seed', function () {
    Artisan::call('db:seed');
    return "All table Seeded!";
});

