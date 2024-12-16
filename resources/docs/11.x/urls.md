# URL генерація

- [Вступ](#introduction)
- [Основи](#the-basics)
    - [Генерація URL-адрес](#generating-urls)
    - [Доступ до поточної URL-адреси](#accessing-the-current-url)
- [URL-адреси для іменованих маршрутів](#urls-for-named-routes)
    - [Підписані URL-адреси](#signed-urls)
- [URL-адреси для дій контролера](#urls-for-controller-actions)
- [Значення за замовчуванням](#default-values)

<a name="introduction"></a>
## Вступ

Laravel надає кілька помічників, які допоможуть вам у створенні URL-адрес для вашого додатку. Ці помічники в першу чергу корисні при створенні посилань у ваших шаблонах і відповідях API або при створенні відповідей перенаправлення на іншу частину вашого додатку.

<a name="the-basics"></a>
## Основи

<a name="generating-urls"></a>
### Генерація URL-адрес

Помічник `url` можна використовувати для створення довільних URL-адрес для вашої програми. Згенерована URL-адреса буде автоматично використовувати схему (HTTP або HTTPS) і хост з поточного запиту, який обробляється програмою:

    $post = App\Models\Post::find(1);

    echo url("/posts/{$post->id}");

    // http://example.com/posts/1

<a name="accessing-the-current-url"></a>
### Доступ до поточної URL-адреси

Якщо шлях до помічника `url` не вказано, повертається екземпляр `Illuminate\Routing\UrlGenerator`, що дозволяє отримати доступ до інформації про поточну URL-адресу:

    // Отримати поточну URL-адресу без рядка запиту...
    echo url()->current();

    // Отримати поточну URL-адресу, включаючи рядок запиту.
    echo url()->full();

    // Отримати повну URL-адресу попереднього запиту...
    echo url()->previous();

До кожного з цих методів також можна отримати доступ через `URL`. [фасади](/docs/{{version}}/facades):

    use Illuminate\Support\Facades\URL;

    echo URL::current();

<a name="urls-for-named-routes"></a>
## URL-адреси для іменованих маршрутів

Помічник `route` можна використовувати для генерації URL-адрес до [іменовані маршрути](/docs/{{version}}/routing#named-routes). Іменовані маршрути дозволяють генерувати URL-адреси без прив'язки до реальної URL-адреси, визначеної у маршруті. Отже, якщо URL-адреса маршруту змінюється, не потрібно вносити зміни у ваші виклики функції `route`. Наприклад, уявіть, що ваша програма містить маршрут, визначений наступним чином:

    Route::get('/post/{post}', function (Post $post) {
        // ...
    })->name('post.show');

Щоб згенерувати URL-адресу цього маршруту, ви можете скористатися допоміжним інструментом `route` ось так:

    echo route('post.show', ['post' => 1]);

    // http://example.com/post/1

Звичайно, помічник `route` також можна використовувати для створення URL-адрес для маршрутів з декількома параметрами:

    Route::get('/post/{post}/comment/{comment}', function (Post $post, Comment $comment) {
        // ...
    })->name('comment.show');

    echo route('comment.show', ['post' => 1, 'comment' => 3]);

    // http://example.com/post/1/comment/3

Будь-які додаткові елементи масиву, які не відповідають параметрам визначення маршруту, будуть додані до рядка запиту URL-адреси:

    echo route('post.show', ['post' => 1, 'search' => 'rocket']);

    // http://example.com/post/1?search=rocket

<a name="eloquent-models"></a>
#### Eloquent моделі

Ви часто генеруєте URL-адреси, використовуючи ключ маршруту (зазвичай первинний ключ) [Eloquent моделі](/docs/{{version}}/eloquent). З цієї причини ви можете передавати моделі Eloquent як значення параметрів. Помічник `route` автоматично витягне ключ маршруту моделі:

    echo route('post.show', ['post' => $post]);

<a name="signed-urls"></a>
### Підписані URL-адреси

Laravel дозволяє легко створювати «підписані» URL-адреси до іменованих маршрутів. Ці URL-адреси мають «підписаний» хеш, доданий до рядка запиту, який дозволяє Laravel перевірити, що URL-адресу не було змінено з моменту її створення. Підписані URL-адреси особливо корисні для маршрутів, які є загальнодоступними, але потребують рівня захисту від маніпуляцій з URL-адресами.

Наприклад, ви можете використовувати підписані URL-адреси для реалізації публічного посилання «відписатися», яке надсилається вашим клієнтам електронною поштою. Щоб створити підписану URL-адресу для іменованого маршруту, скористайтеся методом `signedRoute` на фасаді `URL`:

    use Illuminate\Support\Facades\URL;

    return URL::signedRoute('unsubscribe', ['user' => 1]);

Хеш URL-адреси, передавши аргумент `absolute` методу `signedRoute`:

    return URL::signedRoute('unsubscribe', ['user' => 1], absolute: false);

Якщо ви хочете згенерувати тимчасову підписану URL-адресу маршруту, термін дії якої закінчується через певний проміжок часу, ви можете скористатися методом `temporarySignedRoute`. Коли Laravel перевіряє тимчасову підписану URL-адресу маршруту, він переконується, що час закінчення терміну дії, закодований у підписаній URL-адресі, не минув:

    use Illuminate\Support\Facades\URL;

    return URL::temporarySignedRoute(
        'unsubscribe', now()->addMinutes(30), ['user' => 1]
    );

<a name="validating-signed-route-requests"></a>
#### Перевірка запитів на підписані маршрути

Щоб перевірити, що вхідний запит має дійсний підпис, слід викликати метод `hasValidSignature` на вхідному екземплярі `Illuminate\Http\Request`:

    use Illuminate\Http\Request;

    Route::get('/unsubscribe/{user}', function (Request $request) {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        // ...
    })->name('unsubscribe');

Іноді вам може знадобитися дозволити інтерфейсу програми додавати дані до підписаної URL-адреси, наприклад, при виконанні пагінації на стороні клієнта. Тому ви можете вказати параметри запиту, які слід ігнорувати при перевірці підписаної URL-адреси за допомогою методу `hasValidSignatureWhileIgnoring`. Пам'ятайте, що ігнорування параметрів дозволяє будь-кому змінювати ці параметри в запиті:

    if (! $request->hasValidSignatureWhileIgnoring(['page', 'order'])) {
        abort(401);
    }

Замість того, щоб перевіряти підписані URL-адреси за допомогою екземпляра вхідного запиту, ви можете призначити `signed`. (`Illuminate\Routing\Middleware\ValidateSignature`) [посередники](/docs/{{version}}/middleware) до маршруту. Якщо вхідний запит не має дійсного підпису, проміжне програмне забезпечення автоматично поверне `403` HTTP-відповідь:

    Route::post('/unsubscribe/{user}', function (Request $request) {
        // ...
    })->name('unsubscribe')->middleware('signed');

Якщо ваші підписані URL-адреси не містять домену в хеші URL-адреси, вам слід надати проміжному програмному забезпеченню аргумент `relative`:

    Route::post('/unsubscribe/{user}', function (Request $request) {
        // ...
    })->name('unsubscribe')->middleware('signed:relative');

<a name="responding-to-invalid-signed-routes"></a>
#### Реагування на недійсні підписані маршрути

Коли хтось відвідує підписану URL-адресу, термін дії якої закінчився, він отримає типову сторінку помилки з кодом статусу HTTP `403`. Однак ви можете налаштувати цю поведінку, визначивши власне закриття «render» для виключення `InvalidSignatureException` у файлі `bootstrap/app.php` вашого додатку:

    use Illuminate\Routing\Exceptions\InvalidSignatureException;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (InvalidSignatureException $e) {
            return response()->view('error.link-expired', [], 403);
        });
    })

<a name="urls-for-controller-actions"></a>
## URL-адреси для дій контролера

Функція `action` генерує URL для заданої дії контролера:

    use App\Http\Controllers\HomeController;

    $url = action([HomeController::class, 'index']);

Якщо метод контролера приймає параметри маршруту, ви можете передати асоціативний масив параметрів маршруту як другий аргумент функції:

    $url = action([UserController::class, 'profile'], ['id' => 1]);

<a name="default-values"></a>
## Значення за замовчуванням

Для деяких програм ви можете вказати значення за замовчуванням для певних параметрів URL-адреси для всього запиту. Наприклад, уявіть, що багато ваших маршрутів визначають параметр `{locale}`:

    Route::get('/{locale}/posts', function () {
        // ...
    })->name('post.index');

Завжди передавати `locale` кожного разу, коли ви викликаєте помічник `route`, є громіздким. Тому ви можете скористатися методом `URL::defaults` для визначення значення за замовчуванням для цього параметра, яке завжди буде застосовуватися під час поточного запиту. Ви можете викликати цей метод з [посередники маршрутів](/docs/{{version}}/middleware#assigning-middleware-to-routes) щоб отримати доступ до поточного запиту:

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\URL;
    use Symfony\Component\HttpFoundation\Response;

    class SetDefaultLocaleForUrls
    {
        /**
         * Обробити вхідний запит.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next): Response
        {
            URL::defaults(['locale' => $request->user()->locale]);

            return $next($request);
        }
    }

Після встановлення значення за замовчуванням для параметра `locale` вам більше не потрібно передавати його значення під час генерації URL-адрес за допомогою помічника `route`.

<a name="url-defaults-middleware-priority"></a>
#### URL за замовчуванням та пріоритет проміжного програмного забезпечення

Встановлення значень URL за замовчуванням може перешкоджати роботі Laravel з неявними прив'язками моделей. Тому вам слід [визначте пріоритети посередників](/docs/{{version}}/middleware#sorting-middleware) які встановлюють URL за замовчуванням для виконання перед власним проміжним модулем `SubstituteBindings` Laravel. Ви можете зробити це за допомогою методу `priority` у файлі `bootstrap/app.php` вашого додатку:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->priority([
        \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \App\Http\Middleware\SetDefaultLocaleForUrls::class, // [tl! add]
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ]);
})
```
