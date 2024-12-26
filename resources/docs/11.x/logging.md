# Логування

- [Вступ](#introduction)
- [Конфігурування](#configuration)
    - [Доступні драйвери каналу](#available-channel-drivers)
    - [Налаштування імені каналу](#channel-prerequisites)
    - [Логування попереджень про застарівання](#logging-deprecation-warnings)
- [Побудова стека журналів](#building-log-stacks)
- [Запис повідомлень журналу](#writing-log-messages)
    - [Контекстна інформація](#contextual-information)
    - [Запис у певні канали](#writing-to-specific-channels)
- [Налаштування каналу Monolog](#monolog-channel-customization)
    - [Налаштування Monolog для каналів](#customizing-monolog-for-channels)
    - [Створення обробника каналів Monolog](#creating-monolog-handler-channels)
    - [Створення каналів через фабрики](#creating-custom-channels-via-factories)
- [Перегляд повідомлення журналу за допомогою Pail](#tailing-log-messages-using-pail)
    - [Установка](#pail-installation)
    - [Використання](#pail-usage)
    - [Фільтрація логів](#pail-filtering-logs)
    
<a name="introduction"></a>
## Вступ

Щоб допомогти вам дізнатися більше про те, що відбувається у вашому додатку, Laravel пропонує надійні служби ведення журналу, які дають змогу записувати повідомлення у файли, журнал системних помилок і навіть у Slack, щоб повідомити всю вашу команду.

Ведення журналу Laravel засноване на «каналах». Кожен канал являє собою певний спосіб запису інформації журналу. Наприклад, канал `single` записує файли журналу в один файл журналу, а канал `slack` відправляє повідомлення журналу в Slack. Повідомлення журналу можуть бути записані в кілька каналів залежно від їхньої серйозності.

Під капотом Laravel використовує бібліотеку [Monolog](https://github.com/Seldaek/monolog), яка забезпечує підтримку безлічі потужних обробників журналів. Laravel спрощує налаштування цих обробників, даючи вам змогу змішувати і зіставляти їх для налаштування обробки журналів вашої програми.

<a name="configuration"></a>
## Конфігурування

Усі параметри конфігурації, які керують веденням журналу вашого додатка, розміщені у файлі конфігурації `config/logging.php`. Цей файл дає змогу вам налаштовувати канали журналу вашого застосунку, тому обов'язково перегляньте кожен із доступних каналів та їхні параметри. Нижче ми розглянемо кілька поширених варіантів.

За замовчуванням Laravel буде використовувати канал `stack` під час реєстрації повідомлень. Канал `stack` використовується для об'єднання декількох каналів журналу в один канал. Для отримання додаткової інформації про побудову стеків ознайомтеся з [документацією нижче](#building-log-stacks).

<a name="available-channel-drivers"></a>
### Доступні драйвери каналу

Кожен канал журналу працює через «драйвер». Драйвер визначає, як і де фактично записується повідомлення журналу. Наступні драйвери каналу журналу доступні в кожному додатку Laravel. Запис для більшості цих драйверів уже присутній у файлі конфігурації вашого додатка `config/logging.php`, тому обов'язково перегляньте цей файл, щоб ознайомитися з його вмістом:

| Ім'я         | Опис                                                                       |
| ------------ | ------------------------------------------------------------------------------ |
| `custom`     | Драйвер, який викликає зазначену фабрику для створення каналу.              |
| `daily`      | Драйвер Monolog на основі `RotatingFileHandler` зі щоденною ротацією.       |
| `errorlog`   | Драйвер Monolog на основі `ErrorLogHandler`.                                   |
| `monolog`    | Драйвер фабрики Monolog, що використовує будь-який підтримуваний Monolog обробник. |
| `papertrail` | Драйвер Monolog на основі `SyslogUdpHandler`.                                |
| `single`     | Канал на основі одного файлу або шляху (`StreamHandler`)                        |
| `slack`      | Драйвер Monolog на основі `SlackWebhookHandler`.                                |
| `stack`      | Обгортка для полегшення створення «багатоканальних» каналів.                     |
| `syslog`     | Драйвер Monolog на основі `SyslogHandler`.                                   |

> [!NOTE]
> Ознайомтеся з документацією щодо [просунутої кастомізації каналів](#monolog-channel-customization), щоб дізнатися більше про драйвери `monolog` і `custom`.

<a name="configuring-the-channel-name"></a>
#### Налаштування імені каналу

За замовчуванням екземпляр Monolog створюється з «ім'ям каналу», яке відповідає поточному середовищу, наприклад, `production` або `local`. Щоб змінити це значення, додайте параметр `name` у конфігурацію вашого каналу:

    'stack' => [
        'driver' => 'stack',
        'name' => 'channel-name',
        'channels' => ['single', 'slack'],
    ],

<a name="channel-prerequisites"></a>
### Попередня підготовка каналу

<a name="configuring-the-single-and-daily-channels"></a>
#### Конфігурування каналів Single і Daily

Канали (Channels) `single` і `daily` мають три необов'язкові параметри конфігурації: `bubble`, `permission`, і `locking`.

| Ім'я         | Опис                                                           | За замовчуванням |
| ------------ | -------------------------------------------------------------- | ------------ |
| `bubble`     | Чи повинні повідомлення переходити в інші канали після обробки | `true`       |
| `locking`    | Спробувати заблокувати файл журналу перед записом у нього      | `false`      |
| `permission` | Права доступу до файлу журналу                                 | `0644`       |

Додатково, спосіб зберігання для каналу `daily` можна налаштувати за допомогою змінної середовища `LOG_DAILY_DAYS` або шляхом встановлення параметра конфігурації `days`.

| Name   | Description                                                             | Default |
| ------ | ----------------------------------------------------------------------- | ------- |
| `days` | Кількість днів, протягом яких слід зберігати файли каналу daily.        | `14`    |

<a name="configuring-the-papertrail-channel"></a>
#### Конфігурування каналу Papertrail

Для каналу `papertrail` потрібні параметри конфігурації `host` і `port`. Їх можна визначити за допомогою змінних середовища `PAPERTRAIL_URL` і `PAPERTRAIL_PORT`. Ці значення можна отримати з [Papertrail](https://help.papertrailapp.com/kb/configuration/configuring-centralized-logging-from-php-apps/#send-events-from-php-app).

<a name="configuring-the-slack-channel"></a>
#### Конфігурування каналу Slack

Для каналу `slack` потрібен параметр конфігурації `url`. Це значення може бути визначено через змінну середовища `LOG_SLACK_WEBHOOK_URL`. Ця URL-адреса повинна відповідати URL-адресі [вхідного веб-хука](https://slack.com/apps/A0F7XDUAZ-incoming-webhooks), який ви налаштували для своєї команди Slack.

За замовчуванням Slack отримуватиме логи тільки з рівнем `critical` і вище; однак ви можете налаштувати це, використовуючи змінну середовища `LOG_LEVEL` або змінивши параметр конфігурації `level` у масиві вашого драйвера Slack.

<a name="logging-deprecation-warnings"></a>
### Логування попереджень про застарівання

PHP, Laravel та інші бібліотеки часто повідомляють своїх користувачів про те, що деякі з їхніх функцій застаріли і будуть видалені в майбутній версії. Якщо ви хочете реєструвати ці попередження про застарілість, ви можете вказати бажаний канал журналу `deprecations`, використовуючи змінну середовища `LOG_DEPRECATIONS_CHANNEL` або у файлі конфігурації вашої програми `config/logging.php`:

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    'channels' => [
        // ...
    ]

Або ви можете визначити канал журналу з іменем `deprecations`. Якщо канал журналу з таким ім'ям існує, він завжди буде використовуватися для реєстрації застарівання:

    'channels' => [
        'deprecations' => [
            'driver' => 'single',
            'path' => storage_path('logs/php-deprecation-warnings.log'),
        ],
    ],

<a name="building-log-stacks"></a>
## Побудова стека журналів

Як згадувалося раніше, драйвер `stack` дозволяє для зручності об'єднати кілька каналів в один канал журналу. Щоб проілюструвати, як використовувати стеки журналів, давайте розглянемо приклад конфігурації, яку ви можете побачити в експлуатаційному додатку:

```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['syslog', 'slack'], // [tl! add]
        'ignore_exceptions' => false,
    ],

    'syslog' => [
        'driver' => 'syslog',
        'level' => env('LOG_LEVEL', 'debug'),
        'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
        'replace_placeholders' => true,
    ],

    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
        'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
        'level' => env('LOG_LEVEL', 'critical'),
        'replace_placeholders' => true,
    ],
],
```

Давайте розберемо цю конфігурацію. По-перше, зверніть увагу, що наш канал `stack` об'єднує два інших канали за допомогою параметра `channels`: `syslog` і ``slack`. Таким чином, під час реєстрації повідомлень обидва канали матимуть можливість реєструвати повідомлення. Однак, як ми побачимо нижче, чи дійсно ці канали реєструють повідомлення, може бути визначено серйозністю / «рівнем» повідомлення.

<a name="log-levels"></a>
#### Рівні журналу

Зверніть увагу на параметр конфігурації `level`, присутній у конфігураціях каналів `syslog` і ``slack` у наведеному вище прикладі. Ця опція визначає мінімальний «рівень» повідомлення, яке має бути зареєстровано каналом. Monolog, на якому працюють служби ведення журналів Laravel, пропонує всі рівні журналів, визначені в специфікації [RFC 5424 specification](https://tools.ietf.org/html/rfc5424). Ці рівні журналу в порядку убування критичності: **emergency**, **alert**, **critical**, **error**, **warning**, **notice**, **info**, і **debug**.

Отже, уявіть, що ми реєструємо повідомлення, використовуючи метод `debug`:

    Log::debug('An informational message.');

З огляду на нашу конфігурацію, канал `syslog` записуватиме повідомлення до системного журналу; однак, оскільки повідомлення про помилку не є рівнем `critical` або вищим, то його не буде надіслано до Slack. Однак, якщо ми реєструємо повідомлення рівня `emergency`, то його буде надіслано як до системного журналу, так і до Slack, оскільки рівень `emergency` є вищим за наше мінімальне порогове значення для обох каналів:

    Log::emergency('The system is down!');

<a name="writing-log-messages"></a>
## Запис повідомлень журналу

Ви можете записувати інформацію в журнали за допомогою [фасаду](/docs/{{version}}/facades) `Log`. Як згадувалося раніше, засіб ведення журналу забезпечує вісім рівнів ведення журналу, визначених у специфікації [RFC 5424 specification](https://tools.ietf.org/html/rfc5424): **emergency**, **alert**, **critical**, **error**, **warning**, **notice**, **info**, і **debug**.

    use Illuminate\Support\Facades\Log;

    Log::emergency($message);
    Log::alert($message);
    Log::critical($message);
    Log::error($message);
    Log::warning($message);
    Log::notice($message);
    Log::info($message);
    Log::debug($message);

Ви можете викликати будь-який із цих методів, щоб записати повідомлення для відповідного рівня. За замовчуванням повідомлення буде записано в канал журналу за замовчуванням, як налаштовано вашим файлом конфігурації `logging`:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Models\User;
    use Illuminate\Support\Facades\Log;
    use Illuminate\View\View;
    
    class UserController extends Controller
    {
        /**
         * Показати профіль конкретного користувача.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function show(string $id): View
        {
            Log::info('Showing the user profile for user: {id}', ['id' => $id]);

            return view('user.profile', [
                'user' => User::findOrFail($id)
            ]);
        }
    }

<a name="contextual-information"></a>
### Контекстна інформація

Методам журналу може бути передано масив контекстних даних. Ці контекстні дані будуть відформатовані та відображені в повідомленні журналу:

    use Illuminate\Support\Facades\Log;

    Log::info('User {id} failed to login.', ['id' => $user->id]);

Іноді ви можете вказати деяку контекстну інформацію, яка повинна бути включена в усі наступні записи журналу в певному каналі. Наприклад, ви можете захотіти зареєструвати ідентифікатор запиту, пов'язаний із кожним вхідним запитом до вашого додатка. Для цього ви можете викликати метод `withContext` фасаду `Log`:

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;
    use Symfony\Component\HttpFoundation\Response;
    
    class AssignRequestId
    {
        /**
         * Обробник вхідного запиту .
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next): Response
        {
            $requestId = (string) Str::uuid();

            Log::withContext([
                'request-id' => $requestId
            ]);

            $response = $next($request);

            $response->headers->set('Request-Id', $requestId);

            return $response;
        }
    }

Якщо ви хочете додати спільну інформацію між _всіма_ каналами, ви можете викликати метод `Log::shareContext()`. Цей метод надасть додаткову інформацію всім створеним каналам і всім каналам, які будуть створені згодом.

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;

    class AssignRequestId
    {
         /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next): Response
        {
            $requestId = (string) Str::uuid();

            Log::shareContext([
                'request-id' => $requestId
            ]);

            // ...
        }
    }

> [!NOTE]
> Якщо вам потрібно передавати контекст журналу під час обробки завдань у черзі, ви можете використовувати [middleware завдань](/docs/{{version}}}/queues#job-middleware).

<a name="writing-to-specific-channels"></a>
### Запис у певні канали

За бажанням можна записати повідомлення в канал, відмінний від каналу за замовчуванням вашого додатка. Ви можете використовувати метод `channel` фасаду `Log` для отримання та реєстрації будь-якого каналу, визначеного у вашому файлі конфігурації:

    use Illuminate\Support\Facades\Log;

    Log::channel('slack')->info('Something happened!');

Якщо ви хочете створити стек протоколювання за запитом, що складається з декількох каналів, ви можете використовувати метод `stack`:

    Log::stack(['single', 'slack'])->info('Something happened!');

<a name="on-demand-channels"></a>
#### Канали за запитом

Також можливо створити канал за запитом, надавши конфігурацію під час виконання, без того, щоб ця конфігурація була присутня у файлі `logging` вашого додатка. Для цього ви можете передати масив конфігурації методу `build` фасаду `Log`:

    use Illuminate\Support\Facades\Log;

    Log::build([
      'driver' => 'single',
      'path' => storage_path('logs/custom.log'),
    ])->info('Something happened!');

Ви також можете включити канал за запитом у стек журналів за запитом. Цього можна домогтися, включивши екземпляр вашого каналу за запитом у масив, переданий у метод `stack`:

    use Illuminate\Support\Facades\Log;

    $channel = Log::build([
      'driver' => 'single',
      'path' => storage_path('logs/custom.log'),
    ]);

    Log::stack(['slack', $channel])->info('Something happened!');

<a name="monolog-channel-customization"></a>
## Налаштування каналу Monolog

<a name="customizing-monolog-for-channels"></a>
### Налаштування Monolog для каналів

Іноді потрібен повний контроль над налаштуванням Monolog для наявного каналу. Наприклад, буває необхідно налаштувати власну реалізацію Monolog `FormatterInterface` для вбудованого в Laravel каналу `single`.

Для початку визначте масив `tap` у конфігурації каналу. Масив `tap` повинен містити список класів, які повинні мати можливість налаштовувати (або «торкатися») екземпляр Monolog після його створення. Не існує звичайного місця для розміщення цих класів, тому ви можете створити каталог у своєму додатку, щоб розмістити ці класи:

    'single' => [
        'driver' => 'single',
        'tap' => [App\Logging\CustomizeFormatter::class],
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'replace_placeholders' => true,
    ],

Після того як ви налаштували опцію `tap` свого каналу, ви готові визначити клас, який контролюватиме ваш екземпляр Monolog. Цьому класу потрібен лише один метод: `__invoke`, який отримує екземпляр `Illuminate\Log\Logger`. Екземпляр `Illuminate\Log\Logger` передає всі виклики методів базовому екземпляру Monolog:

    <?php

    namespace App\Logging;
    
    use Illuminate\Log\Logger;
    use Monolog\Formatter\LineFormatter;

    class CustomizeFormatter
    {
        /**
         * Налаштувати переданий екземпляр реєстратора.
         *
         * @param  \Illuminate\Log\Logger  $logger
         * @return void
         */
        public function __invoke(Logger $logger): void
        {
            foreach ($logger->getHandlers() as $handler) {
                $handler->setFormatter(new LineFormatter(
                    '[%datetime%] %channel%.%level_name%: %message% %context% %extra%'
                ));
            }
        }
    }

> [!NOTE]  
> Усі ваші класи «tap» витягуються через [контейнер служб](/docs/{{version}}/container), тому будь-які залежності конструктора, які їм потрібні, будуть автоматично впроваджені.

<a name="creating-monolog-handler-channels"></a>
### Створення обробника каналів Monolog

У Monolog є безліч [доступних обробників](https://github.com/Seldaek/monolog/tree/main/src/Monolog/Handler), а в Laravel з коробки не включені канали для кожного з них. У деяких випадках вам може знадобитися створити власний канал, який є просто екземпляром певного обробника Monolog, у якого немає відповідного драйвера журналу Laravel. Ці канали можуть бути легко створені за допомогою драйвера `monolog`.

При використанні драйвера `monolog` параметр конфігурації `handler` використовується для вказівки того, який обробник буде створено. За бажання будь-які параметри конструктора, необхідні обробнику, можуть бути вказані за допомогою опції конфігурації `with`:

    'logentries' => [
        'driver'  => 'monolog',
        'handler' => Monolog\Handler\SyslogUdpHandler::class,
        'with' => [
            'host' => 'my.logentries.internal.datahubhost.company.com',
            'port' => '10000',
        ],
    ],

<a name="monolog-formatters"></a>
#### Форматери Монолог

Під час використання драйвера `monolog`, Monolog-клас `LineFormatter` буде використовуватися як засіб форматування за замовчуванням. Однак ви можете налаштувати тип засобу форматування, переданого обробнику, використовуючи параметри конфігурації `formatter` і `formatter_with`:

    'browser' => [
        'driver' => 'monolog',
        'handler' => Monolog\Handler\BrowserConsoleHandler::class,
        'formatter' => Monolog\Formatter\HtmlFormatter::class,
        'formatter_with' => [
            'dateFormat' => 'Y-m-d',
        ],
    ],

Якщо ви використовуєте обробник Monolog, який може надавати свій власний модуль форматування, ви можете встановити для параметра конфігурації `formatter` значення `default`:

    'newrelic' => [
        'driver' => 'monolog',
        'handler' => Monolog\Handler\NewRelicHandler::class,
        'formatter' => 'default',
    ],

<a name="monolog-processors"></a>
#### Monolog Процесори (Processors)

Monolog також може обробляти повідомлення перед їхнім записом у журнал. Ви можете створювати свої власні процесори або використовувати [наявні процесори, запропоновані Monolog](https://github.com/Seldaek/monolog/tree/main/src/Monolog/Processor).

Якщо ви хочете кастомізувати процесори для драйвера `monolog`, додайте значення конфігурації `processors` до конфігурації вашого каналу:

     'memory' => [
         'driver' => 'monolog',
         'handler' => Monolog\Handler\StreamHandler::class,
         'with' => [
             'stream' => 'php://stderr',
         ],
         'processors' => [
             // Simple syntax...
             Monolog\Processor\MemoryUsageProcessor::class,

             // With options...
             [
                'processor' => Monolog\Processor\PsrLogMessageProcessor::class,
                'with' => ['removeUsedContextFields' => true],
            ],
         ],
     ],

<a name="creating-custom-channels-via-factories"></a>
### Створення каналів через фабрики

Якщо ви хочете визначити повністю настроюваний канал, у якому у вас є повний контроль над створенням і конфігурацією Monolog, ви можете вказати тип драйвера `custom` у файлі конфігурації `config/logging.php`. Ваша конфігурація повинна містити параметр `via`, що містить ім'я класу фабрики, яка буде викликатися для створення екземпляра Monolog:

    'channels' => [
        'example-custom-channel' => [
            'driver' => 'custom',
            'via' => App\Logging\CreateCustomLogger::class,
        ],
    ],

Після того як ви налаштували канал драйвера `custom`, ви готові визначити клас, який створюватиме ваш екземпляр Monolog. Цьому класу потрібен лише один метод `__invoke`, який повинен повертати екземпляр реєстратора Monolog. Метод отримає масив конфігурації каналів як єдиний аргумент:

    <?php

    namespace App\Logging;

    use Monolog\Logger;

    class CreateCustomLogger
    {
        /**
         * Створити екземпляр власного реєстратора Monolog.
         *
         * @param  array  $config
         * @return \Monolog\Logger
         */
        public function __invoke(array $config): Logger
        {
            return new Logger(/* ... */);
        }
    }

<a name="tailing-log-messages-using-pail"></a>
## Перегляд повідомлення журналу за допомогою Pail

Часто вам може знадобитися переглядати журнали програми в режимі реального часу. Наприклад, під час налагодження проблеми або під час моніторингу журналів програми на предмет певних типів помилок.

Laravel Pail - це пакет, який дає вам змогу легко занурюватися в лог-файли вашого додатка Laravel прямо з командного рядка. На відміну від стандартної команди `tail`, Pail призначений для роботи з будь-яким драйвером журналів, включно з Sentry або Flare. Крім того, Pail надає набір корисних фільтрів, які допоможуть вам швидко знайти те, що ви шукаєте.

<img src="https://laravel.com/img/docs/pail-example.png">

<a name="pail-installation"></a>
### Установка

> [!WARNING]
> Laravel Pail підтримує [PHP 8.2+](https://php.net/releases/) і розширення [PCNTL](https://www.php.net/manual/en/book.pcntl.php).
Щоб почати роботу, встановіть Pail у свій проект за допомогою менеджера пакетів Composer:

```bash
composer require laravel/pail
```

<a name="pail-usage"></a>
### Використання

Щоб почати відстежувати журнали, виконайте команду `pail`:

```bash
php artisan pail
```

Щоб збільшити деталізацію виведення й уникнути усічення (...), використовуйте опцію `-v`:

```bash
php artisan pail -v
```

Для максимальної деталізації та відображення трасувань стека винятків використовуйте опцію `-vvv` :

```bash
php artisan pail -vv
```

Щоб припинити відстеження журналів, натисніть `Ctrl+C` у будь-який момент.

<a name="pail-filtering-logs"></a>
### Фільтрація логів

<a name="pail-filtering-logs-filter-option"></a>
#### `--filter`

Ви можете використовувати опцію `--filter` для фільтрації журналів за їхнім типом, файлом, повідомленням і вмістом трасування стека:

```bash
php artisan pail --filter="QueryException"
```

<a name="pail-filtering-logs-message-option"></a>
#### `--message`

Щоб фільтрувати журнали тільки за їхніми повідомленнями, ви можете використовувати опцію `--message`:

```bash
php artisan pail --message="User created"
```

<a name="pail-filtering-logs-level-option"></a>
#### `--level`

Опцію `--level` можна використовувати для фільтрації журналів за їхнім [рівнем](#log-levels):

```bash
php artisan pail --level=error
```

<a name="pail-filtering-logs-user-option"></a>
#### `--user`

Щоб відображати тільки ті журнали, які були записані під час автентифікованим користувачем, ви можете вказати ідентифікатор користувача в опції `---user`:

```bash
php artisan pail --user=1
```
