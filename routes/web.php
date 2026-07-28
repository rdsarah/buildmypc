<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// Home Route
Route::get('/', function () {
    return view('home');
})->name('home');

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// PC Builder Routes
Route::get('/builder', [BuildController::class, 'index'])->name('builder.index');
Route::post('/builder/add/{product}', [BuildController::class, 'addComponent'])->name('builder.add');
Route::post('/builder/remove/{category}', [BuildController::class, 'removeComponent'])->name('builder.remove');
Route::post('/builder/clear', [BuildController::class, 'clearBuild'])->name('builder.clear');
Route::post('/builder/clone/{template}', [BuildController::class, 'cloneTemplate'])->name('builder.clone');
Route::post('/builder/suggest', [BuildController::class, 'suggestBuild'])->name('builder.suggest');

// Cart & Orders
Route::get('/cart', function () {
    return view('cart.index');
})->name('cart.index');

Route::get('/orders', function () {
    return view('orders.index');
})->name('orders.index');

// Admin Management Routes
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::post('/admin/products/{product}/update', [AdminController::class, 'updateProduct'])->name('admin.products.update');
Route::post('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.updateStatus');
Route::post('/admin/templates', [AdminController::class, 'createTemplate'])->name('admin.templates.create');
Route::delete('/admin/templates/{template}', [AdminController::class, 'deleteTemplate'])->name('admin.templates.delete');

// Review Routes
Route::post('/products/{product}/reviews', [ProductController::class, 'storeReview'])->name('reviews.store');
Route::delete('/reviews/{review}', [ProductController::class, 'destroyReview'])
    ->middleware('auth')
    ->name('reviews.destroy');

// User Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Admin Authentication Routes
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Build Gallery Routes
Route::get('/gallery', [BuildController::class, 'gallery'])->name('gallery.index');
Route::post('/builder/save', [BuildController::class, 'store'])->name('builder.save');
// for build gallery
Route::get('/gallery', [BuildController::class, 'gallery'])->name('gallery.index');
Route::post('/builder/save', [BuildController::class, 'store'])->name('builder.save');
// delete get post for build
Route::delete('/gallery/{id}', [BuildController::class, 'destroy'])->name('builder.destroy');
Route::get('/gallery/{id}/edit', [BuildController::class, 'edit'])->name('builder.edit');
Route::post('/gallery/{id}/add-to-cart', [BuildController::class, 'addToCart'])->name('builder.addToCart');


// Stripe Payment Routes
Route::get('/checkout/stripe', [PaymentController::class, 'showCheckout'])->name('checkout.stripe');
Route::post('/checkout/create-session', [PaymentController::class, 'createSession'])->name('checkout.session');
Route::get('/checkout/success', [PaymentController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [PaymentController::class, 'cancel'])->name('checkout.cancel');