# Projeto laravel para estudo do curso - Laravue

## Middleware

Middlewares são camadas executadas antes do controller, são usadas para verificar ou validar algo no começo da execução do fluxo.

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

***Como criar grupos de middlewares***

Para criar um grupo de middlewares e definir um nome para eles, basta regitrar dentro de `bootstrap/app.php` usando `->$middleware->appendToGroup()` o primeiro parâmetro é o nome do grupo `aliases` e o segundo é o array de classe dos middlewares

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// MIDDLEWARE
use App\Http\Middleware\Test2Middleware;
use App\Http\Middleware\TestMiddleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    // Dentro de middleware defina os grupos de middleware com ->appendToGroup()
    $middleware->appendToGroup('test', [
      TestMiddleware::class,
      Test2Middleware::class
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    //
  })->create();

```

***Definindo nomes para middlewares***

Para definir um nome `aliases` em string para ser chamado ao invés de passar a classe basta usar o método `->alias()`, ele recebe um array onde o index é o nome e o valor é o Middleware

```php
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
      'test' => TestMiddleware::class,
      'test2' => Test2Middleware::class
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    //
  })->create();

```

***Como definir middlewares globais para camadas de rotas***

Para definir middlewares globais para camadas de rotas podemos usar, rotas web `->web()` ou para rotas api `->api()`, o primeiro parâmetro são os últimos middlewares que serão executados e é um array de middlewares, o segundo parametro são os middlewares que vão ser executados primeiro.

```php
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
    $middleware->web(
      [
        TestMiddleware::class
      ],
      [
        Test2Middleware::class
      ]
    );
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    //
  })->create();
```

## Controllers

Controller são classes que são vinculadas a uma rota, elas são utilizadas para receber a request e orquestrar esses dados para serem validados `formRequest`, processados por alguma regra de negócio `Model, Services` e retornar a response `view ou JSON`.
Os Controllers são chamados no segundo parametro dos métodos de `métodos Http` das rotas `Route::get('/', [UserController::class, 'metodo'])`.

**Atenção**: Existem casos em que não será necessário passar o método no segundo index do array do segundo parâmetro da rota, isso é usado para `Controllers Single Action`

```php
<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([], function () {
  // Primeiro parâmetro é a classe controller, segundo é o seu método
  Route::get('/', [UserController::class, 'index']);
});

```

***Exemplo de controller Single Action***: Os controllers que possuem somente uma ação podem usar o método `__invoke` e passar somente a classe no segundo parametro da rota, o laravel é inteligente o bastante para usar esse método para essa rota, isso não impede a classe de ter vários métodos, mas o ideal é usar eles dentro do `__invoke`

```php

// Rota
Route::get('/checkout', CheckoutController::class);
```

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
  public function __invoke(Request $request)
  {
    dd('checkout');
  }
}

```

***Como criar controllers resources***: Controllers resources são classes criadas pelo artisan que já vem com os métodos específicos de cada item do CRUD, por exemplo `index`, `show`, `update`... e também podemos usar um método da classe `Route` para criar automaticamente todas as rotas.

| Método HTTP | URI               | Nome da rota | Controller → Método        |
| ----------- | ----------------- | ------------ | -------------------------- |
| GET / HEAD  | /post             | post.index   | PostController - `index`   |
| POST        | /post             | post.store   | PostController - `store`   |
| GET / HEAD  | /post/create      | post.create  | PostController - `create`  |
| GET / HEAD  | /post/{post}      | post.show    | PostController - `show`    |
| PUT / PATCH | /post/{post}      | post.update  | PostController - `update`  |
| DELETE      | /post/{post}      | post.destroy | PostController - `destroy` |
| GET / HEAD  | /post/{post}/edit | post.edit    | PostController - `edit`    |



```php
// rota
Route::resource('/post', PostController::class);
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
  public function index()
  {
    dd('index');
  }

  public function create()
  {
    //
  }

  public function store(Request $request)
  {
    //
  }

  public function show(User $user)
  {
    //
  }

  public function edit(User $user)
  {
    //
  }

  public function update(Request $request, User $user)
  {
    //
  }

  public function destroy(User $user)
  {
    //
  }
}

```

## Comandos artisan

| Comando | O que faz | Pasta | informações adicionais |
|---------|----------|----------| ---------------------|
| php artisan make:middleware NomeMiddleware | cria um arquivo de middleware | app/Http/Middleware  | - |
| php artisan make:controller NomeController | Criar um arquivo de controller | app/Http/Controllers | - |
| php artisan make:controller NomeController --invokable | Criar um arquivo de controller com o método __invoke | app/Http/Controllers | - |
| php artisan make:controller NomeController --resource | Criar um arquivo de controller com todos os métodos padrões de CRUD index, show, update... | app/Http/Controllers | - |
| php artisan make:controller NomeController --resource --model=user | Criar um arquivo de controller com todos os métodos padrões de CRUD e injeta o model nas dependências dos métodos | app/Http/Controllers | Caso não existe o model o laravel vai iniciar perguntar no terminal para criar o model |
| php artisan route:list | Lista todas as rotas do projeto | - | - |

