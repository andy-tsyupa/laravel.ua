# Обробка помилок

- [Вступ](#introduction)
- [Конфігурація](#configuration)
- [Обробка винятків](#handling-exceptions)
    - [Винятки зі звітності](#reporting-exceptions)
    - [Рівні журналу винятків](#exception-log-levels)
    - [Ігнорування винятків за типами](#ignoring-exceptions-by-type)
    - [Надання винятків](#rendering-exceptions)
    - [Звітні та відображувані винятки](#renderable-exceptions)
- [Винятки, про які повідомляється про регулювання](#throttling-reported-exceptions)
- [Винятки HTTP](#http-exceptions)
    - [Користувацькі сторінки помилок HTTP](#custom-http-error-pages)

<a name="introduction"></a>
## Вступ

Коли ви запускаєте новий проєкт Laravel, обробка помилок і винятків уже налаштована для вас; однак у будь-який момент ви можете використовувати метод `withExceptions` у файлі `bootstrap/app.php` вашого застосунку, щоб керувати тим, як ваш застосунок повідомляє про винятки й обробляє їх.

Об'єкт `$exceptions`, що надається замиканню `withExceptions`, є екземпляром `Illuminate\Foundation\Configuration\Exceptions` та відповідає за управління обробкою винятків у вашому додатку. У цій документації ми заглибимося в цей об'єкт.

<a name="configuration"></a>
## Конфігурування

Параметр `debug` у конфігураційному файлі `config/app.php` визначає, скільки інформації про помилку фактично відображатиметься користувачеві. За замовчуванням цей параметр встановлений, щоб врахувати значення змінної оточення `APP_DEBUG`, яка міститься у вашому файлі `.env`.

Під час локальної розробки ви повинні встановити для змінної оточення `APP_DEBUG` значення `true`. **Під час експлуатації програми це значення завжди має бути `false`. Якщо в робочому оточенні буде встановлено значення `true`, ви ризикуєте розкрити конфіденційні значення конфігурації кінцевим користувачам вашого додатка.**

<a name="handling-exceptions"></a>
## Обробка винятків

<a name="reporting-exceptions"></a>
### Звіт про винятки

У Laravel звіти про винятки використовуються для реєстрації винятків або їхнього надсилання в зовнішню службу [Sentry](https://github.com/getsentry/sentry-laravel) або [Flare](https://flareapp.io). За замовчуванням винятки реєструватимуться на основі вашої конфігурації [logging](/docs/{{version}}/logging). Однак ви можете реєструвати винятки на власний розсуд.

Якщо вам потрібно повідомляти про різні типи винятків різними способами, ви можете використати метод винятків `report` у файлі `bootstrap/app.php` вашого додатка, щоб зареєструвати замикання, яке повинно виконуватися, коли потрібно повідомити про виняток певного типу. Laravel визначить, про який тип винятку повідомляє замикання, досліджуючи підказку типу замикання:

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (InvalidOrderException $e) {
            // ...
        });
    })

Коли ви реєструєте власні замикання для створення звітів про винятки, використовуючи метод `report`, Laravel, як і раніше, реєструє виняток, використовуючи конфігурацію журналювання за замовчуванням для програми. Якщо ви хочете зупинити поширення винятку до стека журналів за замовчуванням, ви можете використовувати метод `stop` під час визначення замикання звіту або повернути `false` із замикання:

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (InvalidOrderException $e) {
            // ...
        })->stop();

        $exceptions->report(function (InvalidOrderException $e) {
            return false;
        });
    })

> [!NOTE]  
> Щоб налаштувати звіт про винятки для переданого винятку, ви можете розглянути можливість використання [звітних винятків](#renderable-exceptions).

<a name="global-log-context"></a>
#### Глобальний вміст журналу

Якщо доступно, Laravel автоматично додає ідентифікатор поточного користувача в кожне повідомлення журналу винятків як контекстні дані. Ви можете визначити свої власні глобальні контекстні дані, використовуючи метод виключення `context` у файлі `bootstrap/app.php` вашої програми. Ця інформація буде включена в кожне повідомлення журналу винятків, записане вашим додатком:

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->context(fn () => [
            'foo' => 'bar',
        ]);
    })

<a name="exception-log-context"></a>
#### Контекст журналу винятків

Додавання контексту до кожного повідомлення в журналі може бути корисним, але іноді конкретне виключення може мати унікальний контекст, який ви хотіли б включити в журнал. Визначивши метод `context` в одному з винятків вашого додатка, ви можете вказати будь-які дані, що відносяться до цього винятку, які повинні бути додані до журналу запису про виняток:

    <?php

    namespace App\Exceptions;

    use Exception;

    class InvalidOrderException extends Exception
    {
        // ...

        /**
         * Получить контекстную информацию исключения.
         *
         * @return array<string, mixed>
         */
        public function context(): array
        {
            return ['order_id' => $this->orderId];
        }
    }

<a name="the-report-helper"></a>
#### Помічник `report`

За бажанням може знадобитися повідомити про виключення, але продовжити обробку поточного запиту. Помічник `report` дає змогу вам швидко повідомити про виняток, не відображаючи сторінку з помилкою для користувача:

    public function isValid(string $value): bool
    {
        try {
            // Проверка `$value` ...
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

<a name="deduplicating-reported-exceptions"></a>
#### Винятки дублікатів

Якщо ви використовуєте функцію `report` у вашому застосунку, ви іноді можете повідомляти про одне й те саме виключення кілька разів, створюючи дублюючі записи в журналах.

Якщо ви хочете, щоб про один екземпляр винятку повідомлялося тільки один раз, ви можете викликати метод винятку `dontReportDuplicates` у файлі `bootstrap/app.php` вашого додатка:

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReportDuplicates();
    })

Тепер, коли функція `report` викликається з тим самим екземпляром винятку, буде повідомлено тільки перший виклик:

```php
$original = new RuntimeException('Whoops!');

report($original); // повідомлено

try {
    throw $original;
} catch (Throwable $caught) {
    report($caught); // проігноровано
}

report($original); // проігноровано
report($caught); // проігноровано
```

<a name="exception-log-levels"></a>
### Рівні журналу винятків

Коли повідомлення записуються в [журнал вашого додатка](/docs/{{version}}}/logging), повідомлення записуються із зазначеним [рівнем журналу](/docs/{{version}}}/logging#log-levels), який вказує на серйозність або важливість повідомлення, яке записується.

Як зазначено вище, навіть коли ви реєструєте користувацький зворотний виклик повідомлення про виняток за допомогою методу `report`, Laravel все одно записуватиме виняток із використанням конфігурації реєстрації журналу за замовчуванням для програми. Однак оскільки рівень журналу іноді може впливати на канали, на яких записується повідомлення, ви можете налаштувати рівень журналу, на якому певні винятки записуються.

Для цього ви можете використовувати метод винятку `level` у файлі `bootstrap/app.php` вашого додатка. Цей метод отримує тип виключення як перший аргумент і рівень журналу як другий аргумент:

    use PDOException;
    use Psr\Log\LogLevel;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->level(PDOException::class, LogLevel::CRITICAL);
    })

<a name="ignoring-exceptions-by-type"></a>
### Ігнорування винятків за типом

Під час створення програми можуть виникнути деякі типи винятків, про які ви ніколи не захочете повідомляти. Щоб ігнорувати ці винятки, ви можете використовувати метод винятків `dontReport` у файлі `bootstrap/app.php` вашого додатка. Про жоден клас, наданий цьому методу, ніколи не буде повідомлено; однак вони все одно можуть мати власну логіку рендерингу:

    use App\Exceptions\InvalidOrderException;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            InvalidOrderException::class,
        ]);
    })

Як альтернативу ви можете просто «позначити» клас винятків за допомогою інтерфейсу `Illuminate\Contracts\Debug\ShouldntReport`. Коли виняток позначено цим інтерфейсом, обробник винятків Laravel ніколи не повідомить про це:

```php
<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;

class PodcastProcessingException extends Exception implements ShouldntReport
{
    //
}
```

Усередині Laravel уже ігнорує деякі типи помилок, наприклад винятки, що виникають через помилки 404 HTTP або відповіді 419 HTTP, згенеровані недійсними токенами CSRF. Якщо ви хочете вказати Laravel припинити ігнорувати певний тип винятку, ви можете використовувати метод винятку `stopIgnoring` у файлі `bootstrap/app.php` вашого застосунку:

    use Symfony\Component\HttpKernel\Exception\HttpException;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->stopIgnoring(HttpException::class);
    })

<a name="rendering-exceptions"></a>
### Відображення винятків

За замовчуванням обробник винятків Laravel перетворює винятки в HTTP-відповідь. Однак ви можете зареєструвати своє замикання для винятків заданого типу. Ви можете домогтися цього, використовуючи метод винятку `render` у файлі `bootstrap/app.php` вашого додатка.

Замикання, передане методу `render`, має повернути екземпляр `Illuminate\Http\Response`, який може бути згенерований за допомогою функції `response`. Laravel визначить, який тип виключення відображає замикання за допомогою типізації аргументів:

    use App\Exceptions\InvalidOrderException;
    use Illuminate\Http\Request;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (InvalidOrderException $e, Request $request) {
            return response()->view('errors.invalid-order', status: 500);
        });
    })

Ви також можете використовувати метод `render` щоб перевизначити відображення для вбудованих винятків Laravel або Symfony, таких, як `NotFoundHttpException`. Якщо замикання, передане методу `render`, не повертає значення, буде використовуватися відображення винятків Laravel за замовчуванням:

    use Illuminate\Http\Request;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Record not found.'
                ], 404);
            }
        });
    })

<a name="rendering-exceptions-as-json"></a>
#### Відображення винятків у форматі JSON

Під час обробки винятку Laravel автоматично визначає, чи має виняток бути відображено у вигляді відповіді HTML або JSON, на основі заголовка `Accept` запиту. Якщо ви хочете налаштувати, як Laravel визначає, чи слід відображати відповіді про винятки HTML або JSON, ви можете використовувати метод `shouldRenderJsonWhen`:

    use Illuminate\Http\Request;
    use Throwable;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('admin/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })

<a name="customizing-the-exception-response"></a>
#### Налаштування відповіді на виняток

У рідкісних випадках вам може знадобитися налаштувати всю HTTP-відповідь, що відображається обробником винятків Laravel. Для цього ви можете зареєструвати закриття налаштування відповіді, використовуючи метод `respond`:

    use Symfony\Component\HttpFoundation\Response;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 419) {
                return back()->with([
                    'message' => 'The page expired, please try again.',
                ]);
            }

            return $response;
        });
    })

<a name="renderable-exceptions"></a>
### Звітні та відображувані винятки

Замість того, щоб налаштовувати користувацьку поведінку звітів і відображення помилок у файлі `bootstrap/app.php` вашого застосунку, ви можете визначити методи `report` і `render` безпосередньо в самих класах винятків вашого застосунку. Коли ці методи існують, фреймворк автоматично викликатиме їх для обробки помилок:

    <?php

    namespace App\Exceptions;

    use Exception;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;

    class InvalidOrderException extends Exception
    {
        /**
         * Відзвітувати про виключення.
         */
        public function report() : void
        {
            // ...
        }

        /**
         * Перетворити виняток у HTTP-відповідь.
         */
        public function render(Request $request): Response
        {
            return response(/* ... */);
        }
    }


Якщо ваш виняток розширює виняток, який уже доступний для візуалізації, як-от вбудований виняток Laravel або Symfony, ви можете повернути `false` з методу `render` винятку, щоб відобразити HTTP-відповідь винятку за замовчуванням:

    /**
     * Перетворити виняток у HTTP-відповідь.
     */
    public function render(Request $request): Response|bool
    {
        if (/** Визначити, чи потрібне для винятку користувацьке відображення для користувача */) {

            return response(/* ... */);
        }

        return false;
    }

Якщо ваше виключення містить призначену для користувача логіку звітності, яка необхідна тільки при виконанні певних умов, то вам може знадобитися вказати Laravel коли повідомляти про виняток, використовуючи конфігурацію обробки винятків за замовчуванням. Для цього ви можете повернути `false` з методу `report` виключення:

    /**
     * Повідомити про виключення.
     */
    public function report(): bool
    {
        if (/** Визначити, чи потрібне для винятку користувацьке відображення для користувача */) {

            return true;
        }

        return false;
    }

> [!NOTE]  
> Ви можете вказати будь-які необхідні залежності методу `report`, і вони будуть автоматично впроваджені в метод [контейнером служб](/docs/{{version}}}/container) Laravel.

<a name="throttling-reported-exceptions"></a>
### Обмеження на кількість зареєстрованих винятків

Якщо ваш застосунок реєструє дуже велику кількість винятків, вам може знадобитися обмежити кількість тих, що фактично реєструються або надсилаються до зовнішнього сервісу відстеження помилок.

Щоб отримати випадкову частоту вибірки винятків, ви можете використовувати метод винятків `throttle` у файлі `bootstrap/app.php` вашого застосунку. Метод `throttle` отримує замикання, яке має повертати екземпляр `Lottery`:

    use Illuminate\Support\Lottery;
    use Throwable;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->throttle(function (Throwable $e) {
            return Lottery::odds(1, 1000);
        });
    })

Також можна умовно вибирати винятки на основі їхнього типу. Якщо ви хочете вибирати тільки екземпляри конкретного класу винятків, ви можете повернути екземпляр `Lottery` тільки для цього класу:

    use App\Exceptions\ApiMonitoringException;
    use Illuminate\Support\Lottery;
    use Throwable;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->throttle(function (Throwable $e) {
            if ($e instanceof ApiMonitoringException) {
                return Lottery::odds(1, 1000);
            }
        });
    })

Ви також можете обмежувати кількість винятків, зареєстрованих або відправлених у зовнішній сервіс відстеження помилок, повернувши екземпляр `Limit` замість `Lottery`. Це корисно, якщо ви хочете захиститися від раптових сплесків винятків, що засмічують ваші логи, наприклад, коли сторонній сервіс, який використовується вашим додатком, недоступний:

    use Illuminate\Broadcasting\BroadcastException;
    use Illuminate\Cache\RateLimiting\Limit;
    use Throwable;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->throttle(function (Throwable $e) {
            if ($e instanceof BroadcastException) {
                return Limit::perMinute(300);
            }
        });
    })

За замовчуванням обмеження використовуватимуть клас винятку як ключ обмеження за кількістю. Ви можете налаштувати це, вказавши свій власний ключ за допомогою методу `by` на `Limit`:

    use Illuminate\Broadcasting\BroadcastException;
    use Illuminate\Cache\RateLimiting\Limit;
    use Throwable;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->throttle(function (Throwable $e) {
            if ($e instanceof BroadcastException) {
                return Limit::perMinute(300)->by($e->getMessage());
            }
        });
    })

Звичайно ж, ви можете повертати змішані екземпляри `Lottery` і `Limit` для різних винятків:

    use App\Exceptions\ApiMonitoringException;
    use Illuminate\Broadcasting\BroadcastException;
    use Illuminate\Cache\RateLimiting\Limit;
    use Illuminate\Support\Lottery;
    use Throwable;

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->throttle(function (Throwable $e) {
            return match (true) {
                $e instanceof BroadcastException => Limit::perMinute(300),
                $e instanceof ApiMonitoringException => Lottery::odds(1, 1000),
                default => Limit::none(),
            };
        });
    })

<a name="http-exceptions"></a>
## HTTP-винятки

Деякі винятки описують коди HTTP-помилок із сервера. Наприклад, це може бути помилка «сторінка не знайдена» (404), «неавторизований доступ» (401) або навіть помилка 500, згенерована розробником. Щоб створити таку відповідь з будь-якої точки вашого додатка, ви можете використовувати глобальний помічник `abort`:

    abort(404);

<a name="custom-http-error-pages"></a>
### Користувацькі сторінки для HTTP помилок

Laravel дає змогу легко відображати користувацькі сторінки помилок для різних кодів стану HTTP. Наприклад, якщо ви хочете налаштувати сторінку помилок для кодів HTTP-стану 404, створіть файл `resources/views/errors/404.blade.php`. Це подання буде відображено для всіх помилок 404, згенерованих вашим додатком. Шаблони в цьому каталозі повинні бути названі відповідно до коду стану HTTP, якому вони відповідають. Екземпляр `Symfony\Component\HttpKernel\Exception\HttpException`, викликаний функцією `abort`, буде передано в шаблон як змінну `$exception`:

    <h2>{{ $exception->getMessage() }}</h2>

Ви можете опублікувати стандартні шаблони сторінок помилок Laravel за допомогою команди `vendor:publish` Artisan. Після публікації шаблонів ви можете налаштувати їх на свій смак:

```shell
php artisan vendor:publish --tag=laravel-errors
```

<a name="fallback-http-error-pages"></a>
#### Запасні сторінки для HTTP помилок

Ви також можете визначити «запасну» сторінку помилки для певного набору кодів стану HTTP. Ця сторінка відображатиметься, якщо немає відповідної сторінки для конкретного коду стану HTTP, який стався. Для цього визначте шаблон `4xx.blade.php` і шаблон `5xx.blade.php` у директорії `resources/views/errors` вашого додатка.

Під час визначення «запасних» сторінок помилок, «запасні» сторінки не впливають на відповіді про помилки `404`, `500` і `503`, оскільки в Laravel є внутрішні, виділені сторінки для цих кодів стану. Щоб налаштувати сторінки, що відображаються для цих кодів стану, необхідно визначити власну сторінку помилок для кожного з них індивідуально.
