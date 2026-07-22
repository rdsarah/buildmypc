<?php

use App\Http\Controllers\BuildController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/{product:slug}', [ProductController::class, 'show'])
    ->name('products.show');

Route::get('/builder', [BuildController::class, 'index'])
    ->name('builder.index');

Route::post('/builder/items', [BuildController::class, 'storeItem'])
    ->name('builder.items.store');

Route::delete('/builder/items/{buildItem}', [BuildController::class, 'destroyItem'])
    ->name('builder.items.destroy');

Route::post('/builder/reset', [BuildController::class, 'reset'])
    ->name('builder.reset');

Route::get('/cart', function () {
    return view('cart.index');
})->name('cart.index');

Route::get('/orders', function () {
    return view('orders.index');
})->name('orders.index');

Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');