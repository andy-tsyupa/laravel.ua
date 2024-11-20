# Посібник з оновлення

- [Оновлення з 10.0 версії до 11.x](#upgrade-11.0)

<a name="high-impact-changes"></a>
## Зміни, що мають великий вплив

<!-- <div class="content-list" markdown="1"> -->

- [Оновлення залежностей](#updating-dependencies)
- [Структура додатка](#application-structure)
- [Типи з плаваючою крапкою](#floating-point-types)
- [Зміна стовпців](#modifying-columns)
- [Мінімальна версія SQLite](#sqlite-minimum-version)
- [Оновлення Sanctum](#updating-sanctum)

<!-- </div> -->

<a name="medium-impact-changes"></a>
## Зміни із середнім ступенем впливу

<!-- <div class="content-list" markdown="1"> -->

- [Carbon 3](#carbon-3)
- [Рехешинг пароля](#password-rehashing)
- [Посекундне обмеження](#per-second-rate-limiting)
- [Пакет Spatie Once](#spatie-once-package)

<!-- </div> -->

<a name="low-impact-changes"></a>
###### Зміни з низьким рівнем впливу

<!-- <div class="content-list" markdown="1"> -->

- [Видалення Doctrine DBAL](#doctrine-dbal-removal)
- [Метод `casts` Eloquent-моделі](#eloquent-model-casts-method)
- [Просторові типи](#spatial-types)
- [Контракт `Enumerable`](#the-enumerable-contract)
- [Контракт `UserProvider`](#the-user-provider-contract)
- [Контракт `Authenticatable`](#the-authenticatable-contract)

<!-- </div> -->

<a name="upgrade-11.0"></a>
## Оновлення з 10.0 версії до 11.x

<a name="estimated-upgrade-time-??-minutes"></a>
#### Приблизний час оновлення: 15 хвилин

> [!NOTE]
> Ми намагаємося задокументувати кожну можливу зміну, яка може призвести до порушення сумісності. Оскільки деякі з цих критичних змін знаходяться в маловідомих частинах фреймворку, тільки частина цих змін може вплинути на ваш додаток. Хочете заощадити час? Ви можете використовувати [Laravel Shift](https://laravelshift.com/) , щоб автоматизувати процес оновлення вашої програми.

<a name="updating-dependencies"></a>
### Оновлення залежностей

**Ймовірність впливу: висока**

#### Потрібен PHP 8.2.0

Laravel тепер вимагає PHP версії 8.2.0 або вище.

#### Потрібен curl 7.34.0

HTTP-клієнту Laravel тепер потрібна версія Curl 7.34.0 або вище.

#### Залежності Composer

Оновіть такі залежності у вашому файлі `composer.json`:

<!-- <div class="content-list" markdown="1"> -->

- `laravel/framework` to `^11.0`
- `nunomaduro/collision` to `^8.1`
- `laravel/breeze` to `^2.0` (якщо встановлено)
- `laravel/cashier` to `^15.0` (якщо встановлено)
- `laravel/dusk` to `^8.0` (якщо встановлено)
- `laravel/jetstream` to `^5.0` (якщо встановлено)
- `laravel/octane` to `^2.3` (якщо встановлено)
- `laravel/passport` to `^12.0` (якщо встановлено)
- `laravel/sanctum` to `^4.0` (якщо встановлено)
- `laravel/scout` to `^10.0` (якщо встановлено)
- `laravel/spark-stripe` to `^5.0` (якщо встановлено)
- `laravel/telescope` to `^5.0` (якщо встановлено)
- `livewire/livewire` to `^3.4` (якщо встановлено)
- `inertiajs/inertia-laravel` to `^1.0` (якщо встановлено)

<!-- </div> -->

Якщо ваш застосунок використовує Laravel Cashier Stripe, Passport, Sanctum, Spark Stripe або Telescope, вам необхідно опублікувати їхні міграції у ваш застосунок. Cashier Stripe, Passport, Sanctum, Spark Stripe і Telescope **більше не завантажують автоматично міграції з власного каталогу міграцій**. Тому вам слід запустити таку команду, щоб опублікувати їхні міграції у вашому застосунку:

```bash
php artisan vendor:publish --tag=cashier-migrations
php artisan vendor:publish --tag=passport-migrations
php artisan vendor:publish --tag=sanctum-migrations
php artisan vendor:publish --tag=spark-migrations
php artisan vendor:publish --tag=telescope-migrations
```

Крім того, вам слід переглянути посібники з оновлення для кожного з цих пакетів, щоб бути в курсі будь-яких додаткових критичних змін:

- [Laravel Cashier Stripe](#cashier-stripe)
- [Laravel Passport](#passport)
- [Laravel Sanctum](#sanctum)
- [Laravel Spark Stripe](#spark-stripe)
- [Laravel Telescope](#telescope)

Якщо ви встановили інсталятор Laravel вручну, вам слід оновити інсталятор через Composer:

```bash
composer global require laravel/installer:^5.6
```

Нарешті, ви можете видалити залежність Composer `doctrine/dbal`, якщо ви раніше додали її до свого додатка, оскільки Laravel більше не залежить від цього пакета.

<a name="application-structure"></a>
### Структура додатка

Laravel 11 представляє нову структуру програми з меншою кількістю файлів за замовчуванням. А саме, нові додатки Laravel містять менше постачальників послуг, посередників і файлів конфігурації.

Однак ми **не рекомендуємо** додаткам Laravel 10, які оновлюються до Laravel 11, намагатися перенести структуру своїх додатків, оскільки Laravel 11 був ретельно налаштований для підтримки структури додатків Laravel 10.

<a name="authentication"></a>
### Аутентифікація

<a name="password-rehashing"></a>
#### Рехешинг пароля

**Ймовірність впливу: Низька**

Laravel 11 автоматично перехешує паролі вашого користувача під час аутентифікації, якщо «робочий фактор» вашого алгоритму хешування було оновлено з моменту останнього хешування пароля.

Зазвичай це не повинно порушувати роботу вашого додатка; однак, якщо поле «password» вашої моделі `User` має ім'я, відмінне від `password`, вам слід вказати ім'я поля через властивість `authPasswordName` моделі:

    protected $authPasswordName = 'custom_password_field';

Так само ви можете відключити перехешування пароля, додавши параметр `rehash_on_login` до файлу конфігурації вашого застосунку `config/hashing.php`:

    'rehash_on_login' => false,

<a name="the-user-provider-contract"></a>
#### Контракт `UserProvider`

**Ймовірність впливу: Низька**

Контракт `Illuminate\Contracts\Auth\UserProvider` отримав новий метод `rehashPasswordIfRequired`. Цей метод відповідає за повторне хешування і збереження пароля користувача в сховище у разі зміни коефіцієнта роботи алгоритму хешування програми.

Якщо ваш додаток або пакет визначає клас, що реалізує цей інтерфейс, вам слід додати у вашу реалізацію новий метод `rehashPasswordIfRequired`. Еталонну реалізацію можна знайти в класі `Illuminate\Auth\EloquentUserProvider`:
```php
public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false);
```

<a name="the-authenticatable-contract"></a>
#### Контракт `Authenticatable`

**Ймовірність впливу: Низька**

Контракт `Illuminate\Contracts\Auth\Authenticatable` отримав новий метод `getAuthPasswordName`. Цей метод відповідає за повернення імені стовпця пароля вашого об'єкта, що аутентифікується.

Якщо ваш додаток або пакет визначає клас, що реалізує цей інтерфейс, вам слід додати у вашу реалізацію новий метод `getAuthPasswordName`:

```php
public function getAuthPasswordName()
{
    return 'password';
}
```

Модель `User` за замовчуванням, увімкнена в Laravel, автоматично отримує цей метод, оскільки цей метод включено в трейт `Illuminate\Auth\Authenticatable`.

<a name="the-authentication-exception-class"></a>
#### Класс `AuthenticationException`

**Ймовірність впливу: Дуже низька**

Метод `redirectTo` класу `Illuminate\Auth\AuthenticationException` тепер вимагає екземпляр `Illuminate\Http\Request` як перший аргумент. Якщо ви вручну перехоплюєте це виключення і викликаєте метод `redirectTo`, вам слід відповідним чином оновити свій код:

```php
if ($e instanceof AuthenticationException) {
    $path = $e->redirectTo($request);
}
```

<a name="email-verification-notification-on-registration"></a>
#### Повідомлення про перевірку електронної пошти під час реєстрації

**Ймовірність впливу: Дуже низька**

Слухач `SendEmailVerificationNotification` тепер автоматично реєструється для події `Registered`, якщо він ще не зареєстрований у `EventServiceProvider` вашого додатка. Якщо `EventServiceProvider` вашого додатка не реєструє цього слухача і ви не хочете, щоб Laravel автоматично реєстрував його для вас, вам слід визначити порожній метод `configureEmailVerification` в `EventServiceProvider` вашого додатка:

```php
protected function configureEmailVerification()
{
    // ...
}
```

<a name="cache"></a>
### Кеш

<a name="cache-key-prefixes"></a>
#### Префікси ключів кешу

**Ймовірність впливу: Дуже низька**

Раніше, якщо префікс ключа кешу був визначений для сховищ кешу DynamoDB, Memcached або Redis, Laravel додавав до префікса `:`. У Laravel 11 префікс ключа кешу не отримує суфікс `:`. Якщо ви хочете зберегти попередню поведінку префіксів, ви можете вручну додати суфікс `:` до префікса ключа кешу.

<a name="collections"></a>
### Колекції

<a name="the-enumerable-contract"></a>
#### Контракт `Enumerable`

**Ймовірність впливу: Низька**

Метод `dump` контракту `Illuminate\Support\Enumerable` було оновлено, щоб приймати змінний аргумент `...$args`. Якщо ви реалізуєте цей інтерфейс, вам слід відповідним чином оновити свою реалізацію:

```php
public function dump(...$args);
```

<a name="database"></a>
### База даних

<a name="sqlite-minimum-version"></a>
#### SQLite 3.26.0+

**Ймовірність впливу: Висока**

Якщо ваша програма використовує базу даних SQLite, потрібна SQLite 3.26.0 або пізніша версія.

<a name="eloquent-model-casts-method"></a>
#### Метод `casts` Eloquent-моделі

**Ймовірність впливу: низька**

Базовий клас моделі Eloquent тепер визначає метод `casts` для підтримки визначення приведення атрибутів. Якщо одна з моделей вашого додатка визначає відношення приведення, це може конфліктувати з методом `casts`, який тепер присутній у базовому класі моделі Eloquent.

<a name="modifying-columns"></a>
#### Зміна стовпців

**Ймовірність впливу: Висока**

При зміні стовпця тепер ви повинні явно включати всі модифікатори, які ви хочете зберегти у визначенні стовпця після його зміни. Будь-які відсутні атрибути будуть видалені. Наприклад, щоб зберегти атрибути `unsigned`, `default` і `comment`, ви маєте явно викликати кожен модифікатор під час зміни стовпчика, навіть якщо ці атрибути було призначено стовпчику під час попередньої міграції.

Наприклад, уявіть, що у вас є міграція, в результаті якої створюється стовпець `votes` з атрибутами `unsigned`, `default` і `comment`:

```php
Schema::create('users', function (Blueprint $table) {
    $table->integer('votes')->unsigned()->default(1)->comment('The vote count');
});
```

Пізніше ви напишете міграцію, яка також змінить стовпець на значення `nullable`:

```php
Schema::table('users', function (Blueprint $table) {
    $table->integer('votes')->nullable()->change();
});
```

У Laravel 10 ця міграція збереже атрибути `unsigned`, `default` і `comment` у стовпці. Однак у Laravel 11 міграція тепер також повинна включати всі атрибути, які раніше були визначені в стовпці. В іншому випадку вони будуть видалені:

```php
Schema::table('users', function (Blueprint $table) {
    $table->integer('votes')
        ->unsigned()
        ->default(1)
        ->comment('The vote count')
        ->nullable()
        ->change();
});
```

Метод `change` не змінює індекси стовпця. Тому ви можете використовувати модифікатори індексу, щоб явно додавати або видаляти індекс при зміні стовпця:

```php
// Add an index...
$table->bigIncrements('id')->primary()->change();

// Drop an index...
$table->char('postal_code', 10)->unique(false)->change();
```

Якщо ви не хочете оновлювати всі наявні міграції «змін» у вашому застосунку, щоб зберегти наявні атрибути стовпця, ви можете просто [скоротити свої міграції](/docs/{{version}}/migrations#squashing-migrations):

```bash
php artisan schema:dump
```

Щойно ваші міграції буде завершено, Laravel «перенесе» базу даних, використовуючи файл схеми вашого застосунку, перш ніж запускати будь-які очікувані міграції.

<a name="floating-point-types"></a>
#### Типи з плаваючою крапкою

**Ймовірність впливу: Висока**

Типи стовпців міграції `double` і `float` були переписані, щоб забезпечити однаковість у всіх базах даних.

Тип стовпця `double` тепер створює еквівалентний стовпчик `DOUBLE` без загальної кількості цифр і місць (цифр після десяткової крапки), що є стандартним синтаксисом SQL. Тому ви можете видалити аргументи для `$total` і `$places`:

```php
$table->double('amount');
```

Стовпець типу `float` тепер створює еквівалентний стовпець `FLOAT` без загальної кількості цифр і місць (цифр після десяткової крапки), але з додатковою специфікацією `$precision` для визначення розміру сховища у вигляді 4-байтового стовпця одинарної точності або 8-байтового стовпця подвійної точності. Таким чином, ви можете видалити аргументи для `$total` і `$places` і вказати необов'язкове значення `$precision` відповідно до вашого бажаного значення і відповідно до документації вашої бази даних:

```php
$table->float('amount', precision: 53);
```

Методи `unsignedDecimal`, `unsignedDouble` і `unsignedFloat` було видалено, оскільки модифікатор `unsigned` для цих типів стовпчиків застарів у MySQL і ніколи не стандартизувався в інших системах баз даних. Однак, якщо ви хочете й надалі використовувати застарілий беззнаковий атрибут для цих типів стовпців, ви можете пов'язати метод `unsigned` із визначенням стовпця:

```php
$table->decimal('amount', total: 8, places: 2)->unsigned();
$table->double('amount')->unsigned();
$table->float('amount', precision: 53)->unsigned();
```

<a name="dedicated-mariadb-driver"></a>
#### Виділений драйвер MariaDB

**Ймовірність впливу: Дуже низька**

Замість того, щоб завжди використовувати драйвер MySQL при підключенні до баз даних MariaDB, Laravel 11 додає спеціальний драйвер бази даних для MariaDB.

Якщо ваша програма підключається до бази даних MariaDB, ви можете оновити конфігурацію підключення новим драйвером MariaDB, щоб у майбутньому скористатися спеціальними функціями MariaDB:

    'driver' => 'mariadb',
    'url' => env('DB_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    // ...

Наразі новий драйвер MariaDB поводиться як поточний драйвер MySQL, за одним винятком: метод побудови схеми `uuid` створює власні стовпчики `UUID` замість стовпців `char(36)`.

Якщо у ваших наявних міграціях використовується метод побудови схеми `uuid`, і ви вирішили використати новий драйвер бази даних `mariadb`, вам слід оновити виклики методу `uuid` у вашій міграції на `char`, щоб уникнути критичних змін або несподіваної поведінки:

```php
Schema::table('users', function (Blueprint $table) {
    $table->char('uuid', 36);.

    // ...
});
```

<a name="spatial-types"></a>
#### Просторові типи

**Ймовірність впливу: Низька**

Типи просторових стовпців міграції міграції бази даних були переписані, щоб забезпечити однаковість у всіх базах даних. Тому ви можете видалити методи `point`, `lineString`, `polygon`, `geometryCollection`, `multiPoint`, `multiLineString`, `multiPolygon` і `multiPolygonZ` зі своїх міграцій і використовувати замість цього методи `geometry` або `geography`:

```php
$table->geometry('shapes');
$table->geography('coordinates');
```

Щоб явно обмежити тип або ідентифікатор системи просторової прив'язки для значень, що зберігаються в стовпчику в MySQL, MariaDB і PostgreSQL, ви можете передати методу `subtype` і `srid`:

```php
$table->geometry('dimension', subtype: 'polygon', srid: 0);
$table->geography('latitude', subtype: 'point', srid: 4326);
```

Модифікатори стовпців `isGeometry` і `projection` граматики PostgreSQL були відповідно видалені.

<a name="doctrine-dbal-removal"></a>
#### Видалення Doctrine DBAL

**Ймовірність впливу: Низька**

Наступний список класів і методів, пов'язаних із Doctrine DBAL, було видалено. Laravel більше не залежить від цього пакета, і реєстрація користувацьких типів Doctrines більше не потрібна для правильного створення і зміни різних типів стовпців, для яких раніше були потрібні користувацькі типи:

- `Illuminate\Database\Schema\Builder::$alwaysUsesNativeSchemaOperationsIfPossible` class property
- `Illuminate\Database\Schema\Builder::useNativeSchemaOperationsIfPossible()` method
- `Illuminate\Database\Connection::usingNativeSchemaOperations()` method
- `Illuminate\Database\Connection::isDoctrineAvailable()` method
- `Illuminate\Database\Connection::getDoctrineConnection()` method
- `Illuminate\Database\Connection::getDoctrineSchemaManager()` method
- `Illuminate\Database\Connection::getDoctrineColumn()` method
- `Illuminate\Database\Connection::registerDoctrineType()` method
- `Illuminate\Database\DatabaseManager::registerDoctrineType()` method
- `Illuminate\Database\PDO` directory
- `Illuminate\Database\DBAL\TimestampType` class
- `Illuminate\Database\Schema\Grammars\ChangeColumn` class
- `Illuminate\Database\Schema\Grammars\RenameColumn` class
- `Illuminate\Database\Schema\Grammars\Grammar::getDoctrineTableDiff()` method

Крім того, більше не потрібна реєстрація користувацьких типів Doctrine через `dbal.types` у файлі конфігурації `database` вашого додатка.

Якщо ви раніше використовували Doctrine DBAL для перевірки вашої бази даних і пов'язаних із нею таблиць, ви можете використовувати нові власні методи схеми Laravel (`Schema::getTables()`, `Schema::getColumns()`, `Schema::getIndexes()` `, `Schema::getForeignKeys()` і т. д.).

<a name="deprecated-schema-methods"></a>

#### Застарілі методи схеми

**Ймовірність впливу: Дуже низька**

Застарілі методи `Schema::getAllTables()`, `Schema::getAllViews()` і `Schema::getAllTypes()`, що ґрунтуються на Doctrine, було видалено на користь нових вбудованих у Laravel `Schema::getTables()`. , `Schema::getViews()` і `Schema::getTypes()`.

Під час використання PostgreSQL і SQL Server жоден із нових методів схеми не прийматиме посилання з трьох частин (наприклад, `database.schema.table`). Тому замість цього вам слід використовувати `connection()` для оголошення бази даних:

```php
Schema::connection('database')->hasTable('schema.table');
```

<a name="get-column-types"></a>
#### Метод побудовника схеми `getColumnType()`

**Ймовірність впливу: Дуже низька**

Метод `Schema::getColumnType()` тепер завжди повертає фактичний тип цього стовпця, а не еквівалентний тип Doctrine DBAL.

<a name="database-connection-interface"></a>
#### Інтерфейс підключення до бази даних

**Ймовірність впливу: Дуже низька**

Інтерфейс `Illuminate\Database\ConnectionInterface` отримав новий метод `scalar`. Якщо ви визначаєте власну реалізацію цього інтерфейсу, вам слід додати у вашу реалізацію метод `scalar`:

```php
public function scalar($query, $bindings = [], $useReadPdo = true);
```

<a name="dates"></a>
### Дати

<a name="carbon-3"></a>
#### Carbon 3

**Ймовірність впливу: Середня**

Laravel 11 підтримує як Carbon 2, так і Carbon 3. Carbon - це бібліотека маніпулювання датами, яка широко використовується Laravel і пакетами в усій екосистемі. Якщо ви оновитеся до Carbon 3, майте на увазі, що методи `diffIn*` тепер повертають числа з плаваючою комою і можуть повертати від'ємні значення для вказівки напряму часу, що є суттєвою відмінністю від Carbon 2. Перегляньте [журнал змін](https://github.com/briannesbitt/Carbon/releases/tag/3.0.0) і [документацію](https://carbon.nesbot.com/docs/#api-carbon-3) Carbon для отримання докладної інформації про те, як обробляти ці та інші зміни.

<a name="mail"></a>
### Пошта

<a name="the-mailer-contract"></a>
#### Контракт `Mailer`

**Ймовірність впливу: Дуже низька**

Контракт `Illuminate\Contracts\Mail\Mailer` отримав новий метод `sendNow`. Якщо ваш додаток або пакет вручну реалізує цей контракт, вам слід додати у свою реалізацію новий метод `sendNow`:

```php
public function sendNow($mailable, array $data = [], $callback = null);
```

<a name="packages"></a>
### Пакети

<a name="publishing-service-providers"></a>
#### Публікація постачальників послуг у додатку

**Ймовірність впливу: Дуже низька**

Якщо ви написали пакет Laravel, який вручну публікує постачальника послуг у каталозі застосунку `app/Providers` і вручну змінює файл конфігурації застосунку `config/app.php` для реєстрації постачальника послуг, вам слід оновити свій пакет, щоб використовувати новий метод `ServiceProvider::addProviderToBootstrapFile`.

Метод `addProviderToBootstrapFile` автоматично додасть опублікованого вами постачальника послуг до файлу `bootstrap/providers.php` додатка, оскільки масив `providers` не існує у файлі конфігурації `config/app.php` у новому додатку Laravel 11.

```php
use Illuminate\Support\ServiceProvider;

ServiceProvider::addProviderToBootstrapFile(Provider::class);
```

<a name="queues"></a>
### Черги

<a name="the-batch-repository-interface"></a>
#### Інтерфейс `BatchRepository`

**Ймовірність впливу: Дуже низька**

Інтерфейс `Illuminate\Bus\BatchRepository` отримав новий метод `rollBack`. Якщо ви реалізуєте цей інтерфейс у своєму власному пакеті або додатку, вам слід додати у свою реалізацію цей метод:

```php
public function rollBack();
```

<a name="synchronous-jobs-in-database-transactions"></a>
#### Синхронні завдання в транзакціях бази даних

**Ймовірність впливу: Дуже низька**

Раніше синхронні завдання (завдання, що використовують драйвер черги `sync`) виконувалися негайно, незалежно від того, чи було для параметра конфігурації `after_commit` з'єднання з чергою встановлено значення `true`, або для завдання було викликано метод `afterCommit`.

У Laravel 11 синхронні завдання черги тепер враховуватимуть конфігурацію з'єднання черги або завдання «після фіксації».

<a name="rate-limiting"></a>
### Обмеження швидкості

<a name="per-second-rate-limiting"></a>
#### Посекундне обмеження

**Ймовірність впливу: Середня**

Laravel 11 підтримує посекундне обмеження швидкості замість похвилинної деталізації. Існує безліч потенційних критичних змін, про які вам слід знати, пов'язаних із цією зміною.

Конструктор класу `GlobalLimit` тепер приймає секунди замість хвилин. Цей клас не документований і зазвичай не буде використовуватися вашим додатком:

```php
new GlobalLimit($attempts, 2 * 60);
```

Конструктор класу `Limit` тепер приймає секунди замість хвилин. Усі документовані варіанти використання цього класу обмежуються статичними конструкторами, такими як `Limit::perMinute` і `Limit::perSecond`. Однак якщо ви створюєте екземпляр цього класу вручну, вам слід оновити додаток, щоб надати секунди конструктору класу:

```php
new Limit($key, $attempts, 2 * 60);
```

Властивість `decayMinutes` класу `Limit` було перейменовано в `decaySeconds` і тепер містить секунди замість хвилин.

Конструктори класів `Illuminate\Queue\Middleware\ThrottlesExceptions` і `Illuminate\Queue\Middleware\ThrottlesExceptionsWithRedis` тепер приймають секунди замість хвилин:

```php
new ThrottlesExceptions($attempts, 2 * 60);
new ThrottlesExceptionsWithRedis($attempts, 2 * 60);
```

<a name="cashier-stripe"></a>
### Cashier Stripe

<a name="updating-cashier-stripe"></a>
#### Оновлення Cashier Stripe

**Ймовірність впливу: Висока**

Laravel 11 більше не підтримує Cashier Stripe 14.x. Тому вам слід оновити залежність Laravel Cashier Stripe вашого додатка до `^15.0` у вашому файлі `composer.json`.

Cashier Stripe 15.0 більше не завантажує міграції автоматично з власного каталогу міграцій. Натомість вам слід запустити наступну команду, щоб опублікувати міграції Cashier Stripe у вашому додатку:

```shell
php artisan vendor:publish --tag=cashier-migrations
```

Ознайомтеся з повним [керівництвом з оновлення Cashier Stripe](https://github.com/laravel/cashier-stripe/blob/15.x/UPGRADE.md), щоб дізнатися про додаткові критичні зміни.

<a name="spark-stripe"></a>
### Spark (Stripe)

<a name="updating-spark-stripe"></a>
#### Оновлення Spark Stripe

**Ймовірність впливу: Висока**

Laravel 11 більше не підтримує Laravel Spark Stripe 4.x. Тому вам слід оновити залежність Laravel Spark Stripe вашого додатка до `^5.0` у файлі `composer.json`.

Spark Stripe 5.0 більше не завантажує міграції автоматично з власного каталогу міграцій. Натомість вам слід запустити наступну команду, щоб опублікувати міграції Spark Stripe у вашому додатку:

```shell
php artisan vendor:publish --tag=spark-migrations
```

Ознайомтеся з повним [посібником з оновлення Spark Stripe](https://spark.laravel.com/docs/spark-stripe/upgrade.html), щоб дізнатися про додаткові критичні зміни.
<a name="passport"></a>
### Passport

<a name="updating-telescope"></a>
#### Оновлення Passport

**Ймовірність впливу: Висока**

Laravel 11 більше не підтримує Laravel Passport 11.x. Тому вам слід оновити залежність Laravel Passport вашого додатка до `^12.0` у вашому файлі `composer.json`.

Passport 12.0 більше не завантажує міграції автоматично з власного каталогу міграцій. Натомість вам слід запустити наступну команду, щоб опублікувати міграції Passport у вашому додатку:
```shell
php artisan vendor:publish --tag=passport-migrations
```

Крім того, тип надання пароля за замовчуванням вимкнено. Ви можете увімкнути його, викликавши метод `enablePasswordGrant` у методі `boot` `AppServiceProvider` вашої програми:

    public function boot(): void
    {
        Passport::enablePasswordGrant();
    }

<a name="sanctum"></a>
### Sanctum

<a name="updating-sanctum"></a>
#### Оновлення Sanctum

**Ймовірність впливу: Висока**

Laravel 11 більше не підтримує Laravel Sanctum 3.x. Тому вам слід оновити залежність Laravel Sanctum вашого додатка до `^4.0` у вашому файлі `composer.json`.

Sanctum 4.0 більше не завантажує міграції автоматично з власного каталогу міграцій. Натомість вам слід запустити наступну команду, щоб опублікувати міграції Sanctum у вашому додатку:
```shell
php artisan vendor:publish --tag=sanctum-migrations
```

Потім у файлі конфігурації вашого застосунку `config/sanctum.php` вам слід оновити посилання на посередників `authenticate_session`, `encrypt_cookies` і `validate_csrf_token` у такий спосіб:

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

<a name="telescope"></a>
### Telescope

<a name="updating-telescope"></a>
#### Оновлення Telescope

**Ймовірність впливу: Висока**

Laravel 11 більше не підтримує Laravel Telescope 4.x. Тому вам слід оновити залежність Laravel Telescope вашого додатка до `^5.0` у вашому файлі `composer.json`.

Telescope 5.0 більше не завантажує міграції автоматично з власного каталогу міграцій. Натомість вам слід запустити наступну команду, щоб опублікувати міграції Telescope у вашому додатку:
```shell
php artisan vendor:publish --tag=telescope-migrations
```

<a name="spatie-once-package"></a>
### Пакет Spatie Once

**Ймовірність впливу: Середня**

Laravel 11 тепер надає власну функцію [`once`](/docs/{{version}}/helpers#method-once), щоб гарантувати, що дане замикання буде виконано тільки один раз. Тому, якщо ваша програма залежить від пакета spatie/once, вам слід видалити його з файлу `composer.json` вашої програми, щоб уникнути конфліктів.

<a name="miscellaneous"></a>
### Різне

Ми також рекомендуємо вам переглянути зміни в `laravel/laravel` [репозиторій GitHub](https://github.com/laravel/laravel). Хоча багато з цих змін не є обов'язковими, ви можете захотіти синхронізувати ці файли з вашим додатком. Деякі з цих змін будуть описані в цьому посібнику з оновлення, а інші, наприклад зміни у файлах конфігурації або коментарях, не будуть розглянуті. Ви можете легко переглянути зміни за допомогою [інструменту порівняння GitHub](https://github.com/laravel/laravel/compare/10.x...11.x) і вибрати, які оновлення важливі для вас.
