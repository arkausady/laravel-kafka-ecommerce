<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;



Route::get('/', [OrderController::class, 'index'])->name('toko');
Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/add-product', [OrderController::class, 'addProduct'])->name('add.product');
