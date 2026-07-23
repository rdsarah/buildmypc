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

use App\Http\Controllers\AdminController;

Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::post('/admin/products/{product}/update', [AdminController::class, 'updateProduct'])->name('admin.products.update');
Route::post('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.updateStatus');