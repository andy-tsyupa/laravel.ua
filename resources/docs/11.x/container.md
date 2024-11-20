# Сервісний контейнер

- [Вступ](#introduction)
    - [Роздільна здатність нульової конфігурації](#zero-configuration-resolution)
    - [Коли утилізувати контейнер](#when-to-use-the-container)
- [Палітурка](#binding)
    - [Основи палітурки](#binding-basics)
    - [Прив'язка інтерфейсів до реалізацій](#binding-interfaces-to-implementations)
    - [Контекстна прив'язка](#contextual-binding)
    - [Зв'язування примітивів](#binding-primitives)
    - [Зв'язування типізованих варіадиків](#binding-typed-variadics)
    - [Тегування](#tagging)
    - [Розширювальні палітурки](#extending-bindings)
- [Вирішення](#resolving)
    - [Метод створення](#the-make-method)
    - [Автоматичне впорскування](#automatic-injection)
- [Виклик та ін'єкція методу](#method-invocation-and-injection)
- [Контейнерні події](#container-events)
- [PSR-11](#psr-11)

<a name="introduction"></a>
## Вступ

Сервісний контейнер Laravel є потужним інструментом для управління залежностями класів та виконання ін'єкції залежностей. Ін'єкція залежностей - це химерний вираз, який по суті означає наступне: залежності класу «впорскуються» в клас через конструктор або, в деяких випадках, через методи «сеттера».

Розглянемо простий приклад:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Repositories\UserRepository;
    use App\Models\User;
    use Illuminate\View\View;

    class UserController extends Controller
    {
        /**
         * Створіть новий екземпляр контролера.
         */
        public function __construct(
            protected UserRepository $users,
        ) {}

        /**
         * Показати профіль для даного користувача.
         */
        public function show(string $id): View
        {
            $user = $this->users->find($id);

            return view('user.profile', ['user' => $user]);
        }
    }

У цьому прикладі `UserController` повинен отримувати користувачів з джерела даних. Отже, ми **введемо** сервіс, який може отримувати користувачів. У цьому контексті наш `UserRepository`, швидше за все, використовує [Красномовний](/docs/{{version}}/eloquent) для отримання інформації про користувачів з бази даних. Однак, оскільки репозиторій є інжектованим, ми можемо легко замінити його іншою реалізацією. Ми також можемо легко «імітувати» або створити фіктивну реалізацію `UserRepository` під час тестування нашого додатку.

Глибоке розуміння контейнера сервісів Laravel необхідне для створення потужного, великого додатку, а також для участі в розробці самого ядра Laravel.

<a name="zero-configuration-resolution"></a>
### Роздільна здатність нульової конфігурації

Якщо клас не має залежностей або залежить лише від інших конкретних класів (не інтерфейсів), контейнеру не потрібно вказувати, як розпізнавати цей клас. Наприклад, ви можете розмістити наступний код у вашому файлі `routes/web.php`:

    <?php

    class Service
    {
        // ...
    }

    Route::get('/', function (Service $service) {
        die($service::class);
    });

У цьому прикладі натискання на маршрут `/` вашого додатка автоматично розпізнає клас `Service` і вставляє його в обробник маршруту. Це змінює правила гри. Це означає, що ви можете розробляти свої програми і використовувати переваги ін'єкції залежностей, не турбуючись про роздуті конфігураційні файли.

На щастя, багато класів, які ви будете писати при створенні Laravel-додатків, автоматично отримують свої залежності через контейнер, в тому числі [контролери](/docs/{{version}}/controllers), [слухачів заходу](/docs/{{version}}/events), [проміжне програмне забезпечення](/docs/{{version}}/middleware)тощо. Крім того, ви можете створювати залежності з підказками у методі `handle` [завдання в черзі](/docs/{{version}}/queues). Після того, як ви відчуєте силу автоматичного введення залежностей без зміни конфігурації, ви вже не зможете розробляти без цього.

<a name="when-to-use-the-container"></a>
### Коли утилізувати контейнер

Завдяки нульовій роздільній здатності конфігурації, ви можете часто вказувати залежність маршрутів, контролерів, слухачів подій та інших об'єктів без необхідності вручну взаємодіяти з контейнером. Наприклад, ви можете створити підказку для об'єкта `Illuminate\Http\Request` у визначенні маршруту, щоб ви могли легко отримати доступ до поточного запиту. Незважаючи на те, що нам ніколи не доведеться взаємодіяти з контейнером для написання цього коду, він керує ін'єкцією цих залежностей за лаштунками:

    use Illuminate\Http\Request;

    Route::get('/', function (Request $request) {
        // ...
    });

У багатьох випадках, завдяки автоматичному впровадженню залежностей та [facades](/docs/{{version}}/facades), ви можете створювати додатки Laravel без необхідності вручну зв'язувати або вирішувати що-небудь з контейнера. **Отже, коли вам доведеться вручну взаємодіяти з контейнером? Давайте розглянемо дві ситуації.

По-перше, якщо ви пишете клас, який реалізує інтерфейс, і хочете підказати цей інтерфейс у маршруті або конструкторі класу, ви повинні [вказати контейнеру, як розпізнати цей інтерфейс](#прив'язка-інтерфейсів-до-реалізацій). По-друге, якщо ви [пишете пакунок Laravel](/docs/{{version}}/packages), яким ви плануєте поділитися з іншими розробниками Laravel, вам може знадобитися прив'язати служби вашого пакунка до контейнера.

<a name="binding"></a>
## Палітурка

<a name="binding-basics"></a>
### Основи палітурки

<a name="simple-bindings"></a>
#### Simple Bindings

Майже всі ваші прив'язки до контейнерів сервісів буде зареєстровано у [service providers](/docs/{{version}}/providers), тож більшість цих прикладів демонструватимуть використання контейнера саме у цьому контексті.

У межах постачальника послуг ви завжди маєте доступ до контейнера через властивість `$this->app`. Ми можемо зареєструвати прив'язку за допомогою методу `bind`, передавши ім'я класу або інтерфейсу, який ми хочемо зареєструвати, разом із закриттям, яке повертає екземпляр класу:

    use App\Services\Transistor;
    use App\Services\PodcastParser;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->bind(Transistor::class, function (Application $app) {
        return new Transistor($app->make(PodcastParser::class));
    });

Зверніть увагу, що ми отримуємо сам контейнер як аргумент до розв'язувача. Потім ми можемо використовувати контейнер для вирішення підзалежностей об'єкта, який ми збираємо.

Як уже згадувалося, зазвичай ви будете взаємодіяти з контейнером в межах постачальника послуг; однак, якщо ви хочете взаємодіяти з контейнером поза межами постачальника послуг, ви можете зробити це за допомогою програми `App` [фасад].(/docs/{{version}}/facades):

    use App\Services\Transistor;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Support\Facades\App;

    App::bind(Transistor::class, function (Application $app) {
        // ...
    });

Ви можете використовувати метод `bindIf` для реєстрації прив'язки контейнера, тільки якщо прив'язка ще не була зареєстрована для даного типу:

```php
$this->app->bindIf(Transistor::class, function (Application $app) {
    return new Transistor($app->make(PodcastParser::class));
});
```

> [!NOTE]  
> Немає необхідності прив'язувати класи до контейнера, якщо вони не залежать від жодних інтерфейсів. Контейнеру не потрібно вказувати, як будувати ці об'єкти, оскільки він може автоматично визначати ці об'єкти за допомогою рефлексії.

<a name="binding-a-singleton"></a>
#### Зв'язування синглу

Метод «синглтон» прив'язує клас або інтерфейс до контейнера, який має бути розв'язаний лише один раз. Після розв'язання одиночного зв'язування той самий екземпляр об'єкта буде повертатися при наступних зверненнях до контейнера:

    use App\Services\Transistor;
    use App\Services\PodcastParser;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->singleton(Transistor::class, function (Application $app) {
        return new Transistor($app->make(PodcastParser::class));
    });

Ви можете використовувати метод `singletonIf` для реєстрації зв'язування одиночного контейнера, тільки якщо зв'язування ще не було зареєстровано для даного типу:

```php
$this->app->singletonIf(Transistor::class, function (Application $app) {
    return new Transistor($app->make(PodcastParser::class));
});
```

<a name="binding-scoped"></a>
#### Зв'язування скопійованих синглетів

Метод `scoped` прив'язує клас або інтерфейс до контейнера, який повинен бути вирішений тільки один раз в рамках даного життєвого циклу запиту / завдання Laravel. Хоча цей метод подібний до методу `ingleton`, екземпляри, зареєстровані за допомогою методу `scoped`, будуть очищені щоразу, коли додаток Laravel починає новий «життєвий цикл», наприклад, коли працівник [Laravel Octane](/docs/{{version}}/octane) обробляє новий запит або коли працівник Laravel [працівник черги](/docs/{{version}}/queues) обробляє нове завдання:

    use App\Services\Transistor;
    use App\Services\PodcastParser;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->scoped(Transistor::class, function (Application $app) {
        return new Transistor($app->make(PodcastParser::class));
    });

<a name="binding-instances"></a>
#### Обов'язкові інстанції

Ви також можете прив'язати існуючий екземпляр об'єкта до контейнера за допомогою методу `instance`. Даний екземпляр завжди буде повертатися при наступних зверненнях до контейнера:

    use App\Services\Transistor;
    use App\Services\PodcastParser;

    $service = new Transistor(new PodcastParser);

    $this->app->instance(Transistor::class, $service);

<a name="binding-interfaces-to-implementations"></a>
### Прив'язка інтерфейсів до реалізацій

Дуже потужною особливістю службового контейнера є його здатність прив'язувати інтерфейс до певної реалізації. Наприклад, припустимо, у нас є інтерфейс `EventPusher` і реалізація `RedisEventPusher`. Після того, як ми закодували нашу реалізацію `RedisEventPusher` цього інтерфейсу, ми можемо зареєструвати її у контейнері сервісів таким чином:

    use App\Contracts\EventPusher;
    use App\Services\RedisEventPusher;

    $this->app->bind(EventPusher::class, RedisEventPusher::class);

Цей оператор вказує контейнеру, що він повинен інжектувати `RedisEventPusher`, коли класу потрібна реалізація `EventPusher`. Тепер ми можемо вказати інтерфейс `EventPusher` у конструкторі класу, який розв'язується контейнером. Пам'ятайте, що контролери, слухачі подій, проміжне програмне забезпечення і різні інші типи класів в додатках Laravel завжди розв'язуються за допомогою контейнера:

    use App\Contracts\EventPusher;

    /**
     * Створіть новий екземпляр класу.
     */
    public function __construct(
        protected EventPusher $pusher
    ) {}

<a name="contextual-binding"></a>
### Контекстна прив'язка

Іноді ви можете мати два класи, які використовують один і той самий інтерфейс, але ви бажаєте вставити різні реалізації у кожен клас. Наприклад, два контролери можуть залежати від різних реалізацій `Illuminate\Contracts\Filesystem\Filesystem` [contract](/docs/{{version}}/contracts). Laravel надає простий, зручний інтерфейс для визначення такої поведінки:

    use App\Http\Controllers\PhotoController;
    use App\Http\Controllers\UploadController;
    use App\Http\Controllers\VideoController;
    use Illuminate\Contracts\Filesystem\Filesystem;
    use Illuminate\Support\Facades\Storage;

    $this->app->when(PhotoController::class)
              ->needs(Filesystem::class)
              ->give(function () {
                  return Storage::disk('local');
              });

    $this->app->when([VideoController::class, UploadController::class])
              ->needs(Filesystem::class)
              ->give(function () {
                  return Storage::disk('s3');
              });

<a name="binding-primitives"></a>
### Зв'язування примітивів

Іноді вам може знадобитися клас, який отримує деякі інжектовані класи, але також потребує інжектованого примітивного значення, наприклад, цілого числа. Ви можете легко використати контекстне зв'язування, щоб вставити будь-яке значення, яке може знадобитися вашому класу:

    use App\Http\Controllers\UserController;
    
    $this->app->when(UserController::class)
              ->needs('$variableName')
              ->give($value);

Іноді клас може залежати від масиву екземплярів [tagged](#tagging). Використовуючи метод `giveTagged`, ви можете легко вставити всі прив'язки контейнера з цим тегом:

    $this->app->when(ReportAggregator::class)
        ->needs('$reports')
        ->giveTagged('reports');

Якщо вам потрібно вставити значення з одного з конфігураційних файлів вашої програми, ви можете скористатися методом `giveConfig`:

    $this->app->when(ReportAggregator::class)
        ->needs('$timezone')
        ->giveConfig('app.timezone');

<a name="binding-typed-variadics"></a>
### Зв'язування типізованих варіадиків

Іноді вам може знадобитися клас, який отримує масив типізованих об'єктів через варіаційний аргумент конструктора:

    <?php

    use App\Models\Filter;
    use App\Services\Logger;

    class Firewall
    {
        /**
         * Екземпляри фільтрів.
         *
         * @var array
         */
        protected $filters;

        /**
         * Створіть новий екземпляр класу.
         */
        public function __construct(
            protected Logger $logger,
            Filter ...$filters,
        ) {
            $this->filters = $filters;
        }
    }

Використовуючи контекстне зв'язування, ви можете усунути цю залежність, надавши методу `give` закриття, яке повертає масив вирішених екземплярів `Filter`:

    $this->app->when(Firewall::class)
              ->needs(Filter::class)
              ->give(function (Application $app) {
                    return [
                        $app->make(NullFilter::class),
                        $app->make(ProfanityFilter::class),
                        $app->make(TooLongFilter::class),
                    ];
              });

Для зручності ви також можете просто вказати масив імен класів, який буде розпізнаватися контейнером, коли `Firewall` потребуватиме екземплярів `Filter`:

    $this->app->when(Firewall::class)
              ->needs(Filter::class)
              ->give([
                  NullFilter::class,
                  ProfanityFilter::class,
                  TooLongFilter::class,
              ]);

<a name="variadic-tag-dependencies"></a>
#### Варіативні залежності тегів

Іноді клас може мати варіаційну залежність, яка підказується як тип даного класу (`Report ...$reports`). Використовуючи методи `needs` та `giveTagged`, ви можете легко вставити всі прив'язки контейнера з цим [tag](#tagging) для даної залежності:

    $this->app->when(ReportAggregator::class)
        ->needs(Report::class)
        ->giveTagged('reports');

<a name="tagging"></a>
### Тегування

Іноді вам може знадобитися вирішити всі зв'язування певної «категорії». Наприклад, можливо, ви створюєте аналізатор звітів, який отримує масив з багатьох різних реалізацій інтерфейсу `Report`. Після реєстрації реалізацій `Report` ви можете призначити їм тег за допомогою методу `tag`:

    $this->app->bind(CpuReport::class, function () {
        // ...
    });

    $this->app->bind(MemoryReport::class, function () {
        // ...
    });

    $this->app->tag([CpuReport::class, MemoryReport::class], 'reports');

Після того, як сервіси було позначено тегами, ви можете легко вирішити їх усі за допомогою методу `tagged` контейнера:

    $this->app->bind(ReportAnalyzer::class, function (Application $app) {
        return new ReportAnalyzer($app->tagged('reports'));
    });

<a name="extending-bindings"></a>
### Розширювальні палітурки

Метод `extend` дозволяє модифікувати отримані сервіси. Наприклад, коли сервіс розв'язано, ви можете запустити додатковий код, щоб прикрасити або налаштувати його. Метод `extend` приймає два аргументи: клас сервісу, який ви розширюєте, і закриття, яке має повернути змінений сервіс. Закриття отримує сервіс, який перетворюється, та екземпляр контейнера:

    $this->app->extend(Service::class, function (Service $service, Application $app) {
        return new DecoratedService($service);
    });

<a name="resolving"></a>
## Вирішення

<a name="the-make-method"></a>
### Метод «зробити

Ви можете використовувати метод `make` для вилучення екземпляра класу з контейнера. Метод `make` приймає ім'я класу або інтерфейсу, який ви бажаєте перетворити:

    use App\Services\Transistor;

    $transistor = $this->app->make(Transistor::class);

Якщо деякі залежності вашого класу не можуть бути вирішені через контейнер, ви можете інжектувати їх, передавши у вигляді асоціативного масиву в метод `makeWith`. Наприклад, ми можемо вручну передати аргумент конструктора `$id`, необхідний сервісу `Transistor`:

    use App\Services\Transistor;

    $transistor = $this->app->makeWith(Transistor::class, ['id' => 1]);

Метод `bound` може бути використаний для визначення того, чи був клас або інтерфейс явно зв'язаний у контейнері:

    if ($this->app->bound(Transistor::class)) {
        // ...
    }

Якщо ви перебуваєте поза межами постачальника послуг у місці вашого коду, яке не має доступу до змінної `$app`, ви можете використати `App` [facade](/docs/{{version}}/facades) або `app` [helper](/docs/{{version}}/helpers#method-app), щоб вирішити екземпляр класу з контейнера:

    use App\Services\Transistor;
    use Illuminate\Support\Facades\App;

    $transistor = App::make(Transistor::class);

    $transistor = app(Transistor::class);

Якщо ви хочете, щоб екземпляр контейнера Laravel був інжектований у клас, який обробляється контейнером, ви можете вказати клас `Illuminate\Container\Container` у конструкторі вашого класу:

    use Illuminate\Container\Container;

    /**
     * Створіть новий екземпляр класу.
     */
    public function __construct(
        protected Container $container
    ) {}

<a name="automatic-injection"></a>
### Автоматичне впорскування

Крім того, що важливо, ви можете вказати залежність у конструкторі класу, який обробляється контейнером, зокрема [контролери](/docs/{{version}}/controllers), [слухачі подій](/docs/{{version}}/events), [проміжне програмне забезпечення](/docs/{{version}}/middleware) та інші. Крім того, ви можете вказати залежність від типу у методі `handle` [queued jobs](/docs/{{version}}/queues). На практиці, саме так більшість ваших об'єктів має оброблятися контейнером.

Наприклад, ви можете вказати репозиторій, визначений вашим додатком, у конструкторі контролера. Сховище буде автоматично розпізнано та підключено до класу:

    <?php

    namespace App\Http\Controllers;

    use App\Repositories\UserRepository;
    use App\Models\User;

    class UserController extends Controller
    {
        /**
         * Створіть новий екземпляр контролера.
         */
        public function __construct(
            protected UserRepository $users,
        ) {}

        /**
         * Показати користувача з заданим ідентифікатором.
         */
        public function show(string $id): User
        {
            $user = $this->users->findOrFail($id);

            return $user;
        }
    }

<a name="method-invocation-and-injection"></a>
## Виклик та ін'єкція методу

Іноді вам може знадобитися викликати метод на екземплярі об'єкта, дозволивши контейнеру автоматично під'єднати залежності цього методу. Наприклад, у випадку з наступним класом:

    <?php

    namespace App;

    use App\Repositories\UserRepository;

    class UserReport
    {
        /**
         * Створіть новий звіт користувача.
         */
        public function generate(UserRepository $repository): array
        {
            return [
                // ...
            ];
        }
    }

Ви можете викликати метод `generate` через контейнер таким чином:

    use App\UserReport;
    use Illuminate\Support\Facades\App;

    $report = App::call([new UserReport, 'generate']);

Метод `call` приймає будь-який PHP-виклик. Метод `call` контейнера може навіть використовуватися для виклику закриття з автоматичною ін'єкцією його залежностей:

    use App\Repositories\UserRepository;
    use Illuminate\Support\Facades\App;

    $result = App::call(function (UserRepository $repository) {
        // ...
    });

<a name="container-events"></a>
## Контейнерні події

Сервісний контейнер генерує подію кожного разу, коли він розв'язує об'єкт. Ви можете прослухати цю подію за допомогою методу `resolving`:

    use App\Services\Transistor;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->resolving(Transistor::class, function (Transistor $transistor, Application $app) {
        // Викликається, коли контейнер обробляє об'єкти типу «Транзистор»...
    });

    $this->app->resolving(function (mixed $object, Application $app) {
        // Викликається, коли контейнер розпізнає об'єкт будь-якого типу...
    });

Як ви можете бачити, об'єкт, що розв'язується, буде передано зворотному виклику, що дозволить вам встановити будь-які додаткові властивості об'єкта до того, як він буде переданий споживачеві.

<a name="psr-11"></a>
## PSR-11

Сервісний контейнер Laravel реалізує інтерфейс [PSR-11](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md). Таким чином, ви можете вказати інтерфейс контейнера PSR-11, щоб отримати екземпляр контейнера Laravel:

    use App\Services\Transistor;
    use Psr\Container\ContainerInterface;

    Route::get('/', function (ContainerInterface $container) {
        $service = $container->get(Transistor::class);

        // ...
    });

Виняток буде згенеровано, якщо заданий ідентифікатор не може бути розпізнаний. Виключенням буде екземпляр `Psr\Container\NotFoundExceptionInterface`, якщо ідентифікатор ніколи не було зв'язано. Якщо ідентифікатор було зв'язано, але його не вдалося розпізнати, буде згенеровано екземпляр `Psr\Container\ContainerExceptionInterface`.
