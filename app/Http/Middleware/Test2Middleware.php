<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Test2Middleware
{
  public function handle(Request $request, Closure $next): Response
  {
    dd('caiu no segundo middleware');

    return $next($request);
  }
}
