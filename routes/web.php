<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/products', function () {
    return view('products.index');
});

Route::get('/builder', function () {
    return view('builder.index');
});

Route::get('/cart', function () {
    return view('cart.index');
});

Route::get('/orders', function () {
    return view('orders.index');
});

Route::get('/admin', function () {
    return view('admin.dashboard');
});