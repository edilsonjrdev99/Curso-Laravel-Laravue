<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// MIDDLEWARES
use App\Http\Middleware\Test2Middleware;
use App\Http\Middleware\TestMiddleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
      'middleware'  => TestMiddleware::class,
      'middleware2' => Test2Middleware::class
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    //
  })->create();
