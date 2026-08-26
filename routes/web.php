<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TrendingController;
use Illuminate\Support\Facades\Route;

// Home Route
Route::get('/', function () {
    return view('home');
})->name('home');

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/compare', [ComparisonController::class, 'index'])->name('comparison.index');

Route::middleware('auth')->group(function () {
    Route::post('/compare/save', [ComparisonController::class, 'save'])->name('comparison.save');
    Route::get('/comparison-history', [ComparisonController::class, 'history'])->name('comparison.history');
    Route::delete('/comparison-history/{comparisonHistory}', [ComparisonController::class, 'destroy'])->name('comparison.destroy');
    Route::get('/trending', [TrendingController::class, 'index'])->name('trending.index');
});

// PC Builder Routes
Route::get('/builder', [BuildController::class, 'index'])->name('builder.index');
Route::post('/builder/add/{product}', [BuildController::class, 'addComponent'])->name('builder.add');
Route::post('/builder/remove/{category}', [BuildController::class, 'removeComponent'])->name('builder.remove');
Route::post('/builder/clear', [BuildController::class, 'clearBuild'])->name('builder.clear');
Route::post('/builder/clone/{template}', [BuildController::class, 'cloneTemplate'])->name('builder.clone');
Route::post('/builder/suggest', [BuildController::class, 'suggestBuild'])->name('builder.suggest');

// Build Gallery Routes
Route::get('/gallery', [BuildController::class, 'gallery'])->name('gallery.index');
Route::post('/builder/save', [BuildController::class, 'store'])->name('builder.save');
Route::delete('/gallery/{id}', [BuildController::class, 'destroy'])->name('builder.destroy');
Route::get('/gallery/{id}/edit', [BuildController::class, 'edit'])->name('builder.edit');
Route::post('/gallery/{id}/add-to-cart', [BuildController::class, 'addToCart'])->name('builder.addToCart');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

// Stripe Payment Routes
Route::get('/checkout/stripe', [PaymentController::class, 'showCheckout'])->name('checkout.stripe');
Route::post('/checkout/create-session', [PaymentController::class, 'createSession'])->name('checkout.session');
Route::get('/checkout/success', [PaymentController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [PaymentController::class, 'cancel'])->name('checkout.cancel');

// Order Routes (Authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// Admin Management Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Product Management
    Route::get('/admin/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::get('/admin/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/admin/products/{product}', [AdminController::class, 'updateFullProduct'])->name('admin.products.updateFull');
    Route::delete('/admin/products/{product}', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');
    Route::post('/admin/products/{product}/update', [AdminController::class, 'updateProduct'])->name('admin.products.update'); // Quick update
    
    // Settings
    Route::post('/admin/settings/toggle-out-of-stock', [AdminController::class, 'toggleHideOutOfStock'])->name('admin.settings.toggleOutOfStock');
    
    // Orders & Templates
    Route::post('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.updateStatus');
    Route::post('/admin/templates', [AdminController::class, 'createTemplate'])->name('admin.templates.create');
    Route::delete('/admin/templates/{template}', [AdminController::class, 'deleteTemplate'])->name('admin.templates.delete');
});

// Review Routes
Route::post('/products/{product}/reviews', [ProductController::class, 'storeReview'])->name('reviews.store');
Route::delete('/reviews/{review}', [ProductController::class, 'destroyReview'])->middleware('auth')->name('reviews.destroy');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Order Routes (Authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});