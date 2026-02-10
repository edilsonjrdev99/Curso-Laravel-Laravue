<?php

use App\Http\Middleware\Test2Middleware;
use App\Http\Middleware\TestMiddleware;
use Illuminate\Support\Facades\Route;

Route::group([], function () {
  Route::get('/', function () {
    return view('welcome');
  });
});
