# Контролери

- [Вступ](#introduction)
- [Написання контролерів](#writing-controllers)
    - [Базові контролери](#basic-controllers)
    - [Контролери одинарної дії](#single-action-controllers)
- [Проміжне програмне забезпечення контролера](#controller-middleware)
- [Контролери ресурсів](#resource-controllers)
    - [Часткові маршрути ресурсів](#restful-partial-resource-routes)
    - [Вкладені ресурси](#restful-nested-resources)
    - [Іменування маршрутів ресурсів](#restful-naming-resource-routes)
    - [Іменування параметрів маршруту ресурсу](#restful-naming-resource-route-parameters)
    - [Масштабування маршрутів ресурсів](#restful-scoping-resource-routes)
    - [Локалізація URI ресурсів](#restful-localizing-resource-uris)
    - [Доповнення контролерів ресурсів](#restful-supplementing-resource-controllers)
    - [Контролери одиночних ресурсів](#singleton-resource-controllers)
- [Ін'єкції залежності та контролери](#dependency-injection-and-controllers)

<a name="introduction"></a>
## Вступ

Замість того, щоб визначати всю логіку обробки запитів як закриття у файлах маршрутів, ви можете організувати цю поведінку за допомогою класів «контролерів». Контролери можуть групувати пов'язану логіку обробки запитів в одному класі. Наприклад, клас `UserController` може обробляти всі вхідні запити, пов'язані з користувачами, зокрема показувати, створювати, оновлювати та видаляти користувачів. За замовчуванням контролери зберігаються у каталозі `app/Http/Controllers`.

<a name="writing-controllers"></a>
## Написання контролерів

<a name="basic-controllers"></a>
### Базові контролери

Щоб швидко згенерувати новий контролер, ви можете виконати команду `make:controller` Artisan. За замовчуванням усі контролери для вашої програми зберігаються у каталозі `app/Http/Controllers`:

```shell
php artisan make:controller UserController
```

Давайте розглянемо приклад базового контролера. Контролер може мати будь-яку кількість загальнодоступних методів, які будуть відповідати на вхідні HTTP-запити:

    <?php

    namespace App\Http\Controllers;
    
    use App\Models\User;
    use Illuminate\View\View;

    class UserController extends Controller
    {
        /**
         * Показати профіль для даного користувача.
         */
        public function show(string $id): View
        {
            return view('user.profile', [
                'user' => User::findOrFail($id)
            ]);
        }
    }

Після того, як ви написали клас і метод контролера, ви можете визначити маршрут до методу контролера таким чином:

    use App\Http\Controllers\UserController;

    Route::get('/user/{id}', [UserController::class, 'show']);

Коли вхідний запит відповідає вказаному URI маршруту, буде викликано метод `show` класу `App\Http\Controllers\UserController`, якому будуть передані параметри маршруту.

> [!NOTE]  
> Контролери не є **обов'язковими** для розширення базового класу. Однак іноді буває зручно розширити базовий клас контролера, який містить методи, що мають бути спільними для всіх ваших контролерів.

<a name="single-action-controllers"></a>
### Контролери одинарної дії

Якщо дія контролера є особливо складною, вам може бути зручно присвятити цілий клас контролерів для цієї єдиної дії. Для цього ви можете визначити єдиний метод `__invoke` у контролері:

    <?php

    namespace App\Http\Controllers;

    class ProvisionServer extends Controller
    {
        /**
         * Надання нового веб-сервера.
         */
        public function __invoke()
        {
            // ...
        }
    }

При реєстрації маршрутів для контролерів одиночної дії вам не потрібно вказувати метод контролера. Замість цього ви можете просто передати маршрутизатору назву контролера:

    use App\Http\Controllers\ProvisionServer;

    Route::post('/server', ProvisionServer::class);

Ви можете згенерувати викликаємий контролер за допомогою опції `--invokable` команди `make:controller` Artisan:

```shell
php artisan make:controller ProvisionServer --invokable
```

> [!NOTE]  
> Заглушки контролерів можна налаштувати за допомогою [публікація заглушок](/docs/{{version}}/artisan#stub-customization).

<a name="controller-middleware"></a>
## Проміжне програмне забезпечення контролера

[Проміжне програмне забезпечення](/docs/{{version}}/middleware) можуть бути призначені маршрутам контролера у ваших файлах маршрутів:

    Route::get('profile', [UserController::class, 'show'])->middleware('auth');

Або ви можете визначити проміжне ПЗ всередині класу вашого контролера. Для цього ваш контролер повинен реалізувати інтерфейс `HasMiddleware`, який передбачає наявність у контролері статичного методу `middleware`. З цього методу ви можете повернути масив проміжного програмного забезпечення, яке має бути застосоване до дій контролера:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Routing\Controllers\HasMiddleware;
    use Illuminate\Routing\Controllers\Middleware;

    class UserController extends Controller implements HasMiddleware
    {
        /**
         * Отримайте проміжне програмне забезпечення, яке має бути призначене контролеру.
         */
        public static function middleware(): array
        {
            return [
                'auth',
                new Middleware('log', only: ['index']),
                new Middleware('subscribed', except: ['store']),
            ];
        }

        // ...
    }

Ви також можете визначити проміжне програмне забезпечення контролерів як закриття, що забезпечує зручний спосіб визначення вбудованого проміжного програмного забезпечення без написання цілого класу проміжного програмного забезпечення:

    use Closure;
    use Illuminate\Http\Request;

    /**
     * Отримайте проміжне програмне забезпечення, яке має бути призначене контролеру.
     */
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                return $next($request);
            },
        ];
    }

<a name="resource-controllers"></a>
## Контролери ресурсів

Якщо ви думаєте про кожну модель Eloquent у вашому додатку як про «ресурс», то типово виконувати однакові набори дій над кожним ресурсом у вашому додатку. Наприклад, уявіть, що ваш додаток містить модель `Фото` та модель `Фільм`. Цілком ймовірно, що користувачі можуть створювати, читати, оновлювати або видаляти ці ресурси.

Через цей поширений випадок використання, маршрутизація ресурсів Laravel призначає типові маршрути створення, читання, оновлення та видалення («CRUD») для контролера за допомогою одного рядка коду. Для початку ми можемо використати опцію `--resource` команди `make:controller` Artisan, щоб швидко створити контролер для виконання цих дій:

```shell
php artisan make:controller PhotoController --resource
```

Ця команда створить контролер за адресою `app/Http/Controllers/PhotoController.php`. Контролер міститиме метод для кожної з доступних операцій над ресурсом. Далі ви можете зареєструвати маршрут ресурсу, який вказуватиме на контролер:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class);

Це єдине оголошення маршруту створює декілька маршрутів для обробки різноманітних дій над ресурсом. Згенерований контролер вже матиме методи для кожної з цих дій. Пам'ятайте, що ви завжди можете отримати швидкий огляд маршрутів вашої програми, виконавши команду `route:list` Artisan.

Ви навіть можете зареєструвати декілька контролерів ресурсів одночасно, передавши масив у метод `resources`:

    Route::resources([
        'photos' => PhotoController::class,
        'posts' => PostController::class,
    ]);

<a name="actions-handled-by-resource-controllers"></a>
#### Дії, що виконуються контролерами ресурсів

Verb      | URI                    | Action       | Route Name
----------|------------------------|--------------|---------------------
GET       | `/photos`              | index        | photos.index
GET       | `/photos/create`       | create       | photos.create
POST      | `/photos`              | store        | photos.store
GET       | `/photos/{photo}`      | show         | photos.show
GET       | `/photos/{photo}/edit` | edit         | photos.edit
PUT/PATCH | `/photos/{photo}`      | update       | photos.update
DELETE    | `/photos/{photo}`      | destroy      | photos.destroy

<a name="customizing-missing-model-behavior"></a>
#### Налаштування поведінки відсутньої моделі

Зазвичай HTTP-відповідь 404 генерується, якщо неявно зв'язану модель ресурсу не знайдено. Однак ви можете налаштувати цю поведінку, викликавши метод `missing` під час визначення маршруту до ресурсу. Метод `missing` приймає закриття, яке буде викликано, якщо неявно зв'язану модель не буде знайдено для жодного з маршрутів ресурсу:

    use App\Http\Controllers\PhotoController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Redirect;

    Route::resource('photos', PhotoController::class)
            ->missing(function (Request $request) {
                return Redirect::route('photos.index');
            });

<a name="soft-deleted-models"></a>
#### Моделі, що видаляються м'яко

Зазвичай, неявне зв'язування моделей не дозволяє отримати моделі, які були [м'яко видалено](/docs/{{version}}/eloquent#soft-deleting), і замість цього поверне HTTP-відповідь 404. Втім, ви можете наказати фреймворку дозволити м'яке видалення моделей, викликавши метод `withTrashed` під час визначення маршруту до ресурсу:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->withTrashed();

Виклик `withTrashed` без аргументів уможливить м'яке видалення моделей для маршрутів ресурсів `show`, `edit` та `update`. Ви можете вказати підмножину цих маршрутів, передавши методу `withTrashed` масив:

    Route::resource('photos', PhotoController::class)->withTrashed(['show']);

<a name="specifying-the-resource-model"></a>
#### Визначення моделі ресурсів

Якщо ви використовуєте [прив'язка моделі маршруту](/docs/{{version}}/routing#route-model-binding) і хочете, щоб методи контролера ресурсу підказували тип екземпляра моделі, ви можете використати опцію `--model` під час генерації контролера:

```shell
php artisan make:controller PhotoController --model=Photo --resource
```

<a name="generating-form-requests"></a>
#### Створення запитів на форми

Ви можете вказати опцію `--requests` при створенні контролера ресурсів, щоб доручити Artisan згенерувати [класи запитів до форми](/docs/{{version}}/validation#form-request-validation) для методів зберігання та оновлення даних контролера:

```shell
php artisan make:controller PhotoController --model=Photo --resource --requests
```

<a name="restful-partial-resource-routes"></a>
### Часткові маршрути ресурсів

При оголошенні маршруту ресурсу ви можете вказати підмножину дій, які повинен обробляти контролер, замість повного набору дій за замовчуванням:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->only([
        'index', 'show'
    ]);

    Route::resource('photos', PhotoController::class)->except([
        'create', 'store', 'update', 'destroy'
    ]);

<a name="api-resource-routes"></a>
#### Маршрути ресурсів API

При оголошенні маршрутів ресурсів, які будуть споживатися API, ви зазвичай хочете виключити маршрути, які представляють HTML-шаблони, такі як `create` і `edit`. Для зручності ви можете використовувати метод `apiResource` для автоматичного виключення цих двох маршрутів:

    use App\Http\Controllers\PhotoController;

    Route::apiResource('photos', PhotoController::class);

Ви можете зареєструвати багато контролерів ресурсів API одночасно, передавши масив у метод `apiResources`:

    use App\Http\Controllers\PhotoController;
    use App\Http\Controllers\PostController;

    Route::apiResources([
        'photos' => PhotoController::class,
        'posts' => PostController::class,
    ]);

Щоб швидко згенерувати контролер ресурсів API, який не містить методів `create` або `edit`, використовуйте ключ `--api` при виконанні команди `make:controller`:

```shell
php artisan make:controller PhotoController --api
```

<a name="restful-nested-resources"></a>
### Вкладені ресурси

Іноді вам може знадобитися визначити маршрути до вкладеного ресурсу. Наприклад, фоторесурс може мати декілька коментарів, які можуть бути прикріплені до фотографії. Щоб вкласти контролери ресурсів, ви можете використовувати «крапкові» позначення у вашій декларації маршруту:

    use App\Http\Controllers\PhotoCommentController;

    Route::resource('photos.comments', PhotoCommentController::class);

Цей маршрут зареєструє вкладений ресурс, до якого можна отримати доступ за допомогою URI, подібних до наведених нижче:

    /photos/{photo}/comments/{comment}

<a name="scoping-nested-resources"></a>
#### Визначення обсягу вкладених ресурсів

Ларавель [неявне зв'язування моделей](/docs/{{version}}/routing#implicit-model-binding-scoping) може автоматично масштабувати вкладені зв'язки таким чином, що розв'язана дочірня модель буде підтверджена як така, що належить до батьківської моделі. Використовуючи метод `scoped` при визначенні вкладеного ресурсу, ви можете увімкнути автоматичне визначення області видимості, а також вказати Laravel, за яким полем слід шукати дочірній ресурс. Для отримання додаткової інформації про те, як це зробити, зверніться до документації по [визначення маршрутів руху ресурсів](#restful-scoping-resource-routes).

<a name="shallow-nesting"></a>
#### Неглибоке гніздування

Часто не обов'язково мати як батьківський, так і дочірній ідентифікатори в межах URI, оскільки дочірній ідентифікатор вже є унікальним ідентифікатором. При використанні унікальних ідентифікаторів, таких як автоінкрементовані первинні ключі для ідентифікації ваших моделей в сегментах URI, ви можете використовувати «неглибоку вкладеність»:

    use App\Http\Controllers\CommentController;

    Route::resource('photos.comments', CommentController::class)->shallow();

Це визначення маршруту визначить наступні маршрути:

Verb      | URI                               | Action       | Route Name
----------|-----------------------------------|--------------|---------------------
GET       | `/photos/{photo}/comments`        | index        | photos.comments.index
GET       | `/photos/{photo}/comments/create` | create       | photos.comments.create
POST      | `/photos/{photo}/comments`        | store        | photos.comments.store
GET       | `/comments/{comment}`             | show         | comments.show
GET       | `/comments/{comment}/edit`        | edit         | comments.edit
PUT/PATCH | `/comments/{comment}`             | update       | comments.update
DELETE    | `/comments/{comment}`             | destroy      | comments.destroy

<a name="restful-naming-resource-routes"></a>
### Іменування маршрутів ресурсів

За замовчуванням всі дії контролера ресурсів мають назву маршруту, але ви можете перевизначити ці назви, передавши масив `names` з потрібними вам назвами маршрутів:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->names([
        'create' => 'photos.build'
    ]);

<a name="restful-naming-resource-route-parameters"></a>
### Іменування параметрів маршруту ресурсу

За замовчуванням `Route::resource` створюватиме параметри маршруту для ваших маршрутів до ресурсів на основі «сингуляризованої» версії назви ресурсу. Ви можете легко замінити цей параметр для кожного ресурсу за допомогою методу `parameters`. Масив, що передається у метод `parameters`, має бути асоціативним масивом назв ресурсів та назв параметрів:

    use App\Http\Controllers\AdminUserController;

    Route::resource('users', AdminUserController::class)->parameters([
        'users' => 'admin_user'
    ]);

У наведеному вище прикладі генерується наступний URI для маршруту `show` ресурсу:

    /users/{admin_user}

<a name="restful-scoping-resource-routes"></a>
### Масштабування маршрутів ресурсів

Ларавель [масштабоване неявне зв'язування моделей](/docs/{{version}}/routing#implicit-model-binding-scoping) може автоматично масштабувати вкладені зв'язки таким чином, що вирішена дочірня модель буде підтверджена як така, що належить до батьківської моделі. Використовуючи метод `scoped` при визначенні вкладеного ресурсу, ви можете увімкнути автоматичне масштабування, а також вказати Laravel, за яким полем має бути отриманий дочірній ресурс:

    use App\Http\Controllers\PhotoCommentController;

    Route::resource('photos.comments', PhotoCommentController::class)->scoped([
        'comment' => 'slug',
    ]);

Цей маршрут зареєструє вкладений ресурс, до якого можна отримати доступ за допомогою URI, подібних до наведених нижче:

    /photos/{photo}/comments/{comment:slug}

При використанні неявного зв'язування за ключем як параметра вкладеного маршруту, Laravel автоматично обмежить область видимості запиту, щоб отримати вкладену модель за її батьком, використовуючи конвенції, щоб вгадати ім'я відношення у батька. У цьому випадку буде припущено, що модель `Photo` має зв'язок з ім'ям `comments` (множина імені параметра маршруту), який можна використати для отримання моделі `Comment`.

<a name="restful-localizing-resource-uris"></a>
### Локалізація URI ресурсів

За замовчуванням `Route::resource` створює URI ресурсу, використовуючи англійські дієслова і правила множини. Якщо вам потрібно локалізувати дієслова `create` і `edit`, ви можете скористатися методом `Route::resourceVerbs`. Це можна зробити на початку методу `boot` у розділі `App\Providers\AppServiceProvider` вашого додатку:

    /**
     * Завантажуйте будь-які сервіси додатків.
     */
    public function boot(): void
    {
        Route::resourceVerbs([
            'create' => 'crear',
            'edit' => 'editar',
        ]);
    }

Плюралізатор Laravel підтримує [кілька різних мов, які ви можете налаштувати відповідно до ваших потреб](/docs/{{version}}/localization#pluralization-language). Після налаштування дієслів і мови множини, реєстрація маршруту ресурсу, наприклад, `Route::resource('publicacion', PublicacionController::class)` створить такі URI:

    /publicacion/crear

    /publicacion/{publicaciones}/editar

<a name="restful-supplementing-resource-controllers"></a>
### Доповнення контролерів ресурсів

Якщо вам потрібно додати додаткові маршрути до контролера ресурсів, окрім стандартного набору маршрутів ресурсів, вам слід визначити ці маршрути до виклику методу `Route::resource`; інакше маршрути, визначені методом `resource`, можуть ненавмисно мати пріоритет над вашими додатковими маршрутами:

    use App\Http\Controller\PhotoController;

    Route::get('/photos/popular', [PhotoController::class, 'popular']);
    Route::resource('photos', PhotoController::class);

> [!NOTE]  
> Не забувайте тримати контролери сфокусованими. Якщо вам регулярно потрібні методи, що виходять за межі типового набору дій з ресурсами, подумайте про те, щоб розділити контролер на два менших контролери.

<a name="singleton-resource-controllers"></a>
### Контролери одиночних ресурсів

Іноді у вашому додатку є ресурси, які можуть існувати лише в одному екземплярі. Наприклад, «профіль» користувача можна редагувати або оновлювати, але користувач не може мати більше одного «профілю». Аналогічно, зображення може мати лише одну «мініатюру». Такі ресурси називаються «синглтонами», що означає, що може існувати один і тільки один екземпляр ресурсу. У цих сценаріях ви можете зареєструвати контролер «одиночного» ресурсу:

```php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::singleton('profile', ProfileController::class);
```

У наведеному вище визначенні одиночного ресурсу буде зареєстровано такі маршрути. Як ви можете бачити, маршрути «створення» не реєструються для одиночних ресурсів, а зареєстровані маршрути не приймають ідентифікатор, оскільки може існувати лише один екземпляр ресурсу:

Verb      | URI                               | Action       | Route Name
----------|-----------------------------------|--------------|---------------------
GET       | `/profile`                        | show         | profile.show
GET       | `/profile/edit`                   | edit         | profile.edit
PUT/PATCH | `/profile`                        | update       | profile.update

Одиничні ресурси також можуть бути вкладені в стандартний ресурс:

```php
Route::singleton('photos.thumbnail', ThumbnailController::class);
```

У цьому прикладі ресурс `photos` отримає всі [стандартні маршрути ресурсів](#actions-handled-by-resource-controller); однак, ресурс `thumbnail` буде одиночним ресурсом з наступними маршрутами:

| Verb      | URI                              | Action  | Route Name               |
|-----------|----------------------------------|---------|--------------------------|
| GET       | `/photos/{photo}/thumbnail`      | show    | photos.thumbnail.show    |
| GET       | `/photos/{photo}/thumbnail/edit` | edit    | photos.thumbnail.edit    |
| PUT/PATCH | `/photos/{photo}/thumbnail`      | update  | photos.thumbnail.update  |

<a name="creatable-singleton-resources"></a>
#### Створювані синглтонні ресурси

Іноді вам може знадобитися визначити маршрути створення та зберігання для одиночного ресурсу. Для цього ви можете викликати метод `creatable` під час реєстрації маршруту синглтонного ресурсу:

```php
Route::singleton('photos.thumbnail', ThumbnailController::class)->creatable();
```

У цьому прикладі буде зареєстровано такі маршрути. Як ви можете бачити, маршрут `DELETE` також буде зареєстровано для створюваних одиночних ресурсів:

| Verb      | URI                                | Action  | Route Name               |
|-----------|------------------------------------|---------|--------------------------|
| GET       | `/photos/{photo}/thumbnail/create` | create  | photos.thumbnail.create  |
| POST      | `/photos/{photo}/thumbnail`        | store   | photos.thumbnail.store   |
| GET       | `/photos/{photo}/thumbnail`        | show    | photos.thumbnail.show    |
| GET       | `/photos/{photo}/thumbnail/edit`   | edit    | photos.thumbnail.edit    |
| PUT/PATCH | `/photos/{photo}/thumbnail`        | update  | photos.thumbnail.update  |
| DELETE    | `/photos/{photo}/thumbnail`        | destroy | photos.thumbnail.destroy |

Якщо ви хочете, щоб Laravel реєстрував маршрут `DELETE` для одиночного ресурсу, але не реєстрував маршрути створення або зберігання, ви можете використати метод `destroyable`:

```php
Route::singleton(...)->destroyable();
```

<a name="api-singleton-resources"></a>
#### API Singleton Resources

Метод `apiSingleton` може бути використаний для реєстрації синглетного ресурсу, який буде маніпулюватися через API, таким чином роблячи маршрути `create` і `edit` непотрібними:

```php
Route::apiSingleton('profile', ProfileController::class);
```

Звичайно, синглетні ресурси API також можуть бути «створюваними», що дозволить реєструвати маршрути «збереження» та «знищення» для ресурсу:

```php
Route::apiSingleton('photos.thumbnail', ProfileController::class)->creatable();
```

<a name="dependency-injection-and-controllers"></a>
## Ін'єкції залежності та контролери

<a name="constructor-injection"></a>
#### Ін'єкція конструктора

Ларавель [service container](/docs/{{version}}/container) використовується для перетворення всіх контролерів Laravel. Таким чином, ви можете вказати будь-які залежності, які можуть знадобитися вашому контролеру в його конструкторі, за допомогою підказки типу. Задекларовані залежності будуть автоматично розпізнані та під'єднані до екземпляру контролера:

    <?php

    namespace App\Http\Controllers;

    use App\Repositories\UserRepository;

    class UserController extends Controller
    {
        /**
         * Створіть новий екземпляр контролера.
         */
        public function __construct(
            protected UserRepository $users,
        ) {}
    }

<a name="method-injection"></a>
#### Метод Ін'єкція

На додаток до ін'єкції конструкторів, ви також можете використовувати підказки типів для методів вашого контролера. Поширеним випадком використання ін'єкції методів є ін'єкція екземпляра `Illuminate\Http\Request` у методи вашого контролера:

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;

    class UserController extends Controller
    {
        /**
         * Зберегти нового користувача.
         */
        public function store(Request $request): RedirectResponse
        {
            $name = $request->name;

            // Зберігати користувача...

            return redirect('/users');
        }
    }

Якщо ваш метод контролера також очікує вхідні дані від параметра маршруту, перелічіть аргументи маршруту після інших залежностей. Наприклад, якщо ваш маршрут визначено так:

    use App\Http\Controllers\UserController;

    Route::put('/user/{id}', [UserController::class, 'update']);

Ви все ще можете ввести `Illuminate\Http\Request` і отримати доступ до параметра `id`, визначивши метод вашого контролера наступним чином:

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;

    class UserController extends Controller
    {
        /**
         * Оновити даного користувача.
         */
        public function update(Request $request, string $id): RedirectResponse
        {
            // Оновити користувача...

            return redirect('/users');
        }
    }
