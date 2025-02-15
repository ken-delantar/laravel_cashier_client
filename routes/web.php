<?php

use App\Http\Controllers\checkoutController;
use App\Http\Controllers\OrderRetrievalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [StripeProductController::class, 'product_retrieve'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/product_listing', [StripeProductController::class, 'product_retrieve'])->name('product_listing');
Route::post('/checkout', [checkoutController::class, 'checkout'])->name('checkout');
Route::get('/customer_order', [OrderRetrievalController::class, 'user_order_retrieval'])->name('order_retrieve');

require __DIR__.'/auth.php';
