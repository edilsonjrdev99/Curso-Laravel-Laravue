<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([], function () {
  Route::get('/', [UserController::class, 'index']);
  Route::get('/checkout', CheckoutController::class);
});
