# Файлове сховище

- [Вступ](#introduction)
- [Конфігурування](#configuration)
    - [Локальний драйвер](#the-local-driver)
    - [Публічний диск](#the-public-disk)
    - [Попередня підготовка драйверів](#driver-prerequisites)
    - [Обмежені та тільки для читання файлові системи](#scoped-and-read-only-filesystems)
    - [Файлові системи, сумісні з Amazon S3](#amazon-s3-compatible-filesystems)
- [Доступ до екземплярів дисків](#obtaining-disk-instances)
    - [Диски за запитом](#on-demand-disks)
- [Отримання файлів](#retrieving-files)
    - [Завантаження файлів](#downloading-files)
    - [URL-адреси файлів](#file-urls)
    - [Тимчасові URL](#temporary-urls)
    - [Метадані файлу](#file-metadata)
- [Зберігання файлів](#storing-files)
    - [Додавання інформації до файлів](#prepending-appending-to-files)
    - [Копіювання та переміщення файлів](#copying-moving-files)
    - [Автоматичне потокове передавання](#automatic-streaming)
    - [Завантаження файлів](#file-uploads)
    - [Видимість файлу](#file-visibility)
- [Видалення файлів](#deleting-files)
- [Каталоги](#directories)
- [Тестування](#testing)
- [Користувацькі файлові системи](#custom-filesystems)

<a name="introduction"></a>
## Вступ

Laravel забезпечує потужну абстракцію файлової системи завдяки чудовому пакету [Flysystem](https://github.com/thephpleague/flysystem) PHP від Френка де Йонга. Інтеграція Laravel з Flysystem містить прості драйвери для роботи з локальними файловими системами, SFTP і Amazon S3. Ба більше, напрочуд просто перемикатися між цими варіантами зберігання: як локального, так і виробничого серверів - оскільки API залишається однаковим для кожної системи.

<a name="configuration"></a>
## Конфігурування

Файл конфігурації файлової системи Laravel знаходиться в `config/filesystems.php`. У цьому файлі ви можете налаштувати всі «диски» файлової системи. Кожен диск являє собою певний драйвер сховища і місце зберігання. Приклади конфігурацій для кожного підтримуваного драйвера включено до конфігураційного файлу, тож ви можете змінити конфігурацію, що відображає ваші уподобання щодо зберігання та облікові дані.

Драйвер `local` взаємодіє з файлами, що зберігаються локально на сервері, на якому запущено застосунок Laravel, у той час як драйвер `s3` використовується для запису в службу хмарного сховища Amazon S3.

> [!NOTE]
> Ви можете налаштувати стільки дисків, скільки захочете, і навіть мати кілька дисків, які використовують один і той самий драйвер.

<a name="the-local-driver"></a>
### Локальний драйвер

У разі використання драйвера `local` усі операції з файлами виконуються відносно кореневого каталогу, визначеного у файлі конфігурації `filesystems`. За замовчуванням це значення задано каталогом `storage/app`. Отже, наступний метод запише файл у `storage/app/example.txt`:

    use Illuminate\Support\Facades\Storage;

    Storage::disk('local')->put('example.txt', 'Contents');

<a name="the-public-disk"></a>
### Публічний диск

Диск `public`, визначений у файлі конфігурації `filesystems` вашого додатка, призначений для файлів, які будуть загальнодоступними. За замовчуванням публічний диск використовує драйвер `local` і зберігає свої файли в `storage/app/public`.

Щоб зробити ці файли доступними з інтернету, ви повинні створити символічне посилання на `storage/app/public` у `public/storage`. Використання цієї угоди про папки дасть змогу зберігати ваші публічні файли в одному каталозі, який може бути легко доступний між розгортаннями під час використання систем розгортання з нульовим часом простою, таких як [Envoyer](https://envoyer.io).

Щоб створити символічне посилання, ви можете використовувати команду `storage:link` Artisan:

```shell
php artisan storage:link
```

Після того як було створено символічне посилання, ви можете створювати URL-адреси для збережених файлів, використовуючи помічник `asset`:

    echo asset('storage/file.txt');

Ви можете налаштувати додаткові символічні посилання у файлі конфігурації `filesystems`. Кожне з налаштованих посилань буде створено, коли ви запустите команду `storage:link`:

    'links' => [
        public_path('storage') => storage_path('app/public'),
        public_path('images') => storage_path('app/images'),
    ],

Команда `storage:unlink` може бути використана для знищення ваших налаштованих символічних посилань:

```shell
php artisan storage:unlink
```

<a name="driver-prerequisites"></a>
### Попередня підготовка драйверів

<a name="s3-driver-configuration"></a>
#### Конфігурування драйвера S3


Перш ніж почати використовувати драйвер S3, вам необхідно встановити пакет Flysystem S3 за допомогою менеджера пакетів Composer:

```shell
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

Масив конфігурації диска S3 знаходиться у вашому файлі конфігурації `config/filesystems.php`. Зазвичай вам слід налаштувати інформацію та облікові дані S3, використовуючи такі змінні середовища, на які посилається файл конфігурації `config/filesystems.php`:

```
AWS_ACCESS_KEY_ID=<your-key-id>
AWS_SECRET_ACCESS_KEY=<your-secret-access-key>
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=<your-bucket-name>
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Для зручності ці змінні середовища відповідають угоді про імена, що використовується в AWS CLI.

<a name="ftp-driver-configuration"></a>
#### Конфігурування драйвера FTP

Для використання драйвера FTP, вам потрібно встановити пакет Flysystem FTP за допомогою менеджера пакетів Composer. Виконайте таку команду:

```shell
composer require league/flysystem-ftp "^3.0"
```

Інтеграція Laravel з Flysystem відмінно працює з FTP; однак, приклад конфігурації за замовчуванням не включено до конфігураційного файлу `filesystems.php` фреймворка. Якщо вам потрібно налаштувати файлову систему FTP, ви можете використовувати приклад конфігурації нижче:

    'ftp' => [
        'driver' => 'ftp',
        'host' => env('FTP_HOST'),
        'username' => env('FTP_USERNAME'),
        'password' => env('FTP_PASSWORD'),

        // Optional FTP Settings...
        // 'port' => env('FTP_PORT', 21),
        // 'root' => env('FTP_ROOT'),
        // 'passive' => true,
        // 'ssl' => true,
        // 'timeout' => 30,
    ],

<a name="sftp-driver-configuration"></a>
#### Конфігурування драйвера SFTP

Для використання драйвера SFTP вам необхідно встановити пакет Flysystem SFTP за допомогою менеджера пакетів Composer. Виконайте таку команду:

```shell
composer require league/flysystem-sftp-v3 "^3.0"
```

Інтеграція Laravel з Flysystem відмінно працює з SFTP; однак, приклад конфігурації за замовчуванням не включено до конфігураційного файлу `filesystems.php` фреймворка. Якщо вам потрібно налаштувати файлову систему SFTP, ви можете використовувати приклад конфігурації нижче:

    'sftp' => [
        'driver' => 'sftp',
        'host' => env('SFTP_HOST'),

        // Settings for basic authentication...
        'username' => env('SFTP_USERNAME'),
        'password' => env('SFTP_PASSWORD'),

        // Settings for SSH key based authentication with encryption password...
        'privateKey' => env('SFTP_PRIVATE_KEY'),
        'passphrase' => env('SFTP_PASSPHRASE'),

        // Settings for file / directory permissions...
        'visibility' => 'private', // `private` = 0600, `public` = 0644
        'directory_visibility' => 'private', // `private` = 0700, `public` = 0755

        // Optional SFTP Settings...
        // 'hostFingerprint' => env('SFTP_HOST_FINGERPRINT'),
        // 'maxTries' => 4,
        // 'passphrase' => env('SFTP_PASSPHRASE'),
        // 'port' => env('SFTP_PORT', 22),
        // 'root' => env('SFTP_ROOT', ''),
        // 'timeout' => 30,
        // 'useAgent' => true,
    ],

<a name="scoped-and-read-only-filesystems"></a>
### Обмежені та тільки для читання файлові системи

Обмежені диски дають змогу вам визначити файлову систему, у якій усі шляхи автоматично доповнюються зазначеним префіксом шляху. Перш ніж створити обмежений диск файлової системи, вам необхідно встановити додатковий пакет Flysystem за допомогою менеджера пакетів Composer:

```shell
composer require league/flysystem-path-prefixing "^3.0"
```

Ви можете створити екземпляр файлової системи з обмеженим шляхом для будь-якого існуючого диска файлової системи, визначивши диск, який використовує драйвер `scoped`. Наприклад, ви можете створити диск, який обмежує ваш існуючий диск `s3` до певного префікса шляху, і потім кожна операція з файлом, яка використовує ваш обмежений диск, буде використовувати вказаний префікс:

```php
's3-videos' => [
    'driver' => 'scoped',
    'disk' => 's3',
    'prefix' => 'path/to/videos',
],
```

«Тільки для читання» диски дозволяють створювати файлові диски, які не дозволяють операції запису. Перш ніж використовувати параметр конфігурації `read-only`, вам необхідно встановити додатковий пакет Flysystem за допомогою менеджера пакетів Composer:

```shell
composer require league/flysystem-read-only "^3.0"
```

Потім ви можете включити параметр конфігурації `read-only` в один або кілька масивів конфігурації ваших дисків:

```php
's3-videos' => [
    'driver' => 's3',
    // ...
    'read-only' => true,
],
```

<a name="amazon-s3-compatible-filesystems"></a>
### Файлові системи, сумісні з Amazon S3

За замовчуванням файл конфігурації вашого додатка `filesystems` містить конфігурацію диска для диска `s3`. Крім використання цього диска для взаємодії з Amazon S3, ви можете використовувати його для взаємодії з будь-якою сумісною з S3 службою зберігання файлів, такою як [MinIO](https://github.com/minio/minio) або [DigitalOcean Spaces](https://www.digitalocean.com/products/spaces/).

Зазвичай після оновлення облікових даних диска для відповідності обліковим даним служби, яку ви плануєте використовувати, вам потрібно лише оновити значення параметра конфігурації `endpoint`. Значення цієї опції зазвичай визначається через змінну оточення `AWS_ENDPOINT`:

    'endpoint' => env('AWS_ENDPOINT', 'https://minio:9000'),

<a name="minio"></a>
#### MinIO

Для того щоб інтеграція Flysystem у Laravel генерувала правильні URL під час використання MinIO, вам слід визначити змінну оточення `AWS_URL`, щоб вона відповідала локальному URL вашого додатка та містила ім'я бакета в шлях URL:

```ini
AWS_URL=http://localhost:9000/local
```

> [!WARNING]  
> Генерація тимчасових URL-адрес для сховища з використанням методу `temporaryUrl` може не працювати при використанні MinIO, якщо клієнт не може отримати доступ до кінцевої точки.

<a name="obtaining-disk-instances"></a>
## Доступ до екземплярів дисків

Фасад `Storage` використовується для взаємодії з будь-яким із ваших сконфігурованих дисків. Наприклад, ви можете використовувати метод `put` фасаду, щоб зберегти аватар на диску за замовчуванням. Якщо ви викликаєте методи фасаду `Storage` без попереднього виклику методу `disk`, то метод буде проксований на диск за замовчуванням:

    use Illuminate\Support\Facades\Storage;

    Storage::put('avatars/1', $content);

Якщо ваш додаток взаємодіє з кількома дисками, то ви можете використовувати метод `disk` фасаду `Storage` для роботи з файлами на зазначеному диску:

    Storage::disk('s3')->put('avatars/1', $content);

<a name="on-demand-disks"></a>
### Диски за запитом

Іноді ви можете захотіти створити диск під час виконання, використовуючи задану конфігурацію, без того, щоб ця конфігурація фактично була присутня у файлі конфігурації вашого додатка `filesystems`. Для цього ви можете передати масив конфігурації методу `build` фасаду `Storage`:

```php
use Illuminate\Support\Facades\Storage;

$disk = Storage::build([
    'driver' => 'local',
    'root' => '/path/to/root',
]);

$disk->put('image.jpg', $content);
```

<a name="retrieving-files"></a>
## Отримання файлів

Метод `get` використовується для отримання вмісту файлу. Необроблений строковий вміст файлу буде повернуто методом. Пам'ятайте, що всі шляхи до файлів мають бути вказані відносно «кореня» диска:

    $contents = Storage::get('file.jpg');

Якщо файл, який ви витягуєте, містить JSON, ви можете використовувати метод `json` для вилучення файлу і декодування його вмісту:

    $orders = Storage::json('orders.json');


Метод `exists` використовується для визначення, чи існує файл на диску:

    if (Storage::disk('s3')->exists('file.jpg')) {
        // ...
    }

Метод `missing` використовується, щоб визначити, чи відсутній файл на диску:

    if (Storage::disk('s3')->missing('file.jpg')) {
        // ...
    }

<a name="downloading-files"></a>
### Завантаження файлів

Метод `download` використовується для генерації відповіді, яка змушує браузер користувача завантажувати файл за вказаним шляхом. Метод `download` приймає ім'я файлу як другий аргумент методу, що визначає ім'я файлу, яке бачить користувач, який завантажує цей файл. Нарешті, ви можете передати масив заголовків HTTP як третій аргумент методу:

    return Storage::download('file.jpg');

    return Storage::download('file.jpg', $name, $headers);

<a name="file-urls"></a>
### URL-адреси файлів

Ви можете використовувати метод `url`, щоб отримати URL для вказаного файлу. Якщо ви використовуєте драйвер `local`, він зазвичай просто додає `/storage` до вказаного шляху і повертає відносну URL-адресу файлу. Якщо ви використовуєте драйвер `s3`, буде повернуто абсолютну зовнішню URL-адресу:

    use Illuminate\Support\Facades\Storage;

    $url = Storage::url('file.jpg');

При використанні драйвера `local` усі файли, які повинні бути загальнодоступними, повинні бути поміщені в каталог `storage/app/public`. Крім того, ви повинні [створити символічне посилання](#the-public-disk) в `public/storage`, яке вказує на каталог `storage/app/public`.

> [!WARNING]  
> При використанні драйвера `local` значення `url`, що повертається, не є URL-кодованим. З цієї причини ми рекомендуємо завжди зберігати ваші файли, використовуючи імена, які будуть створювати допустимі URL-адреси.

<a name="url-host-customization"></a>
#### Налаштування хоста URL

Якщо ви хочете змінити хост для URL-адрес, створених з використанням фасаду `Storage`, ви можете додати або змінити параметр `url` у масиві конфігурації диска:

    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
        'throw' => false,
    ],

<a name="temporary-urls"></a>
### Тимчасові URL

Використовуючи метод `temporaryUrl`, ви можете створювати тимчасові URL-адреси для файлів, що зберігаються за допомогою драйверів `local` і `s3`. Цей метод приймає шлях і екземпляр `DateTime`, що вказує, коли повинен закінчитися доступ до файлу за URL:

    use Illuminate\Support\Facades\Storage;

    $url = Storage::temporaryUrl(
        'file.jpg', now()->addMinutes(5)
    );

<a name="enabling-local-temporary-urls"></a>
#### Увімкнення локальних тимчасових URL-адрес

Якщо ви почали розробку свого додатка до того, як у драйвері `local` з'явилася підтримка тимчасових URL-адрес, вам може знадобитися ввімкнути локальні тимчасові URL-адреси. Для цього додайте опцію `serve` у масив конфігурації вашого `local` диска у файлі конфігурації `config/filesystems.php`:

```php
'local' => [
    'driver' => 'local',
    'root' => storage_path('app/private'),
    'serve' => true, // [tl! add]
    'throw' => false,
],
```

<a name="s3-request-parameters"></a>
#### Параметри запиту S3

Якщо вам потрібно вказати додаткові [параметри запиту S3](https://docs.aws.amazon.com/AmazonS3/latest/API/RESTObjectGET.html#RESTObjectGET-requests), то ви можете передати масив параметрів запиту як третій аргумент методу `temporaryUrl`:

    $url = Storage::temporaryUrl(
        'file.jpg',
        now()->addMinutes(5),
        [
            'ResponseContentType' => 'application/octet-stream',
            'ResponseContentDisposition' => 'attachment; filename=file2.jpg',
        ]
    );

<a name="customizing-temporary-urls"></a>
#### Налаштування тимчасових URL-адрес

Якщо вам потрібно налаштувати спосіб створення тимчасових URL-адрес для певного диска сховища, ви можете використовувати метод `buildTemporaryUrlsUsing`. Наприклад, це може бути корисно, якщо у вас є контролер, що дозволяє завантажувати файли, що зберігаються на диску, який зазвичай не підтримує тимчасові URL-адреси. Зазвичай цей метод слід викликати з `boot` методу сервіс-провайдера:

    <?php

    namespace App\Providers;

    use DateTime;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            Storage::disk('local')->buildTemporaryUrlsUsing(
                function (string $path, DateTime $expiration, array $options) {
                    return URL::temporarySignedRoute(
                        'files.download',
                        $expiration,
                        array_merge($options, ['path' => $path])
                    );
                }
            );
        }
    }

<a name="temporary-upload-urls"></a>
#### Тимчасові URL-адреси для завантаження

> [!WARNING]
> Можливість генерації тимчасових URL-адрес для завантаження підтримується тільки драйвером `s3`.

Якщо вам потрібно створити тимчасову URL-адресу, яку можна використовувати для завантаження файлу безпосередньо з вашого клієнтського додатка на стороні клієнта, ви можете використовувати метод `temporaryUploadUrl`. Цей метод приймає шлях і екземпляр `DateTime`, що вказує, коли URL повинен закінчитися. Метод `temporaryUploadUrl` повертає асоціативний масив, який можна деструктурувати на URL-адресу для завантаження і заголовки, які повинні включатися в запит на завантаження:

    use Illuminate\Support\Facades\Storage;
    
    ['url' => $url, 'headers' => $headers] = Storage::temporaryUploadUrl(
        'file.jpg', now()->addMinutes(5)
    );

Цей метод здебільшого корисний у серверних середовищах, де клієнтський застосунок має безпосередньо завантажувати файли в систему хмарного зберігання, таку як Amazon S3.

<a name="file-metadata"></a>
### Метадані файлу

Крім читання і запису файлів, Laravel також може надавати інформацію про самі файли. Наприклад, метод `size` використовується для отримання розміру файлу в байтах:

    use Illuminate\Support\Facades\Storage;

    $size = Storage::size('file.jpg');

Метод `lastModified` повертає тимчасову мітку UNIX останньої зміни файлу:

    $time = Storage::lastModified('file.jpg');

MIME-тип файлу можна отримати за допомогою методу `mimeType`:

    $mime = Storage::mimeType('file.jpg');

<a name="file-paths"></a>
#### Шляхи до файлів

Ви можете використовувати метод `path`, щоб отримати шлях до вказаного файлу. Якщо ви використовуєте драйвер `local`, він поверне абсолютний шлях до файлу. Якщо ви використовуєте драйвер `s3`, цей метод поверне відносний шлях до файлу в кошику `S3`:

    use Illuminate\Support\Facades\Storage;

    $path = Storage::path('file.jpg');

<a name="storing-files"></a>
## Зберігання файлів

Метод `put` використовується для збереження вмісту файлу на диску. Ви також можете передати `resource` PHP методу `put`, який буде використовувати підтримку базового потоку Flysystem. Пам'ятайте, що всі шляхи до файлів повинні бути вказані щодо «кореневого» розташування, налаштованого для диска:

    use Illuminate\Support\Facades\Storage;

    Storage::put('file.jpg', $contents);

    Storage::put('file.jpg', $resource);

<a name="failed-writes"></a>
#### Обробка помилок запису

Якщо метод `put` (або інші операції «запису») не може записати файл на диск, він поверне `false`:

    if (!Storage::put('file.jpg', $contents)) {
        // Файл не вдалося записати на диск...
    }

За вашим бажанням, ви можете визначити опцію `throw` у конфігураційному масиві диска вашої файлової системи. Коли ця опція встановлена як `true`, методи «запису», такі як `put`, будуть викидати екземпляр `League\Flysystem\UnableToWriteFile`, коли операції запису завершуються невдачею:

    'public' => [
        'driver' => 'local',
        // ...
        'throw' => true,
    ],


<a name="prepending-appending-to-files"></a>
### Додавання інформації до файлів

Методи `prepend` і `append` дають змогу записувати в початок або кінець файлу, відповідно:

    Storage::prepend('file.log', 'Prepended Text');

    Storage::append('file.log', 'Appended Text');

<a name="copying-moving-files"></a>
### Копіювання та переміщення файлів

Метод `copy` використовується для копіювання наявного файлу в нове місце на диску, а метод `move` використовується для перейменування або переміщення наявного файлу в нове місце:

    Storage::copy('old/file.jpg', 'new/file.jpg');

    Storage::move('old/file.jpg', 'new/file.jpg');


<a name="automatic-streaming"></a>
### Автоматичне потокове передавання

Потокова передача файлів у сховище дозволяє значно скоротити використання пам'яті. Якщо ви хочете, щоб Laravel автоматично керував потоковою передачею переданого файлу до вашого сховища, ви можете використовувати методи `putFile` або `putFileAs`. Ці методи приймають екземпляр `Illuminate\Http\File` або `Illuminate\Http\UploadedFile` і автоматично передають файл у потрібне місце:

    use Illuminate\Http\File;
    use Illuminate\Support\Facades\Storage;

    // Автоматично генерувати унікальний ідентифікатор для імені файлу ...
    $path = Storage::putFile('photos', new File('/path/to/photo'));

    // Явно вказати ім'я файлу ...
    $path = Storage::putFileAs('photos', new File('/path/to/photo'), 'photo.jpg');

Слід зазначити кілька важливих моментів, що стосуються методу `putFile`. Зверніть увагу, що ми вказали тільки ім'я каталогу, а не ім'я файлу. За замовчуванням метод `putFile` генерує унікальний ідентифікатор, який слугуватиме ім'ям файлу. Розширення файлу буде визначено шляхом перевірки MIME-типу файлу. Шлях до файлу буде повернуто методом `putFile`, тож ви можете зберегти шлях, включаючи згенероване ім'я файлу, у вашій базі даних.

Методи `putFile` і `putFileAs` також приймають аргумент для визначення «видимості» збереженого файлу. Це особливо корисно, якщо ви зберігаєте файл на хмарному диску, такому як Amazon S3, і хочете, щоб файл був загальнодоступним через згенеровані URL:

    Storage::putFile('photos', new File('/path/to/photo'), 'public');


<a name="file-uploads"></a>
### Завантаження файлів

У веб-додатках одним із найпоширеніших варіантів зберігання файлів є зберігання завантажених користувачем файлів, таких як фотографії та документи. Laravel спрощує зберігання завантажених файлів за допомогою методу `store` екземпляра завантажуваного файлу. Викличте метод `store`, вказавши шлях, за яким ви хочете зберегти завантажений файл:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    class UserAvatarController extends Controller
    {
        /**
         * Оновити аватар користувача.
         */
        public function update(Request $request): string
        {
            $path = $request->file('avatar')->store('avatars');

            return $path;
        }
    }

У цьому прикладі слід зазначити кілька важливих моментів. Зверніть увагу, що ми вказали тільки ім'я каталогу, а не ім'я файлу. За замовчуванням метод `store` генерує унікальний ідентифікатор, який слугуватиме ім'ям файлу. Розширення файлу буде визначено шляхом перевірки MIME-типу файлу. Шлях до файлу буде повернуто методом `store`, тому ви можете зберегти шлях, включно зі згенерованим ім'ям файлу, у своїй базі даних.

Ви також можете викликати метод `putFile` фасаду `Storage`, щоб виконати ту саму операцію збереження файлів, що і в прикладі вище:

    $path = Storage::putFile('avatars', $request->file('avatar'));

<a name="specifying-a-file-name"></a>
#### Вказівка імені файлу

Якщо ви не хочете, щоб ім'я файлу автоматично присвоювалося вашому збереженому файлу, ви можете використовувати метод `storeAs`, який отримує шлях, ім'я файлу та (необов'язковий) диск як аргументи:

    $path = $request->file('avatar')->storeAs(
        'avatars', $request->user()->id
    );

Ви також можете використовувати метод `putFileAs` фасаду `Storage`, який виконуватиме ту саму операцію збереження файлів, що й у прикладі вище:

    $path = Storage::putFileAs(
        'avatars', $request->file('avatar'), $request->user()->id
    );

> [!WARNING]  
> Недруковані та неприпустимі символи Unicode будуть автоматично видалені з шляхів до файлів. З цієї причини, ви _за бажанням_ можете очистити шляхи до файлів перед їх передачею в методи зберігання файлів Laravel. Шляхи до файлів нормалізуються за допомогою методу `League\Flysystem\Util::normalizePath`.

<a name="specifying-a-disk"></a>
#### Вказівка диска

За замовчуванням метод `store` завантажуваного файлу буде використовувати ваш диск за замовчуванням. Якщо ви хочете вказати інший диск, передайте ім'я диска як другий аргумент методу `store`:

    $path = $request->file('avatar')->store(
        'avatars/'.$request->user()->id, 's3'
    );

Якщо ви використовуєте метод `storeAs`, ви можете передати ім'я диска як третій аргумент методу:

    $path = $request->file('avatar')->storeAs(
        'avatars',
        $request->user()->id,
        's3'
    );

<a name="other-uploaded-file-information"></a>
#### Інша інформація про завантажуваний файл

Якщо ви хочете отримати оригінальне ім'я або розширення завантажуваного файлу, ви можете зробити це за допомогою методів `getClientOriginalName` і `getClientOriginalExtension`:

    $file = $request->file('avatar');

    $name = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();

Однак майте на увазі, що методи `getClientOriginalName` і `getClientOriginalExtension` вважаються небезпечними, оскільки ім'я і розширення файлу можуть бути змінені зловмисником. З цієї причини ви зазвичай повинні віддати перевагу методам `hashName` і `extension`, щоб отримати ім'я і розширення для завантажуваного файлу:

    $file = $request->file('avatar');

    $name = $file->hashName(); // Generate a unique, random name...
    $extension = $file->extension(); // Determine the file's extension based on the file's MIME type...

<a name="file-visibility"></a>
### Видимість файлу

В інтеграції Laravel Flysystem «видимість» - це абстракція прав доступу до файлів на декількох платформах. Файли можуть бути оголошені `public` або `private`. Коли файл оголошується `public`, ви вказуєте, що файл зазвичай повинен бути доступний для інших. Наприклад, при використанні драйвера`s3` ви можете отримати URL-адреси для `public` файлів.

Ви можете задати видимість під час запису файлу за допомогою методу `put`:

    use Illuminate\Support\Facades\Storage;

    Storage::put('file.jpg', $contents, 'public');

Якщо файл уже було збережено, його видимість може бути отримано і задано за допомогою методів `getVisibility` і `setVisibility`, відповідно:

    $visibility = Storage::getVisibility('file.jpg');

    Storage::setVisibility('file.jpg', 'public');

Під час взаємодії із завантажуваними файлами, ви можете використовувати методи `storePublicly` і `storePubliclyAs` для збереження завантажуваного файлу з видимістю `public`:

    $path = $request->file('avatar')->storePublicly('avatars', 's3');

    $path = $request->file('avatar')->storePubliclyAs(
        'avatars',
        $request->user()->id,
        's3'
    );

<a name="local-files-and-visibility"></a>
#### Локальні файли та видимість

При використанні драйвера `local`, [видимість](#file-visibility) `public` інтерпретується в право доступу `0755` для каталогів і право доступу `0644` для файлів. Ви можете змінити зіставлення прав доступу у файлі конфігурації `filesystems` вашого додатка:

    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'permissions' => [
            'file' => [
                'public' => 0644,
                'private' => 0600,
            ],
            'dir' => [
                'public' => 0755,
                'private' => 0700,
            ],
        ],
        'throw' => false,
    ],

<a name="deleting-files"></a>
## Видалення файлів

Метод `delete` приймає ім'я одного файлу або масив імен файлів для видалення:

    use Illuminate\Support\Facades\Storage;

    Storage::delete('file.jpg');

    Storage::delete(['file.jpg', 'file2.jpg']);

За необхідності ви можете вказати диск, з якого слід видалити файл:

    use Illuminate\Support\Facades\Storage;

    Storage::disk('s3')->delete('path/file.jpg');

<a name="directories"></a>
## Каталоги

<a name="get-all-files-within-a-directory"></a>
#### Отримання всіх файлів каталогу

Метод `files` повертає масив усіх файлів зазначеного каталогу. Якщо ви хочете отримати список усіх файлів каталогу, включно з усіма підкаталогами, ви можете використовувати метод `allFiles`:

    use Illuminate\Support\Facades\Storage;

    $files = Storage::files($directory);

    $files = Storage::allFiles($directory);

<a name="get-all-directories-within-a-directory"></a>
#### Отримання всіх каталогів із каталогу

Метод `directories` повертає масив усіх каталогів зазначеного каталогу. Крім того, ви можете використовувати метод `allDirectories`, щоб отримати список усіх каталогів усередині вказаного каталогу і всіх його підкаталогів:

    $directories = Storage::directories($directory);

    $directories = Storage::allDirectories($directory);

<a name="create-a-directory"></a>
#### Створення каталогу

Метод `makeDirectory` створить вказаний каталог, включно з усіма необхідними підкаталогами:

    Storage::makeDirectory($directory);

<a name="delete-a-directory"></a>
#### Видалення каталогу

Нарешті, для видалення каталогу і всіх його файлів можна використовувати метод `deleteDirectory`:

    Storage::deleteDirectory($directory);

<a name="testing"></a>
## Тестування

Метод `fake` фасаду `Storage` дозволяє вам легко створювати фейковий диск, який, у поєднанні з утилітами генерації файлів класу `Illuminate\Http\UploadedFile`, значно спрощує тестування завантаження файлів. Наприклад:

```php tab=Pest
<?php
    
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
    
test('albums can be uploaded', function () {
    Storage::fake('photos');

    $response = $this->json('POST', '/photos', [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg')
    ]);

    // Assert one or more files were stored...
    Storage::disk('photos')->assertExists('photo1.jpg');
    Storage::disk('photos')->assertExists(['photo1.jpg', 'photo2.jpg']);

    // Assert one or more files were not stored...
    Storage::disk('photos')->assertMissing('missing.jpg');
    Storage::disk('photos')->assertMissing(['missing.jpg', 'non-existing.jpg']);
    
    // Assert that a given directory is empty...
    Storage::disk('photos')->assertDirectoryEmpty('/wallpapers');
});
```

```php tab=PHPUnit
<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_albums_can_be_uploaded(): void
    {
        Storage::fake('photos');
    
        $response = $this->json('POST', '/photos', [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.jpg')
        ]);
    
        // Перевірка, що один або кілька файлів були збережені...
        Storage::disk('photos')->assertExists('photo1.jpg');
        Storage::disk('photos')->assertExists(['photo1.jpg', 'photo2.jpg']);

        // Перевірка, що один або кілька файлів не були збережені...
        Storage::disk('photos')->assertMissing('missing.jpg');
        Storage::disk('photos')->assertMissing(['missing.jpg', 'non-existing.jpg']);

        // Перевірка, що вказана директорія порожня...
        Storage::disk('photos')->assertDirectoryEmpty('/wallpapers');
    }
}
```

За замовчуванням метод `fake` видалятиме всі файли у своїй тимчасовій директорії. Якщо ви хочете зберегти ці файли, ви можете замість цього використовувати метод «persistentFake». Для отримання додаткової інформації про тестування завантаження файлів ви можете проконсультуватися з [документацією з тестування HTTP, що стосується завантаження файлів](/docs/{{version}}/http-tests#testing-file-uploads).

> [!WARNING]
> Метод `image` вимагає наявності [розширення GD](https://www.php.net/manual/en/book.image.php).

<a name="custom-filesystems"></a>
## Користувацькі файлові системи

Інтеграція Laravel з Flysystem забезпечує підтримку декількох «драйверів» з коробки; однак, Flysystem цим не обмежується і має адаптери для багатьох інших систем зберігання. Ви можете створити власний драйвер, якщо хочете використовувати один із цих додаткових адаптерів у своєму додатку Laravel.

Щоб визначити власну файлову систему, вам знадобиться адаптер Flysystem. Давайте додамо в наш проєкт адаптер Dropbox, підтримуваний спільнотою:

```shell
composer require spatie/flysystem-dropbox
```

Потім ви можете зареєструвати драйвер у методі `boot` одного з [постачальників служб](/docs/{{version}}{{version}}/providers) вашого додатка. Для цього ви повинні використовувати метод `extend` фасаду `Storage`:

    <?php

    namespace App\Providers;

    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Filesystem\FilesystemAdapter;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\ServiceProvider;
    use League\Flysystem\Filesystem;
    use Spatie\Dropbox\Client as DropboxClient;
    use Spatie\FlysystemDropbox\DropboxAdapter;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Реєстрація будь-яких служб додатка.
         */
        public function register(): void
        {
            // ...
        }

        /**
         * Завантаження будь-яких служб програми.
         */
        public function boot(): void
        {
            Storage::extend('dropbox', function (Application $app, array $config) {
                $adapter = new DropboxAdapter(new DropboxClient(
                    $config['authorization_token']
                ));
     
                return new FilesystemAdapter(
                    new Filesystem($adapter, $config),
                    $adapter,
                    $config
                );
            });
        }
    }

Перший аргумент методу `extend` - це ім'я драйвера, а другий - замикання, яке отримує змінні `$app` і `$config`. Замикання має повертати екземпляр `Illuminate\Filesystem\FilesystemAdapter`. Змінна `$config` містить значення, визначені в `config/filesystems.php` для зазначеного диска.

Після того як ви створили і зареєстрували розширення постачальника служби, ви можете використовувати драйвер `dropbox` у вашому файлі конфігурації `config/filesystems.php`.
