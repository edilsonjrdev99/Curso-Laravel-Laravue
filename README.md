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


***Como registrar middleware por rota***

Nos arquivos de rotas definimos um middleware usando `->middleware ou ::middleware`

```php
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\TestMiddleware;

Route::get('/', function () {
  return view('welcome');
})->middleware(TestMiddleware::class);

```

Podemos adicionar mais de um middleware, no parametro de `->middleware()` podemos passar um array de middleware. A ordem dos middlewares importa porque eles são execudados na ordem em que são especificados. O que é responsável por direcionar para o próximo middleware está dentro do método handle de cada middleware `$next($request)` direciona para o próximo enviando o que está no seu parametro

```php
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\TestMiddleware;
use App\Http\Middleware\TestMiddleware2;

// Nesse exemplo primeiramente vai ser executado o Middleware e depois o middleware2

Route::get('/', function () {
  return view('welcome');
})->middleware([TestMiddleware::class, TestMiddleware2::class]);

```

***Como remover um middleware***

Para remover um middleware basta adicionar `->withoutMiddleware() ou ::withoutMiddleware(), seu parametro pode ser um middleware ou um array de middleware`

```php
<?php

use App\Http\Middleware\Test2Middleware;
use App\Http\Middleware\TestMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([TestMiddleware::class, Test2Middleware::class])->group(function () {
  Route::get('/', function () {
    return view('welcome');
  })->withoutMiddleware([Test2Middleware::class]);
});

```

## Comandos artisan

| Comando | O que faz | Pasta |
|---------|----------|----------|
| php artisan make:middleware NomeMiddleware | cria um arquivo de middleware | app/Http/Middleware  |

