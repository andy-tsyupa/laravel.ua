# Планування завдань

- [Вступ](#introduction)
- [Визначення розкладів](#defining-schedules)
    - [Планування команд Artisan](#scheduling-artisan-commands)
    - [Планування надсилання завдань у черзі](#scheduling-queued-jobs)
    - [Планування команд операційної системи](#scheduling-shell-commands)
    - [Параметри періодичності розкладу](#schedule-frequency-options)
    - [Часові пояси](#timezones)
    - [Запобігання дублюванню завдань](#preventing-task-overlaps)
    - [Виконання завдань на одному сервері](#running-tasks-on-one-server)
    - [Фонові завдання](#background-tasks)
    - [Режим технічного обслуговування](#maintenance-mode)
- [Запуск планувальника](#running-the-scheduler)
    - [Завдання з інтервалом менше хвилини](#sub-minute-scheduled-tasks)
    - [Локальний запуск планувальника](#running-the-scheduler-locally)
- [Результат виконання завдання](#task-output)
- [Хуки виконання завдання](#task-hooks)
- [Події](#events)

<a name="introduction"></a>
## Вступ

У минулому ви могли створювати запис конфігурації cron для кожного завдання, яке потрібно було запланувати на своєму сервері. Однак це може швидко стати проблемою, тому що ваш розклад завдань не перебуває в системі управління версіями, і вам доведеться підключатися через SSH, щоб переглянути наявні записи cron або додати додаткові записи.

Планувальник команд Laravel пропонує новий підхід до управління запланованими завданнями на вашому сервері. Планувальник дає змогу вам швидко та виразно визначати розклад команд у самому додатку Laravel. Під час використання планувальника на вашому сервері потрібен лише один запис cron. Розклад завдань зазвичай визначається у файлі `routes/console.php` вашого додатка.

<a name="defining-schedules"></a>
## Визначення розкладів

Ви можете визначити всі заплановані завдання у файлі `routes/console.php` вашого застосунку. Для початку розглянемо приклад. У цьому прикладі ми визначимо замикання, яке викликатиметься щодня опівночі. У замиканні ми виконаємо запит до бази даних для очищення таблиці:

    <?php

    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schedule;

    Schedule::call(function () {
        DB::table('recent_users')->delete();
    })->daily();

На додаток до планування з використанням замикань ви також можете використовувати [об'єкти, що викликаються](https://www.php.net/manual/ru/language.oop5.magic.php#language.oop5.magic.invoke). Об'єкти, що викликаються, - це прості класи PHP, що містять метод `__invoke`:

    Schedule::call(new DeleteRecentUsers)->daily();

Якщо ви віддаєте перевагу зарезервувати файл `routes/console.php` тільки для визначень команд, ви можете використовувати метод `withSchedule` у файлі `bootstrap/app.php` вашого додатка для визначення запланованих завдань. Цей метод приймає замикання, яке отримує екземпляр планувальника:

    use Illuminate\Console\Scheduling\Schedule;

    ->withSchedule(function (Schedule $schedule) {
        $schedule->call(new DeleteRecentUsers)->daily();
    })

Якщо ви хочете переглянути список ваших запланованих завдань і їх подальшого запуску, то ви можете використовувати команду `schedule:list` Artisan:

```bash
php artisan schedule:list
```

<a name="scheduling-artisan-commands"></a>
### Планування команд Artisan

На додаток до планування з використанням замикань ви також можете використовувати [команди Artisan](artisan) і системні команди. Наприклад, ви можете використовувати метод `command` для планування команди Artisan, використовуючи ім'я команди або клас.

Під час планування команд Artisan з використанням імені класу команди ви можете передати масив додаткових аргументів командного рядка, які мають бути передані команді під час її виклику:

    use App\Console\Commands\SendEmailsCommand;
    use Illuminate\Support\Facades\Schedule;

    Schedule::command('emails:send Taylor --force')->daily();

    Schedule::command(SendEmailsCommand::class, ['Taylor', '--force'])->daily();

<a name="scheduling-artisan-closure-commands"></a>
#### Планування команд закриття Artisan

Якщо ви хочете запланувати команду Artisan, визначену замиканням, ви можете пов'язати методи, пов'язані з плануванням, після визначення команди:

    Artisan::command('delete:recent-users', function () {
        DB::table('recent_users')->delete();
    })->purpose('Delete recent users')->daily();

Якщо вам потрібно передати аргументи команді закриття, ви можете передати їх методу `schedule`:

    Artisan::command('emails:send {user} {--force}', function ($user) {
        // ...
    })->purpose('Send emails to the specified user')->schedule(['Taylor', '--force'])->daily();

<a name="scheduling-queued-jobs"></a>
### Планування надсилання завдань у черзі

Метод `job` використовується для планування відправлення [завдання в чергу](/docs/{{version}}/queues). Цей метод забезпечує зручний спосіб планування таких завдань без використання методу `call` із замиканням:

    use App\Jobs\Heartbeat;
    use Illuminate\Support\Facades\Schedule;

    Schedule::job(new Heartbeat)->everyFiveMinutes();

Необов'язкові другий і третій аргументи можуть бути передані методу `job` для вказівки імені черги і з'єднання черги, які повинні використовуватися для постановки завдання в чергу:

    use App\Jobs\Heartbeat;
    use Illuminate\Support\Facades\Schedule;

    // Надсилаємо завдання в чергу «heartbeats» з'єднання «sqs» ...
    Schedule::job(new Heartbeat, 'heartbeats', 'sqs')->everyFiveMinutes();

<a name="scheduling-shell-commands"></a>
### Планування команд операційної системи

Метод `exec` використовується для передачі команди операційній системі:

    use Illuminate\Support\Facades\Schedule;

    Schedule::exec('node /home/forge/script.js')->daily();

<a name="schedule-frequency-options"></a>
### Параметри періодичності розкладу

Ми вже бачили кілька прикладів того, як можна налаштувати завдання на виконання через певні проміжки часу. Однак існує набагато більше параметрів планування, які можна призначити завданню:

<div class="overflow-auto">

| Метод                              | Опис                                                     |
| ---------------------------------- | -------------------------------------------------------- |
| `->cron('* * * * *');`             | Запустити завдання за розкладом із параметрами cron      |
| `->everySecond();`                 | Запускати завдання щомиті                                |
| `->everyTwoSeconds();`             | - кожні 2 секунди                                        |
| `->everyFiveSeconds();`            | - кожні 5 секунд                                         |
| `->everyTenSeconds();`             | - кожні 10 секунд                                        |
| `->everyFifteenSeconds();`         | - кожні 15 секунд                                        |
| `->everyTwentySeconds();`          | - кожні 20 секунд                                        |
| `->everyThirtySeconds();`          | - кожні 30 секунд                                        |
| `->everyMinute();`                 | Запускати завдання щохвилини                             |
| `->everyTwoMinutes();`             | - кожні 2 хвилини                                        |
| `->everyThreeMinutes();`           | - кожні 3 хвилини                                        |
| `->everyFourMinutes();`            | - кожні 4 хвилини                                        |
| `->everyFiveMinutes();`            | - кожні 5 хвилин                                         |
| `->everyTenMinutes();`             | - кожні 10 хвилин                                        |
| `->everyFifteenMinutes();`         | - кожні 15 хвилин                                        |
| `->everyThirtyMinutes();`          | - кожні 30 хвилин                                        |
| `->hourly();`                      |  - щогодини                                              |
| `->hourlyAt(17);`                  | - о 17 хвилин кожної години                              |
| `->everyOddHour($minutes = 0);`    | - кожну непарну годину                                   |
| `->everyTwoHours($minutes = 0);`   | - кожні 2 години                                         |
| `->everyThreeHours($minutes = 0);` | - кожні 3 години                                         |
| `->everyFourHours($minutes = 0);`  | - кожні 4 години                                         |
| `->everySixHours($minutes = 0);`   | - кожні 6 годин                                          |
| `->daily();`                       | - щодня опівночі                                         |
| `->dailyAt('13:00');`              | - щодня о 13:00                                          |
| `->twiceDaily(1, 13);`             | - щодня двічі на день: двічі на день: о 1:00 і 13:00     |
| `->twiceDailyAt(1, 13, 15);`       | - щодня о 1:15 та 13:15.                                 |
| `->weekly();`                      | - щотижня в неділю о 00:00                               |
| `->weeklyOn(1, '8:00');`           | - щотижня в понеділок о 8:00                             |
| `->monthly();`                     | - щомісяця першого числа о 00:00                         |
| `->monthlyOn(4, '15:00');`         | - щомісяця 4 числа о 15:00                               |
| `->twiceMonthly(1, 16, '13:00');`  | - щомісяця двічі на місяць: 1 і 16 числа о 13:00         |
| `->lastDayOfMonth('15:00');`       | - щомісяця в останній день місяця о 15:00                |
| `->quarterly();`                   | - щокварталу в перший день о 00:00                       |
| `->quarterlyOn(4, '14:00');`       | - щокварталу в 4-й день о 14:00.                         |
| `->yearly();`                      | - щорічно в перший день о 00:00                          |
| `->yearlyOn(6, 1, '17:00');`       | - щорічно в червні першого числа о 17:00                 |
| `->timezone('America/New_York');`  | Встановити часовий пояс для завдання                     |

</div>

Ці методи можна комбінувати з додатковими обмеженнями для створення ще більш точних розкладів, які виконуються тільки в певні дні тижня. Наприклад, ви можете запланувати виконання команди щотижня в понеділок:

    use Illuminate\Support\Facades\Schedule;

    // Запускаємо раз на тиждень у понеділок о 13:00 ...
    Schedule::call(function () {
        // ...
    })->weekly()->mondays()->at('13:00');

    // Запускаємо по буднях щогодини з 8 ранку до 5 вечора ...
    Schedule::command('foo')
              ->weekdays()
              ->hourly()
              ->timezone('America/Chicago')
              ->between('8:00', '17:00');

Список додаткових обмежень розкладу можна знайти нижче:

<div class="overflow-auto">

| Метод                                    | Опис                                                       |
| ---------------------------------------- | ---------------------------------------------------------- |
| `->weekdays();`                          | Обмежити виконання завдання робочими днями                 |
| `->weekends();`                          | - вихідними днями                                          |
| `->sundays();`                           | - недільним днем                                           |
| `->mondays();`                           | - понеділком                                               |
| `->tuesdays();`                          | - вівторком                                                |
| `->wednesdays();`                        | - середовищем                                              |
| `->thursdays();`                         | - четвергом                                                |
| `->fridays();`                           | - п'ятницею                                                |
| `->saturdays();`                         | - суботою                                                  |
| `->days(array\|mixed);`                  | - визначеними днями                                        |
| `->between($startTime, $endTime);`       | - часовими інтервалами початку і закінчення                |
| `->unlessBetween($startTime, $endTime);` | - через виключення часових інтервалів початку і закінчення |
| `->when(Closure);`                       | - на основі істинності результату виконаного замикання     |
| `->environments($env);`                  | - оточенням виконання                                      |

</div>

<a name="day-constraints"></a>
#### Денні обмеження

Метод `days` можна використовувати для обмеження виконання завдання певними днями тижня. Наприклад, ви можете запланувати виконання команди щогодини по неділях і середах:

    use Illuminate\Support\Facades\Schedule;

    Schedule::command('emails:send')
                    ->hourly()
                    ->days([0, 3]);

Як альтернативу ви можете використовувати константи, доступні в класі `Illuminate\Console\Scheduling\Schedule\Schedule`, при вказівці днів, в які має виконуватися завдання:

    use Illuminate\Support\Facades;
    use Illuminate\Console\Scheduling\Schedule;

    Facades\Schedule::command('emails:send')
                    ->hourly()
                    ->days([Schedule::SUNDAY, Schedule::WEDNESDAY]);

<a name="between-time-constraints"></a>
#### Обмеження з часовими інтервалами

Метод `between` може використовуватися для обмеження виконання завдання залежно від часу доби:

    Schedule::command('emails:send')
                        ->hourly()
                        ->between('7:00', '22:00');

Так само метод `unlessBetween` може використовуватися для виключення певних періодів часу виконання завдання:

    Schedule::command('emails:send')
                        ->hourly()
                        ->unlessBetween('23:00', '4:00');

<a name="truth-test-constraints"></a>
#### Умовні обмеження

Метод `when` може використовуватися для обмеження виконання завдання на основі істинності результату виконаного замикання. Іншими словами, якщо передане замикання повертає `true`, то завдання буде виконуватися доти, доки жодні інші обмежувальні умови не перешкоджають його запуску:

    Schedule::command('emails:send')->daily()->when(function () {
        return true;
    });

Метод `skip` можна розглядати як протилежний методу `when`. Якщо метод `skip` повертає `true`, то заплановане завдання не буде виконано:

    Schedule::command('emails:send')->daily()->skip(function () {
        return true;
    });

При використанні ланцюжка методів `when`, запланована команда буде виконуватися тільки в тому випадку, якщо всі умови `when` повертають значення `true`.

<a name="environment-constraints"></a>
#### Обмеження оточення виконання

Метод `environment` може використовуватися для виконання завдань тільки в зазначених оточеннях, згідно з визначенням [змінної `APP_ENV` оточення](/docs/{{version}}/configuration#environment-configuration):

    Schedule::command('emails:send')
                ->daily()
                ->environments(['staging', 'production']);

<a name="timezones"></a>
### Часові пояси

Використовуючи метод `timezone`, ви можете вказати, що час запланованої задачі має інтерпретуватися в рамках переданого часового поясу:

    use Illuminate\Support\Facades\Schedule;

    Schedule::command('report:generate')
             ->timezone('America/New_York')
             ->at('2:00')

Якщо ви постійно призначаєте один і той самий часовий пояс для всіх запланованих завдань, то ви можете вказати, який часовий пояс має бути призначено усім розкладам, визначивши параметр ``schedule_timezone` у файлі конфігурації `app` вашого застосунку:

    'timezone' => env('APP_TIMEZONE', 'UTC'),
    
    'schedule_timezone' => 'America/Chicago',

> [!WARNING]
> Пам'ятайте, що в деяких часових поясах використовується літній час. Коли відбувається перехід на літній час, ваше заплановане завдання може запускатися двічі або навіть не запускатися взагалі. З цієї причини ми рекомендуємо за можливості уникати вказівок часових поясів під час планування.

<a name="preventing-task-overlaps"></a>
### Запобігання дублюванню завдань

За замовчуванням заплановані завдання будуть виконуватися, навіть якщо попередній екземпляр завдання все ще виконується. Щоб запобігти цьому, ви можете використовувати метод `withoutOverlapping`:

    use Illuminate\Support\Facades\Schedule;

    Schedule::command('emails:send')->withoutOverlapping();

У цьому прикладі команда `emails:send` [Artisan](artisan) буде запускатися щохвилини за умови, що вона ще не запущена. Метод `withoutOverlapping` особливо корисний, якщо у вас є завдання, які відрізняються за часом виконання, що не дозволяє вам точно передбачити, скільки часу займе поточне завдання.

За необхідності ви можете вказати, скільки хвилин має минути до закінчення блокування завдань, що «перекриваються». За замовчуванням термін блокування закінчується через 24 години:

    Schedule::command('emails:send')->withoutOverlapping(10);

Усередині метод `withoutOverlapping` використовує [кеш](/docs/{{version}}}/cache) вашої програми для отримання блокувань. За необхідності ви можете очистити ці блокування, використовуючи команду Artisan `schedule:clear-cache`. Зазвичай це необхідно тільки в разі, якщо завдання застряє через непередбачену проблему з сервером.

<a name="running-tasks-on-one-server"></a>
### Виконання завдань на одному сервері

> [!WARNING]
> Щоб використовувати цей функціонал, ваш застосунок має використовувати за замовчуванням один із таких драйверів кешу: `database`, `memcached`, `dynamodb`, або `redis`. Крім того, всі сервери повинні взаємодіяти з одним і тим самим центральним сервером кешування.

Якщо планувальник вашого додатка працює на кількох серверах, то ви можете обмежити виконання запланованого завдання тільки на одному сервері. Наприклад, припустимо, що у вас є заплановане завдання, за яким щоп'ятниці ввечері створюється новий звіт. Якщо планувальник завдань працює на трьох робочих серверах, заплановане завдання буде запущено на всіх трьох серверах і тричі згенерує звіт. Не дуже добре!

Щоб вказати, що завдання має виконуватися тільки на одному сервері, використовуйте метод `onOneServer` під час визначення запланованого завдання. Перший сервер, який отримає завдання, забезпечить атомарне блокування завдання, щоб інші сервери не могли одночасно виконувати те саме завдання:

    use Illuminate\Support\Facades\Schedule;

    Schedule::command('report:generate')
                    ->fridays()
                    ->at('17:00')
                    ->onOneServer();

<a name="naming-unique-jobs"></a>
#### Іменування завдань одного сервера

Іноді вам може знадобитися запланувати відправлення одного й того самого завдання з різними параметрами, але при цьому вказати Laravel запускати кожну модифікацію завдання на одному сервері. Для цього ви можете присвоїти кожному визначенню розкладу унікальне ім'я за допомогою методу `name`:

```php
Schedule::job(new CheckUptime('https://laravel.com'))
            ->name('check_uptime:laravel.com')
            ->everyFiveMinutes()
            ->onOneServer();

Schedule::job(new CheckUptime('https://vapor.laravel.com'))
            ->name('check_uptime:vapor.laravel.com')
            ->everyFiveMinutes()
            ->onOneServer();
```

Аналогічно, для запланованих замикань також необхідно присвоїти ім'я, якщо вони мають виконуватися на одному сервері:

```php
Schedule::call(fn () => User::resetApiRequestCount())
    ->name('reset-api-request-count')
    ->daily()
    ->onOneServer();
```

<a name="background-tasks"></a>
### Фонові завдання

За замовчуванням, кілька завдань, запланованих одночасно, будуть виконуватися послідовно відповідно до порядку, яким вони визначені у вашому методі ``chedule``. Якщо у вас є тривалі завдання, це може призвести до того, що наступні завдання почнуться набагато пізніше, ніж очікувалося. Якщо ви хочете запускати завдання у фоновому режимі відповідно до плану, то ви можете використовувати метод `runInBackground`:

    use Illuminate\Support\Facades\Schedule;

    Schedule::command('analytics:report')
             ->daily()
             ->runInBackground();

> [!WARNING]
> Метод `runInBackground` може використовуватися тільки під час планування завдань за допомогою методів `command` і `exec`.

<a name="maintenance-mode"></a>
### Режим технічного обслуговування

Заплановані завдання вашого застосунку не виконуватимуться, коли застосунок перебуває в [режимі обслуговування](/docs/{{version}}}/configuration#maintenance-mode), оскільки ми не хочемо, щоб ваші завдання заважали будь-якому незавершеному процесу обслуговування, що виконується на вашому сервері. Однак, якщо ви хочете примусово запустити завдання навіть у режимі обслуговування, то використовуйте метод `evenInMaintenanceMode` при визначенні завдання:

    Schedule::command('emails:send')->evenInMaintenanceMode();

<a name="running-the-scheduler"></a>
## Запуск планувальника

Тепер, коли ми дізналися, як визначати планування завдання, давайте обговоримо, як же запускати їх на нашому сервері. Команда `schedule:run` Artisan проаналізує всі ваші заплановані завдання і визначить, чи потрібно їх запускати, виходячи з поточного часу сервера.

Отже, під час використання планувальника Laravel нам потрібно додати лише один конфігураційний запис cron на наш сервер, який запускає команду `schedule:run` щохвилини. Якщо ви не знаєте, як додати записи cron на свій сервер, то розгляньте можливість використання такої служби, як [Laravel Forge](https://forge.laravel.com), яка може керувати записами cron за вас:

```shell
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

<a name="sub-minute-scheduled-tasks"></a>
### Завдання з інтервалом менше хвилини

У більшості операційних систем завдання cron обмежені запуском не частіше одного разу на хвилину. Проте, планувальник завдань Laravel дає змогу вам запланувати виконання завдань із частішими інтервалами, навіть щосекунди:

    use Illuminate\Support\Facades\Schedule;

    Schedule::call(function () {
        DB::table('recent_users')->delete();
    })->everySecond();


Коли у вашому застосунку визначено завдання з інтервалом менше хвилини, команда `schedule:run` виконуватиметься до кінця поточної хвилини, а не завершиться негайно. Це дозволяє команді викликати всі необхідні завдання з інтервалом менше хвилини протягом хвилини.

Оскільки завдання з інтервалом менше хвилини, які виконуються довше, ніж очікувалося, можуть затримувати виконання наступних завдань, рекомендується, щоб усі такі завдання були поміщені в чергу завдань або виконували команди у фоновому режимі для обробки фактичного завдання:

    use App\Jobs\DeleteRecentUsers;

    Schedule::job(new DeleteRecentUsers)->everyTenSeconds();

    Schedule::command('users:delete')->everyTenSeconds()->runInBackground();

<a name="interrupting-sub-minute-tasks"></a>
#### Переривання завдань з інтервалом менше хвилини:

Оскільки команда `schedule:run` виконується протягом усієї хвилини за наявності завдань з інтервалом менше хвилини, вам іноді може знадобитися перервати виконання команди під час розгортання вашої програми. В іншому випадку екземпляр команди `schedule:run`, який вже виконується, буде продовжувати використовувати код вашого додатка, розгорнутого раніше, поки не завершиться поточна хвилина.

Для переривання `schedule:run`, що виконуються, ви можете додати команду `schedule:interrupt` до сценарію розгортання вашого застосунку. Цю команду слід викликати після завершення розгортання вашої програми:

```shell
php artisan schedule:interrupt
```

<a name="running-the-scheduler-locally"></a>
## Локальний запуск планувальника

Як правило, на локальній машині немає потреби в додаванні запису cron планувальника. Замість цього ви можете використовувати команду `schedule:work` Artisan. Ця команда працюватиме на передньому плані та викликатиме планувальник щохвилини, поки ви не завершите команду:

```shell
php artisan schedule:work
```

<a name="task-output"></a>
## Результат виконання завдання

Планувальник Laravel пропонує кілька зручних методів для роботи з виведенням результатів, створених запланованими завданнями. По-перше, використовуючи метод `sendOutputTo`, ви можете відправити результат у файл для подальшого перегляду:

    use Illuminate\Support\Facades\Schedule;

    Schedule::command('emails:send')
             ->daily()
             ->sendOutputTo($filePath);

Якщо ви хочете додати результат у вказаний файл, то використовуйте метод `appendOutputTo`:

    Schedule::command('emails:send')
             ->daily()
             ->appendOutputTo($filePath);

Використовуючи метод `emailOutputTo`, ви можете надіслати результат електронною поштою на будь-яку адресу. Перед надсиланням результатів виконання завдання електронною поштою вам слід налаштувати [поштові служби](/docs/{{version}}}/mail) Laravel:

    Schedule::command('report:generate')
             ->daily()
             ->sendOutputTo($filePath)
             ->emailOutputTo('taylor@example.com');

Якщо ви хочете надіслати результат електронною поштою тільки в тому разі, якщо запланована (Artisan або системна) команда завершується ненульовим кодом повернення, використовуйте метод `emailOutputOnFailure`:

    Schedule::command('report:generate')
             ->daily()
             ->emailOutputOnFailure('taylor@example.com');

> [!WARNING]
> Методи `emailOutputTo`, `emailOutputOnFailure`, ``sendOutputTo`, and `appendOutputTo` можуть використовуватися тільки під час планування завдань за допомогою методів `command` і `exec`.

<a name="task-hooks"></a>
## Хуки виконання завдання

Використовуючи методи `before` і `after`, ви можете вказати замикання, які будуть виконуватися до і після виконання запланованого завдання:

    use Illuminate\Support\Facades\Schedule;

    Schedule::command('emails:send')
             ->daily()
             ->before(function () {
                 // Завдання готове до виконання ...
             })
             ->after(function () {
                 // Завдання виконано ...
             });

Методи `onSuccess` і `onFailure` дають змогу вказати замикання, які виконуватимуться в разі успішного або невдалого виконання запланованого завдання. Помилка означає, що запланована (Artisan або системна) команда завершилася ненульовим кодом повернення:

    Schedule::command('emails:send')
             ->daily()
             ->onSuccess(function () {
                 // Завдання успішно виконано ...
             })
             ->onFailure(function () {
                 // Не вдалося виконати завдання ...
             });

Якщо з вашої команди доступний вивід результату, то ви можете отримати до нього доступ у ваших хуках `after`, `onSuccess` або `onFailure`, вказавши тип екземпляра `Illuminate\Support\Stringable` як аргумент `$output` замикання під час визначення вашого хука:

    use Illuminate\Support\Stringable;

    Schedule::command('emails:send')
             ->daily()
             ->onSuccess(function (Stringable $output) {
                 // Завдання успішно виконано ...
             })
             ->onFailure(function (Stringable $output) {
                 // Не вдалося виконати завдання ...
             });

<a name="pinging-urls"></a>
#### Пінгування URL-адрес

Використовуючи методи `pingBefore` і `thenPing`, планувальник може автоматично пінгувати за вказаною URL до або після виконання завдання. Цей метод корисний для повідомлення зовнішньої служби, такої як [Envoyer](https://envoyer.io), про те, що ваше заплановане завдання запущено або завершено:

    Schedule::command('emails:send')
             ->daily()
             ->pingBefore($url)
             ->thenPing($url);

Методи `pingBeforeIf` і `thenPingIf` можуть використовуватися для пінгування за вказаним URL, тільки якщо передана умова `$condition` істинна:

    Schedule::command('emails:send')
             ->daily()
             ->pingBeforeIf($condition, $url)
             ->thenPingIf($condition, $url);

Методи `pingOnSuccess` і `pingOnFailure` можуть використовуватися для пінгування за вказаним URL тільки в разі успішного або невдалого виконання завдання. Помилка означає, що запланована (Artisan або системна) команда завершилася ненульовим кодом повернення:

    Schedule::command('emails:send')
             ->daily()
             ->pingOnSuccess($successUrl)
             ->pingOnFailure($failureUrl);

<a name="events"></a>
## Події

Laravel надсилає різні [події](/docs/{{version}}}/events) у процесі планування. Ви можете [визначити прослуховувачі](/docs/{{version}}}/events) для будь-якої з наступних подій:

| Найменування події                                          |
| ----------------------------------------------------------- |
| `Illuminate\Console\Events\ScheduledTaskStarting`           |
| `Illuminate\Console\Events\ScheduledTaskFinished`           |
| `Illuminate\Console\Events\ScheduledBackgroundTaskFinished` |
| `Illuminate\Console\Events\ScheduledTaskSkipped`            |
| `Illuminate\Console\Events\ScheduledTaskFailed`             |
