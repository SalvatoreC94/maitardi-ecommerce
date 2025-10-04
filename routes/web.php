<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/add/{productId}', [CartController::class, 'add']);
    Route::patch('/update/{itemId}', [CartController::class, 'update']);
    Route::delete('/remove/{itemId}', [CartController::class, 'remove']);
    Route::delete('/clear', [CartController::class, 'clear']);
});

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'place'])->name('checkout.place');
Route::get('/checkout/success/{code}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/debug-cart', function () {
    $svc = app(\App\Services\CartManager::class);
    return response()->json($svc->getDetails());
});
use App\Models\Product;

Route::get('/dev-products', function () {
    return Product::select('id','name','price')->where('is_active', true)->take(6)->get();
});
