# Проміжне програмне забезпечення

- [Вступ](#introduction)
- [Визначення проміжного ПЗ](#defining-middleware)
- [Реєстрація проміжного ПЗ](#registering-middleware)
    - [Глобальне проміжне ПЗ](#global-middleware)
    - [Призначення проміжного ПЗ для маршрутів](#assigning-middleware-to-routes)
    - [Групи проміжного програмного забезпечення](#middleware-groups)
    - [Псевдоніми проміжного програмного забезпечення](#middleware-aliases)
    - [Сортування Middleware](#sorting-middleware)
- [Параметри проміжного ПЗ](#middleware-parameters)
- [Термінальне проміжне програмне забезпечення](#terminable-middleware)

<a name="introduction"></a>
## Вступ

Проміжне програмне забезпечення забезпечує зручний механізм для перевірки та фільтрації HTTP-запитів, що надходять у вашу програму. Наприклад, Laravel містить проміжне програмне забезпечення, яке перевіряє автентифікацію користувача вашої програми. Якщо користувач не автентифікований, проміжне програмне забезпечення перенаправить користувача на екран входу до програми. Однак, якщо користувач автентифікований, проміжне програмне забезпечення дозволить запиту перейти далі в програму.

Додаткове проміжне програмне забезпечення може бути написано для виконання різноманітних завдань, крім автентифікації. Наприклад, проміжне програмне забезпечення журналювання може реєструвати всі вхідні запити до вашої програми. У Laravel включено різноманітне проміжне програмне забезпечення, включаючи проміжне програмне забезпечення для автентифікації та захисту CSRF; проте все визначене користувачем проміжне програмне забезпечення зазвичай знаходиться в `app/Http/Middleware` каталозі вашої програми.

<a name="defining-middleware"></a>
## Визначення проміжного ПЗ

Щоб створити нове проміжне ПЗ, скористайтеся make:middlewareкомандою Artisan:

```shell
php artisan make:middleware EnsureTokenIsValid
```

Ця команда розмістить новий `EnsureTokenIsValid` клас у вашому `app/Http/Middleware` каталозі. У цьому проміжному програмному забезпеченні ми дозволимо доступ до маршруту, лише якщо надані `token` вхідні дані відповідають указаному значенню. В іншому випадку ми переспрямуємо користувачів назад до `home` URI:

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class EnsureTokenIsValid
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next): Response
        {
            if ($request->input('token') !== 'my-secret-token') {
                return redirect('home');
            }

            return $next($request);
        }
    }

Як ви бачите, якщо дані `token` не збігаються з нашим секретним маркером, проміжне програмне забезпечення поверне HTTP-перенаправлення client; інакше запит буде передано далі в додаток. Щоб передати запит глибше в програму (дозволяючи проміжному програмному забезпеченню «пройти»), вам слід викликати `$next` зворотний виклик за допомогою `$request`.

Найкраще уявити собі проміжне програмне забезпечення як серію «рівнів», через які запити HTTP мають пройти, перш ніж потрапити у вашу програму. Кожен рівень може перевірити запит і навіть повністю його відхилити.

> [!NOTE]  
> Усе проміжне програмне забезпечення вирішується через [контейнер сервісу](/docs/{{version}}/container), тому ви можете вказати будь-які потрібні вам залежності в конструкторі проміжного програмного забезпечення.

<a name="before-after-middleware"></a>
<a name="middleware-and-responses"></a>
#### Проміжне програмне забезпечення та відповіді

Звичайно, проміжне ПЗ може виконувати завдання до або після передачі запиту в додаток. Наприклад, таке проміжне програмне забезпечення виконає певне завдання до того, як запит буде оброблено програмою:

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class BeforeMiddleware
    {
        public function handle(Request $request, Closure $next): Response
        {
            // Виконати дію

            return $next($request);
        }
    }

Однак це проміжне програмне забезпечення виконає своє завдання після обробки запиту програмою:

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class AfterMiddleware
    {
        public function handle(Request $request, Closure $next): Response
        {
            $response = $next($request);

            // Виконати дію

            return $response;
        }
    }

<a name="registering-middleware"></a>
## Реєстрація проміжного ПЗ

<a name="global-middleware"></a>
### Глобальне проміжне ПЗ

Якщо ви хочете, щоб проміжне програмне забезпечення запускалося під час кожного запиту HTTP до вашої програми, ви можете додати його до глобального стеку проміжного програмного забезпечення у `bootstrap/app.php` файлі вашої програми:

    use App\Http\Middleware\EnsureTokenIsValid;

    ->withMiddleware(function (Middleware $middleware) {
         $middleware->append(EnsureTokenIsValid::class);
    })

Об’єкт `$middleware`, наданий для `withMiddleware` закриття, є екземпляром `Illuminate\Foundation\Configuration\Middleware` і відповідає за керування проміжним програмним забезпеченням, призначеним для маршрутів вашої програми. Метод `append` додає проміжне програмне забезпечення в кінець списку глобального проміжного програмного забезпечення. Якщо ви бажаєте додати проміжне програмне забезпечення на початок списку, вам слід скористатися цим `prepend` методом.

<a name="manually-managing-laravels-default-global-middleware"></a>
#### Ручне керування стандартним глобальним проміжним програмним забезпеченням Laravel

Якщо ви бажаєте керувати глобальним стеком проміжного програмного забезпечення Laravel вручну, ви можете надати цьому `use` методу стандартний стек глобального проміжного програмного забезпечення Laravel. Потім ви можете за потреби налаштувати стандартний стек проміжного програмного забезпечення:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            // \Illuminate\Http\Middleware\TrustHosts::class,
            \Illuminate\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);
    })


<a name="assigning-middleware-to-routes"></a>
### Призначення проміжного ПЗ для маршрутів

Якщо ви хочете призначити проміжне програмне забезпечення для певних маршрутів, ви можете викликати `middleware` метод під час визначення маршруту:

    use App\Http\Middleware\EnsureTokenIsValid;

    Route::get('/profile', function () {
        // ...
    })->middleware(EnsureTokenIsValid::class);

Ви можете призначити маршруту кілька проміжних програм, передавши методу масив імен проміжних програм `middleware`:

    Route::get('/', function () {
        // ...
    })->middleware([First::class, Second::class]);

<a name="excluding-middleware"></a>
#### За винятком проміжного ПЗ

Призначаючи проміжне програмне забезпечення групі маршрутів, іноді може знадобитися запобігти застосуванню проміжного програмного забезпечення до окремого маршруту в групі. Ви можете зробити це за допомогою `withoutMiddleware` методу:

    use App\Http\Middleware\EnsureTokenIsValid;

    Route::middleware([EnsureTokenIsValid::class])->group(function () {
        Route::get('/', function () {
            // ...
        });

        Route::get('/profile', function () {
            // ...
        })->withoutMiddleware([EnsureTokenIsValid::class]);
    });

Ви також можете виключити певний набір проміжного програмного забезпечення з усієї [group](/docs/{{version}}/routing#route-groups) визначень маршрутів:

    use App\Http\Middleware\EnsureTokenIsValid;

    Route::withoutMiddleware([EnsureTokenIsValid::class])->group(function () {
        Route::get('/profile', function () {
            // ...
        });
    });

Цей `withoutMiddleware` метод може лише видалити проміжне програмне забезпечення маршрутів і не застосовується до [глобального проміжного програмного забезпечення](#global-middleware).

<a name="middleware-groups"></a>
### Групи проміжного програмного забезпечення

Іноді вам може знадобитися згрупувати кілька проміжних програм під одним ключем, щоб полегшити їх призначення маршрутам. Ви можете зробити це за допомогою `appendToGroup` методу у файлі програми `bootstrap/app.php`:

    use App\Http\Middleware\First;
    use App\Http\Middleware\Second;

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('group-name', [
            First::class,
            Second::class,
        ]);

        $middleware->prependToGroup('group-name', [
            First::class,
            Second::class,
        ]);
    })

Групи проміжного програмного забезпечення можуть бути призначені для маршрутів і дій контролера, використовуючи той самий синтаксис, що й окреме проміжне програмне забезпечення:

    Route::get('/', function () {
        // ...
    })->middleware('group-name');

    Route::middleware(['group-name'])->group(function () {
        // ...
    });

<a name="laravels-default-middleware-groups"></a>
#### Стандартні групи проміжного програмного забезпечення Laravel

Laravel включає попередньо визначені групи `web` та `api` групи проміжного програмного забезпечення, які містять загальне проміжне програмне забезпечення, яке ви можете застосувати до веб-маршрутів і маршрутів API. Пам’ятайте, що Laravel автоматично застосовує ці групи проміжного програмного забезпечення до відповідних файлів `routes/web.php` і `routes/api.php`:

| Група `web` проміжного програмного забезпечення
|--------------
| `Illuminate\Cookie\Middleware\EncryptCookies`
| `Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse`
| `Illuminate\Session\Middleware\StartSession`
| `Illuminate\View\Middleware\ShareErrorsFromSession`
| `Illuminate\Foundation\Http\Middleware\ValidateCsrfToken`
| `Illuminate\Routing\Middleware\SubstituteBindings`

| Група `api` проміжного програмного забезпечення
|--------------
| `Illuminate\Routing\Middleware\SubstituteBindings`

Якщо ви бажаєте додати проміжне програмне забезпечення до цих груп або перед ним, ви можете скористатися методами `web` та `api` у файлі програми `bootstrap/app.php`. Методи `web` та `api` є зручною альтернативою методу `appendToGroup`:

    use App\Http\Middleware\EnsureTokenIsValid;
    use App\Http\Middleware\EnsureUserIsSubscribed;

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            EnsureUserIsSubscribed::class,
        ]);

        $middleware->api(prepend: [
            EnsureTokenIsValid::class,
        ]);
    })

Ви навіть можете замінити один із стандартних записів проміжного програмного забезпечення Laravel за замовчуванням власним власним проміжним програмним забезпеченням:

    use App\Http\Middleware\StartCustomSession;
    use Illuminate\Session\Middleware\StartSession;

    $middleware->web(replace: [
        StartSession::class => StartCustomSession::class,
    ]);

Або ви можете повністю видалити проміжне програмне забезпечення:

    $middleware->web(remove: [
        StartSession::class,
    ]);

<a name="manually-managing-laravels-default-middleware-groups"></a>
#### Ручне керування групами проміжного програмного забезпечення Laravel за замовчуванням

Якщо ви хочете вручну керувати всім проміжним програмним забезпеченням у групах Laravel за замовчуванням `web` і `api` проміжним програмним забезпеченням, ви можете повністю перевизначити групи. У наведеному нижче прикладі буде визначено групи проміжного програмного забезпечення `web` та `api` з їхнім проміжним програмним забезпеченням за замовчуванням, що дозволить вам налаштувати їх за потреби:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);

        $middleware->group('api', [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // 'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })

> [!NOTE]  
> За замовчуванням групи проміжного програмного забезпечення `web` та `api` автоматично застосовуються до відповідних файлів `routes/web.php` і `routes/api` .`php` файлів вашої програми `bootstrap/app.php`.

<a name="middleware-aliases"></a>
### Псевдоніми проміжного програмного забезпечення

Ви можете призначити псевдоніми проміжному ПЗ у файлі програми `bootstrap/app.php`. Псевдоніми проміжного програмного забезпечення дозволяють визначити короткий псевдонім для даного класу проміжного програмного забезпечення, що може бути особливо корисним для проміжного програмного забезпечення з довгими іменами класів:

    use App\Http\Middleware\EnsureUserIsSubscribed;

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'subscribed' => EnsureUserIsSubscribed::class
        ]);
    })

Після визначення псевдоніма проміжного програмного забезпечення у `bootstrap/app.php` файлі вашої програми ви можете використовувати його під час призначення проміжного програмного забезпечення маршрутам:

    Route::get('/profile', function () {
        // ...
    })->middleware('subscribed');

Для зручності деякі з вбудованих проміжних програм Laravel за замовчуванням мають псевдоніми. Наприклад, `auth` проміжне програмне забезпечення є псевдонімом проміжного `Illuminate\Auth\Middleware\Authenticate` програмного забезпечення. Нижче наведено список псевдонімів проміжного програмного забезпечення за замовчуванням:

| Псевдонім | Проміжне програмне забезпечення
|-------|------------
`auth` | `Illuminate\Auth\Middleware\Authenticate`
`auth.basic` | `Illuminate\Auth\Middleware\AuthenticateWithBasicAuth`
`auth.session` | `Illuminate\Session\Middleware\AuthenticateSession`
`cache.headers` | `Illuminate\Http\Middleware\SetCacheHeaders`
`can` | `Illuminate\Auth\Middleware\Authorize`
`guest` | `Illuminate\Auth\Middleware\RedirectIfAuthenticated`
`password.confirm` | `Illuminate\Auth\Middleware\RequirePassword`
`precognitive` | `Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests`
`signed` | `Illuminate\Routing\Middleware\ValidateSignature`
`subscribed` | `\Spark\Http\Middleware\VerifyBillableIsSubscribed`
`throttle` | `Illuminate\Routing\Middleware\ThrottleRequests` or `Illuminate\Routing\Middleware\ThrottleRequestsWithRedis`
`verified` | `Illuminate\Auth\Middleware\EnsureEmailIsVerified`

<a name="sorting-middleware"></a>
### Сортування Middleware

Рідко вам може знадобитися, щоб ваше проміжне програмне забезпечення виконувалося в певному порядку, але не контролювало їх порядок, коли вони призначені маршруту. У таких ситуаціях ви можете вказати пріоритет проміжного ПЗ за допомогою `priority` методу у файлі програми `bootstrap/app.php`:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->priority([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })

<a name="middleware-parameters"></a>
## Параметри проміжного ПЗ

Проміжне програмне забезпечення також може отримувати додаткові параметри. Наприклад, якщо вашій програмі потрібно перевірити, що автентифікований користувач має задану «роль» перед виконанням певної дії, ви можете створити `EnsureUserHasRole` проміжне програмне забезпечення, яке отримує ім’я ролі як додатковий аргумент.

Додаткові параметри проміжного програмного забезпечення будуть передані проміжному ПЗ після `$next` аргументу:

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class EnsureUserHasRole
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next, string $role): Response
        {
            if (! $request->user()->hasRole($role)) {
                // Redirect...
            }

            return $next($request);
        }

    }

Параметри проміжного програмного забезпечення можна вказати під час визначення маршруту, розділивши ім’я проміжного програмного забезпечення та параметри за допомогою `:`:

    Route::put('/post/{id}', function (string $id) {
        // ...
    })->middleware('role:editor');

Кілька параметрів можуть бути розділені комами:

    Route::put('/post/{id}', function (string $id) {
        // ...
    })->middleware('role:editor,publisher');

<a name="terminable-middleware"></a>
## Термінальне проміжне програмне забезпечення

Іноді проміжному програмному забезпеченню може знадобитися виконати певну роботу після того, як відповідь HTTP буде надіслано браузеру. Якщо ви визначаєте `terminate` метод у своєму проміжному програмному забезпеченні, а ваш веб-сервер використовує FastCGI, метод `terminate` буде автоматично викликано після надсилання відповіді браузеру:

    <?php

    namespace Illuminate\Session\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class TerminatingMiddleware
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next): Response
        {
            return $next($request);
        }

        /**
         * Handle tasks after the response has been sent to the browser.
         */
        public function terminate(Request $request, Response $response): void
        {
            // ...
        }
    }

Метод `terminate` повинен отримати як запит, так і відповідь. Після того, як ви визначили проміжне програмне забезпечення з можливістю завершення, ви повинні додати його до списку маршрутів або глобального проміжного програмного забезпечення у файлі програми `bootstrap/app.php`.

Під час виклику `terminate` методу у вашому проміжному програмному забезпеченні Laravel вирішить новий екземпляр проміжного програмного забезпечення з [контейнера служби](/docs/{{version}}/container). Якщо ви хочете використовувати той самий екземпляр проміжного програмного забезпечення під час виклику методів `handle` and `terminate`, зареєструйте проміжне програмне забезпечення в контейнері за допомогою методу контейнера `singleton`. Як правило, це слід робити в такий `registerс` посіб `AppServiceProvider`:

    use App\Http\Middleware\TerminatingMiddleware;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TerminatingMiddleware::class);
    }
