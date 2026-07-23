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

use App\Http\Controllers\BuildController;

Route::get('/builder', [BuildController::class, 'index'])->name('builder.index');
Route::post('/builder/add/{product}', [BuildController::class, 'addComponent'])->name('builder.add');
Route::post('/builder/remove/{category}', [BuildController::class, 'removeComponent'])->name('builder.remove');
Route::post('/builder/clear', [BuildController::class, 'clearBuild'])->name('builder.clear');
Route::post('/builder/clone/{template}', [BuildController::class, 'cloneTemplate'])->name('builder.clone');
Route::post('/builder/suggest', [BuildController::class, 'suggestBuild'])->name('builder.suggest');

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
Route::post('/admin/templates', [AdminController::class, 'createTemplate'])->name('admin.templates.create');
Route::delete('/admin/templates/{template}', [AdminController::class, 'deleteTemplate'])->name('admin.templates.delete');