<?php

use App\Http\Middleware\Test2Middleware;
use App\Http\Middleware\TestMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['middleware', 'middleware2'])->group(function () {
  Route::get('/', function () {
    return view('welcome');
  })->withoutMiddleware([Test2Middleware::class]);
});
