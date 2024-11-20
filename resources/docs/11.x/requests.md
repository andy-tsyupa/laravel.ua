# HTTP-запити

- [Вступ](#introduction)
- [Взаємодія із запитом](#interacting-with-the-request)
    - [Доступ до запиту](#accessing-the-request)
    - [Шлях, хост і метод запиту](#request-path-and-method)
    - [Заголовки запитів](#request-headers)
    - [Запит IP-адреси](#request-ip-address)
    - [Обговорення змісту](#content-negotiation)
    - [Запити PSR-7](#psr7-requests)
- [Вхідні дані](#input)
    - [Отримання вхідних даних](#retrieving-input)
    - [Присутність на вході](#input-presence)
    - [Об'єднання додаткових даних](#merging-additional-input)
    - [Старий вхід](#old-input)
    - [Куки](#cookies)
    - [Обрізання та нормалізація вхідних даних](#input-trimming-and-normalization)
- [Файли](#files)
    - [Отримання завантажених файлів](#retrieving-uploaded-files)
    - [Зберігання завантажених файлів](#storing-uploaded-files)
- [Налаштування довірених проксі-серверів](#configuring-trusted-proxies)
- [Налаштування довірених хостів](#configuring-trusted-hosts)

<a name="introduction"></a>
## Вступ

Клас Laravel `Illuminate\Http\Request` надає об'єктно-орієнтований спосіб взаємодії з поточним HTTP-запитом, який обробляється вашим додатком, а також отримання вхідних даних, файлів cookie та файлів, які були надіслані разом із запитом.

<a name="interacting-with-the-request"></a>
## Взаємодія із запитом

<a name="accessing-the-request"></a>
### Доступ до запиту

Щоб отримати екземпляр поточного HTTP-запиту за допомогою ін'єкції залежностей, вам слід вказати клас `Illuminate\Http\Request` у методі закриття маршруту або контролері. Екземпляр вхідного запиту буде автоматично ін'єктований Laravel [сервісний контейнер](/docs/{{version}}/container):

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;

    class UserController extends Controller
    {
        /**
         * Зберегти нового користувача.
         */
        public function store(Request $request): RedirectResponse
        {
            $name = $request->input('name');

            // Зберігати користувача...

            return redirect('/users');
        }
    }

Як уже згадувалося, ви також можете вказати клас `Illuminate\Http\Request` у закритті маршруту. Сервісний контейнер автоматично додасть вхідний запит до закриття, коли він буде виконаний:

    use Illuminate\Http\Request;

    Route::get('/', function (Request $request) {
        // ...
    });

<a name="dependency-injection-route-parameters"></a>
#### Ін'єкція залежності та параметри маршруту

Якщо ваш метод контролера також очікує вхідні дані від параметра маршруту, ви повинні перерахувати параметри маршруту після інших залежностей. Наприклад, якщо ваш маршрут визначено так:

    use App\Http\Controllers\UserController;

    Route::put('/user/{id}', [UserController::class, 'update']);

Ви все ще можете ввести `Illuminate\Http\Request` і отримати доступ до параметра маршруту `id`, визначивши метод контролера наступним чином:

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;

    class UserController extends Controller
    {
        /**
         * Оновити вказаного користувача.
         */
        public function update(Request $request, string $id): RedirectResponse
        {
            // Оновити користувача...

            return redirect('/users');
        }
    }

<a name="request-path-and-method"></a>
### Шлях, хост і метод запиту

Екземпляр `Illuminate\Http\Request` надає різноманітні методи для перевірки вхідного HTTP-запиту і розширює клас `Symfony\Component\HttpFoundation\Request`. Нижче ми обговоримо декілька найбільш важливих методів.

<a name="retrieving-the-request-path"></a>
#### Отримання шляху запиту

Метод `path` повертає інформацію про шлях запиту. Отже, якщо вхідний запит спрямовано на `http://example.com/foo/bar`, метод `path` поверне `foo/bar`:

    $uri = $request->path();

<a name="inspecting-the-request-path"></a>
#### Перевірка шляху/маршруту запиту

Метод `is` дозволяє перевірити, чи відповідає шлях вхідного запиту заданому шаблону. Ви можете використовувати символ `*` як шаблон при використанні цього методу:

    if ($request->is('admin/*')) {
        // ...
    }

За допомогою методу `routeIs` ви можете визначити, чи відповідає вхідний запит [названий маршрут](/docs/{{version}}/routing#named-routes):

    if ($request->routeIs('admin.*')) {
        // ...
    }

<a name="retrieving-the-request-url"></a>
#### Отримання URL-адреси запиту

Для отримання повної URL-адреси вхідного запиту ви можете використовувати методи `url` або `fullUrl`. Метод `url` поверне URL без рядка запиту, тоді як метод `fullUrl` включає рядок запиту:

    $url = $request->url();

    $urlWithQueryString = $request->fullUrl();

Якщо ви хочете додати дані рядка запиту до поточної URL-адреси, ви можете викликати метод `fullUrlWithQuery`. Цей метод об'єднує заданий масив змінних рядка запиту з поточним рядком запиту:

    $request->fullUrlWithQuery(['type' => 'phone']);

Якщо ви хочете отримати поточну URL-адресу без заданого параметра рядка запиту, ви можете скористатися методом `fullUrlWithoutQuery`:

```php
$request->fullUrlWithoutQuery(['type']);
```

<a name="retrieving-the-request-host"></a>
#### Отримання хосту запиту

Ви можете отримати «хост» вхідного запиту за допомогою методів `host`, `httpHost` і `chemeAndHttpHost`:

    $request->host();
    $request->httpHost();
    $request->schemeAndHttpHost();

<a name="retrieving-the-request-method"></a>
#### Отримання методу запиту

Метод `method` поверне HTTP-дієслово для запиту. Ви можете використовувати метод `isMethod` для перевірки відповідності HTTP-дієслова заданому рядку:

    $method = $request->method();

    if ($request->isMethod('post')) {
        // ...
    }

<a name="request-headers"></a>
### Заголовки запитів

Ви можете отримати заголовок запиту з екземпляра `Illuminate\Http\Request` за допомогою методу `header`. Якщо заголовок відсутній у запиті, буде повернуто `null`. Однак метод `header` приймає необов'язковий другий аргумент, який буде повернуто, якщо заголовок відсутній у запиті:

    $value = $request->header('X-Header-Name');

    $value = $request->header('X-Header-Name', 'default');

Метод `hasHeader` може бути використаний для визначення того, чи містить запит заданий заголовок:

    if ($request->hasHeader('X-Header-Name')) {
        // ...
    }

Для зручності можна використовувати метод `bearerToken` для отримання токену пред'явника з заголовку `Authorization`. Якщо такий заголовок відсутній, буде повернуто порожній рядок:

    $token = $request->bearerToken();

<a name="request-ip-address"></a>
### Запит IP-адреси

Метод `ip` може бути використаний для отримання IP-адреси клієнта, який зробив запит до вашого додатку:

    $ipAddress = $request->ip();

Якщо ви хочете отримати масив IP-адрес, включно з усіма IP-адресами клієнтів, які були переадресовані проксі-серверами, ви можете скористатися методом `ips`. «Оригінальна» IP-адреса клієнта буде в кінці масиву:

    $ipAddresses = $request->ips();

Загалом, IP-адреси слід розглядати як ненадійні, контрольовані користувачем дані і використовувати лише в інформаційних цілях.

<a name="content-negotiation"></a>
### Обговорення змісту

Laravel надає декілька методів для перевірки типів вмісту, що запитуються у вхідному запиті через заголовок `Accept`. По-перше, метод `getAcceptableContentTypes` поверне масив, що містить всі типи вмісту, прийнятні для запиту:

    $contentTypes = $request->getAcceptableContentTypes();

Метод `accepts` приймає масив типів контенту і повертає `true`, якщо будь-який з типів контенту прийнятий запитом. В іншому випадку повертається значення `false`:

    if ($request->accepts(['text/html', 'application/json'])) {
        // ...
    }

Ви можете використовувати метод `prefers`, щоб визначити, який тип контенту із заданого масиву типів контенту є найбільш прийнятним для запиту. Якщо жоден з наданих типів вмісту не приймається запитом, буде повернуто `null`:

    $preferred = $request->prefers(['text/html', 'application/json']);

Оскільки багато додатків обслуговують тільки HTML або JSON, ви можете використовувати метод `expectsJson`, щоб швидко визначити, чи очікує вхідний запит відповідь у форматі JSON:

    if ($request->expectsJson()) {
        // ...
    }

<a name="psr7-requests"></a>
### Запити PSR-7

Стандарт [PSR-7](https://www.php-fig.org/psr/psr-7/) визначає інтерфейси для HTTP-повідомлень, включаючи запити та відповіді. Якщо ви хочете отримати екземпляр запиту PSR-7 замість запиту Laravel, вам спочатку потрібно встановити кілька бібліотек. Laravel використовує компонент *Symfony HTTP Message Bridge* для перетворення типових запитів і відповідей Laravel у PSR-7-сумісні реалізації:

```shell
composer require symfony/psr-http-message-bridge
composer require nyholm/psr7
```

Після встановлення цих бібліотек ви можете отримати запит PSR-7, вказавши в інтерфейсі запиту тип підказки для вашого методу закриття маршруту або контролера:

    use Psr\Http\Message\ServerRequestInterface;

    Route::get('/', function (ServerRequestInterface $request) {
        // ...
    });

> [!NOTE]  
> Якщо ви повернете екземпляр відповіді PSR-7 з маршруту або контролера, він буде автоматично перетворений назад в екземпляр відповіді Laravel і відображений фреймворком.

<a name="input"></a>
## Вхідні дані

<a name="retrieving-input"></a>
### Отримання вхідних даних

<a name="retrieving-all-input-data"></a>
#### Отримання всіх вхідних даних

Ви можете отримати всі вхідні дані вхідного запиту у вигляді масиву за допомогою методу `all`. Цей метод можна використовувати незалежно від того, чи вхідний запит отримано з HTML-форми, чи це XHR-запит:

    $input = $request->all();

Використовуючи метод `collect`, ви можете отримати всі вхідні дані вхідного запиту у вигляді [колекція](/docs/{{version}}/collections):

    $input = $request->collect();

The `collect` method also allows you to retrieve a subset of the incoming request's input as a collection:

    $request->collect('users')->each(function (string $user) {
        // ...
    });

<a name="retrieving-an-input-value"></a>
#### Retrieving an Input Value

Using a few simple methods, you may access all of the user input from your `Illuminate\Http\Request` instance without worrying about which HTTP verb was used for the request. Regardless of the HTTP verb, the `input` method may be used to retrieve user input:

    $name = $request->input('name');

You may pass a default value as the second argument to the `input` method. This value will be returned if the requested input value is not present on the request:

    $name = $request->input('name', 'Sally');

When working with forms that contain array inputs, use "dot" notation to access the arrays:

    $name = $request->input('products.0.name');

    $names = $request->input('products.*.name');

You may call the `input` method without any arguments in order to retrieve all of the input values as an associative array:

    $input = $request->input();

<a name="retrieving-input-from-the-query-string"></a>
#### Retrieving Input From the Query String

While the `input` method retrieves values from the entire request payload (including the query string), the `query` method will only retrieve values from the query string:

    $name = $request->query('name');

If the requested query string value data is not present, the second argument to this method will be returned:

    $name = $request->query('name', 'Helen');

You may call the `query` method without any arguments in order to retrieve all of the query string values as an associative array:

    $query = $request->query();

<a name="retrieving-json-input-values"></a>
#### Retrieving JSON Input Values

When sending JSON requests to your application, you may access the JSON data via the `input` method as long as the `Content-Type` header of the request is properly set to `application/json`. You may even use "dot" syntax to retrieve values that are nested within JSON arrays / objects:

    $name = $request->input('user.name');

<a name="retrieving-stringable-input-values"></a>
#### Retrieving Stringable Input Values

Instead of retrieving the request's input data as a primitive `string`, you may use the `string` method to retrieve the request data as an instance of [`Illuminate\Support\Stringable`](/docs/{{version}}/helpers#fluent-strings):

    $name = $request->string('name')->trim();

<a name="retrieving-boolean-input-values"></a>
#### Retrieving Boolean Input Values

When dealing with HTML elements like checkboxes, your application may receive "truthy" values that are actually strings. For example, "true" or "on". For convenience, you may use the `boolean` method to retrieve these values as booleans. The `boolean` method returns `true` for 1, "1", true, "true", "on", and "yes". All other values will return `false`:

    $archived = $request->boolean('archived');

<a name="retrieving-date-input-values"></a>
#### Retrieving Date Input Values

For convenience, input values containing dates / times may be retrieved as Carbon instances using the `date` method. If the request does not contain an input value with the given name, `null` will be returned:

    $birthday = $request->date('birthday');

The second and third arguments accepted by the `date` method may be used to specify the date's format and timezone, respectively:

    $elapsed = $request->date('elapsed', '!H:i', 'Europe/Madrid');

If the input value is present but has an invalid format, an `InvalidArgumentException` will be thrown; therefore, it is recommended that you validate the input before invoking the `date` method.

<a name="retrieving-enum-input-values"></a>
#### Retrieving Enum Input Values

Input values that correspond to [PHP enums](https://www.php.net/manual/en/language.types.enumerations.php) may also be retrieved from the request. If the request does not contain an input value with the given name or the enum does not have a backing value that matches the input value, `null` will be returned. The `enum` method accepts the name of the input value and the enum class as its first and second arguments:

    use App\Enums\Status;

    $status = $request->enum('status', Status::class);

<a name="retrieving-input-via-dynamic-properties"></a>
#### Retrieving Input via Dynamic Properties

You may also access user input using dynamic properties on the `Illuminate\Http\Request` instance. For example, if one of your application's forms contains a `name` field, you may access the value of the field like so:

    $name = $request->name;

When using dynamic properties, Laravel will first look for the parameter's value in the request payload. If it is not present, Laravel will search for the field in the matched route's parameters.

<a name="retrieving-a-portion-of-the-input-data"></a>
#### Retrieving a Portion of the Input Data

If you need to retrieve a subset of the input data, you may use the `only` and `except` methods. Both of these methods accept a single `array` or a dynamic list of arguments:

    $input = $request->only(['username', 'password']);

    $input = $request->only('username', 'password');

    $input = $request->except(['credit_card']);

    $input = $request->except('credit_card');

> [!WARNING]  
> The `only` method returns all of the key / value pairs that you request; however, it will not return key / value pairs that are not present on the request.

<a name="input-presence"></a>
### Input Presence

You may use the `has` method to determine if a value is present on the request. The `has` method returns `true` if the value is present on the request:

    if ($request->has('name')) {
        // ...
    }

When given an array, the `has` method will determine if all of the specified values are present:

    if ($request->has(['name', 'email'])) {
        // ...
    }

The `hasAny` method returns `true` if any of the specified values are present:

    if ($request->hasAny(['name', 'email'])) {
        // ...
    }

The `whenHas` method will execute the given closure if a value is present on the request:

    $request->whenHas('name', function (string $input) {
        // ...
    });

A second closure may be passed to the `whenHas` method that will be executed if the specified value is not present on the request:

    $request->whenHas('name', function (string $input) {
        // The "name" value is present...
    }, function () {
        // The "name" value is not present...
    });

If you would like to determine if a value is present on the request and is not an empty string, you may use the `filled` method:

    if ($request->filled('name')) {
        // ...
    }

The `anyFilled` method returns `true` if any of the specified values is not an empty string:

    if ($request->anyFilled(['name', 'email'])) {
        // ...
    }

The `whenFilled` method will execute the given closure if a value is present on the request and is not an empty string:

    $request->whenFilled('name', function (string $input) {
        // ...
    });

A second closure may be passed to the `whenFilled` method that will be executed if the specified value is not "filled":

    $request->whenFilled('name', function (string $input) {
        // The "name" value is filled...
    }, function () {
        // The "name" value is not filled...
    });

To determine if a given key is absent from the request, you may use the `missing` and `whenMissing` methods:

    if ($request->missing('name')) {
        // ...
    }

    $request->whenMissing('name', function (array $input) {
        // The "name" value is missing...
    }, function () {
        // The "name" value is present...
    });

<a name="merging-additional-input"></a>
### Merging Additional Input

Sometimes you may need to manually merge additional input into the request's existing input data. To accomplish this, you may use the `merge` method. If a given input key already exists on the request, it will be overwritten by the data provided to the `merge` method:

    $request->merge(['votes' => 0]);

The `mergeIfMissing` method may be used to merge input into the request if the corresponding keys do not already exist within the request's input data:

    $request->mergeIfMissing(['votes' => 0]);

<a name="old-input"></a>
### Old Input

Laravel allows you to keep input from one request during the next request. This feature is particularly useful for re-populating forms after detecting validation errors. However, if you are using Laravel's included [validation features](/docs/{{version}}/validation), it is possible that you will not need to manually use these session input flashing methods directly, as some of Laravel's built-in validation facilities will call them automatically.

<a name="flashing-input-to-the-session"></a>
#### Flashing Input to the Session

The `flash` method on the `Illuminate\Http\Request` class will flash the current input to the [session](/docs/{{version}}/session) so that it is available during the user's next request to the application:

    $request->flash();

You may also use the `flashOnly` and `flashExcept` methods to flash a subset of the request data to the session. These methods are useful for keeping sensitive information such as passwords out of the session:

    $request->flashOnly(['username', 'email']);

    $request->flashExcept('password');

<a name="flashing-input-then-redirecting"></a>
#### Flashing Input Then Redirecting

Since you often will want to flash input to the session and then redirect to the previous page, you may easily chain input flashing onto a redirect using the `withInput` method:

    return redirect('form')->withInput();

    return redirect()->route('user.create')->withInput();

    return redirect('form')->withInput(
        $request->except('password')
    );

<a name="retrieving-old-input"></a>
#### Retrieving Old Input

To retrieve flashed input from the previous request, invoke the `old` method on an instance of `Illuminate\Http\Request`. The `old` method will pull the previously flashed input data from the [session](/docs/{{version}}/session):

    $username = $request->old('username');

Laravel also provides a global `old` helper. If you are displaying old input within a [Blade template](/docs/{{version}}/blade), it is more convenient to use the `old` helper to repopulate the form. If no old input exists for the given field, `null` will be returned:

    <input type="text" name="username" value="{{ old('username') }}">

<a name="cookies"></a>
### Cookies

<a name="retrieving-cookies-from-requests"></a>
#### Retrieving Cookies From Requests

All cookies created by the Laravel framework are encrypted and signed with an authentication code, meaning they will be considered invalid if they have been changed by the client. To retrieve a cookie value from the request, use the `cookie` method on an `Illuminate\Http\Request` instance:

    $value = $request->cookie('name');

<a name="input-trimming-and-normalization"></a>
## Input Trimming and Normalization

By default, Laravel includes the `Illuminate\Foundation\Http\Middleware\TrimStrings` and `Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull` middleware in your application's global middleware stack. These middleware will automatically trim all incoming string fields on the request, as well as convert any empty string fields to `null`. This allows you to not have to worry about these normalization concerns in your routes and controllers.

#### Disabling Input Normalization

If you would like to disable this behavior for all requests, you may remove the two middleware from your application's middleware stack by invoking the `$middleware->remove` method in your application's `bootstrap/app.php` file:

    use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
    use Illuminate\Foundation\Http\Middleware\TrimStrings;

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->remove([
            ConvertEmptyStringsToNull::class,
            TrimStrings::class,
        ]);
    })

If you would like to disable string trimming and empty string conversion for a subset of requests to your application, you may use the `trimStrings` and `convertEmptyStringsToNull` middleware methods within your application's `bootstrap/app.php` file. Both methods accept an array of closures, which should return `true` or `false` to indicate whether input normalization should be skipped:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->convertEmptyStringsToNull(except: [
            fn (Request $request) => $request->is('admin/*'),
        ]);

        $middleware->trimStrings(except: [
            fn (Request $request) => $request->is('admin/*'),
        ]);
    })

<a name="files"></a>
## Files

<a name="retrieving-uploaded-files"></a>
### Retrieving Uploaded Files

You may retrieve uploaded files from an `Illuminate\Http\Request` instance using the `file` method or using dynamic properties. The `file` method returns an instance of the `Illuminate\Http\UploadedFile` class, which extends the PHP `SplFileInfo` class and provides a variety of methods for interacting with the file:

    $file = $request->file('photo');

    $file = $request->photo;

You may determine if a file is present on the request using the `hasFile` method:

    if ($request->hasFile('photo')) {
        // ...
    }

<a name="validating-successful-uploads"></a>
#### Validating Successful Uploads

In addition to checking if the file is present, you may verify that there were no problems uploading the file via the `isValid` method:

    if ($request->file('photo')->isValid()) {
        // ...
    }

<a name="file-paths-extensions"></a>
#### File Paths and Extensions

The `UploadedFile` class also contains methods for accessing the file's fully-qualified path and its extension. The `extension` method will attempt to guess the file's extension based on its contents. This extension may be different from the extension that was supplied by the client:

    $path = $request->photo->path();

    $extension = $request->photo->extension();

<a name="other-file-methods"></a>
#### Other File Methods

There are a variety of other methods available on `UploadedFile` instances. Check out the [API documentation for the class](https://github.com/symfony/symfony/blob/6.0/src/Symfony/Component/HttpFoundation/File/UploadedFile.php) for more information regarding these methods.

<a name="storing-uploaded-files"></a>
### Storing Uploaded Files

To store an uploaded file, you will typically use one of your configured [filesystems](/docs/{{version}}/filesystem). The `UploadedFile` class has a `store` method that will move an uploaded file to one of your disks, which may be a location on your local filesystem or a cloud storage location like Amazon S3.

The `store` method accepts the path where the file should be stored relative to the filesystem's configured root directory. This path should not contain a filename, since a unique ID will automatically be generated to serve as the filename.

The `store` method also accepts an optional second argument for the name of the disk that should be used to store the file. The method will return the path of the file relative to the disk's root:

    $path = $request->photo->store('images');

    $path = $request->photo->store('images', 's3');

If you do not want a filename to be automatically generated, you may use the `storeAs` method, which accepts the path, filename, and disk name as its arguments:

    $path = $request->photo->storeAs('images', 'filename.jpg');

    $path = $request->photo->storeAs('images', 'filename.jpg', 's3');

> [!NOTE]  
> For more information about file storage in Laravel, check out the complete [file storage documentation](/docs/{{version}}/filesystem).

<a name="configuring-trusted-proxies"></a>
## Configuring Trusted Proxies

When running your applications behind a load balancer that terminates TLS / SSL certificates, you may notice your application sometimes does not generate HTTPS links when using the `url` helper. Typically this is because your application is being forwarded traffic from your load balancer on port 80 and does not know it should generate secure links.

To solve this, you may enable the `Illuminate\Http\Middleware\TrustProxies` middleware that is included in your Laravel application, which allows you to quickly customize the load balancers or proxies that should be trusted by your application. Your trusted proxies should be specified using the `trustProxies` middleware method in your application's `bootstrap/app.php` file:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: [
            '192.168.1.1',
            '192.168.1.2',
        ]);
    })

In addition to configuring the trusted proxies, you may also configure the proxy headers that should be trusted:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );
    })

> [!NOTE]  
> If you are using AWS Elastic Load Balancing, your `headers` value should be `Request::HEADER_X_FORWARDED_AWS_ELB`. For more information on the constants that may be used in the `headers` value, check out Symfony's documentation on [trusting proxies](https://symfony.com/doc/7.0/deployment/proxies.html).

<a name="trusting-all-proxies"></a>
#### Trusting All Proxies

If you are using Amazon AWS or another "cloud" load balancer provider, you may not know the IP addresses of your actual balancers. In this case, you may use `*` to trust all proxies:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
    })

<a name="configuring-trusted-hosts"></a>
## Configuring Trusted Hosts

By default, Laravel will respond to all requests it receives regardless of the content of the HTTP request's `Host` header. In addition, the `Host` header's value will be used when generating absolute URLs to your application during a web request.

Typically, you should configure your web server, such as Nginx or Apache, to only send requests to your application that match a given hostname. However, if you do not have the ability to customize your web server directly and need to instruct Laravel to only respond to certain hostnames, you may do so by enabling the `Illuminate\Http\Middleware\TrustHosts` middleware for your application.

To enable the `TrustHosts` middleware, you should invoke the `trustHosts` middleware method in your application's `bootstrap/app.php` file. Using the `at` argument of this method, you may specify the hostnames that your application should respond to. Incoming requests with other `Host` headers will be rejected:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustHosts(at: ['laravel.test']);
    })

By default, requests coming from subdomains of the application's URL are also automatically trusted. If you would like to disable this behavior, you may use the `subdomains` argument:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustHosts(at: ['laravel.test'], subdomains: false);
    })

