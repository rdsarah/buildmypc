<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/{product:slug}', [ProductController::class, 'show'])
    ->name('products.show');

Route::get('/builder', function () {
    return view('builder.index');
})->name('builder.index');

Route::get('/cart', function () {
    return view('cart.index');
})->name('cart.index');

Route::get('/orders', function () {
    return view('orders.index');
})->name('orders.index');

Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');