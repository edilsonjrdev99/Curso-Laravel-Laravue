# Projeto laravel para estudo do curso - Laravue

## Middleware

Middleware ...

***Padrão***: É uma classe normal com o método handle, esse método é a execução do middleware, ele possui dois parametros, Request $request com os dados da request e Closure #next uma variável utilizada para informar que ele pode seguir para os próximos arquivos da execução do código

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TestMiddleware
{
  public function handle(Request $request, Closure $next): Response
  {
    // Direciona para o próximo arquivo da execução do código (Middleware ou controller)
    return $next($request);
  }
}

```

***Onde registrar o Middleware globalmente (pouco usado)*** `bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
      // Aqui é onde você registra ele ->append(classe do Middleware::class)
      $middleware->append()
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

```

## Comandos artisan

| Comando | O que faz | Pasta |
|---------|----------|----------|
| php artisan make:middleware NomeMiddleware | cria um arquivo de middleware | app/Http/Middleware  |

