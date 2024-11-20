# Маршрутизація

- [Базова маршрутизація](#basic-routing)
    - [Файли маршрутів за замовчуванням](#the-default-route-files)
    - [Маршрути перенаправлення](#redirect-routes)
    - [Перегляд маршрутів](#view-routes)
    - [Перелік ваших маршрутів](#listing-your-routes)
    - [Налаштування маршрутизації](#routing-customization)
- [Параметри маршруту](#route-parameters)
    - [Необхідні параметри](#required-parameters)
    - [Додаткові параметри](#parameters-optional-parameters)
    - [Обмеження регулярних виразів](#parameters-regular-expression-constraints)
- [Іменовані маршрути](#named-routes)
- [Групи маршрутів](#route-groups)
    - [Проміжне програмне забезпечення](#route-group-middleware)
    - [Контролери](#route-group-controllers)
    - [Маршрутизація субдоменів](#route-group-subdomain-routing)
    - [Префікси маршруту](#route-group-prefixes)
    - [Префікси назви маршруту](#route-group-name-prefixes)
- [Прив'язка моделі маршруту](#route-model-binding)
    - [Неявне зв'язування](#implicit-binding)
    - [Неявне зв'язування перечислення](#implicit-enum-binding)
    - [Явна прив'язка](#explicit-binding)
- [Запасні маршрути](#fallback-routes)
- [Обмеження ставок](#rate-limiting)
    - [Визначення обмежувачів швидкості](#defining-rate-limiters)
    - [Приєднання обмежувачів швидкості до маршрутів](#attaching-rate-limiters-to-routes)
- [Підробка методу форми](#form-method-spoofing)
- [Доступ до поточного маршруту](#accessing-the-current-route)
- [Спільне використання ресурсів між країнами походження (CORS)](#cors)
- [Кешування маршрутів](#route-caching)

<a name="basic-routing"></a>
## Базова маршрутизація

Найпростіші маршрути Laravel приймають URI і закриття, забезпечуючи дуже простий і виразний метод визначення маршрутів і поведінки без складних файлів конфігурації маршрутизації:

    use Illuminate\Support\Facades\Route;

    Route::get('/greeting', function () {
        return 'Hello World';
    });

<a name="the-default-route-files"></a>
### Файли маршрутів за замовчуванням

Всі маршрути Laravel визначаються у ваших файлах маршрутів, які знаходяться в каталозі `routes`. Ці файли автоматично завантажуються Laravel, використовуючи конфігурацію, вказану у файлі `bootstrap/app.php` вашого додатку. Файл `routes/web.php` визначає маршрути для вашого веб-інтерфейсу. Цим маршрутам присвоюється `web` [група проміжного програмного забезпечення] (/docs/{{version}}/middleware#laravels-default-middleware-groups), яка надає такі можливості, як стан сеансу і захист CSRF.

Для більшості програм ви почнете з визначення маршрутів у вашому файлі `routes/web.php`. Доступ до маршрутів, визначених у файлі `routes/web.php`, можна отримати, ввівши URL-адресу визначеного маршруту у вашому браузері. Наприклад, ви можете отримати доступ до наступного маршруту, перейшовши за адресою `http://example.com/user` у вашому браузері:

    use App\Http\Controllers\UserController;

    Route::get('/user', [UserController::class, 'index']);

<a name="api-routes"></a>
#### API роутинг

Якщо ваша програма також пропонує API без стану, ви можете увімкнути маршрутизацію API за допомогою команди `install:api` Artisan:

```shell
php artisan install:api
```

Команда `install:api` встановлює [Laravel Sanctum](/docs/{{version}}/sanctum), який забезпечує надійний, але простий засіб автентифікації токенів API, який можна використовувати для автентифікації сторонніх споживачів API, SPA або мобільних додатків. Крім того, команда `install:api` створює файл `routes/api.php`:

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware(Authenticate::using('sanctum'));

Маршрути у файлі `routes/api.php` не мають стану і призначені до групи `api` [група проміжного програмного забезпечення](/docs/{{version}}/middleware#laravels-default-middleware-groups). Крім того, префікс URI `/api` автоматично застосовується до цих маршрутів, тому вам не потрібно вручну застосовувати його до кожного маршруту у файлі. Ви можете змінити префікс, змінивши файл `bootstrap/app.php` вашого додатку:

    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api/admin',
        // ...
    )

<a name="available-router-methods"></a>
#### Доступні методи маршрутизації

Маршрутизатор дозволяє реєструвати маршрути, які реагують на будь-яке HTTP-дієслово:

    Route::get($uri, $callback);
    Route::post($uri, $callback);
    Route::put($uri, $callback);
    Route::patch($uri, $callback);
    Route::delete($uri, $callback);
    Route::options($uri, $callback);

Іноді вам може знадобитися зареєструвати маршрут, який відповідає на декілька HTTP-дієслів. Ви можете зробити це за допомогою методу `match`. Або ви навіть можете зареєструвати маршрут, який відповідає на всі HTTP-дієслова, за допомогою методу `any`:

    Route::match(['get', 'post'], '/', function () {
        // ...
    });

    Route::any('/', function () {
        // ...
    });

> [!NOTE]  
> При визначенні декількох маршрутів, які мають однаковий URI, маршрути з використанням методів `get`, `post`, `put`, `patch`, `delete` і `options` слід визначати перед маршрутами з використанням методів `any`, `match` і `redirect`. Це гарантує, що вхідний запит буде зіставлено з правильним маршрутом.

<a name="dependency-injection"></a>
#### Ін'єкція залежності

Ви можете вказати будь-які залежності, необхідні для вашого маршруту, у сигнатурі зворотного виклику маршруту. Зазначені залежності буде автоматично розпізнано і додано до зворотного виклику [контейнером сервісів] Laravel (/docs/{{version}}/container). Наприклад, ви можете вказати клас `Illuminate\Http\Request`, щоб поточний HTTP-запит автоматично вставлявся у ваш маршрутний зворотний виклик:

    use Illuminate\Http\Request;

    Route::get('/users', function (Request $request) {
        // ...
    });

<a name="csrf-protection"></a>
#### Захист CSRF

Пам'ятайте, що будь-які HTML-форми, що вказують на маршрути `POST`, `PUT`, `PATCH` або `DELETE`, визначені у файлі маршрутів `web`, повинні містити поле токену CSRF. В іншому випадку запит буде відхилено. Детальніше про захист CSRF можна прочитати у [документації CSRF](/docs/{{version}}/csrf):

    <form method="POST" action="/profile">
        @csrf
        ...
    </form>

<a name="redirect-routes"></a>
### Маршрути перенаправлення

Якщо ви визначаєте маршрут, який перенаправляє на інший URI, ви можете використовувати метод `Route::redirect`. Цей метод надає зручний ярлик, тому вам не потрібно визначати повний маршрут або контролер для виконання простого перенаправлення:

    Route::redirect('/here', '/there');

За замовчуванням `Route::redirect` повертає код статусу `302`. Ви можете налаштувати код стану за допомогою необов'язкового третього параметра:

    Route::redirect('/here', '/there', 301);

Або ви можете використовувати метод `Route::permanentRedirect` для повернення коду стану `301`:

    Route::permanentRedirect('/here', '/there');

> [!WARNING]  
> При використанні параметрів маршруту в маршрутах перенаправлення, наступні параметри зарезервовані Laravel і не можуть бути використані: `destination` і `status`.

<a name="view-routes"></a>
### Перегляд маршрутів

Якщо ваш маршрут має повертати лише [view](/docs/{{version}}/views), ви можете скористатися методом `Route::view`. Як і метод `redirect`, цей метод надає простий ярлик, тому вам не потрібно визначати повний маршрут або контролер. Метод `view` приймає URI як перший аргумент і назву подання як другий аргумент. Крім того, ви можете надати масив даних для передачі до подання як необов'язковий третій аргумент:

    Route::view('/welcome', 'welcome');

    Route::view('/welcome', 'welcome', ['name' => 'Taylor']);

> [!WARNING]  
> При використанні параметрів маршруту у маршрутах перегляду, наступні параметри зарезервовані Laravel і не можуть бути використані: `view`, `data`, `status` та `headers`.

<a name="listing-your-routes"></a>
### Перелік ваших маршрутів

Команда `route:list` Artisan може легко надати огляд усіх маршрутів, визначених вашою програмою:

```shell
php artisan route:list
```

За замовчуванням, проміжне програмне забезпечення маршруту, призначене для кожного маршруту, не буде показано у виводі `route:list`, але ви можете наказати Laravel показувати назви груп проміжного програмного забезпечення маршруту і проміжного програмного забезпечення, додавши до команди опцію `-v`:

```shell
php artisan route:list -v

# Розширити групи проміжного програмного забезпечення...
php artisan route:list -vv
```

Ви також можете наказати Laravel показувати лише маршрути, які починаються з заданого URI:

```shell
php artisan route:list --path=api
```

Крім того, ви можете наказати Laravel приховувати маршрути, визначені сторонніми пакунками, ввівши опцію `--except-vendor` при виконанні команди `route:list`:

```shell
php artisan route:list --except-vendor
```

Так само ви можете наказати Laravel показувати лише маршрути, визначені сторонніми пакунками, задавши опцію `--only-vendor` при виконанні команди `route:list`:

```shell
php artisan route:list --only-vendor
```

<a name="routing-customization"></a>
### Налаштування маршрутизації

За замовчуванням маршрути вашого додатку налаштовуються і завантажуються файлом `bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )->create();
```

Однак іноді вам може знадобитися визначити абсолютно новий файл, який міститиме підмножину маршрутів вашої програми. Для цього ви можете створити закриття `then` для методу `withRouting`. У цьому закритті ви можете зареєструвати будь-які додаткові маршрути, необхідні для вашої програми:

```php
use Illuminate\Support\Facades\Route;

->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    then: function () {
        Route::middleware('api')
            ->prefix('webhooks')
            ->name('webhooks.')
            ->group(base_path('routes/webhooks.php'));
    },
)
```

Or, you may even take complete control over route registration by providing a `using` closure to the `withRouting` method. When this argument is passed, no HTTP routes will be registered by the framework and you are responsible for manually registering all routes:

```php
use Illuminate\Support\Facades\Route;

->withRouting(
    commands: __DIR__.'/../routes/console.php',
    using: function () {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    },
)
```

<a name="route-parameters"></a>
## Параметри маршруту

<a name="required-parameters"></a>
### Необхідні параметри

Іноді вам потрібно перехоплювати сегменти URI в межах вашого маршруту. Наприклад, вам може знадобитися захопити ідентифікатор користувача з URL-адреси. Ви можете зробити це, визначивши параметри маршруту:

    Route::get('/user/{id}', function (string $id) {
        return 'User '.$id;
    });

Ви можете визначити стільки параметрів маршруту, скільки потрібно для вашого маршруту:

    Route::get('/posts/{post}/comments/{comment}', function (string $postId, string $commentId) {
        // ...
    });

Параметри маршруту завжди беруться у фігурні дужки `{}` і мають складатися з літерних символів. У назвах параметрів маршруту також допускається використання символів підкреслення (`_`). Параметри маршруту вставляються у виклики/контролери маршруту у порядку їхнього перерахування - імена аргументів виклику/контролера маршруту не мають значення.

<a name="parameters-and-dependency-injection"></a>
#### Параметри та ін'єкція залежностей

Якщо ваш маршрут має залежності, які ви хочете, щоб контейнер сервісу Laravel автоматично вставляв у зворотний виклик вашого маршруту, ви повинні перерахувати параметри маршруту після залежностей:

    use Illuminate\Http\Request;

    Route::get('/user/{id}', function (Request $request, string $id) {
        return 'User '.$id;
    });

<a name="parameters-optional-parameters"></a>
### Додаткові параметри

Іноді вам може знадобитися вказати параметр маршруту, який не завжди присутній в URI. Ви можете зробити це, поставивши знак `?` після назви параметра. Переконайтеся, що відповідна змінна маршруту має значення за замовчуванням:

    Route::get('/user/{name?}', function (?string $name = null) {
        return $name;
    });

    Route::get('/user/{name?}', function (?string $name = 'John') {
        return $name;
    });

<a name="parameters-regular-expression-constraints"></a>
### Обмеження регулярних виразів

Ви можете обмежити формат параметрів маршруту за допомогою методу `where` на екземплярі маршруту. Метод `where` приймає ім'я параметра і регулярний вираз, що визначає, як слід обмежити параметр:

    Route::get('/user/{name}', function (string $name) {
        // ...
    })->where('name', '[A-Za-z]+');

    Route::get('/user/{id}', function (string $id) {
        // ...
    })->where('id', '[0-9]+');

    Route::get('/user/{id}/{name}', function (string $id, string $name) {
        // ...
    })->where(['id' => '[0-9]+', 'name' => '[a-z]+']);

Для зручності деякі поширені шаблони регулярних виразів мають допоміжні методи, які дозволяють швидко додавати обмеження шаблону до маршрутів:

    Route::get('/user/{id}/{name}', function (string $id, string $name) {
        // ...
    })->whereNumber('id')->whereAlpha('name');

    Route::get('/user/{name}', function (string $name) {
        // ...
    })->whereAlphaNumeric('name');

    Route::get('/user/{id}', function (string $id) {
        // ...
    })->whereUuid('id');

    Route::get('/user/{id}', function (string $id) {
        //
    })->whereUlid('id');

    Route::get('/category/{category}', function (string $category) {
        // ...
    })->whereIn('category', ['movie', 'song', 'painting']);

Якщо вхідний запит не відповідає обмеженням шаблону маршруту, буде повернуто HTTP-відповідь 404.

<a name="parameters-global-constraints"></a>
#### Глобальні обмеження

Якщо ви хочете, щоб параметр маршруту завжди обмежувався заданим регулярним виразом, ви можете використовувати метод `pattern`. Вам слід визначити ці шаблони у методі `boot` класу `App\Providers\AppServiceProvider` вашого додатку:

    use Illuminate\Support\Facades\Route;

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::pattern('id', '[0-9]+');
    }

Після визначення шаблону він автоматично застосовується до всіх маршрутів, що використовують цю назву параметра:

    Route::get('/user/{id}', function (string $id) {
        // Виконується тільки якщо {id} є числовим...
    });

<a name="parameters-encoded-forward-slashes"></a>
#### Кодовані прямі похилі риски

Компонент маршрутизації Laravel дозволяє всі символи, окрім `/`, бути присутніми у значеннях параметрів маршруту. Ви повинні явно дозволити `/` бути частиною вашого заповнювача за допомогою регулярного виразу умови `where`:

    Route::get('/search/{search}', function (string $search) {
        return $search;
    })->where('search', '.*');

> [!WARNING]  
> Кодовані прямі скісні риски підтримуються лише в останньому сегменті маршруту.

<a name="named-routes"></a>
## Іменовані маршрути

Іменовані маршрути дозволяють зручно генерувати URL-адреси або перенаправлення для певних маршрутів. Ви можете вказати назву для маршруту, приєднавши метод `name` до визначення маршруту:

    Route::get('/user/profile', function () {
        // ...
    })->name('profile');

Ви також можете вказати назви маршрутів для дій контролера:

    Route::get(
        '/user/profile',
        [UserProfileController::class, 'show']
    )->name('profile');

> [!WARNING]  
> Назви маршрутів завжди повинні бути унікальними.

<a name="generating-urls-to-named-routes"></a>
#### Генерація URL-адрес до іменованих маршрутів

Після того, як ви присвоїли ім'я маршруту, ви можете використовувати це ім'я при створенні URL-адрес або перенаправлень за допомогою допоміжних функцій Laravel `route` і `redirect`:

    // Генерування URL-адрес...
    $url = route('profile');

    // Створення редиректів...
    return redirect()->route('profile');

    return to_route('profile');

Якщо названий маршрут визначає параметри, ви можете передати їх як другий аргумент функції `route`. Вказані параметри буде автоматично вставлено у згенеровану URL-адресу у правильних позиціях:

    Route::get('/user/{id}/profile', function (string $id) {
        // ...
    })->name('profile');

    $url = route('profile', ['id' => 1]);

Якщо ви передасте додаткові параметри в масиві, ці пари ключ/значення будуть автоматично додані до рядка запиту згенерованої URL-адреси:

    Route::get('/user/{id}/profile', function (string $id) {
        // ...
    })->name('profile');

    $url = route('profile', ['id' => 1, 'photos' => 'yes']);

    // /user/1/profile?photos=yes

> [!NOTE]  
> Іноді вам може знадобитися вказати значення за замовчуванням для параметрів URL, таких як поточна локаль, для всього запиту. Для цього ви можете скористатися методом [`URL::defaults`](/docs/{{version}}/urls#default-values).

<a name="inspecting-the-current-route"></a>
#### Перевірка поточного маршруту

Якщо ви хочете визначити, чи поточний запит було направлено на певний іменований маршрут, ви можете використати метод `named` для екземпляра маршруту. Наприклад, ви можете перевірити назву поточного маршруту за допомогою проміжного програмного забезпечення маршруту:

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    /**
     * Обробити вхідний запит.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route()->named('profile')) {
            // ...
        }

        return $next($request);
    }

<a name="route-groups"></a>
## Групи маршрутів

Групи маршрутів дозволяють вам спільно використовувати атрибути маршруту, такі як проміжне програмне забезпечення, для великої кількості маршрутів без необхідності визначати ці атрибути для кожного окремого маршруту.

Вкладені групи намагаються розумно «об'єднати» атрибути зі своєю батьківською групою. Проміжне програмне забезпечення та умови `where` об'єднуються під час додавання імен та префіксів. Роздільники простору імен і косі риски у префіксах URI додаються автоматично, де це доречно.

<a name="route-group-middleware"></a>
### Проміжне програмне забезпечення

Щоб призначити [middleware](/docs/{{version}}/middleware) усім маршрутам у групі, ви можете скористатися методом `middleware` перед визначенням групи. Проміжне програмне забезпечення виконується у тому порядку, у якому воно перелічено у масиві:

    Route::middleware(['first', 'second'])->group(function () {
        Route::get('/', function () {
            // Використовує перше та друге проміжне програмне забезпечення...
        });

        Route::get('/user/profile', function () {
            // Використовує перше та друге проміжне програмне забезпечення...
        });
    });

<a name="route-group-controllers"></a>
### Контролери

Якщо група маршрутів використовує той самий [контролер](/docs/{{version}}/controllers), ви можете використати метод `controller` для визначення спільного контролера для всіх маршрутів у групі. Тоді при визначенні маршрутів вам потрібно буде вказати лише метод контролера, який вони викликають:

    use App\Http\Controllers\OrderController;

    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders/{id}', 'show');
        Route::post('/orders', 'store');
    });

<a name="route-group-subdomain-routing"></a>
### Маршрутизація субдоменів

Групи маршрутів також можна використовувати для керування маршрутизацією субдоменів. Субдоменам можуть бути призначені параметри маршруту так само, як і URI маршруту, що дозволяє вам захопити частину субдомену для використання у вашому маршруті або контролері. Субдомен можна вказати за допомогою виклику методу `domain` перед визначенням групи:

    Route::domain('{account}.example.com')->group(function () {
        Route::get('user/{id}', function (string $account, string $id) {
            // ...
        });
    });

> [!WARNING]  
> Щоб забезпечити доступність ваших маршрутів до піддоменів, вам слід зареєструвати маршрути до піддоменів до реєстрації маршрутів до кореневого домену. Це запобіжить перезапису маршрутів кореневого домену маршрутами субдоменів, які мають той самий шлях URI.

<a name="route-group-prefixes"></a>
### Префікси маршруту

Метод `prefix` можна використовувати для префіксації кожного маршруту у групі з певним URI. Наприклад, ви можете додати до всіх URI маршрутів у групі префікс `admin`:

    Route::prefix('admin')->group(function () {
        Route::get('/users', function () {
            // Matches The "/admin/users" URL
        });
    });

<a name="route-group-name-prefixes"></a>
### Префікси назви маршруту

Метод `name` може бути використаний для додавання заданого рядка до назви кожного маршруту у групі. Наприклад, ви можете додати до назв усіх маршрутів у групі префікс `admin`. Заданий рядок буде додано до назви маршруту саме так, як вказано, тому ми обов'язково додамо у префікс символ `.`:

    Route::name('admin.')->group(function () {
        Route::get('/users', function () {
            // Маршруту присвоєно ім'я «admin.users»...
        })->name('users');
    });

<a name="route-model-binding"></a>
## Прив'язка моделі маршруту

Коли ви додаєте ідентифікатор моделі до маршруту або дії контролера, вам часто доводиться звертатися до бази даних, щоб отримати модель, яка відповідає цьому ідентифікатору. Прив'язка моделі маршруту в Laravel надає зручний спосіб автоматичного додавання екземплярів моделі безпосередньо до ваших маршрутів. Наприклад, замість того, щоб вставляти ідентифікатор користувача, ви можете вставити весь екземпляр моделі `User`, який відповідає даному ідентифікатору.

Перекладено з DeepL.com (безкоштовна версія)

<a name="implicit-binding"></a>
### Неявне зв'язування

Laravel автоматично розв'язує моделі Eloquent, визначені в маршрутах або діях контролерів, чиї імена змінних, підказані типом, збігаються з іменами сегментів маршруту. Наприклад:

    use App\Models\User;

    Route::get('/users/{user}', function (User $user) {
        return $user->email;
    });

Оскільки змінна `$user` вказана як модель `App\Models\User` Eloquent, а ім'я змінної збігається з сегментом URI `{user}`, Laravel автоматично під'єднає екземпляр моделі, ідентифікатор якого збігається з відповідним значенням з URI запиту. Якщо відповідний екземпляр моделі не буде знайдено в базі даних, буде автоматично згенеровано HTTP-відповідь 404.

Звичайно, неявне зв'язування також можливе при використанні методів контролера. Знову ж таки, зверніть увагу, що сегмент URI `{user}` відповідає змінній `$user` у контролері, яка містить підказку типу `App\Models\User`:

    use App\Http\Controllers\UserController;
    use App\Models\User;

    // Визначення маршруту...
    Route::get('/users/{user}', [UserController::class, 'show']);

    // Визначення методу контролера...
    public function show(User $user)
    {
        return view('user.profile', ['user' => $user]);
    }

<a name="implicit-soft-deleted-models"></a>
#### Моделі, що видаляються м'яко

Зазвичай неявне зв'язування моделей не повертає моделі, які було [soft deleted](/docs/{{version}}/eloquent#soft-deleting). Однак ви можете наказати неявному зв'язуванню отримати ці моделі, додавши метод `withTrashed` до визначення вашого маршруту:

    use App\Models\User;

    Route::get('/users/{user}', function (User $user) {
        return $user->email;
    })->withTrashed();

<a name="customizing-the-key"></a>
<a name="customizing-the-default-key-name"></a>
#### Налаштування ключа

Іноді вам може знадобитися розв'язати моделі Eloquent, використовуючи стовпчик, відмінний від `id`. Для цього ви можете вказати стовпець у визначенні параметра маршруту:

    use App\Models\Post;

    Route::get('/posts/{post:slug}', function (Post $post) {
        return $post;
    });

Якщо ви хочете, щоб прив'язка моделі завжди використовувала стовпець бази даних, відмінний від `id`, при отриманні заданого класу моделі, ви можете перевизначити метод `getRouteKeyName` у моделі Eloquent:

    /**
     * Отримайте ключ маршруту для моделі.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

<a name="implicit-model-binding-scoping"></a>
#### Користувацькі клавіші та масштабування

При неявному зв'язуванні декількох моделей Eloquent в одному визначенні маршруту, ви можете визначити область видимості другої моделі Eloquent так, щоб вона була дочірньою до попередньої моделі Eloquent. Наприклад, розглянемо це визначення маршруту, яке знаходить допис у блозі за міткою для певного користувача:

    use App\Models\Post;
    use App\Models\User;

    Route::get('/users/{user}/posts/{post:slug}', function (User $user, Post $post) {
        return $post;
    });

При використанні неявного зв'язування з користувацькими ключами як параметра вкладеного маршруту, Laravel автоматично розширить область видимості запиту, щоб отримати вкладену модель за її батьком, використовуючи конвенції, щоб вгадати ім'я відношення у батька. У цьому випадку буде припущено, що модель `User` має зв'язок з ім'ям `posts` (форма множини імені параметра маршруту), який можна використати для отримання моделі `Post`.

Якщо ви бажаєте, ви можете наказати Laravel охоплювати «дочірні» прив'язки навіть тоді, коли користувацький ключ не надано. Для цього ви можете викликати метод `scopeBindings` під час визначення маршруту:

    use App\Models\Post;
    use App\Models\User;

    Route::get('/users/{user}/posts/{post}', function (User $user, Post $post) {
        return $post;
    })->scopeBindings();

Або ж ви можете наказати цілій групі визначень маршрутів використовувати прив'язки з масштабуванням:

    Route::scopeBindings()->group(function () {
        Route::get('/users/{user}/posts/{post}', function (User $user, Post $post) {
            return $post;
        });
    });

Аналогічно, ви можете явно вказати Laravel не використовувати зв'язування, викликавши метод `withoutScopedBindings`:

    Route::get('/users/{user}/posts/{post:slug}', function (User $user, Post $post) {
        return $post;
    })->withoutScopedBindings();

<a name="customizing-missing-model-behavior"></a>
#### Налаштування поведінки відсутньої моделі

Зазвичай, якщо неявно зв'язану модель не знайдено, буде згенеровано HTTP-відповідь 404. Однак ви можете налаштувати цю поведінку, викликавши метод `missing` під час визначення маршруту. Метод `missing` приймає закриття, яке буде викликано, якщо неявно зв'язану модель не буде знайдено:

    use App\Http\Controllers\LocationsController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Redirect;

    Route::get('/locations/{location:slug}', [LocationsController::class, 'show'])
            ->name('locations.view')
            ->missing(function (Request $request) {
                return Redirect::route('locations.index');
            });

<a name="implicit-enum-binding"></a>
### Неявне зв'язування перечислення

У PHP 8.1 реалізовано підтримку [Переліки](https://www.php.net/manual/en/language.enumerations.backed.php). Щоб доповнити цю можливість, Laravel дозволяє вам створювати підказки у вигляді [рядкового переліку Enum](https://www.php.net/manual/en/language.enumerations.backed.php) у вашому визначенні маршруту, і Laravel викличе маршрут, тільки якщо цей сегмент маршруту відповідає дійсному значенню Enum. В іншому випадку буде автоматично повернуто HTTP-відповідь 404. Наприклад, з урахуванням наступного Enum:

```php
<?php

namespace App\Enums;

enum Category: string
{
    case Fruits = 'fruits';
    case People = 'people';
}
```

Ви можете визначити маршрут, який буде викликано, тільки якщо сегмент маршруту `{category}` має значення `фрукти` або `люди`. В іншому випадку Laravel поверне HTTP-відповідь 404:

```php
use App\Enums\Category;
use Illuminate\Support\Facades\Route;

Route::get('/categories/{category}', function (Category $category) {
    return $category->value;
});
```

<a name="explicit-binding"></a>
### Явна прив'язка

Вам не потрібно використовувати неявну, засновану на конвенціях роздільну здатність моделей Laravel, щоб використовувати прив'язку моделей. Ви також можете явно вказати, як параметри маршруту відповідають моделям. Щоб зареєструвати явне прив'язування, використовуйте метод `model` маршрутизатора, щоб вказати клас для заданого параметра. Ви повинні визначити ваші явні прив'язки моделей на початку методу `boot` вашого класу `AppServiceProvider`:

    use App\Models\User;
    use Illuminate\Support\Facades\Route;

    /**
     * Завантажуйте будь-які сервіси додатків.
     */
    public function boot(): void
    {
        Route::model('user', User::class);
    }

Далі визначте маршрут, який містить параметр `{user}`:

    use App\Models\User;

    Route::get('/users/{user}', function (User $user) {
        // ...
    });

Оскільки ми прив'язали всі параметри `{user}` до моделі `App\Models\User`, екземпляр цього класу буде інжектовано в маршрут. Так, наприклад, запит до `users/1` інжектує екземпляр `User` з бази даних, який має ідентифікатор `1`.

Якщо відповідний екземпляр моделі не знайдено в базі даних, буде автоматично згенеровано HTTP-відповідь 404.

<a name="customizing-the-resolution-logic"></a>
#### Налаштування логіки роздільної здатності

Якщо ви бажаєте визначити власну логіку розв'язання зв'язування моделі, ви можете скористатися методом `Route::bind`. Закриття, яке ви передаєте методу `bind`, отримає значення сегмента URI і має повернути екземпляр класу, який слід вставити в маршрут. Знову ж таки, ця настройка повинна відбуватися в методі `boot` вашого додатку `AppServiceProvider`:

    use App\Models\User;
    use Illuminate\Support\Facades\Route;

    /**
     * Завантажуйте будь-які сервіси додатків.
     */
    public function boot(): void
    {
        Route::bind('user', function (string $value) {
            return User::where('name', $value)->firstOrFail();
        });
    }

Крім того, ви можете перевизначити метод `resolveRouteBinding` у вашій моделі Eloquent. Цей метод отримає значення сегмента URI і має повернути екземпляр класу, який слід вставити у маршрут:

    /**
     * Отримати модель для обмеженого значення.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('name', $value)->firstOrFail();
    }

Якщо маршрут використовує [неявне масштабування зв'язування](#implicit-model-binding-scoping) метод `resolveChildRouteBinding` буде використано для вирішення прив'язки дочірньої моделі батьківської моделі:

    /**
     * Отримати дочірню модель для обмеженого значення.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return parent::resolveChildRouteBinding($childType, $value, $field);
    }

<a name="fallback-routes"></a>
## Запасні маршрути

За допомогою методу `Route::fallback` ви можете визначити маршрут, який буде виконано, коли жоден інший маршрут не відповідає вхідному запиту. Зазвичай, необроблені запити автоматично повертають сторінку «404» через обробник виключень вашого додатку. Однак, оскільки ви зазвичай визначаєте маршрут `fallback` у вашому файлі `routes/web.php`, до нього буде застосовано все проміжне програмне забезпечення групи проміжного програмного забезпечення `web`. Ви можете додати до цього маршруту додаткове проміжне програмне забезпечення за потреби:

    Route::fallback(function () {
        // ...
    });

> [!WARNING]  
> Запасний маршрут завжди повинен бути останнім маршрутом, зареєстрованим вашим додатком.

<a name="rate-limiting"></a>
## Обмеження ставок

<a name="defining-rate-limiters"></a>
### Визначення обмежувачів швидкості

Laravel включає потужні та настроювані сервіси обмеження швидкості, які ви можете використовувати для обмеження кількості трафіку для певного маршруту або групи маршрутів. Для початку вам слід визначити конфігурацію обмежувача швидкості, яка відповідає потребам вашого додатку.

Обмежувачі швидкості можуть бути визначені у методі `boot` класу `App\Providers\AppServiceProvider` вашого додатку:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Завантажуйте будь-які сервіси додатків.
 */
protected function boot(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });
}
```

Обмежувачі швидкості визначаються за допомогою методу `for` фасаду `RateLimiter`. Метод `for` приймає ім'я обмежувача швидкості та закриття, яке повертає конфігурацію ліміту, що має застосовуватися до маршрутів, які призначено обмежувачу швидкості. Конфігурація обмеження є екземплярами класу `Illuminate\Cache\RateLimiting\Limit`. Цей клас містить корисні «будівельні» методи, за допомогою яких ви можете швидко визначити ліміт. Ім'я обмежувача швидкості може бути будь-яким рядком за вашим бажанням:

    use Illuminate\Cache\RateLimiting\Limit;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\RateLimiter;

    /**
     * Завантажуйте будь-які сервіси додатків.
     */
    protected function boot(): void
    {
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000);
        });
    }

Якщо вхідний запит перевищує вказаний ліміт, Laravel автоматично поверне відповідь з кодом статусу HTTP 429. Якщо ви хочете визначити власну відповідь, яка має бути повернута при перевищенні ліміту, ви можете використовувати метод `response`:

    RateLimiter::for('global', function (Request $request) {
        return Limit::perMinute(1000)->response(function (Request $request, array $headers) {
            return response('Custom response...', 429, $headers);
        });
    });

Оскільки зворотні виклики обмежувача швидкості отримують вхідний екземпляр HTTP-запиту, ви можете створити відповідне обмеження швидкості динамічно на основі вхідного запиту або автентифікованого користувача:

    RateLimiter::for('uploads', function (Request $request) {
        return $request->user()->vipCustomer()
                    ? Limit::none()
                    : Limit::perMinute(100);
    });

<a name="segmenting-rate-limits"></a>
#### Ліміти ставок для сегментації

Іноді вам може знадобитися обмежити швидкість сегментації довільним значенням. Наприклад, ви можете дозволити користувачам доступ до певного маршруту 100 разів на хвилину з кожної IP-адреси. Для цього ви можете використати метод `by` при створенні ліміту швидкості:

    RateLimiter::for('uploads', function (Request $request) {
        return $request->user()->vipCustomer()
                    ? Limit::none()
                    : Limit::perMinute(100)->by($request->ip());
    });

Щоб проілюструвати цю функцію на іншому прикладі, ми можемо обмежити доступ до маршруту до 100 разів на хвилину для ідентифікатора користувача з автентифікацією або 10 разів на хвилину для IP-адреси для гостей:

    RateLimiter::for('uploads', function (Request $request) {
        return $request->user()
                    ? Limit::perMinute(100)->by($request->user()->id)
                    : Limit::perMinute(10)->by($request->ip());
    });

<a name="multiple-rate-limits"></a>
#### Кілька тарифних лімітів

Якщо потрібно, ви можете повернути масив лімітів швидкості для заданої конфігурації обмежувача швидкості. Кожне обмеження швидкості буде оцінено для маршруту відповідно до порядку, в якому вони розміщені у масиві:

    RateLimiter::for('login', function (Request $request) {
        return [
            Limit::perMinute(500),
            Limit::perMinute(3)->by($request->input('email')),
        ];
    });

<a name="attaching-rate-limiters-to-routes"></a>
### Приєднання обмежувачів швидкості до маршрутів

Обмежувачі швидкості можна приєднати до маршрутів або груп маршрутів за допомогою `throttle` [проміжного програмного забезпечення](/docs/{{version}}/middleware). Проміжне програмне забезпечення throttle приймає ім'я обмежувача швидкості, який ви хочете призначити до маршруту:

    Route::middleware(['throttle:uploads'])->group(function () {
        Route::post('/audio', function () {
            // ...
        });

        Route::post('/video', function () {
            // ...
        });
    });

<a name="throttling-with-redis"></a>
#### Дроселювання за допомогою Redis

За замовчуванням проміжне програмне забезпечення `throttle` зіставляється з класом `Illuminate\Routing\Middleware\ThrottleRequests`. Однак, якщо ви використовуєте Redis як драйвер кешу вашого додатку, ви можете вказати Laravel використовувати Redis для керування обмеженням швидкості. Для цього вам слід використати метод `throttleWithRedis` у файлі `bootstrap/app.php` вашого додатку. Цей метод зіставляє проміжне програмне забезпечення `throttle` з класом проміжного програмного забезпечення `Illuminate\Routing\Middleware\ThrottleRequestsWithRedis`:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->throttleWithRedis();
        // ...
    })

<a name="form-method-spoofing"></a>
## Підробка методу форми

HTML-форми не підтримують дії `PUT`, `PATCH` або `DELETE`. Тому при визначенні маршрутів `PUT`, `PATCH` або `DELETE`, які викликаються з HTML-форми, вам потрібно буде додати до форми приховане поле `_method`. Значення, передане з полем `_method`, буде використано як метод HTTP-запиту:

    <form action="/example" method="POST">
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    </form>

Для зручності ви можете скористатися директивою `@method` [Blade](/docs/{{version}}/blade) для створення поля введення `_method`:

    <form action="/example" method="POST">
        @method('PUT')
        @csrf
    </form>

<a name="accessing-the-current-route"></a>
## Доступ до поточного маршруту

Ви можете використовувати методи `current`, `currentRouteName` та `currentRouteAction` на фасаді `Route` для доступу до інформації про маршрут, який обробляє вхідний запит:

    use Illuminate\Support\Facades\Route;

    $route = Route::current(); // Illuminate\Routing\Route
    $name = Route::currentRouteName(); // string
    $action = Route::currentRouteAction(); // string

Ви можете звернутися до документації API як для [базового класу фасаду Маршруту](https://laravel.com/api/{{version}}/Illuminate/Routing/Router.html), так і для [екземпляра Маршруту](https://laravel.com/api/{{version}}/Illuminate/Routing/Route.html), щоб ознайомитися з усіма методами, доступними у класах маршрутизатора та маршруту.

<a name="cors"></a>
## Спільне використання ресурсів між країнами походження (CORS)

Laravel може автоматично відповідати на HTTP-запити CORS `OPTIONS` зі значеннями, які ви налаштовуєте. Запити `OPTIONS` будуть автоматично оброблятися `HandleCors` [проміжним програмним забезпеченням](/docs/{{version}}/middleware), яке автоматично включається до глобального стеку проміжного програмного забезпечення вашого додатку.

Іноді вам може знадобитися налаштувати значення конфігурації CORS для вашої програми. Ви можете зробити це, опублікувавши файл конфігурації `cors` за допомогою команди `config:publish` Artisan:

```shell
php artisan config:publish cors
```

Ця команда розмістить конфігураційний файл `cors.php` у каталозі `config` вашої програми.

> [!NOTE]  
> Для отримання додаткової інформації про CORS та заголовки CORS зверніться до [веб-документації MDN про CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS#The_HTTP_response_headers).

<a name="route-caching"></a>
## Кешування маршрутів

Під час розгортання вашого додатку на виробництві, вам слід скористатися перевагами кешу маршрутів Laravel. Використання кешу маршрутів значно скоротить час, необхідний для реєстрації всіх маршрутів вашого додатку. Щоб згенерувати кеш маршрутів, виконайте команду `route:cache` Artisan:

```shell
php artisan route:cache
```

Після запуску цієї команди ваш файл кешованих маршрутів буде завантажуватися при кожному запиті. Пам'ятайте, що якщо ви додаєте нові маршрути, вам потрібно буде згенерувати новий кеш маршрутів. Тому вам слід виконувати команду `route:cache` лише під час розгортання вашого проекту.

Ви можете скористатися командою `route:clear` для очищення кешу маршрутів:

```shell
php artisan route:clear
```
