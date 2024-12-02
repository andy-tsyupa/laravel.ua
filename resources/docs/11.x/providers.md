# Постачальники послуг

- [Вступ](#introduction)
- [Постачальники послуг з написання текстів](#writing-service-providers)
    - [Метод реєстру](#the-register-method)
    - [Метод завантаження](#the-boot-method)
- [Реєстрація провайдерів](#registering-providers)
- [Відстрочені постачальники](#deferred-providers)

<a name="introduction"></a>
## Вступ

Сервіс-провайдери - це центральне місце початкового завантаження всіх додатків Laravel. Ваш власний додаток, а також усі основні служби та сервіси Laravel завантажуються через них.

Але, що ми маємо на увазі під «початковим завантаженням»? Загалом, ми маємо на увазі **реєстрацію** елементів, включно з реєстрацією зв'язувань контейнера служб (service container), слухачів подій (event listener), посередників (middleware) і навіть маршрутів (route). Сервіс-провайдери є центральним місцем для конфігурації програми.

Laravel використовує десятки постачальників послуг внутрішньо для ініціалізації своїх основних сервісів, таких як поштовий сервіс, черги, кеш та інші. Багато з цих постачальників є «відкладеними», що означає, що вони не будуть завантажені під час кожного запиту, а тільки коли потрібні фактичні сервіси, які вони надають.

Усі визначені користувачем постачальники послуг реєструються у файлі `bootstrap/providers.php`. У цій документації ви дізнаєтеся, як писати власні сервіс-провайдери та реєструвати їх у додатку Laravel.

> [!NOTE]
> Якщо ви хочете дізнатися більше про те, як Laravel обробляє запити і працює зсередини, ознайомтеся з нашою документацією з [життєвого циклу запиту](/docs/{{version}}/lifecycle) Laravel.
> 
<a name="writing-service-providers"></a>
## Написання сервіс-провайдерів

Усі сервіс-провайдери розширюють клас `Illuminate\Support\ServiceProvider`. Більшість сервіс-провайдерів містять метод `register` і `boot`. У межах методу `register` слід **тільки зв'язувати (bind) сутності в [контейнері служб](/docs/{{version}}}/container)**. Ніколи не слід намагатися зареєструвати будь-яких слухачів подій, маршрути або щось інше в методі `register`.

Щоб згенерувати новий сервіс-провайдер, використовуйте команду `make:provider` [Artisan](artisan). Laravel автоматично зареєструє вашого нового сервіс-провайдера у файлі `bootstrap/providers.php` вашого додатка:
```shell
php artisan make:provider RiakServiceProvider
```

<a name="the-register-method"></a>
### Метод `register`

Як згадувалося раніше, у межах методу `register` слід лише пов'язувати сутності в [контейнері служб](/docs/{{version}}}/container). Ніколи не слід намагатися зареєструвати слухачів подій, маршрути або щось інше в методі `register`. Інакше ви можете випадково скористатися підсистемою, чий сервіс-провайдер ще не завантажений.

Давайте поглянемо на пересічний сервіс-провайдер додатка. У будь-якому з методів сервіс-провайдера у вас завжди є доступ до властивості `$app`, яка забезпечує доступ до контейнера служб:
```php
<?php

namespace App\Providers;

use App\Services\Riak\Connection;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class RiakServiceProvider extends ServiceProvider
{
    /**
     * Реєстрація будь-яких служб програми.
     */
    public function register(): void
    {
        $this->app->singleton(Connection::class, function (Application $app) {
            return new Connection(config('riak'));
        });
    }
}
```

Цей сервіс-провайдер визначає тільки метод `register` і використовує цей метод для вказівки, яка саме реалізація `App\Services\Riak\Connection` буде застосована в нашому додатку - за допомогою контейнера служб. Якщо ви ще не знайомі з контейнером служб Laravel, ознайомтеся з [його документацією](/docs/{{version}}}/container).

<a name="the-bindings-and-singletons-properties"></a>
#### Властивості `bindings` і `singletons`

Якщо ваш сервіс-провайдер реєструє багато простих зв'язувань, ви можете використовувати властивості `bindings` і `singletons` замість ручної реєстрації кожного зв'язування контейнера. Коли сервіс-провайдер завантажується фреймворком, він автоматично перевіряє ці властивості та реєструє їхні зв'язування:

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
         * Усі зв'язування контейнера, які мають бути зареєстровані.
         *
         * @var array
         */
        public $bindings = [
            ServerProvider::class => DigitalOceanServerProvider::class,
        ];

        /**
         * Усі синглтони контейнера, які мають бути зареєстровані.
         *
         * @var array
         */
        public $singletons = [
            DowntimeNotifier::class => PingdomDowntimeNotifier::class,
            ServerProvider::class => ServerToolsProvider::class,
        ];
    }

<a name="the-boot-method"></a>
### Метод `boot`

Отже, що, якщо нам потрібно зареєструвати [компонувальник шаблонів](/docs/{{version}}}/views#view-composers) у нашому сервіс-провайдері? Це має бути зроблено в рамках методу `boot`. **Цей метод викликається після реєстрації всіх інших сервіс-провайдерів**, що означає, що в цьому місці у вас вже є доступ до всіх інших служб, які були зареєстровані фреймворком:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\View;
    use Illuminate\Support\ServiceProvider;

    class ComposerServiceProvider extends ServiceProvider
    {
        /**
         * Завантаження будь-яких служб програми.
         */
        public function boot(): void
        {
            View::composer('view', function () {
                // ...
            });
        }
    }

<a name="boot-method-dependency-injection"></a>
#### Впровадження залежності в методі `boot`

Ви можете вказувати тип залежностей у методі `boot` сервіс-провайдера. [Контейнер служб](/docs/{{version}}/container) автоматично впровадить будь-які необхідні залежності:

    use Illuminate\Contracts\Routing\ResponseFactory;

    /**
     * Завантаження будь-яких служб програми.
     */
    public function boot(ResponseFactory $response): void
    {
        $response->macro('serialized', function (mixed $value) {
            // ...
        });
    }

<a name="registering-providers"></a>
## Реєстрація сервіс-провайдерів

Усі постачальники послуг реєструються у файлі конфігурації `bootstrap/providers.php`. Цей файл повертає масив, який містить імена класів постачальників послуг вашого додатка:

    <?php

    return [
        App\Providers\AppServiceProvider::class,
    ];

Коли ви викликаєте команду `make:provider` в Artisan, Laravel автоматично додасть згенерований провайдер у файл `bootstrap/providers.php`. Однак, якщо ви створили клас провайдера вручну, ви повинні вручну додати клас провайдера в масив:

    <?php

    return [
        App\Providers\AppServiceProvider::class,
        App\Providers\ComposerServiceProvider::class, // Ваш новый провайдер
    ];

<a name="deferred-providers"></a>
## Відкладені сервіс-провайдери

Якщо ваш сервіс-провайдер реєструє **тільки** зв'язування в [контейнері служб](/docs/{{version}}}/container), ви можете відкласти його реєстрацію доти, доки одне із зареєстрованих зв'язувань не знадобиться. Відтермінування завантаження такого сервіс-провайдера підвищить продуктивність вашого застосунку, оскільки він не завантажується з файлової системи під час кожного запиту.

Laravel складає і зберігає список усіх служб, що надаються відкладеними сервіс-провайдерами, а також ім'я класу сервіс-провайдера. Laravel завантажить сервіс-провайдер тільки за потреби в одній із цих служб.

Щоб відкласти завантаження сервіс-провайдера, реалізуйте інтерфейс `\Illuminate\Contracts\Support\DeferrableProvider`, описавши метод `provides`. Метод `provides` повинен повернути зв'язування контейнера служби, що реєструються даним класом:

    <?php

    namespace App\Providers;

    use App\Services\Riak\Connection;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\Support\DeferrableProvider;
    use Illuminate\Support\ServiceProvider;

    class RiakServiceProvider extends ServiceProvider implements DeferrableProvider
    {
        /**
         * Реєстрація будь-яких служб додатка.
         */
        public function register(): void
        {
            $this->app->singleton(Connection::class, function (Application $app) {
                return new Connection($app['config']['riak']);
            });
        }

        /**
         * Отримати служби, що надаються постачальником.
         *
         * @return array<int, string>
         */
        public function provides(): array
        {
            return [Connection::class];
        }
    }
