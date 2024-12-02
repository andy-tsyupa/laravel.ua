# Фасади

- [Вступ](#introduction)
- [Коли варто використовувати фасади](#when-to-use-facades)
    - [Фасади проти ін'єкцій залежності](#facades-vs-dependency-injection)
    - [Фасади проти допоміжних функцій](#facades-vs-helper-functions)
- [Як працюють фасади](#how-facades-work)
- [Фасади в реальному часі](#real-time-facades)
- [Посилання на клас фасаду](#facade-class-reference)

<a name="introduction"></a>
## Вступ

У документації Laravel ви побачите приклади коду, який взаємодіє з функціями Laravel через «фасади». Фасади надають «статичний» інтерфейс до класів, які доступні у [службовому контейнері програми](/docs/{{version}}/container). Laravel постачається з багатьма фасадами, які надають доступ до майже всіх можливостей Laravel.

Фасади Laravel слугують «статичними проксі-серверами» для базових класів у сервісному контейнері, забезпечуючи перевагу лаконічного, виразного синтаксису, зберігаючи при цьому більшу тестуємість та гнучкість, ніж традиційні статичні методи. Це цілком нормально, якщо ви не зовсім розумієте, як працюють фасади - просто пливіть за течією і продовжуйте вивчати Laravel.

Всі фасади Laravel визначені у просторі імен `Illuminate\Support\Facades`. Отже, ми можемо легко отримати доступ до такого фасаду:

    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Route;

    Route::get('/cache', function () {
        return Cache::get('key');
    });

У документації по Laravel багато прикладів використовують фасади для демонстрації різних можливостей фреймворку.

<a name="helper-functions"></a>
#### Допоміжні функції

Щоб доповнити фасади, Laravel пропонує різноманітні глобальні «допоміжні функції», які ще більше спрощують взаємодію зі звичайними функціями Laravel. Деякі з поширених допоміжних функцій, з якими ви можете взаємодіяти, - це `view`, `response`, `url`, `config` та інші. Кожна допоміжна функція, яку пропонує Laravel, задокументована разом з відповідною функцією; проте повний список доступний у спеціальній [документації про допоміжні функції](/docs/{{version}}/helpers).

Наприклад, замість того, щоб використовувати фасад `Illuminate\Support\Facades\Response` для створення відповіді у форматі JSON, ми можемо просто використати функцію `response`. Оскільки допоміжні функції є глобально доступними, вам не потрібно імпортувати жодних класів, щоб використовувати їх:

    use Illuminate\Support\Facades\Response;

    Route::get('/users', function () {
        return Response::json([
            // ...
        ]);
    });

    Route::get('/users', function () {
        return response()->json([
            // ...
        ]);
    });

<a name="when-to-use-facades"></a>
## Коли варто використовувати фасади

Фасади мають багато переваг. Вони забезпечують лаконічний синтаксис, що запам'ятовується, який дозволяє використовувати можливості Laravel, не запам'ятовуючи довгі імена класів, які потрібно вводити або налаштовувати вручну. Крім того, завдяки унікальному використанню динамічних методів PHP, їх легко тестувати.

Однак, при використанні фасадів необхідно дотримуватися певної обережності. Основна небезпека фасадів - це «розширення класу». Оскільки фасади дуже прості у використанні і не потребують ін'єкцій, може бути легко дозволити вашим класам продовжувати рости і використовувати багато фасадів в одному класі. При використанні ін'єкції залежностей цей потенціал зменшується завдяки візуальному зворотному зв'язку, який дає великий конструктор, що ваш клас стає занадто великим. Тому, використовуючи фасади, зверніть особливу увагу на розмір вашого класу, щоб його сфера відповідальності залишалася вузькою. Якщо ваш клас стає занадто великим, подумайте про те, щоб розбити його на кілька менших класів.

<a name="facades-vs-dependency-injection"></a>
### Фасади проти ін'єкцій залежності

Однією з основних переваг ін'єкції залежностей є можливість міняти місцями реалізації ін'єктованого класу. Це корисно під час тестування, оскільки ви можете вставити макет або заглушку і стверджувати, що на заглушці були викликані різні методи.

Зазвичай, не можна імітувати або замінити по-справжньому статичний метод класу. Однак, оскільки фасади використовують динамічні методи для проксі-викликів методів до об'єктів, отриманих із службового контейнера, ми можемо тестувати фасади так само, як ми тестували б екземпляри ін'єкційних класів. Наприклад, за наступним маршрутом:

    use Illuminate\Support\Facades\Cache;

    Route::get('/cache', function () {
        return Cache::get('key');
    });

Використовуючи методи фасадного тестування Laravel, ми можемо написати наступний тест для перевірки того, що метод `Cache::get` був викликаний з очікуваним аргументом:

```php tab=Pest
use Illuminate\Support\Facades\Cache;

test('basic example', function () {
    Cache::shouldReceive('get')
         ->with('key')
         ->andReturn('value');

    $response = $this->get('/cache');

    $response->assertSee('value');
});
```

```php tab=PHPUnit
use Illuminate\Support\Facades\Cache;

/**
 * Приклад базового функціонального тесту.
 */
public function test_basic_example(): void
{
    Cache::shouldReceive('get')
         ->with('key')
         ->andReturn('value');

    $response = $this->get('/cache');

    $response->assertSee('value');
}
```

<a name="facades-vs-helper-functions"></a>
### Фасади проти допоміжних функцій

На додаток до фасадів, Laravel включає різноманітні «допоміжні» функції, які можуть виконувати загальні завдання, такі як створення подань, подій, відправлення завдань або надсилання HTTP-відповідей. Багато з цих допоміжних функцій виконують ту ж функцію, що і відповідний фасад. Наприклад, цей виклик фасаду і виклик допоміжної функції еквівалентні:

    return Illuminate\Support\Facades\View::make('profile');

    return view('profile');

Немає жодної практичної різниці між фасадами та допоміжними функціями. Використовуючи допоміжні функції, ви можете тестувати їх так само, як і відповідний фасад. Наприклад, за наступним маршрутом:

    Route::get('/cache', function () {
        return cache('key');
    });

Допоміжна функція `cache` викликає метод `get` у класі, що лежить в основі фасаду `Cache`. Отже, незважаючи на те, що ми використовуємо допоміжну функцію, ми можемо написати наступний тест для перевірки того, що метод було викликано з очікуваним аргументом:

    use Illuminate\Support\Facades\Cache;

    /**
     * Приклад базового функціонального тесту.
     */
    public function test_basic_example(): void
    {
        Cache::shouldReceive('get')
             ->with('key')
             ->andReturn('value');

        $response = $this->get('/cache');

        $response->assertSee('value');
    }

<a name="how-facades-work"></a>
## Як працюють фасади

У Laravel-додатку фасад - це клас, який забезпечує доступ до об'єкта з контейнера. Механізм, який забезпечує цю роботу, знаходиться у класі `Facade`. Фасади Laravel і будь-які користувацькі фасади, які ви створюєте, розширюють базовий клас `Illuminate\Support\Facades\Facade`.

Базовий клас `Facade` використовує чарівний метод `__callStatic()` для відкладення викликів з вашого фасаду до об'єкта, отриманого з контейнера. У наведеному нижче прикладі виконується виклик до системи кешування Laravel. Дивлячись на цей код, можна припустити, що викликається статичний метод `get` у класі `Cache`:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\View\View;

    class UserController extends Controller
    {
        /**
         * Показати профіль для даного користувача.
         */
        public function showProfile(string $id): View
        {
            $user = Cache::get('user:'.$id);

            return view('profile', ['user' => $user]);
        }
    }

Зверніть увагу, що у верхній частині файлу ми «імпортуємо» фасад `Cache`. Цей фасад слугує проксі для доступу до базової реалізації інтерфейсу `Illuminate\Contracts\Cache\Factory`. Будь-які виклики, які ми робимо за допомогою фасаду, будуть передані базовому екземпляру служби кешування Laravel.

Якщо ми подивимося на клас `Illuminate\Support\Facades\Cache`, то побачимо, що в ньому немає статичного методу `get`:

    class Cache extends Facade
    {
        /**
         * Отримайте зареєстровану назву компонента.
         */
        protected static function getFacadeAccessor(): string
        {
            return 'cache';
        }
    }

Замість цього фасад `Cache` розширює базовий клас `Facade` і визначає метод `getFacadeAccessor()`. Завдання цього методу - повернути ім'я прив'язки службового контейнера. Коли користувач звертається до будь-якого статичного методу на фасаді `Cache`, Laravel розпізнає прив'язку `cache` з [сервісного контейнера](/docs/{{version}}/container) і виконує запитуваний метод (в даному випадку `get`) для цього об'єкта.

<a name="real-time-facades"></a>
## Фасади в реальному часі

Використовуючи фасади в реальному часі, ви можете поводитися з будь-яким класом у вашому додатку так, ніби він є фасадом. Щоб проілюструвати, як це можна використовувати, давайте спочатку розглянемо деякий код, який не використовує фасади в реальному часі. Наприклад, припустимо, що наша модель `Podcast` має метод `publish`. Однак, щоб опублікувати подкаст, нам потрібно ввести екземпляр `Publisher`:

    <?php

    namespace App\Models;

    use App\Contracts\Publisher;
    use Illuminate\Database\Eloquent\Model;

    class Podcast extends Model
    {
        /**
         * Опублікуйте подкаст.
         */
        public function publish(Publisher $publisher): void
        {
            $this->update(['publishing' => now()]);

            $publisher->publish($this);
        }
    }

Впровадження реалізації паблішера в метод дозволяє нам легко тестувати метод в ізоляції, оскільки ми можемо висміювати впроваджений паблішер. Однак, це вимагає від нас завжди передавати екземпляр паблішера кожного разу, коли ми викликаємо метод `publish`. Використовуючи фасади в реальному часі, ми можемо підтримувати ту ж саму тестуємість, але при цьому нам не потрібно явно передавати екземпляр `Publisher`. Щоб згенерувати фасад у реальному часі, додайте до простору імен імпортованого класу префікс `Facades`:

    <?php

    namespace App\Models;

    use App\Contracts\Publisher; // [tl! remove]
    use Facades\App\Contracts\Publisher; // [tl! add]
    use Illuminate\Database\Eloquent\Model;

    class Podcast extends Model
    {
        /**
         * Опублікуйте подкаст.
         */
        public function publish(Publisher $publisher): void // [tl! remove]
        public function publish(): void // [tl! add]
        {
            $this->update(['publishing' => now()]);

            $publisher->publish($this); // [tl! remove]
            Publisher::publish($this); // [tl! add]
        }
    }

Коли використовується фасад у реальному часі, реалізація видавця буде вирішена з контейнера сервісу, використовуючи частину імені інтерфейсу або класу, яка з'являється після префікса `Facades`. При тестуванні ми можемо використовувати вбудовані в Laravel помічники тестування фасадів для імітації цього виклику методу:

```php tab=Pest
<?php

use App\Models\Podcast;
use Facades\App\Contracts\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('podcast can be published', function () {
    $podcast = Podcast::factory()->create();

    Publisher::shouldReceive('publish')->once()->with($podcast);

    $podcast->publish();
});
```

```php tab=PHPUnit
<?php

namespace Tests\Feature;

use App\Models\Podcast;
use Facades\App\Contracts\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PodcastTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тестовий приклад.
     */
    public function test_podcast_can_be_published(): void
    {
        $podcast = Podcast::factory()->create();

        Publisher::shouldReceive('publish')->once()->with($podcast);

        $podcast->publish();
    }
}
```

<a name="facade-class-reference"></a>
## Посилання на клас фасаду

Нижче ви знайдете кожен фасад і клас, що лежить в його основі. Це корисний інструмент для швидкого пошуку в документації API для заданого кореня фасаду. Ключ [прив'язка контейнера сервісу](/docs/{{version}}/container) також включено там, де це застосовно.

<div class="overflow-auto">

Фасад  |  Клас  |  Прив'язка сервісного контейнера
------------- | ------------- | -------------
App  |  [Illuminate\Foundation\Application](https://laravel.com/api/{{version}}/Illuminate/Foundation/Application.html)  |  `app`
Artisan  |  [Illuminate\Contracts\Console\Kernel](https://laravel.com/api/{{version}}/Illuminate/Contracts/Console/Kernel.html)  |  `artisan`
Auth  |  [Illuminate\Auth\AuthManager](https://laravel.com/api/{{version}}/Illuminate/Auth/AuthManager.html)  |  `auth`
Auth (Instance)  |  [Illuminate\Contracts\Auth\Guard](https://laravel.com/api/{{version}}/Illuminate/Contracts/Auth/Guard.html)  |  `auth.driver`
Blade  |  [Illuminate\View\Compilers\BladeCompiler](https://laravel.com/api/{{version}}/Illuminate/View/Compilers/BladeCompiler.html)  |  `blade.compiler`
Broadcast  |  [Illuminate\Contracts\Broadcasting\Factory](https://laravel.com/api/{{version}}/Illuminate/Contracts/Broadcasting/Factory.html)  |  &nbsp;
Broadcast (Instance)  |  [Illuminate\Contracts\Broadcasting\Broadcaster](https://laravel.com/api/{{version}}/Illuminate/Contracts/Broadcasting/Broadcaster.html)  |  &nbsp;
Bus  |  [Illuminate\Contracts\Bus\Dispatcher](https://laravel.com/api/{{version}}/Illuminate/Contracts/Bus/Dispatcher.html)  |  &nbsp;
Cache  |  [Illuminate\Cache\CacheManager](https://laravel.com/api/{{version}}/Illuminate/Cache/CacheManager.html)  |  `cache`
Cache (Instance)  |  [Illuminate\Cache\Repository](https://laravel.com/api/{{version}}/Illuminate/Cache/Repository.html)  |  `cache.store`
Config  |  [Illuminate\Config\Repository](https://laravel.com/api/{{version}}/Illuminate/Config/Repository.html)  |  `config`
Cookie  |  [Illuminate\Cookie\CookieJar](https://laravel.com/api/{{version}}/Illuminate/Cookie/CookieJar.html)  |  `cookie`
Crypt  |  [Illuminate\Encryption\Encrypter](https://laravel.com/api/{{version}}/Illuminate/Encryption/Encrypter.html)  |  `encrypter`
Date  |  [Illuminate\Support\DateFactory](https://laravel.com/api/{{version}}/Illuminate/Support/DateFactory.html)  |  `date`
DB  |  [Illuminate\Database\DatabaseManager](https://laravel.com/api/{{version}}/Illuminate/Database/DatabaseManager.html)  |  `db`
DB (Instance)  |  [Illuminate\Database\Connection](https://laravel.com/api/{{version}}/Illuminate/Database/Connection.html)  |  `db.connection`
Event  |  [Illuminate\Events\Dispatcher](https://laravel.com/api/{{version}}/Illuminate/Events/Dispatcher.html)  |  `events`
File  |  [Illuminate\Filesystem\Filesystem](https://laravel.com/api/{{version}}/Illuminate/Filesystem/Filesystem.html)  |  `files`
Gate  |  [Illuminate\Contracts\Auth\Access\Gate](https://laravel.com/api/{{version}}/Illuminate/Contracts/Auth/Access/Gate.html)  |  &nbsp;
Hash  |  [Illuminate\Contracts\Hashing\Hasher](https://laravel.com/api/{{version}}/Illuminate/Contracts/Hashing/Hasher.html)  |  `hash`
Http  |  [Illuminate\Http\Client\Factory](https://laravel.com/api/{{version}}/Illuminate/Http/Client/Factory.html)  |  &nbsp;
Lang  |  [Illuminate\Translation\Translator](https://laravel.com/api/{{version}}/Illuminate/Translation/Translator.html)  |  `translator`
Log  |  [Illuminate\Log\LogManager](https://laravel.com/api/{{version}}/Illuminate/Log/LogManager.html)  |  `log`
Mail  |  [Illuminate\Mail\Mailer](https://laravel.com/api/{{version}}/Illuminate/Mail/Mailer.html)  |  `mailer`
Notification  |  [Illuminate\Notifications\ChannelManager](https://laravel.com/api/{{version}}/Illuminate/Notifications/ChannelManager.html)  |  &nbsp;
Password  |  [Illuminate\Auth\Passwords\PasswordBrokerManager](https://laravel.com/api/{{version}}/Illuminate/Auth/Passwords/PasswordBrokerManager.html)  |  `auth.password`
Password (Instance)  |  [Illuminate\Auth\Passwords\PasswordBroker](https://laravel.com/api/{{version}}/Illuminate/Auth/Passwords/PasswordBroker.html)  |  `auth.password.broker`
Pipeline (Instance)  |  [Illuminate\Pipeline\Pipeline](https://laravel.com/api/{{version}}/Illuminate/Pipeline/Pipeline.html)  |  &nbsp;
Process  |  [Illuminate\Process\Factory](https://laravel.com/api/{{version}}/Illuminate/Process/Factory.html)  |  &nbsp;
Queue  |  [Illuminate\Queue\QueueManager](https://laravel.com/api/{{version}}/Illuminate/Queue/QueueManager.html)  |  `queue`
Queue (Instance)  |  [Illuminate\Contracts\Queue\Queue](https://laravel.com/api/{{version}}/Illuminate/Contracts/Queue/Queue.html)  |  `queue.connection`
Queue (Base Class)  |  [Illuminate\Queue\Queue](https://laravel.com/api/{{version}}/Illuminate/Queue/Queue.html)  |  &nbsp;
RateLimiter  |  [Illuminate\Cache\RateLimiter](https://laravel.com/api/{{version}}/Illuminate/Cache/RateLimiter.html)  |  &nbsp;
Redirect  |  [Illuminate\Routing\Redirector](https://laravel.com/api/{{version}}/Illuminate/Routing/Redirector.html)  |  `redirect`
Redis  |  [Illuminate\Redis\RedisManager](https://laravel.com/api/{{version}}/Illuminate/Redis/RedisManager.html)  |  `redis`
Redis (Instance)  |  [Illuminate\Redis\Connections\Connection](https://laravel.com/api/{{version}}/Illuminate/Redis/Connections/Connection.html)  |  `redis.connection`
Request  |  [Illuminate\Http\Request](https://laravel.com/api/{{version}}/Illuminate/Http/Request.html)  |  `request`
Response  |  [Illuminate\Contracts\Routing\ResponseFactory](https://laravel.com/api/{{version}}/Illuminate/Contracts/Routing/ResponseFactory.html)  |  &nbsp;
Response (Instance)  |  [Illuminate\Http\Response](https://laravel.com/api/{{version}}/Illuminate/Http/Response.html)  |  &nbsp;
Route  |  [Illuminate\Routing\Router](https://laravel.com/api/{{version}}/Illuminate/Routing/Router.html)  |  `router`
Schema  |  [Illuminate\Database\Schema\Builder](https://laravel.com/api/{{version}}/Illuminate/Database/Schema/Builder.html)  |  &nbsp;
Session  |  [Illuminate\Session\SessionManager](https://laravel.com/api/{{version}}/Illuminate/Session/SessionManager.html)  |  `session`
Session (Instance)  |  [Illuminate\Session\Store](https://laravel.com/api/{{version}}/Illuminate/Session/Store.html)  |  `session.store`
Storage  |  [Illuminate\Filesystem\FilesystemManager](https://laravel.com/api/{{version}}/Illuminate/Filesystem/FilesystemManager.html)  |  `filesystem`
Storage (Instance)  |  [Illuminate\Contracts\Filesystem\Filesystem](https://laravel.com/api/{{version}}/Illuminate/Contracts/Filesystem/Filesystem.html)  |  `filesystem.disk`
URL  |  [Illuminate\Routing\UrlGenerator](https://laravel.com/api/{{version}}/Illuminate/Routing/UrlGenerator.html)  |  `url`
Validator  |  [Illuminate\Validation\Factory](https://laravel.com/api/{{version}}/Illuminate/Validation/Factory.html)  |  `validator`
Validator (Instance)  |  [Illuminate\Validation\Validator](https://laravel.com/api/{{version}}/Illuminate/Validation/Validator.html)  |  &nbsp;
View  |  [Illuminate\View\Factory](https://laravel.com/api/{{version}}/Illuminate/View/Factory.html)  |  `view`
View (Instance)  |  [Illuminate\View\View](https://laravel.com/api/{{version}}/Illuminate/View/View.html)  |  &nbsp;
Vite  |  [Illuminate\Foundation\Vite](https://laravel.com/api/{{version}}/Illuminate/Foundation/Vite.html)  |  &nbsp;

</div>
