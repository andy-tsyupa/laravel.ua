# HTTP-відповіді

- [Створення Відповідей](#creating-responses)
    - [Додавання Заголовків до Відповідей](#attaching-headers-to-responses)
    - [Додавання Кукі до Відповідей](#attaching-cookies-to-responses)
    - [Кукі та Шифрування](#cookies-and-encryption)
- [Перенаправлення](#redirects)
    - [Перенаправлення на Іменовані Маршрути](#redirecting-named-routes)
    - [Перенаправлення на Дії Контролера](#redirecting-controller-actions)
    - [Перенаправлення на Зовнішні Домени](#redirecting-external-domains)
    - [Перенаправлення з Передачею Даних Сесії](#redirecting-with-flashed-session-data)
- [Інші Типи Відповідей](#other-response-types)
    - [Відповіді з Поданнями](#view-responses)
    - [JSON Відповіді](#json-responses)
    - [Завантаження Файлів](#file-downloads)
    - [Файлові Відповіді](#file-responses)
- [Макроси Відповідей](#response-macros)

<a name="creating-responses"></a>
## Створення Відповідей

<a name="strings-arrays"></a>
#### Рядки та Масиви

Всі маршрути та контролери повинні повертати відповідь, яка буде надіслана назад до браузера користувача. Laravel надає декілька способів повернення відповідей. Найпростіша відповідь – це повернення рядка з маршруту або контролера. Фреймворк автоматично перетворить рядок у повноцінну HTTP-відповідь:

    Route::get('/', function () {
        return 'Hello World';
    });

Окрім повернення рядків з маршрутів і контролерів, ви також можете повертати масиви. Фреймворк автоматично перетворить масив у JSON-відповідь:

    Route::get('/', function () {
        return [1, 2, 3];
    });

> [!NOTE]  
> Чи знали ви, що можете також повертати [колекції Eloquent](/docs/{{version}}/eloquent-collections) з ваших маршрутів або контролерів? Вони будуть автоматично перетворені у JSON. Спробуйте!

<a name="response-objects"></a>
#### Об'єкти Відповідей

Зазвичай, ви не будете повертати прості рядки або масиви з дій маршрутів. Замість цього, ви повертатимете повноцінні екземпляри `Illuminate\Http\Response` або [подання](/docs/{{version}}/views).

Повернення повного екземпляра `Response` дозволяє налаштувати код стану HTTP і заголовки відповіді. Екземпляр `Response` успадковується від класу `Symfony\Component\HttpFoundation\Response` який надає різноманітні методи для створення HTTP-відповідей:

    Route::get('/home', function () {
        return response('Hello World', 200)
                      ->header('Content-Type', 'text/plain');
    });

<a name="eloquent-models-and-collections"></a>
#### Моделі та Колекції Eloquent

Ви також можете повертати моделі та колекції [Eloquent ORM](/docs/{{version}}/eloquent) прямо з ваших маршрутів і контролерів. У цьому випадку Laravel автоматично перетворить моделі та колекції в JSON-відповіді, враховуючи [приховані атрибути](/docs/{{version}}/eloquent-serialization#hiding-attributes-from-json):

    use App\Models\User;

    Route::get('/user/{user}', function (User $user) {
        return $user;
    });

<a name="attaching-headers-to-responses"></a>
### Додавання Заголовків до Відповідей

Зверніть увагу, що більшість методів відповіді є з'єднуваними, що дозволяє будувати екземпляри відповідей у ланцюжку. Наприклад, ви можете використовувати метод `header` щоб додати кілька заголовків до відповіді перед її відправленням користувачеві:

    return response($content)
                ->header('Content-Type', $type)
                ->header('X-Header-One', 'Header Value')
                ->header('X-Header-Two', 'Header Value');

Або ви можете використовувати метод `withHeaders` щоб вказати масив заголовків, які потрібно додати до відповіді:

    return response($content)
                ->withHeaders([
                    'Content-Type' => $type,
                    'X-Header-One' => 'Header Value',
                    'X-Header-Two' => 'Header Value',
                ]);

<a name="cache-control-middleware"></a>
#### Проміжне ПЗ для Управління Кешуванням

Laravel включає проміжне ПЗ `cache.headers` яке може бути використане для швидкого встановлення заголовка `Cache-Control` для групи маршрутів. Директиви повинні бути вказані у форматі "snake case", розділені крапкою з комою. Якщо `etag` вказано у списку директив, MD5-хеш вмісту відповіді буде автоматично встановлено як ідентифікатор ETag:

    Route::middleware('cache.headers:public;max_age=2628000;etag')->group(function () {
        Route::get('/privacy', function () {
            // ...
        });

        Route::get('/terms', function () {
            // ...
        });
    });

<a name="attaching-cookies-to-responses"></a>
### Додавання Кукі до Відповідей

Ви можете додати кукі до вихідної відповіді `Illuminate\Http\Response` використовуючи метод `cookie` Вам слід передати ім'я, значення і кількість хвилин, протягом яких кукі вважаються дійсними:

    return response('Hello World')->cookie(
        'name', 'value', $minutes
    );

Метод  `cookie` також приймає кілька додаткових аргументів, які використовуються рідше. Зазвичай ці аргументи мають таке ж значення, як і аргументи, що передаються до PHP-методу [setcookie](https://secure.php.net/manual/en/function.setcookie.php) методи:

    return response('Hello World')->cookie(
        'name', 'value', $minutes, $path, $domain, $secure, $httpOnly
    );

Якщо ви хочете забезпечити, щоб кукі були відправлені з вихідною відповіддю, але ще не маєте екземпляра цієї відповіді, ви можете скористатися фасадом `Cookie` , щоб "поставити в чергу" кукі для додавання до відповіді перед її відправленням. Метод `queue` приймає аргументи, необхідні для створення екземпляра кукі. Ці кукі будуть додані до вихідної відповіді перед її відправленням у браузер:

    use Illuminate\Support\Facades\Cookie;

    Cookie::queue('name', 'value', $minutes);

<a name="generating-cookie-instances"></a>
#### Генерація Екземплярів Кукі

Якщо ви хочете створити екземпляр  `Symfony\Component\HttpFoundation\Cookie` який може бути доданий до екземпляра відповіді пізніше, ви можете скористатися глобальним хелпером `cookie`. Цей кукі не буде відправлений назад клієнту, поки він не буде доданий до екземпляра відповіді:

    $cookie = cookie('name', 'value', $minutes);

    return response('Hello World')->cookie($cookie);

<a name="expiring-cookies-early"></a>
#### Дострокове Закінчення Терміну Дії Кукі

Ви можете видалити кукі, завершивши його дію за допомогою методу `withoutCookie` вихідної відповіді:

    return response('Hello World')->withoutCookie('name');

Якщо у вас ще немає екземпляра вихідної відповіді, ви можете використовувати метод `Cookie` фасаду `expire` щоб завершити дію кукі:

    Cookie::expire('name');

<a name="cookies-and-encryption"></a>
### Кукі та Шифрування

За замовчуванням, завдяки проміжному ПЗ `Illuminate\Cookie\Middleware\EncryptCookies` всі кукі, згенеровані Laravel, шифруються та підписуються, щоб їх не можна було змінити або прочитати клієнтом. Якщо ви хочете вимкнути шифрування для конкретного кукі, ви можете додати його ім'я до `encryptCookies`  властивості проміжного ПЗ `bootstrap/app.php` файл:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: [
            'cookie_name',
        ]);
    })

<a name="redirects"></a>
## Перенаправлення

Перенаправлення відповідей - це випадки, коли `Illuminate\Http\RedirectResponse` і містять відповідні заголовки, необхідні для перенаправлення користувача на іншу URL-адресу. Існує декілька способів згенерувати клас `RedirectResponse` екземпляр. Найпростішим методом є використання глобального `redirect` помічник:

    Route::get('/dashboard', function () {
        return redirect('home/dashboard');
    });

Іноді вам може знадобитися перенаправити користувача на попередню сторінку, наприклад, якщо надіслана форма є недійсною. Ви можете зробити це за допомогою глобального `back` допоміжну функцію. Оскільки ця функція використовує [сесія](/docs/{{version}}/session), переконайтеся, що маршрут, який викликає `back` функція використовує функцію `web` група проміжного програмного забезпечення:

    Route::post('/user/profile', function () {
        // Validate the request...

        return back()->withInput();
    });

<a name="redirecting-named-routes"></a>
### Перенаправлення на іменовані маршрути

Коли ви телефонуєте до `redirect` помічник без параметрів, екземпляр `Illuminate\Routing\Redirector` повертається, що дозволяє викликати будь-який метод на `Redirector` наприклад. Наприклад, щоб згенерувати `RedirectResponse` до названого маршруту, ви можете скористатися `route` метод:

    return redirect()->route('login');

Якщо ваш маршрут має параметри, ви можете передати їх як другий аргумент до команди `route` метод:

    // For a route with the following URI: /profile/{id}

    return redirect()->route('profile', ['id' => 1]);

<a name="populating-parameters-via-eloquent-models"></a>
#### Заповнення параметрів за допомогою красномовних моделей

Якщо ви перенаправляєте на маршрут з параметром «ID», який заповнюється з моделі Eloquent, ви можете передати саму модель. Ідентифікатор буде витягнуто автоматично:

    // Для маршруту з наступними параметрами URI: /profile/{id}

    return redirect()->route('profile', [$user]);

Якщо ви хочете налаштувати значення, яке розміщується в параметрі маршруту, ви можете вказати стовпець у визначенні параметра маршруту (`/profile/{id:slug}`) або ви можете перевизначити `getRouteKey` на вашій моделі Eloquent:

    /**
     * Отримати значення ключа маршруту моделі.
     */
    public function getRouteKey(): mixed
    {
        return $this->slug;
    }

<a name="redirecting-controller-actions"></a>
### Перенаправлення на дії контролера

Ви також можете генерувати перенаправлення на [дії контролера](/docs/{{version}}/controllers). Для цього передайте контролер та назву дії до `action` метод:

    use App\Http\Controllers\UserController;

    return redirect()->action([UserController::class, 'index']);

Якщо ваш маршрут контролера вимагає параметрів, ви можете передати їх як другий аргумент до функції `action` метод:

    return redirect()->action(
        [UserController::class, 'profile'], ['id' => 1]
    );

<a name="redirecting-external-domains"></a>
### Перенаправлення на зовнішні домени

Іноді вам може знадобитися перенаправлення на домен за межами вашої програми. Ви можете зробити це, зателефонувавши за номером `away` який створює метод `RedirectResponse` без будь-якого додаткового кодування URL-адреси, валідації або перевірки:

    return redirect()->away('https://www.google.com');

<a name="redirecting-with-flashed-session-data"></a>
### Перенаправлення з перепрошиванням даних сеансу

Перенаправлення на нову URL-адресу та [прошивання даних до сесії](/docs/{{version}}/session#flash-data) зазвичай виконуються одночасно. Зазвичай це робиться після успішного виконання дії, коли в сеансі з'являється повідомлення про успішне завершення. Для зручності ви можете створити `RedirectResponse` і переносити дані в сеанс за допомогою одного простого ланцюжка методів:

    Route::post('/user/profile', function () {
        // ...

        return redirect('dashboard')->with('status', 'Profile updated!');
    });

After the user is redirected, you may display the flashed message from the [session](/docs/{{version}}/session). For example, using [Blade syntax](/docs/{{version}}/blade):

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

<a name="redirecting-with-input"></a>
#### Перенаправлення за допомогою входу

Ви можете використовувати `withInput` методом, передбаченим `RedirectResponse` щоб перезаписати вхідні дані поточного запиту в сеанс, перш ніж перенаправити користувача на нове місце. Зазвичай це робиться, якщо користувач зіткнувся з помилкою валідації. Після того, як вхідні дані було збережено у сеансі, ви можете легко [отримати його](/docs/{{version}}/requests#retrieving-old-input) під час наступного запиту на повторне заповнення форми:

    return back()->withInput();

<a name="other-response-types"></a>
## Інші типи відповідей

The `response` можна використовувати для створення інших типів екземплярів відповідей. Коли `response` викликається без аргументів, реалізацією методу `Illuminate\Contracts\Routing\ResponseFactory` [договір](/docs/{{version}}/contracts) повертається. Цей контракт передбачає кілька корисних методів для створення відповідей.

<a name="view-responses"></a>
### Переглянути відповіді

Якщо вам потрібен контроль над статусом і заголовками відповіді, а також потрібно повернути [вид](/docs/{{version}}/views) в якості вмісту відповіді слід використовувати `view` метод:

    return response()
                ->view('hello', $data, 200)
                ->header('Content-Type', $type);

Звичайно, якщо вам не потрібно передавати кастомний код статусу HTTP або кастомні заголовки, ви можете використовувати глобальний `view` допоміжну функцію.

<a name="json-responses"></a>
### JSON Responses

Метод `json` автоматично встановить значення Заголовок `Content-Type` на `application/json`, а також конвертувати даний масив в JSON за допомогою PHP-функції `json_encode`:

    return response()->json([
        'name' => 'Abigail',
        'state' => 'CA',
    ]);

Якщо ви хочете створити відповідь JSONP, ви можете використовувати метод `json` у поєднанні з методом `withCallback`:

    return response()
                ->json(['name' => 'Abigail', 'state' => 'CA'])
                ->withCallback($request->input('callback'));

<a name="file-downloads"></a>
### Завантаження файлів

Метод `download` може бути використаний для створення відповіді, яка змушує браузер користувача завантажити файл за вказаним шляхом. Метод `download` приймає ім'я файлу як другий аргумент методу, який визначатиме ім'я файлу, яке побачить користувач, що завантажує файл. Нарешті, ви можете передати масив HTTP-заголовків як третій аргумент методу:

    return response()->download($pathToFile);

    return response()->download($pathToFile, $name, $headers);

> [!WARNING]  
> Symfony HttpFoundation, який керує завантаженням файлів, вимагає, щоб файл, який завантажується, мав ASCII ім'я.

<a name="streamed-downloads"></a>
#### Потокове завантаження

Іноді вам може знадобитися перетворити рядок-відповідь певної операції у відповідь, яку можна завантажити, без необхідності записувати вміст операції на диск. У цьому випадку ви можете скористатися методом `streamDownload`. Цей метод приймає в якості аргументів зворотний виклик, ім'я файлу і необов'язковий масив заголовків:

    use App\Services\GitHub;

    return response()->streamDownload(function () {
        echo GitHub::api('repo')
                    ->contents()
                    ->readme('laravel', 'laravel')['contents'];
    }, 'laravel-readme.md');

<a name="file-responses"></a>
### Файлові відповіді

Метод `file` можна використовувати для відображення файлу, наприклад, зображення або PDF, безпосередньо в браузері користувача замість того, щоб ініціювати завантаження. Цей метод приймає абсолютний шлях до файлу як перший аргумент і масив заголовків як другий аргумент:

    return response()->file($pathToFile);

    return response()->file($pathToFile, $headers);

<a name="response-macros"></a>
## Макроси відповіді

Якщо ви хочете визначити користувацьку реакцію, яку ви можете повторно використовувати у різних маршрутах і контролерах, ви можете скористатися методом `macro` на фасаді `Response`. Зазвичай, вам слід викликати цей метод з методу `boot` одного з методів вашого додатку [постачальники послуг](/docs/{{version}}/providers), наприклад, постачальник послуг `App\Providers\AppServiceProvider`:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\Response;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            Response::macro('caps', function (string $value) {
                return Response::make(strtoupper($value));
            });
        }
    }

Функція `macro` приймає ім'я як перший аргумент і закриття як другий аргумент. Закриття макросу буде виконано при виклику імені макросу з реалізації `ResponseFactory` або помічника `response`:

    return response()->caps('foo');
