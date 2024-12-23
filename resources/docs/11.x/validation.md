# Validation

- [Вступ](#introduction)
- [Швидкий старт валідації](#validation-quickstart)
    - [Визначення маршрутів](#quick-defining-the-routes)
    - [Створення контролера](#quick-creating-the-controller)
    - [Написання логіки перевірки](#quick-writing-the-validation-logic)
    - [Відображення помилок валідації](#quick-displaying-the-validation-errors)
    - [Форми заселення](#repopulating-forms)
    - [Примітка щодо необов'язкових полів](#a-note-on-optional-fields)
    - [Формат відповіді на помилку валідації](#validation-error-response-format)
- [Перевірка запиту на заповнення форми](#form-request-validation)
    - [Створення запитів за формою](#creating-form-requests)
    - [Запити на авторизацію форми](#authorizing-form-requests)
    - [Налаштування повідомлень про помилки](#customizing-the-error-messages)
    - [Підготовка вхідних даних до валідації](#preparing-input-for-validation)
- [Створення валідаторів вручну](#manually-creating-validators)
    - [Автоматичне перенаправлення](#automatic-redirection)
    - [Іменовані мішки з помилками](#named-error-bags)
    - [Налаштування повідомлень про помилки](#manual-customizing-the-error-messages)
    - [Виконання додаткової перевірки](#performing-additional-validation)
- [Робота з перевіреними даними](#working-with-validated-input)
- [Робота з повідомленнями про помилки](#working-with-error-messages)
    - [Вказівка користувацьких повідомлень у мовних файлах](#specifying-custom-messages-in-language-files)
    - [Вказівка атрибутів у мовних файлах](#specifying-attribute-in-language-files)
    - [Вказівка значень у мовних файлах](#specifying-values-in-language-files)
- [Доступні правила валідації](#available-validation-rules)
- [Умовне додавання правил](#conditionally-adding-rules)
- [Перевірка масивів](#validating-arrays)
    - [Перевірка введення вкладеного масиву](#validating-nested-array-input)
    - [Індекси та позиції повідомлень про помилки](#error-message-indexes-and-positions)
- [Перевірка файлів](#validating-files)
- [Перевірка паролів](#validating-passwords)
- [Користувацькі правила валідації](#custom-validation-rules)
    - [Використання об'єктів правил](#using-rule-objects)
    - [Використання затворів](#using-closures)
    - [Неявні правила](#implicit-rules)

<a name="introduction"></a>
## Вступ

Laravel надає кілька різних підходів для перевірки вхідних даних вашого додатку. Найчастіше використовується метод `validate`, доступний для всіх вхідних HTTP-запитів. Однак, ми обговоримо й інші підходи до валідації.

Laravel включає в себе широкий спектр зручних правил валідації, які ви можете застосовувати до даних, навіть надаючи можливість валідації, якщо значення є унікальними в даній таблиці бази даних. Ми детально розглянемо кожне з цих правил валідації, щоб ви були знайомі з усіма можливостями валідації в Laravel.

<a name="validation-quickstart"></a>
## Швидкий старт валідації

Щоб дізнатися про потужні функції валідації Laravel, давайте розглянемо повний приклад валідації форми і відображення повідомлень про помилки назад користувачеві. Прочитавши цей огляд високого рівня, ви зможете отримати загальне розуміння того, як перевіряти вхідні дані запитів за допомогою Laravel:

<a name="quick-defining-the-routes"></a>
### Визначення маршрутів

Спочатку припустимо, що в нашому файлі `routes/web.php` визначені наступні маршрути:

    use App\Http\Controllers\PostController;

    Route::get('/post/create', [PostController::class, 'create']);
    Route::post('/post', [PostController::class, 'store']);

Маршрут `GET` відобразить форму для користувача для створення нового запису в блозі, в той час як маршрут `POST` збереже новий запис в базі даних.

<a name="quick-creating-the-controller"></a>
### Створення контролера

Далі розглянемо простий контролер, який обробляє вхідні запити до цих маршрутів. Метод `store` поки що залишимо порожнім:

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\View\View;

    class PostController extends Controller
    {
        /**
         * Показати форму для створення нового запису в блозі.
         */
        public function create(): View
        {
            return view('post.create');
        }

        /**
         * Зберегти нову публікацію в блозі.
         */
        public function store(Request $request): RedirectResponse
        {
            // Підтвердити та зберегти публікацію в блозі...

            $post = /** ... */

            return to_route('post.show', ['post' => $post->id]);
        }
    }

<a name="quick-writing-the-validation-logic"></a>
### Написання логіки перевірки

Тепер ми готові заповнити наш метод `store` логікою для перевірки нового запису в блозі. Для цього ми скористаємося методом `validate`, що надається об'єктом `Illuminate\Http\Request`. Якщо правила валідації пройдуть, ваш код продовжить виконуватися нормально; однак, якщо валідація не пройде, буде згенеровано виключення `Illuminate\Validation\ValidationException` і користувачеві буде автоматично надіслано відповідну відповідь про помилку.

Якщо валідація не пройшла під час традиційного HTTP-запиту, буде згенеровано відповідь з перенаправленням на попередню URL-адресу. Якщо вхідний запит є XHR-запитом, буде повернуто [відповідь у форматі JSON, що містить повідомлення про помилки валідації](#validation-error-response-format).

Щоб краще зрозуміти метод `validate`, давайте повернемося до методу `store`:

    /**
     * Зберегти нову публікацію в блозі.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
        ]);

        // Запис у блозі дійсний...

        return redirect('/posts');
    }

Як бачите, правила валідації передаються в метод `validate`. Не хвилюйтеся - всі доступні правила валідації [задокументовані](#available-validation-rules). Знову ж таки, якщо валідація не пройде, відповідна відповідь буде автоматично згенерована. Якщо валідація пройшла успішно, наш контролер продовжить виконання у звичайному режимі.

Крім того, правила валідації можна вказати як масив правил, а не як один рядок, розділений символом `|`:

    $validatedData = $request->validate([
        'title' => ['required', 'unique:posts', 'max:255'],
        'body' => ['required'],
    ]);

Крім того, ви можете використовувати метод `validateWithBag` для валідації запиту і збереження будь-яких повідомлень про помилки у [іменованому мішку помилок](#named-error-bags):

    $validatedData = $request->validateWithBag('post', [
        'title' => ['required', 'unique:posts', 'max:255'],
        'body' => ['required'],
    ]);

<a name="stopping-on-first-validation-failure"></a>
#### Зупинка при першій помилці валідації

Іноді ви можете захотіти зупинити запуск правил валідації для атрибута після першої невдалої валідації. Для цього призначте атрибуту правило `bail`:

    $request->validate([
        'title' => 'bail|required|unique:posts|max:255',
        'body' => 'required',
    ]);

У цьому прикладі, якщо правило `unique` для атрибута `title` не спрацює, правило `max` не буде перевірено. Правила буде перевірено у порядку їх призначення.

<a name="a-note-on-nested-attributes"></a>
#### Примітка про вкладені атрибути

Якщо вхідний HTTP-запит містить «вкладені» поля, ви можете вказати ці поля в правилах валідації, використовуючи «крапковий» синтаксис:

    $request->validate([
        'title' => 'required|unique:posts|max:255',
        'author.name' => 'required',
        'author.description' => 'required',
    ]);

З іншого боку, якщо ім'я поля містить буквальну крапку, ви можете явно запобігти інтерпретації його як «крапкового» синтаксису, замінивши крапку зворотною косою рискою:

    $request->validate([
        'title' => 'required|unique:posts|max:255',
        'v1\.0' => 'required',
    ]);

<a name="quick-displaying-the-validation-errors"></a>
### Відображення помилок валідації

Отже, що робити, якщо поля вхідного запиту не відповідають заданим правилам валідації? Як згадувалося раніше, Laravel автоматично перенаправить користувача на попереднє місце. Крім того, всі помилки валідації та [запитувані дані](/docs/{{version}}/requests#retrieving-old-input) будуть автоматично [прошиті до сесії](/docs/{{version}}/session#flash-data).

Змінну `$errors` надано у спільний доступ усім переглядам вашої програми за допомогою проміжного програмного забезпечення `Illuminate\View\Middleware\ShareErrorsFromSession`, яке надається групою проміжного програмного забезпечення `web`. Коли це проміжне програмне забезпечення застосовано, змінна `$errors` завжди буде доступна у ваших поданнях, що дозволяє вам зручно вважати, що змінна `$errors` завжди визначена і може бути безпечно використана. Змінна `$errors` буде екземпляром об'єкта `Illuminate\Support\MessageBag`. Для отримання додаткової інформації про роботу з цим об'єктом [зверніться до його документації](#working-with-error-messages).

Отже, у нашому прикладі користувач буде перенаправлений на метод `create` нашого контролера, коли валідація не пройде, що дозволить нам відобразити повідомлення про помилки у поданні:

```blade
<!-- /resources/views/post/create.blade.php -->

<h1>Create Post</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Створити форму повідомлення -->
```

<a name="quick-customizing-the-error-messages"></a>
#### Налаштування повідомлень про помилки

Кожне вбудоване правило перевірки Laravel має повідомлення про помилку, яке знаходиться у файлі `lang/en/validation.php` вашого додатку. Якщо у вашому додатку немає каталогу `lang`, ви можете вказати Laravel створити його за допомогою команди `lang:publish` Artisan.

У файлі `lang/en/validation.php` ви знайдете запис перекладу для кожного правила перевірки. Ви можете змінювати або модифікувати ці повідомлення відповідно до потреб вашої програми.

Крім того, ви можете скопіювати цей файл в іншу мовну директорію, щоб перекласти повідомлення для мови вашого додатку. Щоб дізнатися більше про локалізацію Laravel, перегляньте повну [документацію з локалізації](/docs/{{version}}/localization).

> [!WARNING]  
> За замовчуванням, каркас програми Laravel не містить каталогу `lang`. Якщо ви бажаєте налаштувати мовні файли Laravel, ви можете опублікувати їх за допомогою команди `lang:publish` Artisan.

<a name="quick-xhr-requests-and-validation"></a>
#### Запити та валідація XHR

У цьому прикладі ми використовували традиційну форму для надсилання даних до додатку. Однак багато додатків отримують XHR-запити від фронтенду на JavaScript. При використанні методу `validate` під час XHR-запиту, Laravel не буде генерувати відповідь з перенаправленням. Замість цього Laravel генерує [JSON-відповідь, що містить всі помилки валідації](#validation-error-response-format). Ця відповідь у форматі JSON буде надіслана з кодом стану HTTP 422.

<a name="the-at-error-directive"></a>
#### Директива `@error` Директива

Ви можете використовувати директиву `@error` [Blade](/docs/{{version}}/blade) для швидкого визначення наявності повідомлень про помилки валідації для певного атрибута. У директиві `@error` ви можете повторювати змінну `$message` для виведення повідомлення про помилку:

```blade
<!-- /resources/views/post/create.blade.php -->

<label for="title">Post Title</label>

<input id="title"
    type="text"
    name="title"
    class="@error('title') is-invalid @enderror">

@error('title')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror
```

Якщо ви використовуєте [іменовані пакунки помилок](#named-error-bags), ви можете передати назву пакунка помилок як другий аргумент до директиви `@error`:

```blade
<input ... class="@error('title', 'post') is-invalid @enderror">
```

<a name="repopulating-forms"></a>
### Повторне заповнення форм

Коли Laravel генерує відповідь з перенаправленням через помилку валідації, фреймворк автоматично [перезаписує всі дані запиту в сесію](/docs/{{version}}/session#flash-data). Це робиться для того, щоб ви могли зручно отримати доступ до даних під час наступного запиту і повторно заповнити форму, яку намагався надіслати користувач.

Щоб отримати флеш-дані з попереднього запиту, викличте метод `old` на екземплярі `Illuminate\Http\Request`. Метод `old` витягне попередні дані, що промайнули, з [session](/docs/{{version}}/session):

    $title = $request->old('title');

Laravel також надає глобальний помічник `old`. Якщо ви відображаєте старі дані у шаблоні [Blade template](/docs/{{version}}/blade), зручніше використовувати `old` для повторного заповнення форми. Якщо для даного поля не існує старих даних, буде повернуто `null`:

```blade
<input type="text" name="title" value="{{ old('title') }}">
```

<a name="a-note-on-optional-fields"></a>
### Примітка щодо необов'язкових полів

За замовчуванням, Laravel включає проміжні модулі `TrimStrings` і `ConvertEmptyStringsToNull` до глобального стеку проміжних модулів вашого додатку. Через це вам часто потрібно позначати «необов'язкові» поля запиту як `nullable`, якщо ви не хочете, щоб валідатор вважав `null`-значення недійсними. Наприклад:

    $request->validate([
        'title' => 'required|unique:posts|max:255',
        'body' => 'required',
        'publish_at' => 'nullable|date',
    ]);

У цьому прикладі ми вказуємо, що поле `publish_at` може бути або `null`, або дійсним представленням дати. Якщо до визначення правила не додати модифікатор `nullable`, валідатор вважатиме `null` недійсною датою.

<a name="validation-error-response-format"></a>
### Формат відповіді на помилку валідації

Коли ваш додаток генерує виключення `Illuminate\Validation\ValidationException` і вхідний HTTP-запит очікує відповідь у форматі JSON, Laravel автоматично відформатує повідомлення про помилки для вас і поверне HTTP-відповідь `422 Unprocessable Entity`.

Нижче ви можете переглянути приклад формату JSON-відповіді для помилок валідації. Зверніть увагу, що вкладені ключі помилок розбиваються на «крапки» у форматі нотації:

```json
{
    "message": "The team name must be a string. (and 4 more errors)",
    "errors": {
        "team_name": [
            "The team name must be a string.",
            "The team name must be at least 1 characters."
        ],
        "authorization.role": [
            "The selected authorization.role is invalid."
        ],
        "users.0.email": [
            "The users.0.email field is required."
        ],
        "users.2.email": [
            "The users.2.email must be a valid email address."
        ]
    }
}
```

<a name="form-request-validation"></a>
## Перевірка запиту на заповнення форми

<a name="creating-form-requests"></a>
### Створення запитів за формою

Для більш складних сценаріїв валідації ви можете створити «запит на форму». Запити на формі - це спеціальні класи запитів, які інкапсулюють власну логіку перевірки та авторизації. Щоб створити клас запиту на форму, ви можете скористатися командою `make:request` Artisan CLI:

```shell
php artisan make:request StorePostRequest
```

Згенерований клас запиту форми буде розміщено в каталозі `app/Http/Requests`. Якщо цього каталогу не існує, він буде створений при виконанні команди `make:request`. Кожен запит форми, згенерований Laravel, має два методи: `authorize` і `rules`.

Як ви вже здогадалися, метод `authorize` відповідає за визначення того, чи може поточний авторизований користувач виконати дію, представлену в запиті, в той час як метод `rules` повертає правила валідації, які слід застосувати до даних запиту:

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
        ];
    }

> [!NOTE]  
> Ви можете вказати будь-які залежності, які вам потрібні, у сигнатурі методу `rules`. Вони будуть автоматично вирішені за допомогою [службового контейнера] Laravel (/docs/{{version}}/container).

Отже, як оцінюються правила валідації? Все, що вам потрібно зробити, це передати запит у методі вашого контролера. Вхідний запит з форми перевіряється до виклику методу контролера, тобто вам не потрібно перевантажувати контролер логікою перевірки:

    /**
     * Store a new blog post.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        // The incoming request is valid...

        // Retrieve the validated input data...
        $validated = $request->validated();

        // Retrieve a portion of the validated input data...
        $validated = $request->safe()->only(['name', 'email']);
        $validated = $request->safe()->except(['name', 'email']);

        // Store the blog post...

        return redirect('/posts');
    }

Якщо валідація не пройшла, буде згенеровано відповідь перенаправлення, щоб відправити користувача назад до попереднього місця розташування. Помилки також будуть відображені в сеансі, щоб їх можна було переглянути. Якщо запит був XHR-запитом, користувачеві буде повернуто HTTP-відповідь з кодом статусу 422, включаючи [JSON-представлення помилок валідації](#validation-error-response-format).

> [!NOTE]  
> Потрібно додати перевірку запитів у реальному часі до вашого фронтенду Laravel на основі Inertia? Ознайомтеся з [Laravel Precognition](/docs/{{version}}/precognition).

<a name="performing-additional-validation-on-form-requests"></a>
#### Виконання додаткової перевірки

Іноді вам потрібно виконати додаткову перевірку після завершення первинної перевірки. Ви можете зробити це за допомогою методу `after` у запиті форми.

Метод `after` повинен повертати масив викликів або закриття, які будуть викликані після завершення валідації. Ці виклики отримають екземпляр `Illuminate\Validation\Validator`, що дозволить вам за потреби виводити додаткові повідомлення про помилки:

    use Illuminate\Validation\Validator;

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->somethingElseIsInvalid()) {
                    $validator->errors()->add(
                        'field',
                        'Something is wrong with this field!'
                    );
                }
            }
        ];
    }

Як зазначалося, масив, що повертається методом `after`, може також містити класи, що викликаються. Метод `__invoke` цих класів отримає екземпляр `Illuminate\Validation\Validator`:

```php
use App\Validation\ValidateShippingTime;
use App\Validation\ValidateUserStatus;
use Illuminate\Validation\Validator;

/**
 * Get the "after" validation callables for the request.
 */
public function after(): array
{
    return [
        new ValidateUserStatus,
        new ValidateShippingTime,
        function (Validator $validator) {
            //
        }
    ];
}
```

<a name="request-stopping-on-first-validation-rule-failure"></a>
#### Зупинка на першій помилці валідації

Додавши властивість `stopOnFirstFailure` до класу запиту, ви можете повідомити валідатору, що він повинен припинити перевірку всіх атрибутів, як тільки виникне одна помилка перевірки:

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

<a name="customizing-the-redirect-location"></a>
#### Налаштування місця перенаправлення

Як ми вже обговорювали раніше, відповідь перенаправлення буде згенерована, щоб відправити користувача назад на попереднє місце розташування, якщо запит не пройшов валідацію форми. Однак ви можете налаштувати цю поведінку. Для цього визначте властивість `$redirect` у вашому запиті до форми:

    /**
     * The URI that users should be redirected to if validation fails.
     *
     * @var string
     */
    protected $redirect = '/dashboard';

Або, якщо ви хочете перенаправляти користувачів на іменований маршрут, ви можете визначити властивість `$redirectRoute`:

    /**
     * The route that users should be redirected to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'dashboard';

<a name="authorizing-form-requests"></a>
### Запити на авторизацію форми

Клас запиту форми також містить метод `authorize`. За допомогою цього методу ви можете визначити, чи дійсно автентифікований користувач має право оновлювати даний ресурс. Наприклад, ви можете визначити, чи дійсно користувачеві належить коментар у блозі, який він намагається оновити. Швидше за все, ви будете взаємодіяти з вашими [воротами і політиками авторизації](/docs/{{version}}/authorization) у рамках цього методу:

    use App\Models\Comment;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $comment = Comment::find($this->route('comment'));

        return $comment && $this->user()->can('update', $comment);
    }

Оскільки всі запити до форм розширюють базовий клас запитів Laravel, ми можемо використовувати метод `user` для доступу до користувача, що пройшов авторизацію. Також зверніть увагу на виклик методу `route` у прикладі вище. Цей метод надає доступ до параметрів URI, визначених у маршруті, що викликається, таких як параметр `{comment}` у наведеному нижче прикладі:

    Route::post('/comment/{comment}');

Тому, якщо ваша програма використовує переваги [прив'язки моделі маршруту](/docs/{{version}}/routing#route-model-binding), ваш код можна зробити ще більш лаконічним, отримавши доступ до розв'язаної моделі як властивості запиту:

    return $this->user()->can('update', $this->comment);

Якщо метод `authorize` повертає `false`, автоматично буде повернуто HTTP-відповідь з кодом статусу 403 і ваш метод контролера не буде виконано.

Якщо ви плануєте обробляти логіку авторизації запиту в іншій частині вашого додатку, ви можете видалити метод `authorize` повністю або просто повернути `true`:

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

> [!NOTE]  
> Ви можете вказати будь-які залежності, які вам потрібні, у сигнатурі методу `authorize`. Вони будуть автоматично вирішені за допомогою [службового контейнера Laravel](/docs/{{version}}/container).

<a name="customizing-the-error-messages"></a>
### Налаштування повідомлень про помилки

Ви можете налаштувати повідомлення про помилки, які використовує запит форми, перевизначивши метод `messages`. Цей метод має повертати масив пар атрибут/правило і відповідних їм повідомлень про помилки:

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A title is required',
            'body.required' => 'A message is required',
        ];
    }

<a name="customizing-the-validation-attributes"></a>
#### Налаштування атрибутів перевірки

Багато повідомлень про помилки у вбудованих правилах валідації Laravel містять заповнювач `:attribute`. Якщо ви бажаєте замінити заповнювач `:attribute` у вашому повідомленні про помилку валідації на власне ім'я атрибута, ви можете вказати власні імена, перевизначивши метод `attributes`. Цей метод має повертати масив пар атрибут/ім'я:

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => 'email address',
        ];
    }

<a name="preparing-input-for-validation"></a>
### Підготовка вхідних даних до валідації

Якщо вам потрібно підготувати або очистити будь-які дані із запиту перед застосуванням правил перевірки, ви можете скористатися методом `prepareForValidation`:

    use Illuminate\Support\Str;

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->slug),
        ]);
    }

Аналогічно, якщо вам потрібно нормалізувати будь-які дані запиту після завершення перевірки, ви можете використовувати метод `passedValidation`:

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $this->replace(['name' => 'Taylor']);
    }

<a name="manually-creating-validators"></a>
## Створення валідаторів вручну

Якщо ви не хочете використовувати метод `validate` у запиті, ви можете створити екземпляр валідатора вручну за допомогою `Validator` [facade](/docs/{{version}}/facades). Метод `make` на фасаді створює новий екземпляр валідатора:

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;

    class PostController extends Controller
    {
        /**
         * Store a new blog post.
         */
        public function store(Request $request): RedirectResponse
        {
            $validator = Validator::make($request->all(), [
                'title' => 'required|unique:posts|max:255',
                'body' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect('post/create')
                            ->withErrors($validator)
                            ->withInput();
            }

            // Retrieve the validated input...
            $validated = $validator->validated();

            // Retrieve a portion of the validated input...
            $validated = $validator->safe()->only(['name', 'email']);
            $validated = $validator->safe()->except(['name', 'email']);

            // Store the blog post...

            return redirect('/posts');
        }
    }

Перший аргумент, що передається методу `make` - це дані, які підлягають перевірці. Другий аргумент - масив правил валідації, які слід застосувати до даних.

Після визначення того, що валідація запиту не пройшла успішно, ви можете використати метод `withErrors` для виведення повідомлень про помилки в сеанс. При використанні цього методу змінна `$errors` після перенаправлення буде автоматично передана вашим представленням, що дозволить вам легко відобразити їх користувачеві. Метод `withErrors` приймає валідатор, `MessageBag` або PHP `array`.

#### Зупинка при першій помилці валідації

Метод `stopOnFirstFailure` інформує валідатор про те, що він повинен припинити перевірку всіх атрибутів, як тільки виникне одна помилка валідації:

    if ($validator->stopOnFirstFailure()->fails()) {
        // ...
    }

<a name="automatic-redirection"></a>
### Автоматичне перенаправлення

Якщо ви хочете створити екземпляр валідатора вручну, але при цьому скористатися перевагами автоматичного перенаправлення, яке пропонує метод `validate` в HTTP-запиті, ви можете викликати метод `validate` для існуючого екземпляра валідатора. Якщо валідація завершиться невдало, користувача буде автоматично перенаправлено або, у випадку запиту XHR, буде повернуто [JSON-відповідь](#validation-error-response-format):

    Validator::make($request->all(), [
        'title' => 'required|unique:posts|max:255',
        'body' => 'required',
    ])->validate();

Ви можете використовувати метод `validateWithBag` для збереження повідомлень про помилки у [іменованому мішку помилок](#named-error-bags), якщо валідація не вдасться:

    Validator::make($request->all(), [
        'title' => 'required|unique:posts|max:255',
        'body' => 'required',
    ])->validateWithBag('post');

<a name="named-error-bags"></a>
### Іменовані мішки з помилками

Якщо ви маєте декілька форм на одній сторінці, ви можете назвати `MessageBag`, що містить помилки валідації, щоб ви могли отримати повідомлення про помилки для конкретної форми. Для цього передайте ім'я як другий аргумент функції `withErrors`:

    return redirect('register')->withErrors($validator, 'login');

Після цього ви можете отримати доступ до іменованого екземпляра `MessageBag` зі змінної `$errors`:

```blade
{{ $errors->login->first('email') }}
```

<a name="manual-customizing-the-error-messages"></a>
### Налаштування повідомлень про помилки

Якщо потрібно, ви можете вказати власні повідомлення про помилки, які екземпляр валідатора має використовувати замість стандартних повідомлень про помилки, що надаються Laravel. Існує декілька способів вказати користувацькі повідомлення. По-перше, ви можете передати кастомні повідомлення як третій аргумент методу `Validator::make`:

    $validator = Validator::make($input, $rules, $messages = [
        'required' => 'The :attribute field is required.',
    ]);

У цьому прикладі заповнювач `:attribute` буде замінено на справжню назву поля, що перевіряється. Ви також можете використовувати інші заповнювачі у повідомленнях про перевірку. Наприклад:

    $messages = [
        'same' => 'The :attribute and :other must match.',
        'size' => 'The :attribute must be exactly :size.',
        'between' => 'The :attribute value :input is not between :min - :max.',
        'in' => 'The :attribute must be one of the following types: :values',
    ];

<a name="specifying-a-custom-message-for-a-given-attribute"></a>
#### Вказівка спеціального повідомлення для заданого атрибута

Іноді вам може знадобитися вказати спеціальне повідомлення про помилку лише для певного атрибута. Ви можете зробити це за допомогою крапкових позначень. Спочатку вкажіть назву атрибута, а потім правило:\

    $messages = [
        'email.required' => 'We need to know your email address!',
    ];

<a name="specifying-custom-attribute-values"></a>
#### Вказівка користувацьких значень атрибутів

Багато вбудованих повідомлень про помилки у Laravel містять заповнювач `:attribute`, який замінюється на назву поля або атрибута, що перевіряється. Щоб налаштувати значення, які використовуються для заміни цих заповнювачів для певних полів, ви можете передати масив користувацьких атрибутів як четвертий аргумент методу `Validator::make`:

    $validator = Validator::make($input, $rules, $messages, [
        'email' => 'email address',
    ]);

<a name="performing-additional-validation"></a>
### Виконання додаткової перевірки

Іноді вам потрібно виконати додаткову перевірку після завершення первинної перевірки. Ви можете зробити це за допомогою методу `after` валідатора. Метод `after` приймає закриття або масив викликів, які буде викликано після завершення перевірки. Ці виклики отримають екземпляр `Illuminate\Validation\Validator`, що дозволить вам за потреби згенерувати додаткові повідомлення про помилки:

    use Illuminate\Support\Facades\Validator;

    $validator = Validator::make(/* ... */);

    $validator->after(function ($validator) {
        if ($this->somethingElseIsInvalid()) {
            $validator->errors()->add(
                'field', 'Something is wrong with this field!'
            );
        }
    });

    if ($validator->fails()) {
        // ...
    }

Як зазначалося, метод `after` також приймає масив викликів, що особливо зручно, якщо ваша логіка «після валідації» інкапсульована у класах, що викликаються, які отримають екземпляр `Illuminate\Validation\Validator` через їхній метод `__invoke`:

```php
use App\Validation\ValidateShippingTime;
use App\Validation\ValidateUserStatus;

$validator->after([
    new ValidateUserStatus,
    new ValidateShippingTime,
    function ($validator) {
        // ...
    },
]);
```

<a name="working-with-validated-input"></a>
## Робота з перевіреними даними

Після перевірки даних вхідного запиту за допомогою запиту через форму або вручну створеного екземпляра валідатора, ви можете захотіти отримати дані вхідного запиту, які дійсно пройшли перевірку. Це можна зробити кількома способами. По-перше, ви можете викликати метод `validated` на запиті форми або екземплярі валідатора. Цей метод повертає масив даних, які було перевірено:

    $validated = $request->validated();

    $validated = $validator->validated();

Крім того, ви можете викликати метод `Safe` на запиті форми або екземплярі валідатора. Цей метод повертає екземпляр `Illuminate\Support\ValidatedInput`. Цей об'єкт має методи `only`, `except` та `all` для отримання підмножини перевірених даних або всього масиву перевірених даних:

    $validated = $request->safe()->only(['name', 'email']);

    $validated = $request->safe()->except(['name', 'email']);

    $validated = $request->safe()->all();

Крім того, екземпляр `Illuminate\Support\ValidatedInput` можна перебирати та отримувати доступ до нього як до масиву:

    // Validated data may be iterated...
    foreach ($request->safe() as $key => $value) {
        // ...
    }

    // Validated data may be accessed as an array...
    $validated = $request->safe();

    $email = $validated['email'];

Якщо ви хочете додати додаткові поля до перевірених даних, ви можете викликати метод `merge`:

    $validated = $request->safe()->merge(['name' => 'Taylor Otwell']);

Якщо ви хочете отримати перевірені дані як екземпляр [колекції](/docs/{{version}}/collections), ви можете викликати метод `collect`:

    $collection = $request->safe()->collect();

<a name="working-with-error-messages"></a>
## Робота з повідомленнями про помилки

Після виклику методу `errors` на екземплярі `Validator` ви отримаєте екземпляр `Illuminate\Support\MessageBag`, який має безліч зручних методів для роботи з повідомленнями про помилки. Змінна `$errors`, яка автоматично стає доступною для всіх подань, також є екземпляром класу `MessageBag`.

<a name="retrieving-the-first-error-message-for-a-field"></a>
#### Отримання першого повідомлення про помилку для поля

Щоб отримати перше повідомлення про помилку для заданого поля, використовуйте метод `first`:

    $errors = $validator->errors();

    echo $errors->first('email');

<a name="retrieving-all-error-messages-for-a-field"></a>
#### Отримання всіх повідомлень про помилки для поля

Якщо вам потрібно отримати масив усіх повідомлень для заданого поля, використовуйте метод `get`:

    foreach ($errors->get('email') as $message) {
        // ...
    }

Якщо ви перевіряєте поле форми у вигляді масиву, ви можете отримати всі повідомлення для кожного з елементів масиву за допомогою символу `*`:

    foreach ($errors->get('attachments.*') as $message) {
        // ...
    }

<a name="retrieving-all-error-messages-for-all-fields"></a>
#### Отримання всіх повідомлень про помилки для всіх полів

Щоб отримати масив всіх повідомлень для всіх полів, використовуйте метод `all`:

    foreach ($errors->all() as $message) {
        // ...
    }

<a name="determining-if-messages-exist-for-a-field"></a>
#### Визначення наявності повідомлень для поля

Метод `has` можна використовувати для визначення наявності повідомлень про помилки для заданого поля:

    if ($errors->has('email')) {
        // ...
    }

<a name="specifying-custom-messages-in-language-files"></a>
### Вказівка користувацьких повідомлень у мовних файлах

Кожне вбудоване правило перевірки Laravel має повідомлення про помилку, яке знаходиться у файлі `lang/en/validation.php` вашого додатку. Якщо у вашому додатку немає каталогу `lang`, ви можете вказати Laravel створити його за допомогою команди `lang:publish` Artisan.

У файлі `lang/en/validation.php` ви знайдете запис перекладу для кожного правила перевірки. Ви можете змінювати або модифікувати ці повідомлення відповідно до потреб вашої програми.

Крім того, ви можете скопіювати цей файл в іншу мовну директорію, щоб перекласти повідомлення для мови вашої програми. Щоб дізнатися більше про локалізацію Laravel, перегляньте повну [документацію з локалізації](/docs/{{version}}/localization).

> [!WARNING]  
> За замовчуванням, каркас програми Laravel не містить каталогу `lang`. Якщо ви бажаєте налаштувати мовні файли Laravel, ви можете опублікувати їх за допомогою команди `lang:publish` Artisan.

<a name="custom-messages-for-specific-attributes"></a>
#### Кастомні повідомлення для певних атрибутів

Ви можете налаштувати повідомлення про помилки, що використовуються для певних комбінацій атрибутів і правил у мовних файлах валідації вашого додатку. Для цього додайте ваші налаштування повідомлень до масиву `custom` у мовному файлі `lang/xx/validation.php` вашого додатку:

    'custom' => [
        'email' => [
            'required' => 'We need to know your email address!',
            'max' => 'Your email address is too long!'
        ],
    ],

<a name="specifying-attribute-in-language-files"></a>
### Вказівка атрибутів у мовних файлах

Багато вбудованих повідомлень про помилки у Laravel містять заповнювач `:attribute`, який замінюється на назву поля або атрибута, що перевіряється. Якщо ви хочете, щоб частина `:attribute` вашого повідомлення про перевірку була замінена користувацьким значенням, ви можете вказати ім'я користувацького атрибута у масиві `attributes` вашого мовного файлу `lang/xx/validation.php`:

    'attributes' => [
        'email' => 'email address',
    ],

> [!WARNING]  
> За замовчуванням, каркас програми Laravel не містить каталогу `lang`. Якщо ви бажаєте налаштувати мовні файли Laravel, ви можете опублікувати їх за допомогою команди `lang:publish` Artisan.

<a name="specifying-values-in-language-files"></a>
### Вказівка значень у мовних файлах

Деякі повідомлення про помилки вбудованих правил валідації Laravel містять заповнювач `:value`, який замінюється поточним значенням атрибута запиту. Однак, іноді вам може знадобитися замінити частину `:value` у вашому повідомленні про помилку на власне представлення значення. Наприклад, розглянемо наступне правило, яке визначає, що номер кредитної картки є обов'язковим, якщо `payment_type` має значення `cc`:

    Validator::make($request->all(), [
        'credit_card_number' => 'required_if:payment_type,cc'
    ]);

Якщо це правило перевірки не спрацює, воно видасть наступне повідомлення про помилку:

```none
Поле «Номер кредитної картки» є обов'язковим для заповнення, якщо тип оплати - безготівковий.
```

Замість того, щоб відображати `cc` як значення типу платежу, ви можете вказати більш зручне для користувача представлення значення у вашому мовному файлі `lang/xx/validation.php`, визначивши масив `values`:

    'values' => [
        'payment_type' => [
            'cc' => 'credit card'
        ],
    ],

> [!WARNING]  
> За замовчуванням, каркас програми Laravel не містить каталогу `lang`. Якщо ви бажаєте налаштувати мовні файли Laravel, ви можете опублікувати їх за допомогою команди `lang:publish` Artisan.

Після визначення цього значення правило перевірки видасть наступне повідомлення про помилку:

```none
Поле «Номер кредитної картки» є обов'язковим для заповнення, якщо тип оплати - кредитна картка.
```

<a name="available-validation-rules"></a>
## Доступні правила валідації

Нижче наведено список усіх доступних правил валідації та їхні функції:

<style>
    .collection-method-list > p {
        columns: 10.8em 3; -moz-columns: 10.8em 3; -webkit-columns: 10.8em 3;
    }

    .collection-method-list a {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<div class="collection-method-list" markdown="1">

[Accepted](#rule-accepted)
[Accepted If](#rule-accepted-if)
[Active URL](#rule-active-url)
[After (Date)](#rule-after)
[After Or Equal (Date)](#rule-after-or-equal)
[Alpha](#rule-alpha)
[Alpha Dash](#rule-alpha-dash)
[Alpha Numeric](#rule-alpha-num)
[Array](#rule-array)
[Ascii](#rule-ascii)
[Bail](#rule-bail)
[Before (Date)](#rule-before)
[Before Or Equal (Date)](#rule-before-or-equal)
[Between](#rule-between)
[Boolean](#rule-boolean)
[Confirmed](#rule-confirmed)
[Current Password](#rule-current-password)
[Date](#rule-date)
[Date Equals](#rule-date-equals)
[Date Format](#rule-date-format)
[Decimal](#rule-decimal)
[Declined](#rule-declined)
[Declined If](#rule-declined-if)
[Different](#rule-different)
[Digits](#rule-digits)
[Digits Between](#rule-digits-between)
[Dimensions (Image Files)](#rule-dimensions)
[Distinct](#rule-distinct)
[Doesnt Start With](#rule-doesnt-start-with)
[Doesnt End With](#rule-doesnt-end-with)
[Email](#rule-email)
[Ends With](#rule-ends-with)
[Enum](#rule-enum)
[Exclude](#rule-exclude)
[Exclude If](#rule-exclude-if)
[Exclude Unless](#rule-exclude-unless)
[Exclude With](#rule-exclude-with)
[Exclude Without](#rule-exclude-without)
[Exists (Database)](#rule-exists)
[Extensions](#rule-extensions)
[File](#rule-file)
[Filled](#rule-filled)
[Greater Than](#rule-gt)
[Greater Than Or Equal](#rule-gte)
[Hex Color](#rule-hex-color)
[Image (File)](#rule-image)
[In](#rule-in)
[In Array](#rule-in-array)
[Integer](#rule-integer)
[IP Address](#rule-ip)
[JSON](#rule-json)
[Less Than](#rule-lt)
[Less Than Or Equal](#rule-lte)
[Lowercase](#rule-lowercase)
[MAC Address](#rule-mac)
[Max](#rule-max)
[Max Digits](#rule-max-digits)
[MIME Types](#rule-mimetypes)
[MIME Type By File Extension](#rule-mimes)
[Min](#rule-min)
[Min Digits](#rule-min-digits)
[Missing](#rule-missing)
[Missing If](#rule-missing-if)
[Missing Unless](#rule-missing-unless)
[Missing With](#rule-missing-with)
[Missing With All](#rule-missing-with-all)
[Multiple Of](#rule-multiple-of)
[Not In](#rule-not-in)
[Not Regex](#rule-not-regex)
[Nullable](#rule-nullable)
[Numeric](#rule-numeric)
[Present](#rule-present)
[Present If](#rule-present-if)
[Present Unless](#rule-present-unless)
[Present With](#rule-present-with)
[Present With All](#rule-present-with-all)
[Prohibited](#rule-prohibited)
[Prohibited If](#rule-prohibited-if)
[Prohibited Unless](#rule-prohibited-unless)
[Prohibits](#rule-prohibits)
[Regular Expression](#rule-regex)
[Required](#rule-required)
[Required If](#rule-required-if)
[Required If Accepted](#rule-required-if-accepted)
[Required Unless](#rule-required-unless)
[Required With](#rule-required-with)
[Required With All](#rule-required-with-all)
[Required Without](#rule-required-without)
[Required Without All](#rule-required-without-all)
[Required Array Keys](#rule-required-array-keys)
[Same](#rule-same)
[Size](#rule-size)
[Sometimes](#validating-when-present)
[Starts With](#rule-starts-with)
[String](#rule-string)
[Timezone](#rule-timezone)
[Unique (Database)](#rule-unique)
[Uppercase](#rule-uppercase)
[URL](#rule-url)
[ULID](#rule-ulid)
[UUID](#rule-uuid)

</div>

<a name="rule-accepted"></a>
#### accepted

Поле, що перевіряється, повинно мати значення `«yes»`, `«on»`, `1`, `«1»`, `«true` або `»true"`. Це корисно для перевірки прийняття «Умов використання» або подібних полів.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

Поле, що перевіряється, має бути `«yes»`, `«on»`, `1`, `«1»`, `«true` або `»true"`, якщо інше поле, що перевіряється, дорівнює вказаному значенню. Це корисно для перевірки прийняття «Умов використання» або подібних полів.

<a name="rule-active-url"></a>
#### active_url

Поле, що перевіряється, повинно мати дійсний запис A або AAAA згідно з PHP-функцією `dns_get_record`. Ім'я хоста з наданої URL-адреси витягується за допомогою PHP-функції `parse_url` перед передачею в `dns_get_record`.

<a name="rule-after"></a>
#### after:_date_

Поле, що перевіряється, має бути значенням після заданої дати. Дати буде передано у PHP-функцію `strtotime` для перетворення у дійсний екземпляр `DateTime`:

    'start_date' => 'required|date|after:tomorrow'

Замість того, щоб передавати рядок дати для обчислення `strtotime`, ви можете вказати інше поле для порівняння з датою:

    'finish_date' => 'required|date|after:start_date'

<a name="rule-after-or-equal"></a>
#### after\_or\_equal:_date_

Поле, що перевіряється, має бути значенням після або рівним вказаній даті. Для отримання додаткової інформації дивіться правило [після](#правило-після).

<a name="rule-alpha"></a>
#### alpha

Поле, що перевіряється, повинно містити виключно символи Unicode, що містяться в [`\p{L}`](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) та [`\p{M}`](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=).

Щоб обмежити це правило перевірки символами з діапазону ASCII (`a-z` і `A-Z`), ви можете додати до правила перевірки опцію `ascii`:

```php
'username' => 'alpha:ascii',
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

Поле, що перевіряється, повинно містити виключно алфавітно-цифрові символи Unicode, що містяться у [`\p{L}`](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [`\p{M}`](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [`\p{N}`](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=), а також ASCII тире (`-`) і ASCII підкреслення (`_`).

Щоб обмежити це правило перевірки символами з діапазону ASCII (`a-z` і `A-Z`), ви можете додати до правила перевірки опцію `ascii`:

```php
'username' => 'alpha_dash:ascii',
```

<a name="rule-alpha-num"></a>
#### alpha_num

Поле, що перевіряється, повинно повністю складатися з буквено-цифрових символів Unicode, що містяться у [`\p{L}`](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [`\p{M}`](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=) та [`\p{N}`](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=).

Щоб обмежити це правило перевірки символами з діапазону ASCII (`a-z` і `A-Z`), ви можете додати до правила перевірки опцію `ascii`:

```php
'username' => 'alpha_num:ascii',
```

<a name="rule-array"></a>
#### array

Поле, що перевіряється, має бути PHP `масивом`.

Коли до правила `array` додаються додаткові значення, кожен ключ у вхідному масиві повинен бути присутнім у списку значень, наданих правилу. У наступному прикладі ключ `admin` у вхідному масиві є недійсним, оскільки він не міститься у списку значень, наданих правилу `array`:

    use Illuminate\Support\Facades\Validator;

    $input = [
        'user' => [
            'name' => 'Taylor Otwell',
            'username' => 'taylorotwell',
            'admin' => true,
        ],
    ];

    Validator::make($input, [
        'user' => 'array:name,username',
    ]);

Загалом, ви завжди повинні вказувати ключі масиву, які можуть бути присутніми у вашому масиві.

<a name="rule-ascii"></a>
#### ascii

Поле, що перевіряється, повинно бути повністю 7-бітними символами ASCII.

<a name="rule-bail"></a>
#### bail

Зупинити запуск правил валідації для поля після першої помилки валідації.

Тоді як правило `bail` зупиняє перевірку певного поля лише тоді, коли воно стикається з помилкою валідації, метод `stopOnFirstFailure` інформує валідатор про те, що він повинен зупинити перевірку всіх атрибутів, як тільки виникне одна помилка валідації:

    if ($validator->stopOnFirstFailure()->fails()) {
        // ...
    }

<a name="rule-before"></a>
#### before:_date_

Поле, що перевіряється, має бути значенням, що передує даті. Дату буде передано у функцію PHP `strtotime` для перетворення у коректний екземпляр `DateTime`. Крім того, як і в правилі [`after`](#rule-after), як значення `date` може бути передано ім'я іншого поля, що перевіряється.

<a name="rule-before-or-equal"></a>
#### before\_or\_equal:_date_

Поле, що перевіряється, має бути значенням, що передує або дорівнює заданій даті. Дату буде передано у функцію PHP `strtotime` для перетворення у коректний екземпляр `DateTime`. Крім того, як і в правилі [`after`](#rule-after), як значення `date` може бути передано ім'я іншого поля, що перевіряється.

<a name="rule-between"></a>
#### between:_min_,_max_

Поле, що перевіряється, повинно мати розмір між заданими значеннями _min_ і _max_ (включно). Рядки, числа, масиви та файли оцінюються так само, як і правило [``size`](#правило-розмір).

<a name="rule-boolean"></a>
#### boolean

Поле, що перевіряється, повинно мати можливість бути приведене до логічного типу. Допустимими значеннями є `true`, `false`, `1`, `0`, `«1»` та `«0»`.

<a name="rule-confirmed"></a>
#### confirmed

Поле, що перевіряється, повинно мати відповідне поле `{поле}_підтвердження`. Наприклад, якщо поле для перевірки - `пароль`, то у вхідних даних має бути відповідне поле `підтвердження_пароля`.

<a name="rule-current-password"></a>
#### current_password

Поле, що перевіряється, має збігатися з паролем автентифікованого користувача. Ви можете вказати [захист автентифікації](/docs/{{version}}/authentication) за допомогою першого параметра правила:

    'password' => 'current_password:api'

<a name="rule-date"></a>
#### date

Поле, що перевіряється, має бути дійсною, нерелятивною датою згідно з функцією PHP `strtotime`.

<a name="rule-date-equals"></a>
#### date_equals:_date_

Поле, що перевіряється, має дорівнювати заданій даті. Дату буде передано у функцію PHP `strtotime` для перетворення у дійсний екземпляр `DateTime`.

<a name="rule-date-format"></a>
#### date_format:_format_,...

Поле, що перевіряється, має відповідати одному з наведених _форматів_. При перевірці поля слід використовувати **або** `date`, або `date_format`, але не обидва. Це правило перевірки підтримує всі формати, що підтримуються класом PHP [DateTime](https://www.php.net/manual/en/class.datetime.php).

<a name="rule-decimal"></a>
#### decimal:_min_,_max_

Поле, що перевіряється, має бути числовим і містити вказану кількість знаків після коми:

    // Must have exactly two decimal places (9.99)...
    'price' => 'decimal:2'

    // Must have between 2 and 4 decimal places...
    'price' => 'decimal:2,4'

<a name="rule-declined"></a>
#### declined

Поле, що перевіряється, має бути `no`, `off`, `0`, `0`, `0`, `false` або `false`.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

Поле, що перевіряється, має бути `no`, `off`, `0`, `0`, `false` або `false`, якщо інше поле, що перевіряється, дорівнює вказаному значенню.

<a name="rule-different"></a>
#### different:_field_

Поле, що перевіряється, повинно мати значення, відмінне від _поля_.

<a name="rule-digits"></a>
#### digits:_value_

Ціле число, що перевіряється, повинно мати точну довжину _value_.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

Цілочисельна перевірка повинна мати довжину між заданими значеннями _min_ та _max_.

<a name="rule-dimensions"></a>
#### dimensions

Файл, що перевіряється, має бути зображенням, яке відповідає обмеженням на розміри, вказаним у параметрах правила:

    'avatar' => 'dimensions:min_width=100,min_height=200'

Доступні обмеження: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_.

Обмеження _відношення_ слід представляти як відношення ширини до висоти. Це може бути задано або дробом, наприклад `3/2`, або плаваючою точкою, наприклад `1.5`:

    'avatar' => 'dimensions:ratio=3/2'

Оскільки це правило вимагає декількох аргументів, ви можете використовувати метод `Rule::dimensions` для вільної побудови правила:

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;

    Validator::make($data, [
        'avatar' => [
            'required',
            Rule::dimensions()->maxWidth(1000)->maxHeight(500)->ratio(3 / 2),
        ],
    ]);

<a name="rule-distinct"></a>
#### distinct

При перевірці масивів поле, що перевіряється, не повинно мати повторюваних значень:

    'foo.*.id' => 'distinct'

За замовчуванням Distinct використовує нестрогі порівняння змінних. Щоб використовувати суворі порівняння, ви можете додати параметр `strict` до визначення правила перевірки:

    'foo.*.id' => 'distinct:strict'

Ви можете додати `ignore_case` до аргументів правила перевірки, щоб змусити правило ігнорувати відмінності у написанні великих літер:

    'foo.*.id' => 'distinct:ignore_case'

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

Поле, що перевіряється, не повинно починатися з одного з наведених значень.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

Поле, що перевіряється, не повинно закінчуватися одним із заданих значень.

<a name="rule-email"></a>
#### email

Поле для перевірки має бути відформатовано як адреса електронної пошти. Це правило перевірки використовує пакет [`egulias/email-validator`](https://github.com/egulias/EmailValidator) для перевірки адреси електронної пошти. За замовчуванням застосовується валідатор `RFCValidation`, але ви також можете застосувати інші стилі валідації:

    'email' => 'email:rfc,dns'

У наведеному вище прикладі буде застосовано перевірки `RFCValidation` та `DNSCheckValidation`. Ось повний список стилів валідації, які ви можете застосувати:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation`
- `strict`: `NoRFCWarningsValidation`
- `dns`: `DNSCheckValidation`
- `spoof`: `SpoofCheckValidation`
- `filter`: `FilterEmailValidation`
- `filter_unicode`: `FilterEmailValidation::unicode()`

</div>

Валідатор `filter`, який використовує функцію PHP `filter_var`, постачається разом з Laravel і був стандартною поведінкою перевірки email-адрес до версії 5.8.

> [!WARNING]  
> Валідатори `dns` та `spoof` вимагають розширення PHP `intl`.

<a name="rule-ends-with"></a>
#### ends_with:_foo_,_bar_,...

Поле, що перевіряється, повинно закінчуватися одним з наведених значень.

<a name="rule-enum"></a>
#### enum

Правило `Enum` - це правило на основі класу, яке перевіряє, чи містить поле, що перевіряється, дійсне значення переліку. Правило `Enum` приймає ім'я зчислення як єдиний аргумент конструктора. При перевірці примітивних значень, правилу `Enum` слід надати підкріплений Enum:

    use App\Enums\ServerStatus;
    use Illuminate\Validation\Rule;

    $request->validate([
        'status' => [Rule::enum(ServerStatus::class)],
    ]);

Методи `only` та `except` правила `Enum` можуть бути використані для обмеження того, які випадки перечислення слід вважати допустимими:

    Rule::enum(ServerStatus::class)
        ->only([ServerStatus::Pending, ServerStatus::Active]);

    Rule::enum(ServerStatus::class)
        ->except([ServerStatus::Pending, ServerStatus::Active]);

Метод `when` можна використовувати для умовної модифікації правила `Enum`:

```php
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

Rule::enum(ServerStatus::class)
    ->when(
        Auth::user()->isAdmin(),
        fn ($rule) => $rule->only(...),
        fn ($rule) => $rule->only(...),
    );
```

<a name="rule-exclude"></a>
#### exclude

Поле, що перевіряється, буде виключено з даних запиту, що повертаються методами `validate` і `validated`.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

Поле, що перевіряється, буде виключено з даних запиту, що повертаються методами `validate` і `validated`, якщо поле _інше_поле_ дорівнює _значення_.

Якщо потрібна складна логіка умовного виключення, ви можете використовувати метод `Rule::excludeIf`. Цей метод приймає логічне значення або закриття. Якщо передано закриття, закриття має повертати значення `true` або `false`, щоб вказати, чи слід виключити поле, яке перевіряється:

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;

    Validator::make($request->all(), [
        'role_id' => Rule::excludeIf($request->user()->is_admin),
    ]);

    Validator::make($request->all(), [
        'role_id' => Rule::excludeIf(fn () => $request->user()->is_admin),
    ]);

<a name="rule-exclude-unless"></a>
#### exclude_unless:_anotherfield_,_value_

Поле, що перевіряється, буде виключено з даних запиту, що повертаються методами `validate` і `validated`, якщо тільки поле _іншого поля_ не дорівнює _значенню_. Якщо _значення_ дорівнює `null` (`exclude_unless:name,null`), поле, що перевіряється, буде виключено, якщо тільки поле порівняння не дорівнює `null` або якщо поле порівняння відсутнє в даних запиту.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

Поле, що перевіряється, буде виключено з даних запиту, що повертаються методами `validate` і `validated`, якщо присутнє поле _anotherfield_.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

Поле, що перевіряється, буде виключено з даних запиту, що повертаються методами `validate` і `validated`, якщо поле _інше_ поле відсутнє.

<a name="rule-exists"></a>
#### exists:_table_,_column_

Поле, що перевіряється, повинно існувати в даній таблиці бази даних.

<a name="basic-usage-of-exists-rule"></a>
#### Basic Usage of Exists Rule

    'state' => 'exists:states'

Якщо параметр `column` не вказано, буде використано назву поля. Отже, у цьому випадку правило перевірить, що таблиця бази даних `states` містить запис зі значенням стовпця `state`, яке збігається зі значенням атрибута `state` запиту.

<a name="specifying-a-custom-column-name"></a>
#### Specifying a Custom Column Name

Ви можете явно вказати ім'я стовпця бази даних, який має використовуватися правилом перевірки, розмістивши його після імені таблиці бази даних:

    'state' => 'exists:states,abbreviation'

Іноді вам може знадобитися вказати конкретне з'єднання з базою даних, яке буде використовуватися для запиту `exists`. Це можна зробити, додавши назву з'єднання до назви таблиці:

    'email' => 'exists:connection.staff,email'

Замість того, щоб вказувати ім'я таблиці безпосередньо, ви можете вказати модель Eloquent, яку слід використовувати для визначення імені таблиці:

    'user_id' => 'exists:App\Models\User,id'

Якщо ви хочете налаштувати запит, який виконується за правилом валідації, ви можете використовувати клас `Rule` для вільного визначення правила. У цьому прикладі ми також задамо правила валідації у вигляді масиву замість того, щоб використовувати символ `|` для їхнього розділення:

    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;

    Validator::make($data, [
        'email' => [
            'required',
            Rule::exists('staff')->where(function (Builder $query) {
                return $query->where('account_id', 1);
            }),
        ],
    ]);

Ви можете явно вказати ім'я стовпця бази даних, який має використовуватися правилом `exists`, згенерованим методом `Rule::exists`, передавши ім'я стовпця як другий аргумент методу `exists`:

    'state' => Rule::exists('states', 'abbreviation'),

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

Файл, що перевіряється, повинен мати призначене користувачем розширення, що відповідає одному з перелічених розширень:

    'photo' => ['required', 'extensions:jpg,png'],

> [!WARNING]  
> Ніколи не слід покладатися на перевірку файлу лише за його розширенням, призначеним користувачем. Зазвичай це правило слід використовувати у поєднанні з правилами [`mimes`](#rule-mimes) або [`mimetypes`](#rule-mimetypes).

<a name="rule-file"></a>
#### file

Поле для перевірки має бути успішно завантаженим файлом.

<a name="rule-filled"></a>
#### filled

Поле, що перевіряється, не повинно бути порожнім, якщо воно присутнє.

<a name="rule-gt"></a>
#### gt:_field_

Поле, що перевіряється, має бути більшим за задане _поле_ або _значення_. Обидва поля мають бути одного типу. Рядки, числа, масиви та файли оцінюються за тими ж правилами, що і правило [``розмір``](#розмір-правило).

<a name="rule-gte"></a>
#### gte:_field_

Поле, що перевіряється, має бути більшим або рівним заданому _полю_ або _значенню_. Обидва поля мають бути одного типу. Рядки, числа, масиви і файли оцінюються за тими ж правилами, що і правило [``size`](#правило-розмір).

<a name="rule-hex-color"></a>
#### hex_color

Поле, що перевіряється, повинно містити дійсне значення кольору в [шістнадцятковому](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) форматі.

<a name="rule-image"></a>
#### image

Файл для перевірки повинен бути зображенням (jpg, jpeg, png, bmp, gif, svg або webp).

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

Поле, що перевіряється, повинно бути включено до заданого списку значень. Оскільки це правило часто вимагає «розбиття» масиву, для швидкої побудови правила можна скористатися методом `Rule::in`:

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;

    Validator::make($data, [
        'zones' => [
            'required',
            Rule::in(['first-zone', 'second-zone']),
        ],
    ]);

Коли правило `in` поєднується з правилом `array`, кожне значення у вхідному масиві має бути присутнім у списку значень, наданих правилу `in`. У наступному прикладі код аеропорту `LAS` у вхідному масиві є недійсним, оскільки він не міститься у списку аеропортів, наданому правилу `in`:

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;

    $input = [
        'airports' => ['NYC', 'LAS'],
    ];

    Validator::make($input, [
        'airports' => [
            'required',
            'array',
        ],
        'airports.*' => Rule::in(['NYC', 'LIT']),
    ]);

<a name="rule-in-array"></a>
#### in_array:_anotherfield_.*

Поле, що перевіряється, повинно існувати у значеннях _іншого поля_.

<a name="rule-integer"></a>
#### integer

Поле, що перевіряється, має бути цілим числом.

> [!WARNING]  
> Це правило перевірки не перевіряє, чи вхідні дані мають тип змінної «integer», а лише те, що вони мають тип, прийнятний для правила `FILTER_VALIDATE_INT` у PHP. Якщо вам потрібно перевірити, що введене число є числом, використовуйте це правило у поєднанні з [правилом перевірки `numeric`](#rule-numeric).

<a name="rule-ip"></a>
#### ip

Поле, що перевіряється, повинно містити IP-адресу.

<a name="ipv4"></a>
#### ipv4

Поле, що перевіряється, має бути IPv4-адресою.

<a name="ipv6"></a>
#### ipv6

Поле, що перевіряється, має бути IPv6-адресою.

<a name="rule-json"></a>
#### json

Поле, що перевіряється, має бути коректним JSON-рядком.

<a name="rule-lt"></a>
#### lt:_field_

Поле, що перевіряється, має бути меншим за задане _поле_. Обидва поля повинні бути одного типу. Рядки, числа, масиви та файли обчислюються за тими ж правилами, що і правило [`розмір`](#розмір-правило).

<a name="rule-lte"></a>
#### lte:_field_

Поле, що перевіряється, має бути меншим за задане _поле_. Обидва поля повинні бути одного типу. Рядки, числа, масиви та файли обчислюються за тими ж правилами, що і правило [`розмір`](#розмір-правило).

<a name="rule-lowercase"></a>
#### lowercase

Поле, що перевіряється, має бути нижнього регістру.

<a name="rule-mac"></a>
#### mac_address

Поле, що перевіряється, має бути MAC-адресою.

<a name="rule-max"></a>
#### max:_value_

Поле, що перевіряється, має бути меншим або рівним максимальному _значенню_. Рядки, числа, масиви та файли оцінюються так само, як і правило [``size`](#rule-size).

<a name="rule-max-digits"></a>
#### max_digits:_value_

Ціле число, що перевіряється, повинно мати максимальну довжину _value_.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

Файл, що перевіряється, повинен відповідати одному з наведених MIME-типів:

    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime'

Для визначення MIME-типу завантаженого файлу буде прочитано вміст файлу і фреймворк спробує вгадати MIME-тип, який може відрізнятися від наданого клієнтом.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

Файл, що перевіряється, повинен мати MIME-тип, що відповідає одному з перерахованих розширень:

    'photo' => 'mimes:jpg,bmp,png'

Незважаючи на те, що вам потрібно вказати лише розширення, це правило фактично перевіряє MIME-тип файлу, читаючи вміст файлу і вгадуючи його MIME-тип. Повний список MIME-типів і відповідних їм розширень можна знайти за цим посиланням:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME Types and Extensions

Це правило перевірки не перевіряє відповідність між типом MIME і розширенням, яке користувач присвоїв файлу. Наприклад, правило перевірки `mimes:png` вважатиме файл, що містить коректний вміст у форматі PNG, коректним зображенням у форматі PNG, навіть якщо він має назву `photo.txt`. Якщо ви хочете перевірити розширення файлу, призначене користувачем, ви можете скористатися правилом [`extensions`](#rule-extensions).

<a name="rule-min"></a>
#### min:_value_

Поле, що перевіряється, повинно мати мінімальне _значення_. Рядки, числа, масиви та файли оцінюються так само, як і правило [``size`](#rule-size).

<a name="rule-min-digits"></a>
#### min_digits:_value_

Ціле число, що перевіряється, повинно мати мінімальну довжину _value_.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

Поле, що перевіряється, має бути кратним _значенню_.

<a name="rule-missing"></a>
#### missing

Поле, яке перевіряється, не повинно бути присутнім у вхідних даних.

 <a name="rule-missing-if"></a>
 #### missing_if:_anotherfield_,_value_,...

Поле, що перевіряється, не повинно бути присутнім, якщо поле _інше_ дорівнює будь-якому _значенню_.

 <a name="rule-missing-unless"></a>
 #### missing_unless:_anotherfield_,_value_

Поле, що перевіряється, не повинно бути присутнім, якщо тільки поле _інше_ не дорівнює якомусь _значенню_.

 <a name="rule-missing-with"></a>
 #### missing_with:_foo_,_bar_,...

Поле, що перевіряється, не повинно бути присутнім _тільки_, якщо_ присутнє будь-яке з інших вказаних полів.

 <a name="rule-missing-with-all"></a>
 #### missing_with_all:_foo_,_bar_,...

Поле, що перевіряється, не повинно бути присутнім _тільки_, якщо_ всі інші вказані поля присутні.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

Поле, що перевіряється, не повинно входити до заданого списку значень. Для вільної побудови правила можна використовувати метод `Rule::notIn`:

    use Illuminate\Validation\Rule;

    Validator::make($data, [
        'toppings' => [
            'required',
            Rule::notIn(['sprinkles', 'cherries']),
        ],
    ]);

<a name="rule-not-regex"></a>
#### not_regex:_pattern_

Поле, що перевіряється, не повинно збігатися з заданим регулярним виразом.

Внутрішньо це правило використовує функцію PHP `preg_match`. Вказаний шаблон має відповідати формату, який вимагає `preg_match`, а отже, містити допустимі роздільники. Наприклад: `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]  
> При використанні шаблонів `regex` / `not_regex` може знадобитися вказати правила перевірки за допомогою масиву замість використання розділювачів `|`, особливо якщо регулярний вираз містить символ `|`.

<a name="rule-nullable"></a>
#### nullable

Поле, що перевіряється, може бути `null`.

<a name="rule-numeric"></a>
#### numeric

Поле, що перевіряється, має бути [числовим](https://www.php.net/manual/en/function.is-numeric.php).

<a name="rule-present"></a>
#### present

Поле, що перевіряється, повинно існувати у вхідних даних.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

Поле, що перевіряється, має бути присутнім, якщо поле _інше_ дорівнює будь-якому _значенню_.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

Поле, що перевіряється, має бути присутнім, якщо тільки поле _інше_ не дорівнює якомусь _значенню_.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

Поле, що перевіряється, має бути присутнім _тільки_ якщо_ присутнє будь-яке з інших вказаних полів.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

Поле, що перевіряється, має бути присутнім _тільки_ якщо_ присутні всі інші вказані поля.

<a name="rule-prohibited"></a>
#### prohibited

Поле, що перевіряється, має бути відсутнім або порожнім. Поле вважається «порожнім», якщо воно відповідає одному з наступних критеріїв:

<div class="content-list" markdown="1">

- Значення `null`.
- Значення - порожній рядок.
- Значення - порожній масив або порожній об'єкт `Countable`.
- Значення - завантажений файл з порожнім шляхом.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

Поле, що перевіряється, має бути відсутнім або порожнім, якщо поле _інше_ дорівнює будь-якому _значенню_. Поле вважається «порожнім», якщо воно відповідає одному з наступних критеріїв:

<div class="content-list" markdown="1">

- Значення `null`.
- Значення - порожній рядок.
- Значення - порожній масив або порожній об'єкт `Countable`.
- Значення - завантажений файл з порожнім шляхом.

</div>

Якщо потрібна складна логіка умовної заборони, ви можете використовувати метод `Rule::prohibitedIf`. Цей метод приймає логічне значення або закриття. Якщо передано закриття, закриття має повертати значення `true` або `false`, щоб вказати, чи слід заборонити поле, яке перевіряється, чи ні:

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;

    Validator::make($request->all(), [
        'role_id' => Rule::prohibitedIf($request->user()->is_admin),
    ]);

    Validator::make($request->all(), [
        'role_id' => Rule::prohibitedIf(fn () => $request->user()->is_admin),
    ]);

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

Поле, що перевіряється, має бути відсутнім або порожнім, якщо тільки поле _інше_ не дорівнює будь-якому _значенню_. Поле вважається «порожнім», якщо воно відповідає одному з наступних критеріїв:

<div class="content-list" markdown="1">

- Значення `null`.
- Значення - порожній рядок.
- Значення - порожній масив або порожній об'єкт `Countable`.
- Значення - завантажений файл з порожнім шляхом.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

Якщо поле, що перевіряється, не є пропущеним або порожнім, то всі поля в _іншому полі_ повинні бути пропущеними або порожніми. Поле вважається «порожнім», якщо воно відповідає одному з наступних критеріїв:

<div class="content-list" markdown="1">

- Значення `null`.
- Значення - порожній рядок.
- Значення - порожній масив або порожній об'єкт `Countable`.
- Значення - завантажений файл з порожнім шляхом.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

Поле, що перевіряється, має відповідати заданому регулярному виразу.

Внутрішньо це правило використовує функцію PHP `preg_match`. Вказаний шаблон має відповідати формату, який вимагає `preg_match`, а отже, містити допустимі роздільники. Наприклад: `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]  
> При використанні шаблонів `regex` / `not_regex` може знадобитися вказати правила у масиві замість використання розділювачів `|`, особливо якщо регулярний вираз містить символ `|`.

<a name="rule-required"></a>
#### required

Поле, що перевіряється, має бути присутнім у вхідних даних і не бути порожнім. Поле вважається «порожнім», якщо воно відповідає одному з наступних критеріїв:

<div class="content-list" markdown="1">

- Значення `null`.
- Значення - порожній рядок.
- Значення - порожній масив або порожній об'єкт `Countable`.
- Значення - завантажений файл без шляху.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

Поле, що перевіряється, має бути присутнім і не бути порожнім, якщо поле _інше_ поле дорівнює будь-якому _значенню_.

Якщо ви хочете створити складнішу умову для правила `required_if`, ви можете скористатися методом `Rule::requiredIf`. Цей метод приймає логічне значення або закриття. При передачі закриття, закриття має повернути значення `true` або `false`, щоб вказати, чи є поле, що перевіряється, обов'язковим для заповнення:

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;

    Validator::make($request->all(), [
        'role_id' => Rule::requiredIf($request->user()->is_admin),
    ]);

    Validator::make($request->all(), [
        'role_id' => Rule::requiredIf(fn () => $request->user()->is_admin),
    ]);

<a name="rule-required-if-accepted"></a>
#### required_if_accepted:_anotherfield_,...

Поле, що перевіряється, має бути присутнім і не бути порожнім, якщо поле _інше поле_ дорівнює `«yes»`, `«on»`, `1`, `«1»`, `«true»` або `«true»`.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

Поле, що перевіряється, має бути присутнім і не бути порожнім, якщо тільки поле _інше_ не дорівнює будь-якому _значенню_. Це також означає, що _інше_ поле має бути присутнім у даних запиту, якщо тільки _значення_ не дорівнює `null`. Якщо _значення_ дорівнює `null` (`required_unless:name,null`), поле, що перевіряється, буде обов'язковим, якщо тільки поле порівняння не дорівнює `null` або поле порівняння відсутнє в даних запиту.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

Поле, що перевіряється, повинно бути присутнім і не порожнім _тільки_, якщо_ будь-яке з інших вказаних полів присутнє і не порожнє.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

Поле, що перевіряється, повинно бути присутнім і не порожнім _тільки_ якщо_ всі інші вказані поля присутні і не порожні.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

Поле, що перевіряється, має бути присутнім і не повинно бути порожнім _тільки тоді_, коли_ будь-яке з інших вказаних полів порожнє або відсутнє.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

Поле, що перевіряється, має бути присутнім і не повинно бути порожнім _тільки тоді_, коли_ всі інші вказані поля порожні або відсутні.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

Поле, що перевіряється, має бути масивом і містити принаймні вказані ключі.

<a name="rule-same"></a>
#### same:_field_

Введене _поле_ має збігатися з полем, що перевіряється.

<a name="rule-size"></a>
#### size:_value_

Поле, що перевіряється, повинно мати розмір, що відповідає заданому _значенню_. Для рядкових даних _значення_ відповідає кількості символів. Для числових даних _значення_ відповідає заданому цілочисельному значенню (атрибут також повинен мати правило `numeric` або `integer`). Для масиву _size_ відповідає `count` масиву. Для файлів _size_ відповідає розміру файлу в кілобайтах. Розглянемо деякі приклади:

    // Validate that a string is exactly 12 characters long...
    'title' => 'size:12';

    // Validate that a provided integer equals 10...
    'seats' => 'integer|size:10';

    // Validate that an array has exactly 5 elements...
    'tags' => 'array|size:5';

    // Validate that an uploaded file is exactly 512 kilobytes...
    'image' => 'file|size:512';

<a name="rule-starts-with"></a>
#### starts_with:_foo_,_bar_,...

Поле, що перевіряється, повинно починатися з одного з наведених значень.

<a name="rule-string"></a>
#### string

Поле, що перевіряється, має бути рядковим. Якщо ви хочете дозволити, щоб поле також було `null`, ви повинні призначити для нього правило `nullable`.

<a name="rule-timezone"></a>
#### timezone

Поле, що перевіряється, має бути дійсним ідентифікатором часового поясу відповідно до методу `DateTimeZone::listIdentifiers`.

До цього правила перевірки можна також додати аргументи [прийняті методом `DateTimeZone::listIdentifiers`](https://www.php.net/manual/en/datetimezone.listidentifiers.php):

    'timezone' => 'required|timezone:all';

    'timezone' => 'required|timezone:Africa';

    'timezone' => 'required|timezone:per_country,US';

<a name="rule-unique"></a>
#### unique:_table_,_column_

Поле, що перевіряється, не повинно існувати в даній таблиці бази даних.

**Specifying a Custom Table / Column Name:**

Замість того, щоб вказувати ім'я таблиці безпосередньо, ви можете вказати модель Eloquent, яку слід використовувати для визначення імені таблиці:

    'email' => 'unique:App\Models\User,email_address'

Опція `column` може бути використана для вказівки відповідного стовпця бази даних. Якщо параметр `column` не вказано, буде використано назву поля, яке перевіряється.

    'email' => 'unique:users,email_address'

**Specifying a Custom Database Connection**

Іноді вам може знадобитися встановити спеціальне з'єднання для запитів до бази даних, зроблених валідатором. Для цього ви можете додати назву з'єднання до назви таблиці:

    'email' => 'unique:connection.users,email_address'

**Forcing a Unique Rule to Ignore a Given ID:**

Іноді вам може знадобитися проігнорувати певний ідентифікатор під час унікальної перевірки. Наприклад, розглянемо екран «оновлення профілю», який містить ім'я користувача, адресу електронної пошти та місцезнаходження. Ви, ймовірно, захочете перевірити, що адреса електронної пошти є унікальною. Однак, якщо користувач змінює лише поле імені, але не поле електронної пошти, ви не хочете, щоб виникала помилка перевірки, оскільки користувач вже є власником відповідної адреси електронної пошти.

Щоб вказати валідатору ігнорувати ідентифікатор користувача, ми скористаємося класом `Rule` для вільного визначення правила. У цьому прикладі ми також задамо правила перевірки у вигляді масиву замість того, щоб використовувати символ `|` для розділення правил:

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;

    Validator::make($data, [
        'email' => [
            'required',
            Rule::unique('users')->ignore($user->id),
        ],
    ]);

> [!WARNING]  
> Ви ніколи не повинні передавати до методу `ignore` будь-які дані запиту, що контролюються користувачем. Натомість, ви повинні передавати лише згенерований системою унікальний ідентифікатор, такий як автоінкрементований ідентифікатор або UUID з екземпляра моделі Eloquent. В іншому випадку ваш додаток буде вразливим до атаки SQL-ін'єкції.

Замість того, щоб передавати значення ключа моделі до методу `ignore`, ви також можете передати весь екземпляр моделі. Laravel автоматично витягне ключ з моделі:

    Rule::unique('users')->ignore($user)

Якщо у вашій таблиці використовується ім'я стовпця первинного ключа, відмінне від `id`, ви можете вказати ім'я стовпця при виклику методу `ignore`:

    Rule::unique('users')->ignore($user->id, 'user_id')

За замовчуванням правило `unique` перевіряє унікальність стовпця, що відповідає імені атрибута, який перевіряється. Однак, ви можете передати іншу назву стовпця як другий аргумент методу `unique`:

    Rule::unique('users', 'email_address')->ignore($user->id)

**Adding Additional Where Clauses:**

Ви можете вказати додаткові умови запиту, налаштувавши запит за допомогою методу `where`. Наприклад, додамо умову запиту, яка обмежує область пошуку лише тими записами, у яких значення стовпця `account_id` дорівнює `1`:

    'email' => Rule::unique('users')->where(fn (Builder $query) => $query->where('account_id', 1))

<a name="rule-uppercase"></a>
#### uppercase

Поле, що перевіряється, має бути верхнім регістром.

<a name="rule-url"></a>
#### url

Поле, що перевіряється, має бути дійсною URL-адресою.

Якщо ви хочете вказати протоколи URL, які слід вважати дійсними, ви можете передати протоколи як параметри правила перевірки:

```php
'url' => 'url:http,https',

'game' => 'url:minecraft,steam',
```

<a name="rule-ulid"></a>
#### ulid

Поле, що перевіряється, має бути дійсним [Універсально унікальним лексикографічно сортувальним ідентифікатором](https://github.com/ulid/spec) (ULID).

<a name="rule-uuid"></a>
#### uuid

Поле, що перевіряється, має бути дійсним універсальним унікальним ідентифікатором (UUID) за стандартом RFC 4122 (версія 1, 3, 4 або 5).

<a name="conditionally-adding-rules"></a>
## Conditionally Adding Rules

<a name="skipping-validation-when-fields-have-certain-values"></a>
#### Skipping Validation When Fields Have Certain Values

Іноді вам може знадобитися не перевіряти певне поле, якщо інше поле має задане значення. Це можна зробити за допомогою правила перевірки `виключити_якщо`. У цьому прикладі поля `appointment_date` і `doctor_name` не будуть перевірені, якщо поле `has_appointment` має значення `false`:

    use Illuminate\Support\Facades\Validator;

    $validator = Validator::make($data, [
        'has_appointment' => 'required|boolean',
        'appointment_date' => 'exclude_if:has_appointment,false|required|date',
        'doctor_name' => 'exclude_if:has_appointment,false|required|string',
    ]);

Крім того, ви можете використовувати правило `виключити_якщо`, щоб не валідувати певне поле, якщо інше поле не має заданого значення:

    $validator = Validator::make($data, [
        'has_appointment' => 'required|boolean',
        'appointment_date' => 'exclude_unless:has_appointment,true|required|date',
        'doctor_name' => 'exclude_unless:has_appointment,true|required|string',
    ]);

<a name="validating-when-present"></a>
#### Validating When Present

У деяких ситуаціях вам може знадобитися виконати перевірку для поля **тільки**, якщо це поле присутнє у даних, які перевіряються. Щоб швидко це зробити, додайте правило «іноді» до списку правил:

    $v = Validator::make($data, [
        'email' => 'sometimes|required|email',
    ]);

У наведеному вище прикладі поле `email` буде перевірено, тільки якщо воно присутнє в масиві `$data`.

> [!NOTE]  
> Якщо ви намагаєтеся перевірити поле, яке завжди має бути присутнім, але може бути порожнім, ознайомтеся з [цією приміткою про необов'язкові поля](#a-note-on-optional-fields).

<a name="complex-conditional-validation"></a>
#### Complex Conditional Validation

Іноді вам може знадобитися додати правила перевірки, засновані на більш складній умовній логіці. Наприклад, ви можете вимагати, щоб певне поле було заповнене, тільки якщо інше поле має значення більше 100. Або вам може знадобитися, щоб два поля мали певне значення тільки тоді, коли присутнє інше поле. Додавання цих правил валідації не повинно бути складним. Спочатку створіть екземпляр `Validator` зі своїми _статичними правилами_, які ніколи не змінюються:

    use Illuminate\Support\Facades\Validator;

    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'games' => 'required|numeric',
    ]);

Уявімо, що наш веб-додаток призначений для колекціонерів ігор. Якщо колекціонер реєструється в нашому додатку і має більше 100 ігор, ми хочемо, щоб він пояснив, чому він володіє такою кількістю ігор. Наприклад, можливо, він керує магазином з перепродажу ігор, а можливо, йому просто подобається колекціонувати ігри. Щоб умовно додати цю вимогу, ми можемо використати метод «іноді» для екземпляра «Валідатор».

    use Illuminate\Support\Fluent;

    $validator->sometimes('reason', 'required|max:500', function (Fluent $input) {
        return $input->games >= 100;
    });

Перший аргумент, що передається до методу `sometimes` - це назва поля, яке ми умовно перевіряємо. Другий аргумент - список правил, які ми хочемо додати. Якщо закриття, передане як третій аргумент, повертає значення `true`, правила буде додано. Цей метод дозволяє легко створювати складні умовні перевірки. Ви навіть можете додати умовні перевірки для декількох полів одночасно:

    $validator->sometimes(['reason', 'cost'], 'required', function (Fluent $input) {
        return $input->games >= 100;
    });

> [!NOTE]  
> Параметр `$input`, переданий вашому закриттю, буде екземпляром `Illuminate\Support\Fluent` і може бути використаний для доступу до ваших вхідних даних і файлів, що перевіряються.

<a name="complex-conditional-array-validation"></a>
#### Complex Conditional Array Validation

Іноді вам може знадобитися перевірити поле на основі іншого поля у тому самому вкладеному масиві, індекс якого ви не знаєте. У таких ситуаціях ви можете дозволити закриттю отримувати другий аргумент, який буде поточним окремим елементом у масиві, що перевіряється:

    $input = [
        'channels' => [
            [
                'type' => 'email',
                'address' => 'abigail@example.com',
            ],
            [
                'type' => 'url',
                'address' => 'https://example.com',
            ],
        ],
    ];

    $validator->sometimes('channels.*.address', 'email', function (Fluent $input, Fluent $item) {
        return $item->type === 'email';
    });

    $validator->sometimes('channels.*.address', 'url', function (Fluent $input, Fluent $item) {
        return $item->type !== 'email';
    });

Як і параметр `$input`, переданий закриттю, параметр `$item` є екземпляром `Illuminate\Support\Fluent`, коли дані атрибута є масивом; інакше це рядок.

<a name="validating-arrays"></a>
## Validating Arrays

Як описано у [документації до правила перевірки масиву](#rule-array), правило `array` приймає список дозволених ключів масиву. Якщо у масиві присутні додаткові ключі, валідація не пройде:

    use Illuminate\Support\Facades\Validator;

    $input = [
        'user' => [
            'name' => 'Taylor Otwell',
            'username' => 'taylorotwell',
            'admin' => true,
        ],
    ];

    Validator::make($input, [
        'user' => 'array:name,username',
    ]);

Загалом, ви завжди повинні вказувати ключі масиву, яким дозволено бути присутніми у вашому масиві. Інакше методи валідатора `validate` і `validated` повернуть усі перевірені дані, включно з масивом і всіма його ключами, навіть якщо ці ключі не було перевірено іншими правилами перевірки вкладених масивів.

<a name="validating-nested-array-input"></a>
### Validating Nested Array Input

Перевірка вкладених масивів полів вводу на основі форми не повинна бути складною. Ви можете використовувати «крапкову нотацію» для валідації атрибутів у масиві. Наприклад, якщо вхідний HTTP-запит містить поле `photos[profile]`, ви можете перевірити його таким чином:

    use Illuminate\Support\Facades\Validator;

    $validator = Validator::make($request->all(), [
        'photos.profile' => 'required|image',
    ]);

Ви також можете перевірити кожен елемент масиву. Наприклад, щоб переконатися, що кожен імейл у полі введення масиву є унікальним, ви можете зробити наступне:

    $validator = Validator::make($request->all(), [
        'person.*.email' => 'email|unique:users',
        'person.*.first_name' => 'required_with:person.*.last_name',
    ]);

Так само ви можете використовувати символ `*`, вказуючи [спеціальні повідомлення перевірки у ваших мовних файлах](#custom-messages-for-specific-attributes), що спрощує використання єдиного повідомлення перевірки для полів на основі масивів:

    'custom' => [
        'person.*.email' => [
            'unique' => 'Each person must have a unique email address',
        ]
    ],

<a name="accessing-nested-array-data"></a>
#### Accessing Nested Array Data

Іноді вам може знадобитися отримати доступ до значення для певного елемента вкладеного масиву при призначенні правил перевірки для атрибута. Ви можете зробити це за допомогою методу `Rule::forEach`. Метод `forEach` приймає закриття, яке буде викликатися для кожної ітерації атрибута масиву, що перевіряється, і отримуватиме значення атрибута та явне, повністю розгорнуте ім'я атрибута. Закриття має повернути масив правил, які слід застосувати до елемента масиву:

    use App\Rules\HasPermission;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;

    $validator = Validator::make($request->all(), [
        'companies.*.id' => Rule::forEach(function (string|null $value, string $attribute) {
            return [
                Rule::exists(Company::class, 'id'),
                new HasPermission('manage-company', $value),
            ];
        }),
    ]);

<a name="error-message-indexes-and-positions"></a>
### Error Message Indexes and Positions

Під час перевірки масивів ви можете посилатися на індекс або позицію певного елемента, який не пройшов перевірку, у повідомленні про помилку, яке виводить ваша програма. Для цього ви можете включити заповнювачі `:index` (починається з `0`) і `:position` (починається з `1`) у ваше [користувацьке повідомлення про помилку](#manual-customizing-the-error-messages):

    use Illuminate\Support\Facades\Validator;

    $input = [
        'photos' => [
            [
                'name' => 'BeachVacation.jpg',
                'description' => 'A photo of my beach vacation!',
            ],
            [
                'name' => 'GrandCanyon.jpg',
                'description' => '',
            ],
        ],
    ];

    Validator::validate($input, [
        'photos.*.description' => 'required',
    ], [
        'photos.*.description.required' => 'Please describe photo #:position.',
    ]);

У наведеному вище прикладі валідація не пройде і користувач отримає помилку _«Будь ласка, опишіть фото #2.»_.

При необхідності ви можете посилатися на більш глибоко вкладені індекси і позиції через `другий-індекс`, `друга-позиція`, `третій-індекс`, `третя-позиція` і т.д.

    'photos.*.attributes.*.string' => 'Invalid attribute for photo #:second-position.',

<a name="validating-files"></a>
## Validating Files

Laravel надає різноманітні правила валідації, які можна використовувати для перевірки завантажених файлів, такі як `mimes`, `image`, `min` і `max`. Хоча ви можете вказати ці правила індивідуально при перевірці файлів, Laravel також пропонує зручний конструктор правил перевірки файлів, який може виявитися для вас зручним:

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rules\File;

    Validator::validate($input, [
        'attachment' => [
            'required',
            File::types(['mp3', 'wav'])
                ->min(1024)
                ->max(12 * 1024),
        ],
    ]);

Якщо ваша програма приймає зображення, завантажені користувачами, ви можете використовувати метод конструктора `image` правила `File`, щоб вказати, що завантажений файл має бути зображенням. Крім того, правило `dimensions` можна використовувати для обмеження розмірів зображення:

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;
    use Illuminate\Validation\Rules\File;

    Validator::validate($input, [
        'photo' => [
            'required',
            File::image()
                ->min(1024)
                ->max(12 * 1024)
                ->dimensions(Rule::dimensions()->maxWidth(1000)->maxHeight(500)),
        ],
    ]);

> [!NOTE]  
> Докладнішу інформацію про перевірку розмірів зображень можна знайти у [документації до правил визначення розмірів](#rule-dimensions).

<a name="validating-files-file-sizes"></a>
#### File Sizes

Для зручності мінімальний та максимальний розмір файлу можна вказати у вигляді рядка з суфіксом, що вказує на одиниці виміру розміру файлу. Підтримуються суфікси `kb`, `mb`, `gb` та `tb`:

```php
File::image()
    ->min('1kb')
    ->max('10mb')
```

<a name="validating-files-file-types"></a>
#### File Types

Незважаючи на те, що при виклику методу `types` вам потрібно вказати лише розширення, цей метод фактично перевіряє MIME-тип файлу, читаючи вміст файлу і вгадуючи його MIME-тип. Повний список MIME-типів і відповідних їм розширень можна знайти за цим посиланням:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="validating-passwords"></a>
## Validating Passwords

Щоб переконатися, що паролі мають достатній рівень складності, ви можете використовувати об'єкт правил `Password` у Laravel:

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rules\Password;

    $validator = Validator::make($request->all(), [
        'password' => ['required', 'confirmed', Password::min(8)],
    ]);

Об'єкт правила `Пароль` дозволяє вам легко налаштувати вимоги до складності пароля для вашої програми, наприклад, вказати, що пароль повинен містити принаймні одну літеру, цифру, символ або символи у змішаному регістрі:

    // Require at least 8 characters...
    Password::min(8)

    // Require at least one letter...
    Password::min(8)->letters()

    // Require at least one uppercase and one lowercase letter...
    Password::min(8)->mixedCase()

    // Require at least one number...
    Password::min(8)->numbers()

    // Require at least one symbol...
    Password::min(8)->symbols()

Крім того, ви можете переконатися, що пароль не був скомпрометований під час витоку публічних даних про витік паролів, використовуючи метод «безкомпромісності»:

    Password::min(8)->uncompromised()

Внутрішній об'єкт правила `Password` використовує модель [k-Anonymity](https://en.wikipedia.org/wiki/K-anonymity), щоб визначити, чи був витік пароля через сервіс [haveibeenpwned.com](https://haveibeenpwned.com) без шкоди для конфіденційності або безпеки користувача.

За замовчуванням, якщо пароль з'являється хоча б один раз у витоку даних, він буде вважатися скомпрометованим. Ви можете налаштувати цей поріг за допомогою першого аргументу методу `uncompromised`:

    // Переконайтеся, що пароль з'являється менше 3 разів в одному і тому ж витоку даних...
    Password::min(8)->uncompromised(3);

Звичайно, ви можете об'єднати всі методи в ланцюжок у наведених вище прикладах:

    Password::min(8)
        ->letters()
        ->mixedCase()
        ->numbers()
        ->symbols()
        ->uncompromised()

<a name="defining-default-password-rules"></a>
#### Defining Default Password Rules

Можливо, вам буде зручно вказати правила перевірки паролів за замовчуванням в одному місці вашої програми. Ви можете легко зробити це за допомогою методу `Password::defaults`, який приймає закриття. Закриття, передане методу `defaults`, має повертати конфігурацію правила перевірки паролів за замовчуванням. Зазвичай правило `defaults` слід викликати у методі `boot` одного з постачальників послуг вашої програми:

```php
use Illuminate\Validation\Rules\Password;

/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Password::defaults(function () {
        $rule = Password::min(8);

        return $this->app->isProduction()
                    ? $rule->mixedCase()->uncompromised()
                    : $rule;
    });
}
```

Потім, коли ви захочете застосувати правила за замовчуванням до певного пароля, що проходить перевірку, ви можете викликати метод `defaults` без аргументів:

    'password' => ['required', Password::defaults()],

Іноді вам може знадобитися додати додаткові правила перевірки до стандартних правил перевірки паролів. Для цього ви можете скористатися методом `rules`:

    use App\Rules\ZxcvbnRule;

    Password::defaults(function () {
        $rule = Password::min(8)->rules([new ZxcvbnRule]);

        // ...
    });

<a name="custom-validation-rules"></a>
## Custom Validation Rules

<a name="using-rule-objects"></a>
### Using Rule Objects

Laravel надає безліч корисних правил валідації, але ви можете вказати деякі з них самостійно. Одним із способів реєстрації користувацьких правил перевірки є використання об'єктів правил. Щоб створити новий об'єкт правила, ви можете скористатися командою `make:rule` Artisan. Давайте скористаємося цією командою для створення правила, яке перевіряє, чи є рядок великими літерами. Laravel помістить нове правило до каталогу `app/Rules`. Якщо цього каталогу не існує, Laravel створить його, коли ви виконаєте команду Artisan для створення правила:

```shell
php artisan make:rule Uppercase
```

Після того, як правило створено, ми можемо визначити його поведінку. Об'єкт правила містить єдиний метод: `validate`. Цей метод отримує ім'я атрибута, його значення і зворотний виклик, який має бути викликаний у разі невдачі з повідомленням про помилку перевірки:

    <?php

    namespace App\Rules;

    use Closure;
    use Illuminate\Contracts\Validation\ValidationRule;

    class Uppercase implements ValidationRule
    {
        /**
         * Run the validation rule.
         */
        public function validate(string $attribute, mixed $value, Closure $fail): void
        {
            if (strtoupper($value) !== $value) {
                $fail('The :attribute must be uppercase.');
            }
        }
    }

Після того, як правило визначено, ви можете приєднати його до валідатора, передавши екземпляр об'єкта правила разом з іншими валідаторними правилами:

    use App\Rules\Uppercase;

    $request->validate([
        'name' => ['required', 'string', new Uppercase],
    ]);

#### Translating Validation Messages

Замість буквального повідомлення про помилку у закритті `$fail` ви можете вказати [ключ рядка перекладу](/docs/{{version}}/localization) і доручити Laravel перекласти повідомлення про помилку:

    if (strtoupper($value) !== $value) {
        $fail('validation.uppercase')->translate();
    }

Якщо потрібно, ви можете вказати замінники заповнювачів і бажану мову як перший і другий аргументи методу `translate`:

    $fail('validation.location')->translate([
        'value' => $this->value,
    ], 'fr')

#### Accessing Additional Data

Якщо ваш користувацький клас правил перевірки потребує доступу до всіх інших даних, що проходять перевірку, ваш клас правил може реалізувати інтерфейс `Illuminate\Contracts\Validation\DataAwareRule`. Цей інтерфейс вимагає, щоб ваш клас визначив метод `setData`. Цей метод буде автоматично викликано Laravel (перед початком перевірки) з усіма даними, що перевіряються:

    <?php

    namespace App\Rules;

    use Illuminate\Contracts\Validation\DataAwareRule;
    use Illuminate\Contracts\Validation\ValidationRule;

    class Uppercase implements DataAwareRule, ValidationRule
    {
        /**
         * All of the data under validation.
         *
         * @var array<string, mixed>
         */
        protected $data = [];

        // ...

        /**
         * Встановіть дані на перевірку.
         *
         * @param  array<string, mixed>  $data
         */
        public function setData(array $data): static
        {
            $this->data = $data;

            return $this;
        }
    }

Або, якщо ваше правило валідації вимагає доступу до екземпляра валідатора, який виконує валідацію, ви можете реалізувати інтерфейс `ValidatorAwareRule`:

    <?php

    namespace App\Rules;

    use Illuminate\Contracts\Validation\ValidationRule;
    use Illuminate\Contracts\Validation\ValidatorAwareRule;
    use Illuminate\Validation\Validator;

    class Uppercase implements ValidationRule, ValidatorAwareRule
    {
        /**
         * The validator instance.
         *
         * @var \Illuminate\Validation\Validator
         */
        protected $validator;

        // ...

        /**
         * Встановіть поточний валідатор.
         */
        public function setValidator(Validator $validator): static
        {
            $this->validator = $validator;

            return $this;
        }
    }

<a name="using-closures"></a>
### Using Closures

Якщо вам потрібна функціональність користувацького правила лише один раз у вашій програмі, ви можете використовувати закриття замість об'єкта правила. Закриття отримує ім'я атрибута, значення атрибута та зворотний виклик `$fail`, який слід викликати, якщо валідація не пройшла:

    use Illuminate\Support\Facades\Validator;
    use Closure;

    $validator = Validator::make($request->all(), [
        'title' => [
            'required',
            'max:255',
            function (string $attribute, mixed $value, Closure $fail) {
                if ($value === 'foo') {
                    $fail("The {$attribute} is invalid.");
                }
            },
        ],
    ]);

<a name="implicit-rules"></a>
### Implicit Rules

За замовчуванням, коли атрибут, який перевіряється, відсутній або містить порожній рядок, звичайні правила перевірки, включно з користувацькими, не виконуються. Наприклад, правило [`unique`](#rule-unique) не буде застосовано до порожнього рядка:

    use Illuminate\Support\Facades\Validator;

    $rules = ['name' => 'unique:users,name'];

    $input = ['name' => ''];

    Validator::make($input, $rules)->passes(); // true

Для того, щоб користувацьке правило працювало навіть тоді, коли атрибут порожній, правило має передбачати, що атрибут є обов'язковим. Щоб швидко створити новий об'єкт неявного правила, ви можете скористатися командою `make:rule` Artisan з опцією `--implicit`:

```shell
php artisan make:rule Uppercase --implicit
```

> [!WARNING]  
> «Неявне» правило лише «передбачає», що атрибут є обов'язковим. Чи дійсно воно робить недійсним відсутній або порожній атрибут, залежить від вас.
