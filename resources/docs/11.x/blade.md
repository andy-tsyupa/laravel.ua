# Blade шаблони

- [Вступ](#introduction)
    - [Вищий рівень Blade за допомогою Livewire](#supercharging-blade-with-livewire)
- [Відображення даних](#displaying-data)
    - [Кодування об'єктів HTML](#html-entity-encoding)
    - [Фреймворки Blade та JavaScript](#blade-and-javascript-frameworks)
- [Blade дерективи](#blade-directives)
    - [Оператори If](#if-statements)
    - [Оператори Switch](#switch-statements)
    - [Цикли](#loops)
    - [Змінні Loop](#the-loop-variable)
    - [Умовні класи](#conditional-classes)
    - [Додаткові атрибути](#additional-attributes)
    - [Підключення дочірніх шаблонів](#including-subviews)
    - [Деректива `@once`](#the-once-directive)
    - [Вихідний PHP](#raw-php)
    - [Коментарі](#comments)
- [Компоненти](#components)
    - [Відмальовування компонентів](#rendering-components)
    - [Передача даних до компонентів](#passing-data-to-components)
    - [Атрибути компонентів](#component-attributes)
    - [Зарезервовані ключові слова](#reserved-keywords)
    - [Слоти](#slots)
    - [Вбудовані шаблони компонентів](#inline-component-views)
    - [Динамічні компоненти](#dynamic-components)
    - [Ручна реєстрація компонентів](#manually-registering-components)
- [Анонімні компоненти](#anonymous-components)
    - [Анонімні Index компоненти](#anonymous-index-components)
    - [Властивості / Атрибути даних](#data-properties-attributes)
    - [Доступ до батьківських даних](#accessing-parent-data)
    - [Анонімні шляхи до компонентів](#anonymous-component-paths)
- [Створенн макетів](#building-layouts)
    - [Макети з використанням компонентів](#layouts-using-components)
    - [Макети з використанням успадкування шаблонів](#layouts-using-template-inheritance)
- [Форми](#forms)
    - [Поле CSRF](#csrf-field)
    - [Поле Method](#method-field)
    - [Помилки валідації](#validation-errors)
- [Стеки](#stacks)
- [Зовнішні служби](#service-injection)
- [Рендиринг шаблонів Blade з рядка](#rendering-inline-blade-templates)
- [Рендиринг фрагментів Blade](#rendering-blade-fragments)
- [Розширення Blade](#extending-blade)
    - [Користувацькі обробники вводу](#custom-echo-handlers)
    - [Користувацькі операто If](#custom-if-statements)

<a name="introduction"></a>
## Вступ

Blade - це простий, але потужний движок шаблонів, який входить до складу Laravel. На відміну від деяких PHP-шаблонізаторів, Blade не обмежує вас у використанні звичайного PHP-коду у ваших шаблонах. Насправді, всі шаблони Blade компілюються у звичайний PHP-код і кешуються до тих пір, поки їх не буде змінено, а це означає, що Blade додає до вашого додатку практично нульові накладні витрати для вашого додатку. Файли шаблонів Blade мають розширення `.blade.php` і зазвичай зберігаються в каталозі `resources/views`.

Подання блейдів можуть бути повернуті з маршрутів або контролерів за допомогою глобального помічника `view`. Звичайно, як зазначено у документації до [views](/docs/{{version}}/views), дані можуть бути передані до подання Blade за допомогою другого аргументу допоміжного засобу `view`:

    Route::get('/', function () {
        return view('greeting', ['name' => 'Finn']);
    });

<a name="supercharging-blade-with-livewire"></a>
### Вищий рівень Blade за допомогою Livewire

Хочете підняти свої шаблони Blade на новий рівень і легко створювати динамічні інтерфейси? Перевірте [Laravel Livewire](https://livewire.laravel.com). Livewire дозволяє писати компоненти Blade, доповнені динамічною функціональністю, яка зазвичай можлива лише у фронтенд-фреймворках, таких як React або Vue, забезпечуючи чудовий підхід до створення сучасних реактивних фронтендів без складнощів, рендерингу на стороні клієнта або етапів побудови, властивих багатьом JavaScript-фреймворкам.

<a name="displaying-data"></a>
## Відображення даних

Ви можете відображати дані, які передаються до ваших подань Blade, обгорнувши змінну фігурними дужками. Наприклад, за таким маршрутом:

    Route::get('/', function () {
        return view('welcome', ['name' => 'Samantha']);
    });

Ви можете вивести вміст змінної `name` таким чином:

```blade
Hello, {{ $name }}.
```

> [!NOTE]  
> Оператори `{{ }}` у Blade автоматично надсилаються через функцію PHP `htmlspecialchars` для запобігання XSS-атакам.

Ви не обмежуєтеся відображенням вмісту змінних, переданих у подання. Ви також можете повторювати результати будь-якої функції PHP. Фактично, ви можете помістити будь-який PHP-код, який ви бажаєте, всередину оператора відлуння Blade:

```blade
The current UNIX timestamp is {{ time() }}.
```

<a name="html-entity-encoding"></a>
### Кодування об'єктів HTML

За замовчуванням Blade (і функція Laravel `e`) кодує HTML-об'єкти подвійним кодуванням. Якщо ви хочете вимкнути подвійне кодування, викличте метод `Blade::withoutDoubleEncoding` з методу `boot` вашого `AppServiceProvider`:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\Blade;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            Blade::withoutDoubleEncoding();
        }
    }

<a name="displaying-unescaped-data"></a>
#### Відображення неформатованих даних

За замовчуванням, оператори Blade `{{ }}` автоматично передаються через функцію PHP `htmlspecialchars` для запобігання XSS-атакам. Якщо ви не хочете, щоб ваші дані екранувалися, ви можете використовувати наступний синтаксис:

```blade
Hello, {!! $name !!}.
```

> [!WARNING]  
> Будьте дуже обережні, повторюючи вміст, наданий користувачами вашого додатку. Для запобігання XSS-атакам при відображенні даних, наданих користувачами, зазвичай слід використовувати екранований синтаксис подвійної фігурної дужки.

<a name="blade-and-javascript-frameworks"></a>
### Фреймворки Blade та JavaScript

Оскільки багато фреймворків JavaScript також використовують «фігурні» дужки, щоб вказати, що певний вираз має відображатися у браузері, ви можете використовувати символ `@`, щоб повідомити рушій рендерингу Blade, що вираз має залишатися недоторканим. Наприклад:

```blade
<h1>Laravel</h1>

Hello, @{{ name }}.
```

У цьому прикладі символ `@` буде видалено Blade, але вираз `{{ name }}` залишиться недоторканим рушієм Blade, що дасть змогу відобразити його у вашому JavaScript-фреймворку.

Символ `@` також може використовуватися для уникнення директив Blade:

```blade
{{-- Blade template --}}
@@if()

<!-- HTML output -->
@if()
```

<a name="rendering-json"></a>
#### Рендеринг JSON

Іноді ви можете передати масив у ваше подання з наміром відрендерити його як JSON, щоб ініціалізувати змінну JavaScript. Наприклад:

```blade
<script>
    var app = <?php echo json_encode($array); ?>;
</script>
```

Однак замість ручного виклику `json_encode` ви можете скористатися директивою методу `Illuminate\Support\Js::from`. Метод `from` приймає ті самі аргументи, що і функція PHP `json_encode`, однак він гарантує, що результуючий JSON буде належним чином екрановано для включення в лапки HTML. Метод `from` поверне рядок `JSON.parse` оператора JavaScript, який перетворить заданий об'єкт або масив у коректний об'єкт JavaScript:

```blade
<script>
    var app = {{ Illuminate\Support\Js::from($array) }};
</script>
```

Останні версії каркасу додатків Laravel включають фасад `Js`, який забезпечує зручний доступ до цієї функціональності у ваших шаблонах Blade:

```blade
<script>
    var app = {{ Js::from($array) }};
</script>
```

> [!WARNING]  
> Ви повинні використовувати метод `Js::from` тільки для перетворення існуючих змінних у JSON. Шаблони Blade засновані на регулярних виразах і спроби передати в директиву складний вираз можуть призвести до непередбачуваних збоїв.

<a name="the-at-verbatim-directive"></a>
#### Директива «дослівно

Якщо ви відображаєте змінні JavaScript у великій частині вашого шаблону, ви можете обернути HTML у директиву `@verbatim`, щоб не додавати символ `@` до кожного оператора ехо-сигналу Blade:

```blade
@verbatim
    <div class="container">
        Hello, {{ name }}.
    </div>
@endverbatim
```

<a name="blade-directives"></a>
## Blade дерективи

На додаток до успадкування шаблонів і відображення даних, Blade також надає зручні комбінації клавіш для загальних структур управління PHP, таких як умовні оператори і цикли. Ці комбінації забезпечують дуже чистий, лаконічний спосіб роботи зі структурами керування PHP, залишаючись при цьому знайомими для їхніх аналогів у PHP.

<a name="if-statements"></a>
### Оператори If

Ви можете створювати оператори `if` за допомогою директив `@if`, `@elseif`, `@else` та `@endif`. Ці директиви працюють так само, як і їхні аналоги у PHP:

```blade
@if (count($records) === 1)
    I have one record!
@elseif (count($records) > 1)
    I have multiple records!
@else
    I don't have any records!
@endif
```

Для зручності у Blade також передбачено директиву `@unless`:

```blade
@unless (Auth::check())
    You are not signed in.
@endunless
```

На додаток до вже розглянутих умовних директив, директиви `@isset` і `@empty` можна використовувати як зручні ярлики для відповідних функцій PHP:

```blade
@isset($records)
    // $records is defined and is not null...
@endisset

@empty($records)
    // $records is "empty"...
@endempty
```

<a name="authentication-directives"></a>
#### Директиви автентифікації

Директиви `@auth` та `@guest` можна використовувати для швидкого визначення, чи є поточний користувач [автентифіковано](/docs/{{version}}/authentication) або є гостем:

```blade
@auth
    // The user is authenticated...
@endauth

@guest
    // The user is not authenticated...
@endguest
```

Якщо потрібно, ви можете вказати захист автентифікації, який слід перевіряти при використанні директив `@auth` і `@guest`:

```blade
@auth('admin')
    // Користувача автентифіковано...
@endauth

@guest('admin')
    // Користувач не автентифікований...
@endguest
```

<a name="environment-directives"></a>
#### Директиви про навколишнє середовище

Перевірити, чи працює програма у виробничому середовищі, можна за допомогою директиви `@production`:

```blade
@production
    // Специфічний зміст виробництва...
@endproduction
```

Або ви можете визначити, чи працює програма у певному середовищі за допомогою директиви `@env`:

```blade
@env('staging')
    // Додаток працює в режимі «стадіювання»...
@endenv

@env(['staging', 'production'])
    // Додаток знаходиться в стадії «стадіювання» або «виробництва»...
@endenv
```

<a name="section-directives"></a>
#### Директиви розділу

Ви можете визначити, чи має секція успадкування шаблону вміст за допомогою директиви `@hasSection`:

```blade
@hasSection('navigation')
    <div class="pull-right">
        @yield('navigation')
    </div>

    <div class="clearfix"></div>
@endif
```

Ви можете використовувати директиву `sectionMissing`, щоб визначити, що розділ не має вмісту:

```blade
@sectionMissing('navigation')
    <div class="pull-right">
        @include('default-navigation')
    </div>
@endif
```

<a name="session-directives"></a>
#### Директиви сесії

Директива `@session` може бути використана для визначення того, чи є [сесія](/docs/{{version}}/session) значення існує. Якщо значення сеансу існує, буде обчислено вміст шаблону у директивах `@session` та `@endsession`. У вмісті директиви `@session` ви можете використовувати ехо-передачу змінної `$value` для відображення значення сеансу:

```blade
@session('status')
    <div class="p-4 bg-green-100">
        {{ $value }}
    </div>
@endsession
```

<a name="switch-statements"></a>
### Оператори Switch

Оператори перемикання можна побудувати за допомогою директив `@switch`, `@case`, `@break`, `@default` та `@endswitch`:

```blade
@switch($i)
    @case(1)
        First case...
        @break

    @case(2)
        Second case...
        @break

    @default
        Default case...
@endswitch
```

<a name="loops"></a>
### Цикли

На додаток до умовних операторів, Blade надає прості директиви для роботи з циклічними структурами PHP. Знову ж таки, кожна з цих директив працює так само, як і їхні аналоги у PHP:

```blade
@for ($i = 0; $i < 10; $i++)
    The current value is {{ $i }}
@endfor

@foreach ($users as $user)
    <p>This is user {{ $user->id }}</p>
@endforeach

@forelse ($users as $user)
    <li>{{ $user->name }}</li>
@empty
    <p>No users</p>
@endforelse

@while (true)
    <p>I'm looping forever.</p>
@endwhile
```

> [!NOTE]  
> Під час ітерації в циклі `foreach` ви можете використовувати [loop variable](#the-loop-variable) щоб отримати цінну інформацію про цикл, наприклад, чи перебуваєте ви на першій або останній ітерації циклу.

При використанні циклів ви також можете пропустити поточну ітерацію або завершити цикл за допомогою директив `@continue` та `@break`:

```blade
@foreach ($users as $user)
    @if ($user->type == 1)
        @continue
    @endif

    <li>{{ $user->name }}</li>

    @if ($user->number == 5)
        @break
    @endif
@endforeach
```

Ви також можете включити умову продовження або перерви в декларацію директиви:

```blade
@foreach ($users as $user)
    @continue($user->type == 1)

    <li>{{ $user->name }}</li>

    @break($user->number == 5)
@endforeach
```

<a name="the-loop-variable"></a>
### Змінні Loop

Під час виконання циклу `foreach` всередині циклу буде доступна змінна `$loop`. Ця змінна надає доступ до деяких корисних бітів інформації, таких як поточний індекс циклу і те, чи це перша або остання ітерація циклу:

```blade
@foreach ($users as $user)
    @if ($loop->first)
        This is the first iteration.
    @endif

    @if ($loop->last)
        This is the last iteration.
    @endif

    <p>This is user {{ $user->id }}</p>
@endforeach
```

Якщо ви перебуваєте у вкладеному циклі, ви можете отримати доступ до змінної `$loop` батьківського циклу через властивість `parent`:

```blade
@foreach ($users as $user)
    @foreach ($user->posts as $post)
        @if ($loop->parent->first)
            Це перша ітерація батьківського циклу.
        @endif
    @endforeach
@endforeach
```

Змінна `$loop` також містить безліч інших корисних властивостей:

| Власність           | Опис                                            |
|--------------------|--------------------------------------------------------|
| `$loop->index`     | Індекс поточної ітерації циклу (починається з 0).      |
| `$loop->iteration` | Поточна ітерація циклу (починається з 1).              |
| `$loop->remaining` | Ітерації, що залишилися в циклі.                       |
| `$loop->count`     | Загальна кількість елементів у масиві, що ітерується.  |
| `$loop->first`     | Чи це перша ітерація циклу.                            |
| `$loop->last`      | Чи це остання ітерація циклу.                          |
| `$loop->even`      | Чи це рівномірна ітерація через цикл.                  |
| `$loop->odd`       | Чи це непарна ітерація через цикл.                     |
| `$loop->depth`     | Рівень вкладеності поточного циклу.                    |
| `$loop->parent`    | У вкладеному циклі - змінна батьківського циклу.       |

<a name="conditional-classes"></a>
### Умовні класи

Директива `@class` умовно компілює рядок класів CSS. Директива приймає масив класів, де ключ масиву містить клас або класи, які ви хочете додати, а значення є логічним виразом. Якщо елемент масиву має числовий ключ, він завжди буде включений до списку класів, що відображається:

```blade
@php
    $isActive = false;
    $hasError = true;
@endphp

<span @class([
    'p-4',
    'font-bold' => $isActive,
    'text-gray-500' => ! $isActive,
    'bg-red' => $hasError,
])></span>

<span class="p-4 text-gray-500 bg-red"></span>
```

Аналогічно, директива `@style` може бути використана для умовного додавання вбудованих CSS-стилів до HTML-елемента:

```blade
@php
    $isActive = true;
@endphp

<span @style([
    'background-color: red',
    'font-weight: bold' => $isActive,
])></span>

<span style="background-color: red; font-weight: bold;"></span>
```

<a name="additional-attributes"></a>
### Додаткові атрибути

Для зручності ви можете використовувати директиву `@checked`, щоб легко вказати, чи є заданий HTML-прапорець «перевіреним». Ця директива буде повторювати `checked`, якщо задана умова набуває значення `true`:

```blade
<input type="checkbox"
        name="active"
        value="active"
        @checked(old('active', $user->active)) />
```

Аналогічно, директива `@selected` може бути використана, щоб вказати, що даний параметр вибору має бути «вибраним»:

```blade
<select name="version">
    @foreach ($product->versions as $version)
        <option value="{{ $version }}" @selected(old('version') == $version)>
            {{ $version }}
        </option>
    @endforeach
</select>
```

Додатково, директива `@disabled` може бути використана, щоб вказати, що даний елемент має бути «вимкненим»:

```blade
<button type="submit" @disabled($errors->isNotEmpty())>Submit</button>
```

Крім того, директива `@readonly` може бути використана для того, щоб вказати, що даний елемент має бути «тільки для читання»:

```blade
<input type="email"
        name="email"
        value="email@laravel.com"
        @readonly($user->isNotAdmin()) />
```

Крім того, директива `@required` може бути використана, щоб вказати, що даний елемент має бути «обов'язковим»:

```blade
<input type="text"
        name="title"
        value="title"
        @required($user->isAdmin()) />
```

<a name="including-subviews"></a>
### Підключення дочірніх шаблонів

> [!NOTE]  
> Хоча ви можете вільно використовувати директиву `@include`, Blade [components](#components) надають подібну функціональність і мають кілька переваг над директивою `@include`, наприклад, зв'язування даних та атрибутів.

Директива `@include` у Blade дозволяє вам включити подання Blade з іншого подання. Усі змінні, доступні для батьківського подання, будуть доступні для включеного подання:

```blade
<div>
    @include('shared.errors')

    <form>
        <!-- Form Contents -->
    </form>
</div>
```

Незважаючи на те, що включене подання успадкує всі дані, доступні у батьківському поданні, ви також можете передати масив додаткових даних, які мають бути доступні включеному поданню:

```blade
@include('view.name', ['status' => 'complete'])
```

Якщо ви спробуєте `@include` неіснуючий вид, Laravel видасть помилку. Якщо ви бажаєте включити представлення, яке може бути присутнім, а може і не бути, вам слід використовувати директиву `@includeIf`:

```blade
@includeIf('view.name', ['status' => 'complete'])
```

Якщо ви хочете `@включити` подання, якщо заданий логічний вираз набуває значення `істина` або `хибність`, ви можете використати директиви `@включити коли` та `@включити якщо`, ви можете використати директиви `@включити якщо`:

```blade
@includeWhen($boolean, 'view.name', ['status' => 'complete'])

@includeUnless($boolean, 'view.name', ['status' => 'complete'])
```

Щоб включити перше подання, яке існує з заданого масиву подань, ви можете використати директиву `includeFirst`:

```blade
@includeFirst(['custom.admin', 'admin'], ['status' => 'complete'])
```

> [!WARNING]  
> Вам слід уникати використання констант `__DIR__` та `__FILE__` у ваших поданнях Blade, оскільки вони посилатимуться на місце розташування кешованого, скомпільованого подання.

<a name="rendering-views-for-collections"></a>
#### Створення подань для колекцій

Ви можете об'єднати цикли та інклуди в один рядок за допомогою директиви Blade `@each`:

```blade
@each('view.name', $jobs, 'job')
```

Перший аргумент директиви `@each` - це вид, який потрібно відрендерити для кожного елемента масиву або колекції. Другий аргумент - це масив або колекція, над якими ви хочете ітераційно переглянути, а третій аргумент - це ім'я змінної, яке буде присвоєно поточному ітераційному елементу у поданні. Так, наприклад, якщо ви виконуєте ітерацію над масивом `jobs`, зазвичай ви хочете отримати доступ до кожної роботи як до змінної `job` у поданні. Ключ масиву для поточної ітерації буде доступний як змінна `key` у поданні.

You may also pass a fourth argument to the `@each` directive. This argument determines the view that will be rendered if the given array is empty.

```blade
@each('view.name', $jobs, 'job', 'view.empty')
```

> [!WARNING]  
> Представлення, що рендериться за допомогою `@each`, не успадковують змінні від батьківського представлення. Якщо дочірньому представленню потрібні ці змінні, слід використовувати директиви `@foreach` та `@include`.

<a name="the-once-directive"></a>
### Деректива `@once`

Директива `@once` дозволяє визначити частину шаблону, яка буде оброблятися лише один раз за цикл рендерингу. Це може бути корисно для виштовхування заданого фрагмента JavaScript у заголовок сторінки за допомогою [стеки](#stacks). Наприклад, якщо ви рендерите заданий [компонент](#components) У циклі ви можете виштовхувати JavaScript у заголовок лише при першому рендерингу компонента:

```blade
@once
    @push('scripts')
        <script>
            // Ваш власний JavaScript...
        </script>
    @endpush
@endonce
```

Оскільки директива `@once` часто використовується разом з директивами `@push` або `@prepend`, для вашої зручності доступні директиви `@pushOnce` і `@prependOnce`:

```blade
@pushOnce('scripts')
    <script>
        // Ваш власний JavaScript...
    </script>
@endPushOnce
```

<a name="raw-php"></a>
### Вихідний PHP

У деяких ситуаціях корисно вбудовувати PHP-код у ваші перегляди. Ви можете використовувати директиву Blade `@php` для виконання блоку звичайного PHP у вашому шаблоні:

```blade
@php
    $counter = 1;
@endphp
```

Або, якщо вам потрібно використовувати PHP лише для імпорту класу, ви можете використати директиву `@use`:

```blade
@use('App\Models\Flight')
```

До директиви `@use` можна додати другий аргумент для псевдоніму імпортованого класу:

```php
@use('App\Models\Flight', 'FlightModel')
```

<a name="comments"></a>
### Коментарі

Blade також дозволяє вам визначати коментарі у ваших поданнях. Однак, на відміну від HTML-коментарів, Blade-коментарі не включаються до HTML, що повертається вашим додатком:

```blade
{{-- Цей коментар не буде присутній у відрендереному HTML --}}
```

<a name="components"></a>
## Компоненти

Компоненти та слоти мають ті самі переваги, що й секції, макети та включення, але дехто може знайти ментальну модель компонентів та слотів простішою для розуміння. Існує два підходи до написання компонентів: компоненти на основі класів та анонімні компоненти.

Щоб створити компонент на основі класу, ви можете скористатися командою `make:component` Artisan. Щоб проілюструвати використання компонентів, ми створимо простий компонент `Alert`. Команда `make:component` розмістить компонент у каталозі `app/View/Components`:

```shell
php artisan make:component Alert
```

Команда `make:component` також створить шаблон подання для компонента. Вигляд буде розміщено у каталозі `resources/views/components`. Під час написання компонентів для власного додатка компоненти автоматично виявляються у каталогах `app/View/Components` та `resources/views/components`, тому подальша реєстрація компонентів зазвичай не потрібна.

Ви також можете створювати компоненти у підкаталогах:

```shell
php artisan make:component Forms/Input
```

Наведена вище команда створить компонент `Input` у каталозі `app/View/Components/Forms`, а подання буде розміщено у каталозі `resources/views/components/forms`.

Якщо ви хочете створити анонімний компонент (компонент, який містить лише шаблон Blade і не має класу), ви можете використати прапорець `--view` під час виклику команди `make:component`:

```shell
php artisan make:component forms.input --view
```

Наведена вище команда створить файл Blade за адресою `resources/views/components/forms/input.blade.php`, який можна буде відобразити як компонент за допомогою `<x-forms.input />`.

<a name="manually-registering-package-components"></a>
#### Реєстрація компонентів пакета вручну

Під час написання компонентів для власної програми, компоненти автоматично виявляються у каталогах `app/View/Components` та `resources/views/components`.

Однак, якщо ви збираєте пакунок, який використовує компоненти Blade, вам доведеться вручну зареєструвати клас компонента та його псевдонім у HTML-тегах. Зазвичай вам слід реєструвати компоненти у методі `boot` постачальника послуг вашого пакунка:

    use Illuminate\Support\Facades\Blade;

    /**
     * Запустіть сервіси вашого пакету.
     */
    public function boot(): void
    {
        Blade::component('package-alert', Alert::class);
    }

Після того, як ваш компонент зареєстровано, його можна рендерити, використовуючи його псевдонім тегу:

```blade
<x-package-alert/>
```

Крім того, ви можете використовувати метод `componentNamespace` для автоматичного завантаження класів компонентів за домовленістю. Наприклад, пакунок `Nightshade` може містити компоненти `Calendar` та `ColorPicker`, які знаходяться у просторі імен `Package\Views\Components`:

    use Illuminate\Support\Facades\Blade;

    /**
     * Запустіть сервіси вашого пакету.
     */
    public function boot(): void
    {
        Blade::componentNamespace('Nightshade\\Views\\Components', 'nightshade');
    }

Це дозволить використовувати компоненти пакунків у просторі імен їхніх постачальників за допомогою синтаксису `package-name::`:

```blade
<x-nightshade::calendar />
<x-nightshade::color-picker />
```

Blade автоматично визначить клас, який пов'язано з цим компонентом, за назвою компонента у паскаль-палітрі. Підкаталоги також підтримуються за допомогою «крапкових» позначень.

<a name="rendering-components"></a>
### Відмальовування компонентів

Щоб відобразити компонент, ви можете використати тег компонента Blade у одному з ваших шаблонів Blade. Теги компонентів Blade починаються з рядка `x-`, за яким слідує назва класу компонента у регістрі кебаб:

```blade
<x-alert/>

<x-user-profile/>
```

Якщо клас компонента вкладено глибше в директорії `app/View/Components`, ви можете використовувати символ `.` для позначення вкладеності директорій. Наприклад, якщо ми припускаємо, що компонент знаходиться за адресою `app/View/Components/Inputs/Button.php`, ми можемо відобразити його таким чином:

```blade
<x-inputs.button/>
```

Якщо ви бажаєте умовно відрендерити ваш компонент, ви можете визначити метод `shouldRender` у класі вашого компонента. Якщо метод `shouldRender` повертає значення `false`, компонент не буде відрендерено:

    use Illuminate\Support\Str;

    /**
     * Чи потрібно рендерити компонент
     */
    public function shouldRender(): bool
    {
        return Str::length($this->message) > 0;
    }

<a name="passing-data-to-components"></a>
### Передача даних до компонентів

Ви можете передавати дані до компонентів Blade за допомогою HTML-атрибутів. Жорстко закодовані примітивні значення можна передавати компоненту за допомогою простих рядків атрибутів HTML. PHP-вирази та змінні слід передавати компоненту через атрибути, що використовують символ `:` як префікс:

```blade
<x-alert type="error" :message="$message"/>
```

Ви повинні визначити всі атрибути даних компонента в конструкторі його класу. Усі загальнодоступні властивості компонента автоматично стануть доступними для подання компонента. Немає необхідності передавати дані до представлення з методу `render` компонента:

    <?php

    namespace App\View\Components;

    use Illuminate\View\Component;
    use Illuminate\View\View;

    class Alert extends Component
    {
        /**
         * Створіть екземпляр компонента.
         */
        public function __construct(
            public string $type,
            public string $message,
        ) {}

        /**
         * Отримати вигляд / вміст, який представляє компонент.
         */
        public function render(): View
        {
            return view('components.alert');
        }
    }

Коли ваш компонент рендериться, ви можете відобразити вміст загальнодоступних змінних вашого компонента, повторивши змінні за іменами:

```blade
<div class="alert alert-{{ $type }}">
    {{ $message }}
</div>
```

<a name="casing"></a>
#### Кожух

Аргументи конструктора компонента слід вказувати з використанням `camelCase`, тоді як при посиланні на імена аргументів у ваших HTML-атрибутах слід використовувати `kebab-case`. Наприклад, розглянемо наступний конструктор компонента:

    /**
     * Створіть екземпляр компонента.
     */
    public function __construct(
        public string $alertType,
    ) {}

Аргумент `$alertType` можна надати компоненту таким чином:

```blade
<x-alert alert-type="danger" />
```

<a name="short-attribute-syntax"></a>
#### Короткий синтаксис атрибутів

Передаючи атрибути компонентам, ви також можете використовувати синтаксис «короткого атрибута». Це часто буває зручно, оскільки імена атрибутів часто збігаються з іменами змінних, яким вони відповідають:

```blade
{{-- Короткий синтаксис атрибутів. --}}
<x-profile :$userId :$name />

{{-- Еквівалентно... --}}
<x-profile :user-id="$userId" :name="$name" />
```

<a name="escaping-attribute-rendering"></a>
#### Уникнення рендерингу атрибутів

Оскільки деякі фреймворки JavaScript, такі як Alpine.js, також використовують атрибути з префіксом двокрапки, ви можете використовувати префікс подвійної двокрапки (`::`), щоб повідомити Blade, що атрибут не є виразом PHP. Наприклад, у наступному прикладі:

```blade
<x-button ::class="{ danger: isDeleting }">
    Submit
</x-button>
```

Наступний HTML-код буде відрендерено Blade:

```blade
<button :class="{ danger: isDeleting }">
    Submit
</button>
```

<a name="component-methods"></a>
#### Компонентні методи

На додаток до загальнодоступних змінних, доступних у шаблоні вашого компонента, можна викликати будь-які загальнодоступні методи компонента. Наприклад, уявіть собі компонент, який має метод `isSelected`:

    /**
     * Визначити, чи є заданий варіант поточним вибраним варіантом.
     */
    public function isSelected(string $option): bool
    {
        return $option === $this->selected;
    }

Ви можете виконати цей метод з шаблону компонента, викликавши змінну, що відповідає назві методу:

```blade
<option {{ $isSelected($value) ? 'selected' : '' }} value="{{ $value }}">
    {{ $label }}
</option>
```

<a name="using-attributes-slots-within-component-class"></a>
#### Доступ до атрибутів та слотів у класах компонентів

Блейд-компоненти також дозволяють отримати доступ до імені, атрибутів та слоту компонента всередині методу рендеру класу. Однак, щоб отримати доступ до цих даних, ви повинні повернути закриття з методу `render` вашого компонента. Закриття отримає масив `$data` як єдиний аргумент. Цей масив буде містити декілька елементів, які надають інформацію про компонент:

    use Closure;

    /**
     * Отримати вигляд / вміст, який представляє компонент.
     */
    public function render(): Closure
    {
        return function (array $data) {
            // $data['componentName'];
            // $data['attributes'];
            // $data['slot'];

            return '<div>Components content</div>';
        };
    }

Ім'я компонента `componentName` дорівнює імені, яке використовується в HTML-тезі після префікса `x-`. Отже, `componentName` `<x-alert />` буде `alert`. Елемент `attributes` міститиме всі атрибути, які були присутні у тезі HTML. Елемент ``slot`` є екземпляром `Illuminate\Support\HtmlString`` із вмістом слоту компонента.

Закриття має повернути рядок. Якщо повернутий рядок відповідає існуючому представленню, це представлення буде відрендерено; інакше, повернутий рядок буде оцінено як вбудоване представлення Blade.

<a name="additional-dependencies"></a>
#### Додаткові залежності

Якщо ваш компонент потребує залежностей від Laravel [сервісний контейнер](/docs/{{version}}/container), ви можете перерахувати їх перед будь-яким атрибутом даних компонента, і вони будуть автоматично додані контейнером:

```php
use App\Services\AlertCreator;

/**
 * Створіть екземпляр компонента.
 */
public function __construct(
    public AlertCreator $creator,
    public string $type,
    public string $message,
) {}
```

<a name="hiding-attributes-and-methods"></a>
#### Приховування атрибутів / методів

Якщо ви хочете, щоб деякі загальнодоступні методи або властивості не були доступні як змінні у шаблоні вашого компонента, ви можете додати їх до властивості масиву `$except` у вашому компоненті:

    <?php

    namespace App\View\Components;

    use Illuminate\View\Component;

    class Alert extends Component
    {
        /**
         * Властивості / методи, які не повинні бути доступні шаблону компонента.
         *
         * @var array
         */
        protected $except = ['type'];

        /**
         * Створіть екземпляр компонента.
         */
        public function __construct(
            public string $type,
        ) {}
    }

<a name="component-attributes"></a>
### Атрибути компонентів

Ми вже розглянули, як передавати атрибути даних компоненту, але іноді вам може знадобитися вказати додаткові атрибути HTML, такі як `class`, які не є частиною даних, необхідних для роботи компонента. Зазвичай ці додаткові атрибути потрібно передавати до кореневого елемента шаблону компонента. Наприклад, уявіть, що ми хочемо відрендерити компонент `alert` таким чином:

```blade
<x-alert type="error" :message="$message" class="mt-4"/>
```

Усі атрибути, які не є частиною конструктора компонента, будуть автоматично додані до «мішка атрибутів» компонента. Цей мішок атрибутів автоматично стає доступним для компонента через змінну `$attributes`. Усі атрибути можуть бути відображені у компоненті шляхом повторного виклику цієї змінної:

```blade
<div {{ $attributes }}>
    <!-- Склад компонентів -->
</div>
```

> [!WARNING]  
> Використання директив типу `@env` у тегах компонентів наразі не підтримується. Наприклад, `<x-alert :live=«@env(“production”)»/>` не буде скомпільовано.

<a name="default-merged-attributes"></a>
#### Атрибути за замовчуванням / Об'єднані атрибути

Іноді вам може знадобитися вказати значення за замовчуванням для атрибутів або об'єднати додаткові значення з деякими атрибутами компонента. Для цього ви можете скористатися методом `merge` пакета атрибутів. Цей метод особливо корисний для визначення набору класів CSS за замовчуванням, які завжди слід застосовувати до компонента:

```blade
<div {{ $attributes->merge(['class' => 'alert alert-'.$type]) }}>
    {{ $message }}
</div>
```

Якщо припустити, що цей компонент використовується саме так:

```blade
<x-alert type="error" :message="$message" class="mb-4"/>
```

Остаточний, відрендерений HTML компонента виглядатиме так, як показано нижче:

```blade
<div class="alert alert-error mb-4">
    <!-- Вміст змінної $message -->
</div>
```

<a name="conditionally-merge-classes"></a>
#### Умовне об'єднання класів

Іноді вам може знадобитися об'єднати класи, якщо задана умова є «істинною». Ви можете зробити це за допомогою методу `class`, який приймає масив класів, де ключ масиву містить клас або класи, які ви бажаєте додати, а значення є логічним виразом. Якщо елемент масиву має числовий ключ, він завжди буде включений до списку класів, що виводиться:

```blade
<div {{ $attributes->class(['p-4', 'bg-red' => $hasError]) }}>
    {{ $message }}
</div>
```

Якщо вам потрібно об'єднати інші атрибути у вашому компоненті, ви можете зв'язати метод `merge` з методом `class`:

```blade
<button {{ $attributes->class(['p-4'])->merge(['type' => 'button']) }}>
    {{ $slot }}
</button>
```

> [!NOTE]  
> Якщо вам потрібно умовно скомпілювати класи на інших HTML-елементах, які не повинні отримувати об'єднані атрибути, ви можете використовувати [`@class` директива](#conditional-classes).

<a name="non-class-attribute-merging"></a>
#### Об'єднання некласових атрибутів

При об'єднанні атрибутів, які не є атрибутами `class`, значення, надані методу `merge`, вважатимуться значеннями атрибута «за замовчуванням». Однак, на відміну від атрибута `class`, ці атрибути не будуть об'єднані зі значеннями атрибутів, що вводяться. Натомість вони будуть перезаписані. Наприклад, реалізація компонента `button` може виглядати наступним чином:

```blade
<button {{ $attributes->merge(['type' => 'button']) }}>
    {{ $slot }}
</button>
```

Щоб відрендерити компонент кнопки з користувацьким `типом`, його можна вказати при споживанні компонента. Якщо тип не вказано, буде використано тип `button`:

```blade
<x-button type="submit">
    Submit
</x-button>
```

У цьому прикладі HTML-сторінка компонента `button` буде виглядати так:

```blade
<button type="submit">
    Submit
</button>
```

Якщо ви хочете, щоб атрибут, відмінний від `class`, мав значення за замовчуванням та додані значення, ви можете використати метод `prepends`. У цьому прикладі атрибут `data-controller` завжди починатиметься з `profile-controller`, а будь-які додаткові значення `data-controller` будуть розміщені після цього значення за замовчуванням:

```blade
<div {{ $attributes->merge(['data-controller' => $attributes->prepends('profile-controller')]) }}>
    {{ $slot }}
</div>
```

<a name="filtering-attributes"></a>
#### Отримання та фільтрація атрибутів

Ви можете фільтрувати атрибути за допомогою методу `filter`. Цей метод приймає закриття, яке має повертати значення `true`, якщо ви хочете зберегти атрибут у пакеті атрибутів:

```blade
{{ $attributes->filter(fn (string $value, string $key) => $key == 'foo') }}
```

Для зручності ви можете використовувати метод `whereStartsWith`, щоб отримати всі атрибути, ключі яких починаються з заданого рядка:

```blade
{{ $attributes->whereStartsWith('wire:model') }}
```

І навпаки, метод `whereDoesNotStartWith` можна використовувати для виключення всіх атрибутів, ключі яких починаються з заданого рядка:

```blade
{{ $attributes->whereDoesntStartWith('wire:model') }}
```

За допомогою методу `first` ви можете відрендерити перший атрибут у заданому пакеті атрибутів:

```blade
{{ $attributes->whereStartsWith('wire:model')->first() }}
```

Якщо ви хочете перевірити, чи присутній атрибут у компоненті, ви можете скористатися методом `has`. Цей метод приймає ім'я атрибута як єдиний аргумент і повертає логічне значення, яке вказує, чи присутній атрибут, чи ні:

```blade
@if ($attributes->has('class'))
    <div>Class attribute is present</div>
@endif
```

Якщо в метод `has` передано масив, метод визначить, чи всі задані атрибути присутні на компоненті:

```blade
@if ($attributes->has(['name', 'class']))
    <div>All of the attributes are present</div>
@endif
```

Метод `hasAny` може бути використаний для визначення наявності будь-якого з заданих атрибутів на компоненті:

```blade
@if ($attributes->hasAny(['href', ':href', 'v-bind:href']))
    <div>One of the attributes is present</div>
@endif
```

Ви можете отримати значення конкретного атрибута за допомогою методу `get`:

```blade
{{ $attributes->get('class') }}
```

<a name="reserved-keywords"></a>
### Зарезервовані ключові слова

За замовчуванням, деякі ключові слова зарезервовано для внутрішнього використання Blade для рендерингу компонентів. Наступні ключові слова не можуть бути визначені як загальнодоступні властивості або імена методів у ваших компонентах:

<div class="content-list" markdown="1">

- `data`
- `render`
- `resolveView`
- `shouldRender`
- `view`
- `withAttributes`
- `withName`

</div>

<a name="slots"></a>
### Слоти

Вам часто потрібно буде передавати додатковий вміст до вашого компонента через «слоти». Слоти компонента відображаються шляхом повторення змінної `$slot`. Щоб дослідити цю концепцію, уявімо, що компонент `alert` має наступну розмітку:

```blade
<!-- /resources/views/components/alert.blade.php -->

<div class="alert alert-danger">
    {{ $slot }}
</div>
```

Ми можемо передати вміст у «слот», вставивши його у компонент:

```blade
<x-alert>
    <strong>Whoops!</strong> Something went wrong!
</x-alert>
```

Іноді компонент може потребувати відображення декількох різних слотів у різних місцях у межах компонента. Давайте модифікуємо наш компонент сповіщення, щоб додати слот «title»:

```blade
<!-- /resources/views/components/alert.blade.php -->

<span class="alert-title">{{ $title }}</span>

<div class="alert alert-danger">
    {{ $slot }}
</div>
```

Ви можете визначити вміст іменованого слоту за допомогою тегу `x-slot`. Будь-який вміст, що не міститься у явному тезі `x-slot`, буде передано компоненту у змінній `$slot`:

```xml
<x-alert>
    <x-slot:title>
        Помилка сервера
    </x-slot>

    <strong>Упс.!</strong> Щось пішло не так!
</x-alert>
```

Ви можете викликати метод слоту `isEmpty`, щоб визначити, чи містить слот вміст:

```blade
<span class="alert-title">{{ $title }}</span>

<div class="alert alert-danger">
    @if ($slot->isEmpty())
        This is default content if the slot is empty.
    @else
        {{ $slot }}
    @endif
</div>
```

Крім того, метод `hasActualContent` можна використовувати, щоб визначити, чи містить слот будь-який «фактичний» вміст, який не є HTML-коментарем:

```blade
@if ($slot->hasActualContent())
    Сфера не містить коментарів.
@endif
```

<a name="scoped-slots"></a>
#### Склоподібні слоти

Якщо ви використовували JavaScript-фреймворк, такий як Vue, ви можете бути знайомі зі слотами, які дозволяють отримати доступ до даних або методів з компонента всередині слота. Ви можете досягти подібної поведінки в Laravel, визначивши загальнодоступні методи або властивості у вашому компоненті і отримати доступ до компонента всередині слоту через змінну `$component`. У цьому прикладі ми припустимо, що компонент `x-alert` має загальнодоступний метод `formatAlert`, визначений у класі компонента:

```blade
<x-alert>
    <x-slot:title>
        {{ $component->formatAlert('Server Error') }}
    </x-slot>

    <strong>Упс!</strong> Щось пішло не так!
</x-alert>
```

<a name="slot-attributes"></a>
#### Атрибути слоту

Як і компоненти Blade, ви можете призначити додаткові [атрибути](#component-attributes) до слотів, таких як імена класів CSS:

```xml
<x-card class="shadow-sm">
    <x-slot:heading class="font-bold">
        Заголовок
    </x-slot>

    Content

    <x-slot:footer class="text-sm">
        Нижній колонтитул
    </x-slot>
</x-card>
```

Щоб взаємодіяти з атрибутами слота, ви можете звернутися до властивості `attributes` змінної слота. Для отримання додаткової інформації про роботу з атрибутами зверніться до документації по [атрибути компонентів](#component-attributes):

```blade
@props([
    'heading',
    'footer',
])

<div {{ $attributes->class(['border']) }}>
    <h1 {{ $heading->attributes->class(['text-lg']) }}>
        {{ $heading }}
    </h1>

    {{ $slot }}

    <footer {{ $footer->attributes->class(['text-gray-700']) }}>
        {{ $footer }}
    </footer>
</div>
```

<a name="inline-component-views"></a>
### Вбудовані шаблони компонентів

Для дуже маленьких компонентів керування класом компонента та шаблоном вигляду компонента може здаватися громіздким. З цієї причини ви можете повернути розмітку компонента безпосередньо з методу `render`:

    /**
     * Отримати вигляд / вміст, який представляє компонент.
     */
    public function render(): string
    {
        return <<<'blade'
            <div class="alert alert-danger">
                {{ $slot }}
            </div>
        blade;
    }

<a name="generating-inline-view-components"></a>
#### Створення компонентів вбудованого представлення

Щоб створити компонент, який відображає вбудоване подання, ви можете використати опцію `inline` при виконанні команди `make:component`:

```shell
php artisan make:component Alert --inline
```

<a name="dynamic-components"></a>
### Динамічні компоненти

Іноді вам може знадобитися відрендерити компонент, але ви не знаєте, який саме компонент слід відрендерити до часу виконання. У цій ситуації ви можете використати вбудований компонент `dynamic-component` для відображення компонента на основі значення або змінної під час виконання:

```blade
// $componentName = "secondary-button";

<x-dynamic-component :component="$componentName" class="mt-4" />
```

<a name="manually-registering-components"></a>
### Ручна реєстрація компонентів

> [!WARNING]  
> Наведена нижче документація щодо реєстрації компонентів вручну призначена насамперед для тих, хто пише пакунки Laravel, які містять компоненти перегляду. Якщо ви не пишете пакунки, ця частина документації про компоненти може бути для вас неактуальною.

Під час написання компонентів для власної програми, компоненти автоматично виявляються у каталогах `app/View/Components` та `resources/views/components`.

Однак, якщо ви збираєте пакунок, який використовує компоненти Blade, або розміщуєте компоненти у нестандартних каталогах, вам потрібно буде вручну зареєструвати клас компонента та його псевдонім у HTML-тегах, щоб Laravel знав, де знайти компонент. Зазвичай ви маєте реєструвати компоненти у методі `boot` постачальника послуг вашого пакунка:

    use Illuminate\Support\Facades\Blade;
    use VendorPackage\View\Components\AlertComponent;

    /**
     * Запустіть сервіси вашого пакету.
     */
    public function boot(): void
    {
        Blade::component('package-alert', AlertComponent::class);
    }

Після того, як ваш компонент зареєстровано, його можна рендерити, використовуючи його псевдонім тегу:

```blade
<x-package-alert/>
```

#### Автозавантаження компонентів пакунків

Крім того, ви можете використовувати метод `componentNamespace` для автоматичного завантаження класів компонентів за домовленістю. Наприклад, пакунок `Nightshade` може містити компоненти `Calendar` та `ColorPicker`, які знаходяться у просторі імен `Package\Views\Components`:

    use Illuminate\Support\Facades\Blade;

    /**
     * Запустіть сервіси вашого пакету.
     */
    public function boot(): void
    {
        Blade::componentNamespace('Nightshade\\Views\\Components', 'nightshade');
    }

Це дозволить використовувати компоненти пакунків у просторі імен їхніх постачальників за допомогою синтаксису `package-name::`:

```blade
<x-nightshade::calendar />
<x-nightshade::color-picker />
```

Blade автоматично визначить клас, який пов'язано з цим компонентом, за назвою компонента у паскаль-палітрі. Підкаталоги також підтримуються за допомогою «крапкових» позначень.

<a name="anonymous-components"></a>
## Анонімні компоненти

Подібно до вбудованих компонентів, анонімні компоненти надають механізм керування компонентом за допомогою одного файлу. Однак, анонімні компоненти використовують єдиний файл подання і не мають пов'язаного з ним класу. Щоб визначити анонімний компонент, вам потрібно лише розмістити шаблон Blade у вашому каталозі `resources/views/components`. Наприклад, якщо ви визначили компонент за адресою `resources/views/components/alert.blade.php`, ви можете просто відрендерити його таким чином:

```blade
<x-alert/>
```

Ви можете використовувати символ `.`, щоб вказати, що компонент вкладено глибше всередині каталогу `components`. Наприклад, припускаючи, що компонент визначено за адресою `resources/views/components/inputs/button.blade.php`, ви можете відобразити його таким чином:

```blade
<x-inputs.button/>
```

<a name="anonymous-index-components"></a>
### Анонімні Index компоненти

Іноді, коли компонент складається з багатьох шаблонів Blade, ви можете захотіти згрупувати шаблони даного компонента в одному каталозі. Наприклад, уявіть собі компонент «акордеон» з такою структурою каталогів:

```none
/resources/views/components/accordion.blade.php
/resources/views/components/accordion/item.blade.php
```

Ця структура каталогів дозволяє вам візуалізувати компонент акордеона та його елемент таким чином:

```blade
<x-accordion>
    <x-accordion.item>
        ...
    </x-accordion.item>
</x-accordion>
```

Однак, щоб рендерити компонент акордеона через `x-accordion`, ми були змушені розмістити шаблон компонента акордеона «index» у директорії `resources/views/components` замість того, щоб вкласти його у директорію `accordion` разом з іншими шаблонами, пов'язаними з акордеоном.

На щастя, Blade дозволяє розміщувати файл `index.blade.php` у каталозі шаблонів компонента. Коли шаблон `index.blade.php` існує для компонента, він буде відображатися як «кореневий» вузол компонента. Отже, ми можемо продовжувати використовувати той самий синтаксис Blade, що й у прикладі вище, але структуру каталогів буде змінено таким чином:

```none
/resources/views/components/accordion/index.blade.php
/resources/views/components/accordion/item.blade.php
```

<a name="data-properties-attributes"></a>
### Властивості / Атрибути даних

Оскільки анонімні компоненти не мають жодного асоційованого класу, ви можете задатися питанням, як відрізнити, які дані слід передавати компоненту як змінні, а які атрибути слід розміщувати в атрибутах компонентаs [сумка з атрибутами](#component-attributes).

Ви можете вказати, які атрибути слід вважати змінними даних за допомогою директиви `@props` у верхній частині шаблону Blade вашого компонента. Усі інші атрибути компонента будуть доступні через пакунок атрибутів компонента. Якщо ви бажаєте надати змінній даних значення за замовчуванням, ви можете вказати ім'я змінної як ключ масиву, а значення за замовчуванням - як значення масиву:

```blade
<!-- /resources/views/components/alert.blade.php -->

@props(['type' => 'info', 'message'])

<div {{ $attributes->merge(['class' => 'alert alert-'.$type]) }}>
    {{ $message }}
</div>
```

Враховуючи визначення компонента вище, ми можемо відрендерити компонент таким чином:

```blade
<x-alert type="error" :message="$message" class="mb-4"/>
```

<a name="accessing-parent-data"></a>
### Доступ до батьківських даних

Іноді вам може знадобитися доступ до даних батьківського компонента всередині дочірнього. У цих випадках ви можете використовувати директиву `@aware`. Наприклад, уявіть, що ми створюємо складний компонент меню, який складається з батьківського `<x-menu>` та дочірнього `<x-menu.item>`:

```blade
<x-menu color="purple">
    <x-menu.item>...</x-menu.item>
    <x-menu.item>...</x-menu.item>
</x-menu>
```

Компонент `<x-menu>` може мати наступну реалізацію:

```blade
<!-- /resources/views/components/menu/index.blade.php -->

@props(['color' => 'gray'])

<ul {{ $attributes->merge(['class' => 'bg-'.$color.'-200']) }}>
    {{ $slot }}
</ul>
```

Оскільки проп `color` було передано лише у батьківський елемент (`<x-menu>`), він не буде доступний всередині `<x-menu.item>`. Однак, якщо ми використаємо директиву `@aware`, ми можемо зробити його доступним і всередині `<x-menu.item>`:

```blade
<!-- /resources/views/components/menu/item.blade.php -->

@aware(['color' => 'gray'])

<li {{ $attributes->merge(['class' => 'text-'.$color.'-800']) }}>
    {{ $slot }}
</li>
```

> [!WARNING]  
> Директива `@aware` не може отримати доступ до батьківських даних, які не були явно передані батьківському компоненту через HTML-атрибути. Директива `@aware` не може отримати доступ до значень за замовчуванням `@props`, які не було явно передано батьківському компоненту.

<a name="anonymous-component-paths"></a>
### Анонімні шляхи до компонентів

Як ми вже обговорювали раніше, анонімні компоненти зазвичай визначаються шляхом розміщення шаблону Blade у вашому каталозі `resources/views/components`. Втім, іноді вам може знадобитися зареєструвати у Laravel інші шляхи до анонімних компонентів на додаток до шляху за замовчуванням.

Метод `anonymousComponentPath` приймає «шлях» до розташування анонімного компонента як перший аргумент і необов'язковий «простір імен», у якому слід розміщувати компоненти, як другий аргумент. Зазвичай цей метод слід викликати з методу `boot` одного з методів вашої програми [постачальники послуг](/docs/{{version}}/providers):

    /**
     * Завантажуйте будь-які сервіси додатків.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(__DIR__.'/../components');
    }

Якщо шляхи до компонентів зареєстровано без зазначеного префікса, як у наведеному вище прикладі, вони також можуть бути відображені у ваших компонентах Blade без відповідного префікса. Наприклад, якщо у зареєстрованому вище шляху існує компонент `panel.blade.php`, він може відображатися так:

```blade
<x-panel />
```

Префікс «namespaces» може бути переданий як другий аргумент методу `anonymousComponentPath`:

    Blade::anonymousComponentPath(__DIR__.'/../components', 'dashboard');

Якщо надано префікс, компоненти у цьому «просторі імен» можуть бути відрендереними шляхом додавання префікса до простору імен компонента до імені компонента під час його візуалізації:

```blade
<x-dashboard::panel />
```

<a name="building-layouts"></a>
## Створенн макетів

<a name="layouts-using-components"></a>
### Макети з використанням компонентів

Більшість веб-додатків підтримують однаковий загальний макет на різних сторінках. Було б неймовірно громіздко і важко підтримувати наш додаток, якби нам довелося повторювати весь макет HTML у кожному створеному поданні. На щастя, зручно визначити цей макет як єдиний [Компонент леза](#components) а потім використовуємо його в нашому додатку.

<a name="defining-the-layout-component"></a>
#### Визначення компонента макета

Наприклад, уявіть, що ми створюємо додаток зі списком справ. Ми можемо визначити компонент `layout`, який виглядає наступним чином:

```blade
<!-- resources/views/components/layout.blade.php -->

<html>
    <head>
        <title>{{ $title ?? 'Todo Manager' }}</title>
    </head>
    <body>
        <h1>Todos</h1>
        <hr/>
        {{ $slot }}
    </body>
</html>
```

<a name="applying-the-layout-component"></a>
#### Застосування компонента «Макет

Після визначення компонента `layout` ми можемо створити подання Blade, яке використовує цей компонент. У цьому прикладі ми створимо просте подання, яке відображатиме наш список завдань:

```blade
<!-- resources/views/tasks.blade.php -->

<x-layout>
    @foreach ($tasks as $task)
        {{ $task }}
    @endforeach
</x-layout>
```

Пам'ятайте, що вміст, який вставляється у компонент, буде передано до змінної за замовчуванням `$slot` у нашому компоненті `layout`. Як ви могли помітити, наш `layout` також враховує слот `$title`, якщо він передбачений; інакше буде показано заголовок за замовчуванням. Ми можемо вставити власний заголовок зі списку завдань, використовуючи стандартний синтаксис слоту, описаний у розділі [документація до компонентів](#components):

```blade
<!-- resources/views/tasks.blade.php -->

<x-layout>
    <x-slot:title>
        Custom Title
    </x-slot>

    @foreach ($tasks as $task)
        {{ $task }}
    @endforeach
</x-layout>
```

Тепер, коли ми визначили наш макет і подання списку завдань, нам просто потрібно повернути подання «завдання» з маршруту:

    use App\Models\Task;

    Route::get('/tasks', function () {
        return view('tasks', ['tasks' => Task::all()]);
    });

<a name="layouts-using-template-inheritance"></a>
### Макети з використанням успадкування шаблонів

<a name="defining-a-layout"></a>
#### Визначення макета

Макети також можуть бути створені за допомогою «успадкування шаблону». Це був основний спосіб створення додатків до впровадження [компоненти](#components).

Для початку давайте розглянемо простий приклад. Спочатку ми розглянемо макет сторінки. Оскільки більшість веб-додатків підтримують однаковий загальний макет на різних сторінках, зручно визначити цей макет як єдине представлення Blade:

```blade
<!-- resources/views/layouts/app.blade.php -->

<html>
    <head>
        <title>App Name - @yield('title')</title>
    </head>
    <body>
        @section('sidebar')
            Це головна бічна панель.
        @show

        <div class="container">
            @yield('content')
        </div>
    </body>
</html>
```

Як бачите, цей файл містить типову розмітку HTML. Однак зверніть увагу на директиви `@section` і `@yield`. Директива `@section`, як випливає з назви, визначає розділ вмісту, тоді як директива `@yield` використовується для відображення вмісту цього розділу.

Тепер, коли ми визначили макет для нашого додатку, давайте визначимо дочірню сторінку, яка успадкує макет.

<a name="extending-a-layout"></a>
#### Розширення макета

Визначаючи дочірнє подання, використовуйте директиву Blade `@extends`, щоб вказати, який макет має «успадкувати» дочірнє подання. Подання, які розширюють компонування Blade, можуть вставляти вміст у секції компонування за допомогою директив `@section`. Пам'ятайте, як показано у наведеному вище прикладі, вміст цих секцій буде відображено у макеті за допомогою `@yield`:

```blade
<!-- resources/views/child.blade.php -->

@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @@parent

    <p>This is appended to the master sidebar.</p>
@endsection

@section('content')
    <p>This is my body content.</p>
@endsection
```

У цьому прикладі секція `sidebar` використовує директиву `@@parent` для додавання (а не перезапису) вмісту до бічної панелі макета. Директива `@@parent` буде замінена вмістом макета під час рендерингу подання.

> [!NOTE]  
> На відміну від попереднього прикладу, цей розділ `sidebar` закінчується директивою `@endsection` замість `@show`. Директива `@endsection` лише визначає розділ, тоді як `@show` визначає і **негайно виводить** розділ.

Директива `@yield` також приймає значення за замовчуванням як другий параметр. Це значення буде виведено, якщо розділ, що виводиться, не визначено:

```blade
@yield('content', 'Default content')
```

<a name="forms"></a>
## Форми

<a name="csrf-field"></a>
### Поле CSRF

Кожного разу, коли ви визначаєте HTML-форму у вашому додатку, ви повинні включити приховане поле токену CSRF у форму, щоб [захист CSRF](/docs/{{version}}/csrf) проміжне програмне забезпечення може підтвердити запит. Ви можете використовувати директиву `@csrf` Blade для генерації поля токена:

```blade
<form method="POST" action="/profile">
    @csrf

    ...
</form>
```

<a name="method-field"></a>
### Поле Method

Оскільки HTML-форми не можуть робити запити `PUT`, `PATCH` або `DELETE`, вам потрібно додати приховане поле `_method` для підміни цих HTTP-дієслів. Директива Blade `@method` може створити це поле для вас:

```blade
<form action="/foo/bar" method="POST">
    @method('PUT')

    ...
</form>
```

<a name="validation-errors"></a>
### Помилки валідації

Директива `@error` може бути використана для швидкої перевірки [validation error messages](/docs/{{version}}/validation#quick-displaying-the-validation-errors) існують для даного атрибута. У директиві `@error` ви можете повторити змінну `$message` для виведення повідомлення про помилку:

```blade
<!-- /resources/views/post/create.blade.php -->

<label for="title">Post Title</label>

<input id="title"
    type="text"
    class="@error('title') is-invalid @enderror">

@error('title')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror
```

Оскільки директива `@error` компілюється в оператор if, ви можете використовувати директиву `@else` для відображення вмісту, якщо для якогось атрибута немає помилки:

```blade
<!-- /resources/views/auth.blade.php -->

<label for="email">Email address</label>

<input id="email"
    type="email"
    class="@error('email') is-invalid @else is-valid @enderror">
```

Можеш пройти. [ім'я конкретного пакета помилок](/docs/{{version}}/validation#named-error-bags) як другий параметр директиви `@error` для отримання повідомлень про помилки валідації на сторінках з декількома формами:

```blade
<!-- /resources/views/auth.blade.php -->

<label for="email">Email address</label>

<input id="email"
    type="email"
    class="@error('email', 'login') is-invalid @enderror">

@error('email', 'login')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror
```

<a name="stacks"></a>
## Стеки

Blade дозволяє вам натискати на іменовані стеки, які можуть бути рендеринговані деінде в іншому поданні або компонуванні. Це може бути особливо корисно для вказівки будь-яких бібліотек JavaScript, необхідних для ваших дочірніх подань:

```blade
@push('scripts')
    <script src="/example.js"></script>
@endpush
```

Якщо ви хочете «виштовхнути» вміст, якщо заданий логічний вираз набуває значення «істина», ви можете скористатися директивою `@pushIf`:

```blade
@pushIf($shouldPush, 'scripts')
    <script src="/example.js"></script>
@endPushIf
```

Ви можете натискати на стек стільки разів, скільки потрібно. Щоб відобразити весь вміст стеку, передайте ім'я стеку в директиву `@stack`:

```blade
<head>
    <!-- Head Contents -->

    @stack('scripts')
</head>
```

Якщо ви хочете вставити вміст на початок стека, використовуйте директиву `@prepend`:

```blade
@push('scripts')
    Це буде другий...
@endpush

// Later...

@prepend('scripts')
    Це буде перший...
@endprepend
```

<a name="service-injection"></a>
## Зовнішні служби

Директива `@inject` може бути використана для отримання сервісу з Laravel [service container](/docs/{{version}}/container). The first argument passed to `@inject` is the name of the variable the service will be placed into, while the second argument is the class or interface name of the service you wish to resolve:

```blade
@inject('metrics', 'App\Services\MetricsService')

<div>
    Щомісячний дохід: {{ $metrics->monthlyRevenue() }}.
</div>
```

<a name="rendering-inline-blade-templates"></a>
## Рендиринг шаблонів Blade з рядка

Іноді вам може знадобитися перетворити сирий рядок шаблону Blade у коректний HTML. Ви можете зробити це за допомогою методу `render`, що надається фасадом `Blade`. Метод `render` приймає рядок шаблону Blade і необов'язковий масив даних, які потрібно надати шаблону:

```php
use Illuminate\Support\Facades\Blade;

return Blade::render('Hello, {{ $name }}', ['name' => 'Julian Bashir']);
```

Laravel рендерить вбудовані шаблони Blade, записуючи їх до каталогу `storage/framework/views`. Якщо ви хочете, щоб Laravel видалив ці тимчасові файли після відображення шаблону Blade, ви можете передати аргумент `deleteCachedView` до методу:

```php
return Blade::render(
    'Hello, {{ $name }}',
    ['name' => 'Julian Bashir'],
    deleteCachedView: true
);
```

<a name="rendering-blade-fragments"></a>
## Рендиринг фрагментів Blade

При використанні фронтенд фреймворків, таких як [Turbo](https://turbo.hotwired.dev/) and [htmx](https://htmx.org/), Іноді вам може знадобитися повернути лише частину шаблону Blade у вашій HTTP-відповіді. «Фрагменти» Blade дозволяють вам робити саме це. Для початку помістіть частину вашого шаблону Blade у директиви `@fragment` та `@endfragment`:

```blade
@fragment('user-list')
    <ul>
        @foreach ($users as $user)
            <li>{{ $user->name }}</li>
        @endforeach
    </ul>
@endfragment
```

Потім, під час рендерингу подання, яке використовує цей шаблон, ви можете викликати метод `fragment`, щоб вказати, що тільки вказаний фрагмент повинен бути включений у вихідну HTTP-відповідь:

```php
return view('dashboard', ['users' => $users])->fragment('user-list');
```

Метод `fragmentIf` дозволяє умовно повернути фрагмент подання на основі заданої умови. В іншому випадку буде повернуто все представлення:

```php
return view('dashboard', ['users' => $users])
    ->fragmentIf($request->hasHeader('HX-Request'), 'user-list');
```

Методи `fragments` та `fragmentsIf` дозволяють повернути у відповіді декілька фрагментів подання. Фрагменти будуть об'єднані разом:

```php
view('dashboard', ['users' => $users])
    ->fragments(['user-list', 'comment-list']);

view('dashboard', ['users' => $users])
    ->fragmentsIf(
        $request->hasHeader('HX-Request'),
        ['user-list', 'comment-list']
    );
```

<a name="extending-blade"></a>
## Розширення Blade

Blade дозволяє вам визначати власні користувацькі директиви за допомогою методу `directive`. Коли компілятор Blade зустріне користувацьку директиву, він викличе передбачений зворотний виклик з виразом, який містить директива.

Наступний приклад створює директиву `@datetime($var)`, яка форматує заданий `$var`, що має бути екземпляром `DateTime`:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\Blade;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Реєструйте будь-які сервіси додатків.
         */
        public function register(): void
        {
            // ...
        }

        /**
         * Завантажуйте будь-які сервіси додатків.
         */
        public function boot(): void
        {
            Blade::directive('datetime', function (string $expression) {
                return "<?php echo ($expression)->format('m/d/Y H:i'); ?>";
            });
        }
    }

Як ви можете бачити, ми приєднаємо метод `format` до будь-якого виразу, переданого в директиву. Отже, у цьому прикладі кінцевий PHP, згенерований цією директивою, буде таким:

    <?php echo ($var)->format('m/d/Y H:i'); ?>

> [!WARNING]  
> Після оновлення логіки директиви Blade вам потрібно буде видалити всі кешовані подання Blade. Видалити кешовані подання Blade можна за допомогою команди Artisan `view:clear`.

<a name="custom-echo-handlers"></a>
### Користувацькі обробники вводу

Якщо ви спробуєте «відлунити» об'єкт за допомогою Blade, буде викликано метод об'єкта `__toString`. Метод [`__toString`](https://www.php.net/manual/en/language.oop5.magic.php#object.tostring) є одним з вбудованих «магічних методів» PHP. Однак іноді ви можете не мати контролю над методом `__toString` даного класу, наприклад, коли клас, з яким ви взаємодієте, належить до сторонньої бібліотеки.

У цих випадках Blade дозволяє вам зареєструвати власний обробник ехо-сигналу для цього типу об'єктів. Для цього вам слід викликати метод `stringable` у Blade. Метод `stringable` приймає закриття. Це закриття повинне містити підказку типу об'єкта, за рендеринг якого воно відповідає. Зазвичай, метод `stringable` слід викликати у методі `boot` класу `AppServiceProvider` вашого додатку:

    use Illuminate\Support\Facades\Blade;
    use Money\Money;

    /**
     * Завантажуйте будь-які сервіси додатків.
     */
    public function boot(): void
    {
        Blade::stringable(function (Money $money) {
            return $money->formatTo('en_GB');
        });
    }

Після визначення власного обробника відлуння ви можете просто відлунити об'єкт у шаблоні Blade:

```blade
Cost: {{ $money }}
```

<a name="custom-if-statements"></a>
### Користувацькі операто If

Програмування користувацьких директив іноді буває складнішим, ніж це необхідно при визначенні простих користувацьких умовних операторів. З цієї причини у Blade передбачено метод `Blade::if`, який дозволяє швидко визначати користувацькі умовні директиви за допомогою закриття. Наприклад, давайте визначимо користувацьку умовну директиву, яка перевіряє сконфігурований за замовчуванням «диск» для програми. Ми можемо зробити це у методі `boot` нашого `AppServiceProvider`:

    use Illuminate\Support\Facades\Blade;

    /**
     * Завантажуйте будь-які сервіси додатків.
     */
    public function boot(): void
    {
        Blade::if('disk', function (string $value) {
            return config('filesystems.default') === $value;
        });
    }

Після того, як ви визначили користувацьку умову, ви можете використовувати її у своїх шаблонах:

```blade
@disk('local')
    <!-- The application is using the local disk... -->
@elsedisk('s3')
    <!-- The application is using the s3 disk... -->
@else
    <!-- The application is using some other disk... -->
@enddisk

@unlessdisk('local')
    <!-- The application is not using the local disk... -->
@enddisk
```