# Сервісний контейнер

- [Вступ](#introduction)
    - [Неконфігуроване впровадження](#zero-configuration-resolution)
    - [Коли використовувати контейнер](#when-to-use-the-container)
- [Зв'язування](#binding)
    - [Основи зв'язувань](#binding-basics)
    - [Зв'язування інтерфейсів і реалізацій](#binding-interfaces-to-implementations)
    - [Контекстна прив'язка](#contextual-binding)
    - [Контекстуальні атрибути](#binding-primitives)
    - [Зв'язування типізованих варіацій](#binding-typed-variadics)
    - [Додавання міток](#tagging)
    - [Розширюваність зв'язувань](#extending-bindings)
- [Витяг](#resolving)
    - [Метод make](#the-make-method)
    - [Автоматичне впровадження залежностей](#automatic-injection)
- [Виклик і впровадження методу](#method-invocation-and-injection)
- [Події контейнера](#container-events)
- [PSR-11](#psr-11)

<a name="introduction"></a>
## Вступ

Контейнер служб (service container, сервіс-контейнер) Laravel - це потужний інструмент для управління залежностями класів і виконання впровадження залежностей. Впровадження залежностей - це химерна фраза, яка, по суті, означає наступне: залежності класів «вводяться» в клас через конструктор у вигляді аргументів або, в деяких випадках, через методи-сеттери. Під час створення класу або виклику методів фреймворк дивиться на список аргументів і, якщо потрібно, створює екземпляри необхідних класів і сам подає їх на вхід конструктора або методу.

Давайте подивимося на простий приклад:

    <?php

    namespace App\Http\Controllers;

    use App\Services\AppleMusic;
    use Illuminate\View\View;

    class PodcastController extends Controller
    {
        /**
         * Створити новий екземпляр контролера.
         *
         * @param  UserRepository  $users
         * @return void
         */
        public function __construct(
            protected AppleMusic $apple,
        ) {}

        /**
         * Показати інформацію про цей подкаст.
         */
        public function show(string $id): View
        {
            return view('podcasts.show', [
                'podcast' => $this->apple->findPodcast($id)
            ]);
        }
    }

У цьому прикладі `PodcastController` необхідно отримати подкасти з джерела даних, такого як Apple Music. Отже, ми **впровадимо** сервіс, здатний витягувати подкасти. Оскільки служба впроваджена, ми можемо легко «імітувати» або створити фіктивну реалізацію служби `AppleMusic` при тестуванні нашого додатка.

Глибоке розуміння контейнера служб Laravel необхідне для створення великого, потужного застосунку, а також для внесення внеску в саме ядро Laravel.

<a name="zero-configuration-resolution"></a>
### Неконфігуроване впровадження

Якщо клас не має залежностей або залежить тільки від інших конкретних класів (не інтерфейсів), контейнер не потрібно інструктувати про те, як створювати цей клас. Наприклад, ви можете помістити наступний код у свій файл `routes/web.php`:

    <?php

    class Service
    {
        // ...
    }

    Route::get('/', function (Service $service) {
        die($service::class);
    });

У цьому прикладі, під час відвідування `/` вашого додатка, маршрут автоматично отримає клас `Service` і впровадить його в обробнику вашого маршруту. Це змінює правила гри. Це означає, що ви можете розробити свій додаток і скористатися перевагами впровадження залежностей, не турбуючись про роздуті файли конфігурації.

На щастя, багато класів, які ви будете писати при створенні додатка Laravel, автоматично отримують свої залежності через контейнер, включно з [контролерами](/docs/{{version}}/controllers), [слухачами подій](/docs/{{version}}/events), [посередниками](/docs/{{{version}}}/middleware ) і т.д. Крім того, ви можете вказати залежності в методі `handle` обробки [завдань у черзі](/docs/{{version}}/queues). Як тільки ви відчуєте всю міць автоматичного неконфігурованого впровадження залежностей, ви відчуєте неможливість розробки без неї.

<a name="when-to-use-the-container"></a>
### Коли використовувати контейнер

Завдяки неконфігурованому впровадженню, ви часто будете оголошувати типи залежностей у маршрутах, контролерах, слухачах подій та інших місцях, не взаємодіючи з контейнером безпосередньо. Наприклад, ви можете вказати об'єкт `Illuminate\Http\Request` у визначенні вашого маршруту, для того, щоб легко отримати доступ до поточного запиту. Незважаючи на те, що нам ніколи не потрібно взаємодіяти з контейнером для написання цього коду, він керує впровадженням цих залежностей за лаштунками:

    use Illuminate\Http\Request;

    Route::get('/', function (Request $request) {
        // ...
    });

У багатьох випадках, завдяки автоматичному впровадженню залежностей і [фасадам](/docs/{{version}}/facades), ви можете будувати додатки Laravel без необхідності **колись** вручну зв'язувати або витягати що-небудь із контейнера. **У яких же випадках є необхідність вручну взаємодіяти з контейнером**. Давайте розглянемо дві ситуації.

По-перше, якщо ви пишете клас, що реалізує інтерфейс, і хочете оголосити тип цього інтерфейсу в конструкторі маршруту або класу, то ви маєте [повідомити контейнер, як отримати цей інтерфейс](#binding-interfaces-to-implementations). По-друге, якщо ви [пишете пакет Laravel](/docs/{{version}}/packages), яким плануєте поділитися з іншими розробниками Laravel, вам може знадобитися зв'язати служби вашого пакета в контейнері.

<a name="binding"></a>
## Зв'язування

<a name="binding-basics"></a>
### Основи зв'язувань

<a name="simple-bindings"></a>
#### Просте зв'язування

Майже всі ваші зв'язування в контейнері служб будуть зареєстровані в [постачальниках служб](/docs/{{version}}/providers), тому в більшості цих прикладів буде продемонстровано використання контейнера в цьому контексті.

Усередині постачальника служб у вас завжди є доступ до контейнера через властивість `$this->app`. Ми можемо зареєструвати зв'язування, використовуючи метод `bind`, передавши ім'я класу або інтерфейсу, які ми хочемо зареєструвати, разом із замиканням, що повертає екземпляр класу:
    use App\Services\Transistor;
    use App\Services\PodcastParser;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->bind(Transistor::class, function (Application $app) {
        return new Transistor($app->make(PodcastParser::class));
    });

Зверніть увагу, що ми отримуємо сам контейнер як аргумент. Потім ми можемо використовувати контейнер для вилучення підзалежностей об'єкта, який ми створюємо.

Як уже згадувалося, ви зазвичай будете взаємодіяти з контейнером усередині постачальників служб; однак, якщо ви хочете взаємодіяти з контейнером в інших частинах програми, ви можете зробити це через [фасад](/docs/{{version}}/facades) `App`:
    use App\Services\Transistor;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Support\Facades\App;

    App::bind(Transistor::class, function (Application $app) {
        // ...
    });


Ви можете використовувати метод `bindIf` для реєстрації прив'язки контейнера тільки в тому випадку, якщо прив'язка вже не була зареєстрована для даного типу:

```php
$this->app->bindIf(Transistor::class, function (Application $app) {
    return new Transistor($app->make(PodcastParser::class));
});
```

> [!NOTE]  
> Немає необхідності прив'язувати класи в контейнері, якщо вони не залежать від будь-яких інтерфейсів. Контейнеру не потрібно вказувати, як створювати ці об'єкти, оскільки він може автоматично витягувати ці об'єкти за допомогою рефлексії.

<a name="binding-a-singleton"></a>
#### Зв'язування одинаків

Метод `singleton` пов'язує в контейнері клас або інтерфейс, який має бути витягнутий тільки один раз. При наступних зверненнях до цього класу з контейнера буде повернуто отриманий раніше екземпляр об'єкта:

    use App\Services\Transistor;
    use App\Services\PodcastParser;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->singleton(Transistor::class, function (Application $app) {
        return new Transistor($app->make(PodcastParser::class));
    });

Ви можете використовувати метод `singletonIf` для реєстрації синглтон-прив'язки контейнера тільки в тому разі, якщо прив'язку вже не було зареєстровано для цього типу:

```php
$this->app->singletonIf(Transistor::class, function (Application $app) {
    return new Transistor($app->make(PodcastParser::class));
});
```

<a name="binding-scoped"></a>
#### Зв'язування одинаків із заданою областю дії

Метод `scoped` пов'язує в контейнері клас або інтерфейс, який повинен бути витягнутий тільки один раз протягом даного життєвого циклу запиту / завдання Laravel. Хоча цей метод схожий на метод `singleton` схожий на метод `scoped`, екземпляри, зареєстровані за допомогою методу `scoped`, будуть скидатися щоразу, коли додаток Laravel запускає новий «життєвий цикл», наприклад, коли [Laravel Octane](/docs/{{version}}}/octane) обробляє новий запит або коли [черги](/docs/{{{version}}}/queues) обробляють нове завдання:

    use App\Services\Transistor;
    use App\Services\PodcastParser;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->scoped(Transistor::class, function (Application $app) {
        return new Transistor($app->make(PodcastParser::class));
    });

Ви можете використовувати метод `scopedIf` для реєстрації прив'язки контейнера з обмеженою областю дії, тільки якщо прив'язка ще не зареєстрована для даного типу:

    $this->app->scopedIf(Transistor::class, function (Application $app) {
        return new Transistor($app->make(PodcastParser::class));
    });

<a name="binding-instances"></a>
#### Зв'язування екземплярів

Ви також можете прив'язати наявний екземпляр об'єкта в контейнері, використовуючи метод `instance`. Переданий екземпляр завжди буде повернуто з контейнера при наступних викликах:

    use App\Services\Transistor;
    use App\Services\PodcastParser;

    $service = new Transistor(new PodcastParser);

    $this->app->instance(Transistor::class, $service);

<a name="binding-interfaces-to-implementations"></a>
### Зв'язування інтерфейсів і реалізацій

Дуже потужна функція контейнера служб - це його здатність пов'язувати інтерфейс із конкретною реалізацією. Наприклад, припустимо, що в нас є інтерфейс `EventPusher` і реалізація `RedisEventPusher`. Після того як ми написали нашу реалізацію `RedisEventPusher` цього інтерфейсу, ми можемо зареєструвати його в контейнері таким чином:

    use App\Contracts\EventPusher;
    use App\Services\RedisEventPusher;

    $this->app->bind(EventPusher::class, RedisEventPusher::class);

Цей запис повідомляє контейнеру, що він повинен впровадити `RedisEventPusher`, коли класу потрібна реалізація `EventPusher`. Тепер ми можемо вказати інтерфейс `EventPusher` у конструкторі класу, який буде витягнуто контейнером. Пам'ятайте, що контролери, слухачі подій, посередники та деякі інші типи класів у додатках Laravel завжди виконуються за допомогою контейнера:

    use App\Contracts\EventPusher;

    /**
     * Створити новий екземпляр класу.
     */
    public function __construct(
        protected EventPusher $pusher,
    ) {}

<a name="contextual-binding"></a>
### Контекстна прив'язка

Іноді у вас може бути два класи, які використовують один і той самий інтерфейс, але ви хочете впровадити різні реалізації в кожен клас. Наприклад, два контролери можуть залежати від різних реалізацій [контракту](/docs/{{version}}}/contracts) `Illuminate\Contracts\Filesystem\Filesystem`. Laravel пропонує простий і зрозумілий інтерфейс для визначення цієї поведінки:

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

<a name="contextual-attributes"></a>
### Контекстуальні атрибути

Оскільки контекстна прив'язка часто використовується для впровадження реалізацій драйверів або значень конфігурації, Laravel пропонує безліч атрибутів контекстної прив'язки, які дають змогу впроваджувати ці типи значень без ручного визначення контекстних прив'язок у ваших постачальників послуг.

Наприклад, атрибут `Storage` може використовуватися для впровадження певного [диска зберігання](/docs/{{version}}/filesystem):

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Container\Attributes\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;

class PhotoController extends Controller
{
    public function __construct(
        #[Storage('local')] protected Filesystem $filesystem
    )
    {
        // ...
    }
}
```

На додаток до атрибута `Storage`, Laravel пропонує атрибути `Auth`, `Cache`, `Config`, `DB`, `Log`, `RouteParameter` і [`Tag`](#tagging):

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Container\Attributes\Cache;
use Illuminate\Container\Attributes\Config;
use Illuminate\Container\Attributes\DB;
use Illuminate\Container\Attributes\Log;
use Illuminate\Container\Attributes\Tag;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Database\Connection;
use Psr\Log\LoggerInterface;

class PhotoController extends Controller
{
    public function __construct(
        #[Auth('web')] protected Guard $auth,
        #[Cache('redis')] protected Repository $cache,
        #[Config('app.timezone')] protected string $timezone,
        #[DB('mysql')] protected Connection $connection,
        #[Log('daily')] protected LoggerInterface $log,
        #[Tag('reports')] protected iterable $reports,
    )
    {
        // ...
    }
}
```

Крім того, Laravel надає атрибут `CurrentUser` для додавання поточного автентифікованого користувача в заданий маршрут або клас:

```php
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

Route::get('/user', function (#[CurrentUser] User $user) {
    return $user;
})->middleware('auth');
```

<a name="defining-custom-attributes"></a>
#### Визначення користувацьких атрибутів

Ви можете створювати свої власні контекстні атрибути, реалізуючи контракт `Illuminate\Contracts\Container\ContextualAttribute`. Контейнер викличе метод `resolve` вашого атрибута, який має дозволити значення, що має бути введене в клас, який використовує атрибут. У наведеному нижче прикладі ми повторно реалізуємо вбудований атрибут Laravel `Config`:

    <?php

    namespace App\Attributes;

    use Illuminate\Contracts\Container\ContextualAttribute;

    #[Attribute(Attribute::TARGET_PARAMETER)]
    class Config implements ContextualAttribute
    {
        /**
         * Create a new attribute instance.
         */
        public function __construct(public string $key, public mixed $default = null)
        {
        }

        /**
         * Resolve the configuration value.
         *
         * @param  self  $attribute
         * @param  \Illuminate\Contracts\Container\Container  $container
         * @return mixed
         */
        public static function resolve(self $attribute, Container $container)
        {
            return $container->make('config')->get($attribute->key, $attribute->default);
        }
    }

<a name="binding-primitives"></a>
### Зв'язування примітивів

Іноді у вас може бути клас, який отримує деякі впроваджені класи, але також потребує примітива, такого як ціле число. Ви можете легко використовувати контекстну прив'язку, щоб впровадити будь-яке значення, яке може знадобитися вашому класу:

    use App\Http\Controllers\UserController;

    $this->app->when(UserController::class)
              ->needs('$variableName')
              ->give($value);

Іноді клас може залежати від масиву екземплярів, об'єднаних [міткою](#tagging). Використовуючи метод `giveTagged`, ви можете легко їх впровадити:

    $this->app->when(ReportAggregator::class)
        ->needs('$reports')
        ->giveTagged('reports');

Якщо вам потрібно впровадити значення з одного з конфігураційних файлів вашого додатка, то ви можете використовувати метод `giveConfig`:

    $this->app->when(ReportAggregator::class)
        ->needs('$timezone')
        ->giveConfig('app.timezone');

<a name="binding-typed-variadics"></a>
### Зв'язування типізованих варіацій

Іноді у вас може бути клас, який отримує масив типізованих об'єктів з використанням змінної кількості аргументів (_прим. перекл.: далі «варіації»_) конструктора:

    <?php

    use App\Models\Filter;
    use App\Services\Logger;

    class Firewall
    {
        /**
         * Створити новий екземпляр класу.
         */
        public function __construct(
            protected Logger $logger,
            Filter ...$filters,
        ) {
            $this->filters = $filters;
        }
    }

Використовуючи контекстну прив'язку, ви можете впровадити таку залежність, використовуючи метод `give` із замиканням, яке повертає масив впроваджуваних екземплярів `Filter`:

    $this->app->when(Firewall::class)
              ->needs(Filter::class)
              ->give(function (Application $app) {
                    return [
                        $app->make(NullFilter::class),
                        $app->make(ProfanityFilter::class),
                        $app->make(TooLongFilter::class),
                    ];
              });

Для зручності ви також можете просто передати масив імен класів, які будуть надані контейнером щоразу, коли для `Firewall` потрібні екземпляри `Filter`:

    $this->app->when(Firewall::class)
              ->needs(Filter::class)
              ->give([
                  NullFilter::class,
                  ProfanityFilter::class,
                  TooLongFilter::class,
              ]);

<a name="variadic-tag-dependencies"></a>
#### Мітки варіативних залежностей

Іноді клас може мати варіативну залежність, що вказує на тип як переданий клас (`Report ...$reports`). Використовуючи методи `needs` і `giveTagged`, ви можете легко впровадити всі прив'язки контейнера з цією [міткою](#tagging) для зазначеної залежності:

    $this->app->when(ReportAggregator::class)
        ->needs(Report::class)
        ->giveTagged('reports');

<a name="tagging"></a>
### Додавання міток

Іноді може знадобитися отримати всі прив'язки певної «категорії». Наприклад, можливо, ви створюєте аналізатор звітів, який отримує масив з безлічі різних реалізацій інтерфейсу `Report`. Після реєстрації реалізацій `Report` ви можете призначити їм мітку за допомогою методу `tag`:

    $this->app->bind(CpuReport::class, function () {
        // ...
    });

    $this->app->bind(MemoryReport::class, function () {
        // ...
    });

    $this->app->tag([CpuReport::class, MemoryReport::class], 'reports');

Після того як служби позначені, ви можете легко всі їх отримати за допомогою методу `tagged`:

    $this->app->bind(ReportAnalyzer::class, function (Application $app) {
        return new ReportAnalyzer($app->tagged('reports'));
    });

<a name="extending-bindings"></a>
### Розширюваність зв'язувань

Метод `extend` дозволяє модифікувати витягнуті служби. Наприклад, коли служба отримана, ви можете виконати додатковий код для декорування або конфігурації служби. Метод `extend` приймає два аргументи: клас служби, який ви розширюєте, і замикання, яке має повертати модифіковану службу. Замикання отримує службу, яка витягується, і екземпляр контейнера:

    $this->app->extend(Service::class, function (Service $service, Application $app) {
        return new DecoratedService($service);
    });

<a name="resolving"></a>
## Витяг

<a name="the-make-method"></a>
### Метод `make`

Ви можете використовувати метод `make` для вилучення екземпляра класу з контейнера. Метод `make` приймає ім'я класу або інтерфейсу, який ви хочете отримати:

    use App\Services\Transistor;

    $transistor = $this->app->make(Transistor::class);

Якщо деякі залежності вашого класу не можуть бути дозволені через контейнер, ви можете ввести їх, передавши їх як асоціативний масив у метод `makeWith`. Наприклад, ми можемо вручну передати конструктору аргумент `$id`, необхідний службою `Transistor`:

    use App\Services\Transistor;

    $transistor = $this->app->makeWith(Transistor::class, ['id' => 1]);

Метод `bound` може бути використаний для визначення, чи був клас або інтерфейс явно прив'язаний у контейнері:

    if ($this->app->bound(Transistor::class)) {
        // ...
    }

Якщо ви перебуваєте за межами постачальника служб і не маєте доступу до змінної `$app`, ви можете використовувати [фасад](/docs/{{version}}}/facades) `App` для отримання екземпляра класу з контейнера:

    use App\Services\Transistor;
    use Illuminate\Support\Facades\App;

    $transistor = App::make(Transistor::class);

Якщо ви хочете, щоб сам екземпляр контейнера Laravel був впроваджений у клас, що витягується контейнером, ви можете вказати клас `Illuminate\Container\Container` у конструкторі вашого класу:

    use Illuminate\Container\Container;

    /**
     * Створити новий екземпляр класу.
     */
    public function __construct(
        protected Container $container,
    ) {}

<a name="automatic-injection"></a>
### Автоматичне впровадження залежностей

Важливо, що в якості альтернативи, ви можете оголосити тип залежності в конструкторі класу, який витягується контейнером, включно з [контролерами](/docs/{{version}}}/controllers), [слухачами подій](/docs/{{version}}}/events), [посередниками](/docs/{{version}}}/middleware ) і т.д. Крім того, ви можете оголосити залежності в методі `handle` обробки [завдань у черзі](/docs/{{version}}/queues). На практиці саме так контейнер має витягувати більшість ваших об'єктів.

Наприклад, ви можете оголосити сервіс, визначений вашим додатком, у конструкторі контролера. Сервіс буде автоматично отримано і впроваджено в клас:

    <?php

    namespace App\Http\Controllers;

    use App\Services\AppleMusic;

    class PodcastController extends Controller
    {
        /**
         * Створити новий екземпляр контролера.
         */
        public function __construct(
            protected AppleMusic $apple,
        ) {}

        /**
         * Показати інформацію про цей подкаст.
         */
        public function show(string $id): Podcast
        {
            return $this->apple->findPodcast($id);
        }
    }


<a name="method-invocation-and-injection"></a>
## Виклик і впровадження методу

Іноді вам може знадобитися викликати метод для екземпляра об'єкта, дозволяючи контейнеру автоматично вводити залежності цього методу. Наприклад, враховуючи наступний клас:

    <?php

    namespace App;

    use App\Services\AppleMusic;

    class PodcastStats
    {
        /**
         * Створіть новий звіт про статистику подкастів.
         */
        public function generate(AppleMusic $apple): array
        {
            return [
                // ...
            ];
        }
    }

Ви можете викликати метод `generate` через контейнер у такий спосіб:

    use App\PodcastStats;
    use Illuminate\Support\Facades\App;

    $stats = App::call([new PodcastStats, 'generate']);

Метод `call` приймає будь-який PHP-код, що викликається. Метод контейнера `call` може навіть використовуватися для виклику замикання при автоматичному впровадженні його залежностей:

    use App\Services\AppleMusic;
    use Illuminate\Support\Facades\App;

    $result = App::call(function (AppleMusic $apple) {
        // ...
    });

<a name="container-events"></a>
## Події контейнера

Контейнер служб ініціює подію щоразу, коли витягує об'єкт. Ви можете прослухати цю подію за допомогою методу `resolving`:

    use App\Services\Transistor;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->resolving(Transistor::class, function (Transistor $transistor, Application $app) {
        // Викликається, коли контейнер витягує об'єкти типу `Transistor` ...
    });

    $this->app->resolving(function (mixed $object, Application $app) {
        // Викликається, коли контейнер витягує об'єкт будь-якого типу ...
    });

Як бачите, об'єкт, що витягується, буде передано в замикання, що дасть вам змогу встановити будь-які додаткові властивості об'єкта, перш ніж його буде передано його одержувачу.

<a name="rebinding"></a>
### Перепривязка

Метод `rebinding` дозволяє вам прослуховувати, коли служба повторно прив'язується до контейнера, тобто вона знову реєструється або перевизначається після початкової прив'язки. Це може бути корисно, коли вам потрібно оновити залежності або змінити поведінку кожного разу при оновленні певної прив'язки:

    use App\Contracts\PodcastPublisher;
    use App\Services\SpotifyPublisher;
    use App\Services\TransistorPublisher;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->bind(PodcastPublisher::class, SpotifyPublisher::class);

    $this->app->rebinding(
        PodcastPublisher::class,
        function (Application $app, PodcastPublisher $newInstance) {
            //
        },
    );

    // Нове зв'язування викличе повторне закриття...
    $this->app->bind(PodcastPublisher::class, TransistorPublisher::class);

<a name="psr-11"></a>
## PSR-11

Контейнер служб Laravel реалізує інтерфейс [PSR-11](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container). Тому ви можете оголосити тип інтерфейсу контейнера PSR-11, щоб отримати екземпляр контейнера Laravel:

    use App\Services\Transistor;
    use Psr\Container\ContainerInterface;

    Route::get('/', function (ContainerInterface $container) {
        $service = $container->get(Transistor::class);

        // ...
    });

Виняток викидається, якщо цей ідентифікатор не може бути отриманий. Винятком буде екземпляр `Psr\Container\NotFoundExceptionInterface`, якщо ідентифікатор ніколи не був прив'язаний. Якщо ідентифікатор було прив'язано, але його не можна отримати, буде кинуто екземпляр `Psr\Container\ContainerExceptionInterface`.
