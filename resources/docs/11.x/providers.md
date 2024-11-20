# Постачальники послуг

- [Вступ](#introduction)
- [Постачальники послуг з написання текстів](#writing-service-providers)
    - [Метод реєстру](#the-register-method)
    - [Метод завантаження](#the-boot-method)
- [Реєстрація провайдерів](#registering-providers)
- [Відстрочені постачальники](#deferred-providers)

<a name="introduction"></a>
## Вступ

Провайдери послуг - це центральне місце для всього бутстрапінгу додатків Laravel. Ваш власний додаток, як і всі основні сервіси Laravel, завантажуються через постачальників послуг.

Але що ми маємо на увазі під «завантаженим»? Загалом, ми маємо на увазі **реєстрацію** речей, включаючи реєстрацію прив'язок контейнерів сервісів, слухачів подій, проміжного програмного забезпечення і навіть маршрутів. Постачальники послуг - це центральне місце для конфігурації вашого додатку.

Laravel використовує десятки провайдерів для завантаження своїх основних сервісів, таких як поштовий сервер, черга, кеш та інші. Багато з цих провайдерів є «відкладеними» провайдерами, тобто вони завантажуються не за кожним запитом, а лише тоді, коли послуги, які вони надають, дійсно потрібні.

Всі визначені користувачем постачальники послуг реєструються у файлі `bootstrap/providers.php`. У наступній документації ви дізнаєтеся, як написати власні постачальники послуг і зареєструвати їх у вашому додатку Laravel.

> [!NOTE]  
> Якщо ви хочете дізнатися більше про те, як Laravel обробляє запити і працює всередині системи, ознайомтеся з нашою документацією по Laravel [життєвий цикл запиту](/docs/{{version}}/lifecycle).

<a name="writing-service-providers"></a>
## Постачальники послуг з написання текстів

Усі постачальники послуг розширюють клас `Illuminate\Support\ServiceProvider`. Більшість постачальників послуг містять метод `register` та метод `boot`. У методі `register` вам слід **лише прив'язувати речі до [контейнера сервісу](/docs/{{version}}/container)**. Ви ніколи не повинні намагатися реєструвати будь-які слухачі подій, маршрути або будь-яку іншу функціональність у методі `register`.

Artisan CLI може створити нового провайдера за допомогою команди `make:provider`:

```shell
php artisan make:provider RiakServiceProvider
```

<a name="the-register-method"></a>
### Метод реєстру

Як згадувалося раніше, у методі `register` ви повинні прив'язувати речі лише до [службового контейнера](/docs/{{version}}/container). Ви ніколи не повинні намагатися реєструвати будь-які слухачі подій, маршрути або будь-яку іншу функціональність за допомогою методу `register`. Інакше ви можете випадково скористатися сервісом, який надається постачальником послуг, який ще не завантажився.

Давайте подивимось на базовий провайдер послуг. У будь-якому методі вашого постачальника послуг ви завжди маєте доступ до властивості `$app`, яка надає доступ до контейнера послуг:

    <?php

    namespace App\Providers;

    use App\Services\Riak\Connection;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Support\ServiceProvider;

    class RiakServiceProvider extends ServiceProvider
    {
        /**
         * Реєструйте будь-які сервіси додатків.
         */
        public function register(): void
        {
            $this->app->singleton(Connection::class, function (Application $app) {
                return new Connection(config('riak'));
            });
        }
    }

Цей постачальник послуг визначає лише метод `register` і використовує цей метод для визначення реалізації `App\Services\Riak\Connection` у контейнері послуг. Якщо ви ще не знайомі з контейнером сервісів Laravel, перегляньте [його документацію](/docs/{{version}}/container).

<a name="the-bindings-and-singletons-properties"></a>
#### Властивості `bindings` та `singletons` Властивості `bindings` та `singletons`

Якщо ваш постачальник послуг реєструє багато простих прив'язок, ви можете скористатися властивостями `bindings` і `ingletons` замість того, щоб вручну реєструвати кожну прив'язку контейнера. Коли фреймворк завантажуватиме сервіс-провайдер, він автоматично перевірить наявність цих властивостей і зареєструє їх прив'язки:

    <?php

    namespace App\Providers;

    use App\Contracts\DowntimeNotifier;
    use App\Contracts\ServerProvider;
    use App\Services\DigitalOceanServerProvider;
    use App\Services\PingdomDowntimeNotifier;
    use App\Services\ServerToolsProvider;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Всі прив'язки контейнера, які повинні бути зареєстровані.
         *
         * @var array
         */
        public $bindings = [
            ServerProvider::class => DigitalOceanServerProvider::class,
        ];

        /**
         * Всі контейнерні синглетони, які повинні бути зареєстровані.
         *
         * @var array
         */
        public $singletons = [
            DowntimeNotifier::class => PingdomDowntimeNotifier::class,
            ServerProvider::class => ServerToolsProvider::class,
        ];
    }

<a name="the-boot-method"></a>
### Метод завантаження

Отже, що робити, якщо нам потрібно зареєструвати [переглянути композитора](/docs/{{version}}/views#view-composers) в межах нашого постачальника послуг? Це має бути зроблено в методі `boot`. **Цей метод викликається після реєстрації всіх інших постачальників послуг**, що означає, що ви маєте доступ до всіх інших послуг, які були зареєстровані фреймворком:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\View;
    use Illuminate\Support\ServiceProvider;

    class ComposerServiceProvider extends ServiceProvider
    {
        /**
         * Завантажуйте будь-які сервіси додатків.
         */
        public function boot(): void
        {
            View::composer('view', function () {
                // ...
            });
        }
    }

<a name="boot-method-dependency-injection"></a>
#### Ін'єкція залежності від методу завантаження

Ви можете вказати залежності для методу `boot` вашого постачальника послуг у вигляді підказки. Контейнер [service container](/docs/{{version}}/container) автоматично додасть усі потрібні вам залежності:

    use Illuminate\Contracts\Routing\ResponseFactory;

    /**
     * Завантажуйте будь-які сервіси додатків.
     */
    public function boot(ResponseFactory $response): void
    {
        $response->macro('serialized', function (mixed $value) {
            // ...
        });
    }

<a name="registering-providers"></a>
## Реєстрація провайдерів

Всі постачальники послуг реєструються у файлі конфігурації `bootstrap/providers.php`. Цей файл повертає масив, який містить назви класів постачальників послуг вашого додатку:

    <?php

    // Цей файл автоматично генерується Laravel...

    return [
        App\Providers\AppServiceProvider::class,
    ];

Коли ви викликаєте команду `make:provider` Artisan, Laravel автоматично додасть створений провайдер до файлу `bootstrap/providers.php`. Однак, якщо ви створили клас провайдера вручну, вам слід додати його до масиву вручну:

    <?php

    // Цей файл автоматично генерується Laravel...

    return [
        App\Providers\AppServiceProvider::class,
        App\Providers\ComposerServiceProvider::class, // [tl! add]
    ];

<a name="deferred-providers"></a>
## Відстрочені постачальники

Якщо ваш провайдер **лише** реєструє прив'язки у [контейнері послуг](/docs/{{version}}/container), ви можете відкласти його реєстрацію до моменту, коли одна із зареєстрованих прив'язок буде дійсно потрібна. Відкладення завантаження такого провайдера покращить продуктивність вашої програми, оскільки він не буде завантажуватися з файлової системи за кожним запитом.

Laravel складає і зберігає список усіх послуг, що надаються відкладеними постачальниками послуг, разом з назвою класу постачальника послуг. Після цього, лише коли ви намагаєтеся вирішити проблему з одним із цих сервісів, Laravel завантажує постачальника послуг.

Щоб відкласти завантаження провайдера, реалізуйте інтерфейс `\Illuminate\Contracts\Support\DeferrableProvider` і визначте метод `provides`. Метод `provides` повинен повертати прив'язки контейнера послуг, зареєстровані провайдером:

    <?php

    namespace App\Providers;

    use App\Services\Riak\Connection;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\Support\DeferrableProvider;
    use Illuminate\Support\ServiceProvider;

    class RiakServiceProvider extends ServiceProvider implements DeferrableProvider
    {
        /**
         * Реєструйте будь-які сервіси додатків.
         */
        public function register(): void
        {
            $this->app->singleton(Connection::class, function (Application $app) {
                return new Connection($app['config']['riak']);
            });
        }

        /**
         * Отримуйте послуги, що надаються провайдером.
         *
         * @return array<int, string>
         */
        public function provides(): array
        {
            return [Connection::class];
        }
    }
