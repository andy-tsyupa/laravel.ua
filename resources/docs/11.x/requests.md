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

Метод `collect` також дозволяє отримати підмножину вхідних даних запиту у вигляді колекції:

    $request->collect('users')->each(function (string $user) {
        // ...
    });

<a name="retrieving-an-input-value"></a>
#### Отримання вхідного значення

Використовуючи кілька простих методів, ви можете отримати доступ до всього користувацького вводу з вашого екземпляра `Illuminate\Http\Request`, не турбуючись про те, яке HTTP-дієслово було використано для запиту. Незалежно від HTTP-дієслова, метод `input` може бути використаний для отримання даних користувача:

    $name = $request->input('name');

Ви можете передати значення за замовчуванням як другий аргумент методу `input`. Це значення буде повернуто, якщо у запиті відсутнє запитуване вхідне значення:

    $name = $request->input('name', 'Sally');

При роботі з формами, що містять вхідні дані у вигляді масивів, використовуйте «крапкову» нотацію для доступу до масивів:

    $name = $request->input('products.0.name');

    $names = $request->input('products.*.name');

Ви можете викликати метод `input` без жодних аргументів, щоб отримати всі вхідні значення у вигляді асоціативного масиву:

    $input = $request->input();

<a name="retrieving-input-from-the-query-string"></a>
#### Отримання вхідних даних з рядка запиту

У той час як метод `input` повертає значення з усього корисного навантаження запиту (включно з рядком запиту), метод `query` повертає значення лише з рядка запиту:

    $name = $request->query('name');

Якщо даних у запитуваному рядку значення запиту немає, буде повернуто другий аргумент цього методу:

    $name = $request->query('name', 'Helen');

Ви можете викликати метод `query` без жодних аргументів, щоб отримати всі значення рядка запиту у вигляді асоціативного масиву:

    $query = $request->query();

<a name="retrieving-json-input-values"></a>
#### Отримання вхідних значень JSON

Надсилаючи JSON-запити до вашого додатку, ви можете отримати доступ до JSON-даних за допомогою методу `input`, якщо в заголовку `Content-Type` запиту правильно встановлено значення `application/json`. Ви навіть можете використовувати «крапковий» синтаксис для отримання значень, вкладених у масиви/об'єкти JSON:

    $name = $request->input('user.name');

<a name="retrieving-stringable-input-values"></a>
#### Отримання рядкових вхідних значень

Замість того, щоб отримувати вхідні дані запиту як примітивний `string`, ви можете використовувати метод `string` для отримання даних запиту як екземпляр [`Illuminate\Support\Stringable`](/docs/{{version}}/helpers#fluent-strings):

    $name = $request->string('name')->trim();

<a name="retrieving-boolean-input-values"></a>
#### Отримання булевих вхідних значень

При роботі з елементами HTML, такими як прапорці, ваша програма може отримувати «істинні» значення, які насправді є рядками. Наприклад, «true» або «on». Для зручності ви можете використовувати метод `boolean` для отримання цих значень як булевих. Метод `boolean` повертає значення `true` для 1, «1», true, «true», «on» і «yes». Для всіх інших значень повертається `false`:

    $archived = $request->boolean('archived');

<a name="retrieving-date-input-values"></a>
#### Отримання вхідних значень дати

Для зручності вхідні значення, що містять дати/час, можуть бути отримані як екземпляри Carbon за допомогою методу `date`. Якщо запит не містить вхідного значення з такою назвою, буде повернуто `null`:

    $birthday = $request->date('birthday');

Другий і третій аргументи, що приймаються методом `date`, можуть бути використані для вказівки формату дати і часового поясу відповідно:

    $elapsed = $request->date('elapsed', '!H:i', 'Europe/Madrid');

Якщо вхідне значення присутнє, але має невірний формат, буде згенеровано виключення `InvalidArgumentException`; тому рекомендується перевіряти вхідні дані перед викликом методу `date`.

<a name="retrieving-enum-input-values"></a>
#### Отримання вхідних значень перечислення

Із запиту також можуть бути отримані вхідні значення, які відповідають [перелікам PHP](https://www.php.net/manual/en/language.types.enumerations.php). Якщо запит не містить вхідного значення з вказаним іменем або перерахування не має відповідного вхідному значенню опорного значення, буде повернуто `null`. Метод `enum` приймає ім'я вхідного значення та клас зчислення як перший та другий аргументи:

    use App\Enums\Status;

    $status = $request->enum('status', Status::class);

<a name="retrieving-input-via-dynamic-properties"></a>
#### Отримання вхідних даних за допомогою динамічних властивостей

Ви також можете отримати доступ до даних користувача за допомогою динамічних властивостей екземпляра `Illuminate\Http\Request`. Наприклад, якщо одна з форм вашого додатку містить поле `name`, ви можете отримати доступ до значення цього поля таким чином:

    $name = $request->name;

При використанні динамічних властивостей Laravel спочатку шукає значення параметра в корисному навантаженні запиту. Якщо його там немає, Laravel шукатиме поле у параметрах відповідного маршруту.

<a name="retrieving-a-portion-of-the-input-data"></a>
#### Отримання частини вхідних даних

Якщо вам потрібно отримати підмножину вхідних даних, ви можете використовувати методи `only` та `except`. Обидва ці методи приймають один `масив` або динамічний список аргументів:

    $input = $request->only(['username', 'password']);

    $input = $request->only('username', 'password');

    $input = $request->except(['credit_card']);

    $input = $request->except('credit_card');

> [!WARNING]  
> Метод `only` повертає всі пари ключ/значення, які ви запитуєте; однак він не повертає пари ключ/значення, яких немає у запиті.

<a name="input-presence"></a>
### Присутність на вході

Ви можете використовувати метод `has`, щоб визначити, чи присутнє значення в запиті. Метод `has` повертає значення `true`, якщо значення присутнє у запиті:

    if ($request->has('name')) {
        // ...
    }

При отриманні масиву метод `has` визначить, чи всі задані значення є присутніми:

    if ($request->has(['name', 'email'])) {
        // ...
    }

Метод `hasAny` повертає значення `true`, якщо будь-яке з вказаних значень присутнє:

    if ($request->hasAny(['name', 'email'])) {
        // ...
    }

Метод `whenHas` виконає задане закриття, якщо значення присутнє у запиті:

    $request->whenHas('name', function (string $input) {
        // ...
    });

Друге закриття може бути передано до методу `whenHas`, який буде виконано, якщо вказане значення відсутнє у запиті:

    $request->whenHas('name', function (string $input) {
        // The "name" value is present...
    }, function () {
        // The "name" value is not present...
    });

Якщо ви хочете визначити, чи присутнє значення в запиті і чи не є воно порожнім рядком, ви можете використати метод `filled`:

    if ($request->filled('name')) {
        // ...
    }

Метод `anyFilled` повертає значення `true`, якщо жодне з вказаних значень не є порожнім рядком:

    if ($request->anyFilled(['name', 'email'])) {
        // ...
    }

Метод `whenFilled` виконає задане закриття, якщо значення присутнє в запиті і не є порожнім рядком:

    $request->whenFilled('name', function (string $input) {
        // ...
    });

Друге закриття може бути передано до методу `whenFilled`, який буде виконано, якщо вказане значення не буде «заповнене»:

    $request->whenFilled('name', function (string $input) {
        // The "name" value is filled...
    }, function () {
        // The "name" value is not filled...
    });

Щоб визначити, чи відсутній ключ у запиті, ви можете використовувати методи `missing` та `whenMissing`:

    if ($request->missing('name')) {
        // ...
    }

    $request->whenMissing('name', function (array $input) {
        // The "name" value is missing...
    }, function () {
        // The "name" value is present...
    });

<a name="merging-additional-input"></a>
### Об'єднання додаткових вхідних даних

Іноді вам може знадобитися вручну об'єднати додаткові вхідні дані з наявними у запиті. Для цього ви можете скористатися методом `merge`. Якщо заданий вхідний ключ вже існує у запиті, він буде замінений даними, наданими методу `merge`:

    $request->merge(['votes' => 0]);

Метод `mergeIfMissing` можна використовувати для об'єднання вхідних даних у запиті, якщо відповідні ключі ще не існують у вхідних даних запиту:

    $request->mergeIfMissing(['votes' => 0]);

<a name="old-input"></a>
### Старий вхід

Laravel дозволяє зберігати дані з одного запиту під час наступного запиту. Ця функція особливо корисна для повторного заповнення форм після виявлення помилок валідації. Однак, якщо ви використовуєте включені в Laravel [функції валідації](/docs/{{version}}/validation), можливо, вам не потрібно буде вручну використовувати ці методи збереження даних сеансу безпосередньо, оскільки деякі вбудовані засоби валідації Laravel викликають їх автоматично.

<a name="flashing-input-to-the-session"></a>
#### Блимає вхід до сесії

Метод `flash` у класі `Illuminate\Http\Request` спалахне поточним введенням у [session](/docs/{{version}}/session), щоб воно було доступне під час наступного запиту користувача до програми:

    $request->flash();

Ви також можете використовувати методи `flashOnly` і `flashExcept` для запису підмножини даних запиту до сеансу. Ці методи корисні для збереження конфіденційної інформації, наприклад, паролів, поза сеансом:

    $request->flashOnly(['username', 'email']);

    $request->flashExcept('password');

<a name="flashing-input-then-redirecting"></a>
#### Миготливий вхід, а потім перенаправлення

Оскільки вам часто потрібно буде спалахувати введення в сеансі, а потім перенаправляти на попередню сторінку, ви можете легко зв'язати спалахування введення з перенаправленням за допомогою методу `withInput`:

    return redirect('form')->withInput();

    return redirect()->route('user.create')->withInput();

    return redirect('form')->withInput(
        $request->except('password')
    );

<a name="retrieving-old-input"></a>
#### Отримання старих даних

Щоб отримати дані з попереднього запиту, викличте метод `old` для екземпляра `Illuminate\Http\Request`. Метод `old` витягне попередні дані з [session](/docs/{{version}}/session):

    $username = $request->old('username');

Laravel також надає глобальний помічник `old`. Якщо ви відображаєте старі дані у шаблоні [Blade template](/docs/{{version}}/blade), зручніше використовувати `old` для повторного заповнення форми. Якщо для даного поля не існує старих даних, буде повернуто `null`:

    <input type="text" name="username" value="{{ old('username') }}">

<a name="cookies"></a>
### Куки.

<a name="retrieving-cookies-from-requests"></a>
#### Отримання файлів cookie із запитів

Всі файли cookie, створені фреймворком Laravel, зашифровані і підписані кодом автентифікації, що означає, що вони будуть вважатися недійсними, якщо будуть змінені клієнтом. Щоб отримати значення cookie із запиту, використовуйте метод `cookie` в екземплярі `Illuminate\Http\Request`:

    $value = $request->cookie('name');

<a name="input-trimming-and-normalization"></a>
## Обрізання та нормалізація вхідних даних

За замовчуванням Laravel включає проміжне програмне забезпечення `Illuminate\Foundation\Http\Middleware\TrimStrings` та `Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull` у глобальний стек проміжного програмного забезпечення вашого додатку. Це проміжне програмне забезпечення буде автоматично обрізати всі вхідні рядкові поля в запиті, а також перетворювати всі порожні рядкові поля в `null`. Це дозволяє вам не турбуватися про нормалізацію у ваших маршрутах і контролерах.

#### Вимкнення нормалізації вводу

Якщо ви хочете вимкнути цю поведінку для всіх запитів, ви можете видалити обидва проміжні модулі зі стека проміжного програмного забезпечення вашого додатка, викликавши метод `$middleware->remove` у файлі `bootstrap/app.php` вашого додатка:

    use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
    use Illuminate\Foundation\Http\Middleware\TrimStrings;

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->remove([
            ConvertEmptyStringsToNull::class,
            TrimStrings::class,
        ]);
    })

Якщо ви хочете вимкнути обрізання рядків та перетворення порожніх рядків для підмножини запитів до вашого додатку, ви можете скористатися методами проміжного програмного забезпечення `trimStrings` та `convertEmptyStringsToNull` у файлі `bootstrap/app.php` вашого додатку. Обидва методи приймають масив закриттів, які мають повертати значення `true` або `false`, щоб вказати, чи слід пропустити нормалізацію вхідних даних:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->convertEmptyStringsToNull(except: [
            fn (Request $request) => $request->is('admin/*'),
        ]);

        $middleware->trimStrings(except: [
            fn (Request $request) => $request->is('admin/*'),
        ]);
    })

<a name="files"></a>
## Файли

<a name="retrieving-uploaded-files"></a>
### Отримання завантажених файлів

Ви можете отримати завантажені файли з екземпляра `Illuminate\Http\Request` за допомогою методу `file` або за допомогою динамічних властивостей. Метод `file` повертає екземпляр класу `Illuminate\Http\UploadedFile`, який розширює клас PHP `SplFileInfo` і надає різноманітні методи для взаємодії з файлом:

    $file = $request->file('photo');

    $file = $request->photo;

Ви можете визначити наявність файлу в запиті за допомогою методу `hasFile`:

    if ($request->hasFile('photo')) {
        // ...
    }

<a name="validating-successful-uploads"></a>
#### Перевірка успішних завантажень

На додаток до перевірки наявності файлу, ви можете перевірити, чи не було проблем із завантаженням файлу за допомогою методу `isValid`:

    if ($request->file('photo')->isValid()) {
        // ...
    }

<a name="file-paths-extensions"></a>
#### Шляхи та розширення файлів

Клас `UploadedFile` також містить методи для доступу до повного шляху до файлу та його розширення. Метод `extension` спробує визначити розширення файлу на основі його вмісту. Це розширення може відрізнятися від розширення, яке було надано клієнтом:

    $path = $request->photo->path();

    $extension = $request->photo->extension();

<a name="other-file-methods"></a>
#### Інші файлові методи

Існує безліч інших методів, доступних для екземплярів `UploadedFile`. Зверніться до [API документації для класу](https://github.com/symfony/symfony/blob/6.0/src/Symfony/Component/HttpFoundation/File/UploadedFile.php) для отримання додаткової інформації про ці методи.

<a name="storing-uploaded-files"></a>
### Зберігання завантажених файлів

Для зберігання завантаженого файлу ви зазвичай використовуєте одну з налаштованих вами [файлових систем](/docs/{{version}}/filesystem). Клас `UploadedFile` має метод `store`, який переміщує завантажений файл на один з ваших дисків, що може бути місцем у вашій локальній файловій системі або хмарному сховищі, наприклад, Amazon S3.

Метод `store` приймає шлях, де має бути збережено файл відносно налаштованого кореневого каталогу файлової системи. Цей шлях не повинен містити ім'я файлу, оскільки замість нього буде автоматично згенеровано унікальний ідентифікатор.

Метод `store` також приймає необов'язковий другий аргумент для назви диска, на якому слід зберігати файл. Метод поверне шлях до файлу відносно кореня диска:

    $path = $request->photo->store('images');

    $path = $request->photo->store('images', 's3');

Якщо ви не бажаєте, щоб ім'я файлу генерувалося автоматично, ви можете скористатися методом `storeAs`, який приймає шлях, ім'я файлу та ім'я диска як аргументи:

    $path = $request->photo->storeAs('images', 'filename.jpg');

    $path = $request->photo->storeAs('images', 'filename.jpg', 's3');

> [!NOTE]  
> Для отримання додаткової інформації про зберігання файлів у Laravel перегляньте повну [документацію про зберігання файлів](/docs/{{version}}/filesystem).

<a name="configuring-trusted-proxies"></a>
## Налаштування довірених проксі-серверів

Під час запуску ваших додатків за балансувальником навантаження, який припиняє дію сертифікатів TLS / SSL, ви можете помітити, що ваш додаток іноді не генерує HTTPS-посилання при використанні допоміжного засобу `url`. Зазвичай це відбувається тому, що ваш додаток отримує трафік від балансувальника навантаження через порт 80 і не знає, що він повинен генерувати безпечні посилання.

Щоб вирішити цю проблему, ви можете увімкнути проміжне програмне забезпечення `Illuminate\Http\Middleware\TrustProxies`, яке входить до складу вашого додатку Laravel, що дозволить вам швидко налаштувати балансувальники навантаження або проксі-сервери, яким має довіряти ваш додаток. Довірені проксі-сервери повинні бути вказані за допомогою методу `trustProxies` у файлі `bootstrap/app.php` вашого додатку:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: [
            '192.168.1.1',
            '192.168.1.2',
        ]);
    })

Окрім налаштування довірених проксі-серверів, ви також можете налаштувати заголовки проксі-серверів, яким слід довіряти:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );
    })

> [!NOTE]  
> Якщо ви використовуєте AWS Elastic Load Balancing, ваше значення `headers` має бути `Request::HEADER_X_FORWARDED_AWS_ELB`. Для отримання додаткової інформації про константи, які можна використовувати у значенні `headers`, зверніться до документації Symfony щодо [довірчих проксі-серверів](https://symfony.com/doc/7.0/deployment/proxies.html).

<a name="trusting-all-proxies"></a>
#### Довіра до всіх проксі-серверів

Якщо ви використовуєте Amazon AWS або інший «хмарний» провайдер балансувальника навантаження, ви можете не знати IP-адреси ваших реальних балансувальників. У цьому випадку ви можете використовувати `*`, щоб довіряти всім проксі-серверам:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
    })

<a name="configuring-trusted-hosts"></a>
## Налаштування довірених хостів

За замовчуванням Laravel відповідатиме на всі отримані запити незалежно від вмісту заголовка `Host` HTTP-запиту. Крім того, значення заголовка `Host` буде використовуватися при генерації абсолютних URL-адрес до вашого додатку під час веб-запиту.

Зазвичай, ви повинні налаштувати ваш веб-сервер, наприклад, Nginx або Apache, так, щоб він надсилав лише ті запити до вашого додатку, які відповідають заданому імені хоста. Однак, якщо у вас немає можливості налаштувати веб-сервер безпосередньо і вам потрібно вказати Laravel відповідати тільки на певні імена хостів, ви можете зробити це, увімкнувши проміжне програмне забезпечення `Illuminate\Http\Middleware\TrustHosts` для вашого додатку.

Щоб увімкнути проміжне програмне забезпечення `TrustHosts`, вам слід викликати метод проміжного програмного забезпечення `trustHosts` у файлі `bootstrap/app.php` вашого додатку. Використовуючи аргумент `at` цього методу, ви можете вказати імена хостів, на які повинен відповідати ваш додаток. Вхідні запити з іншими заголовками `Host` будуть відхилені:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustHosts(at: ['laravel.test']);
    })

За замовчуванням, запити, що надходять із субдоменів URL-адреси програми, також автоматично вважаються довіреними. Якщо ви хочете вимкнути таку поведінку, ви можете використовувати аргумент `субдомени`:

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustHosts(at: ['laravel.test'], subdomains: false);
    })

