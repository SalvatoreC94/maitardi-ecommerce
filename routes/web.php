<?php

use App\Http\Controllers\CartController;

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

