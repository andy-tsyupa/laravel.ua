# Хелпери

- [Вступ](#introduction)
- [Доступні методи](#available-methods)
- [Інші утиліти](#other-utilities)
    - [Benchmarking](#benchmarking)
    - [Дати](#dates)
    - [Лотерея](#lottery)
    - [Pipeline](#pipeline)
    - [Sleep](#sleep)

<a name="introduction"></a>
## Вступ

Laravel містить безліч глобальних «допоміжних» функцій. Багато з цих функцій використовуються самим фреймворком; однак, ви можете використовувати їх у своїх власних додатках, якщо вважаєте зручними.

<a name="available-methods"></a>
## Доступні методи


<a name="arrays-and-objects-method-list"></a>
### Масиви та об'єкти

<div class="docs-column-list" markdown="1">

- [Arr::accessible](#method-array-accessible)
- [Arr::add](#method-array-add)
- [Arr::collapse](#method-array-collapse)
- [Arr::crossJoin](#method-array-crossjoin)
- [Arr::divide](#method-array-divide)
- [Arr::dot](#method-array-dot)
- [Arr::except](#method-array-except)
- [Arr::exists](#method-array-exists)
- [Arr::first](#method-array-first)
- [Arr::flatten](#method-array-flatten)
- [Arr::forget](#method-array-forget)
- [Arr::get](#method-array-get)
- [Arr::has](#method-array-has)
- [Arr::hasAny](#method-array-hasany)
- [Arr::isAssoc](#method-array-isassoc)
- [Arr::isList](#method-array-islist)
- [Arr::join](#method-array-join)
- [Arr::keyBy](#method-array-keyby)
- [Arr::last](#method-array-last)
- [Arr::map](#method-array-map)
- [Arr::mapSpread](#method-array-map-spread)
- [Arr::mapWithKeys](#method-array-map-with-keys)
- [Arr::only](#method-array-only)
- [Arr::pluck](#method-array-pluck)
- [Arr::prepend](#method-array-prepend)
- [Arr::prependKeysWith](#method-array-prependkeyswith)
- [Arr::pull](#method-array-pull)
- [Arr::query](#method-array-query)
- [Arr::random](#method-array-random)
- [Arr::set](#method-array-set)
- [Arr::shuffle](#method-array-shuffle)
- [Arr::sort](#method-array-sort)
- [Arr::sortDesc](#method-array-sort-desc)
- [Arr::sortRecursive](#method-array-sort-recursive)
- [Arr::take](#method-array-take)
- [Arr::toCssClasses](#method-array-to-css-classes)
- [Arr::toCssStyles](#method-array-to-css-styles)
- [Arr::undot](#method-array-undot)
- [Arr::where](#method-array-where)
- [Arr::whereNotNull](#method-array-where-not-null)
- [Arr::wrap](#method-array-wrap)
- [data_fill](#method-data-fill)
- [data_get](#method-data-get)
- [data_set](#method-data-set)
- [data_forget](#method-data-forget)
- [head](#method-head)
- [last](#method-last)
</div>

<a name="numbers-method-list"></a>
### Числа

<div class="docs-column-list" markdown="1">

- [Number::abbreviate](#method-number-abbreviate)
- [Number::clamp](#method-number-clamp)
- [Number::currency](#method-number-currency)
- [Number::defaultCurrency](#method-default-currency)
- [Number::defaultLocale](#method-default-locale)
- [Number::fileSize](#method-number-file-size)
- [Number::forHumans](#method-number-for-humans)
- [Number::format](#method-number-format)
- [Number::ordinal](#method-number-ordinal)
- [Number::pairs](#method-number-pairs)
- [Number::percentage](#method-number-percentage)
- [Number::spell](#method-number-spell)
- [Number::trim](#method-number-trim)
- [Number::useLocale](#method-number-use-locale)
- [Number::withLocale](#method-number-with-locale)
- [Number::useCurrency](#method-number-use-currency)
- [Number::withCurrency](#method-number-with-currency)

</div>

<a name="paths-method-list"></a>
### Шляхи

<div class="docs-column-list" markdown="1">

- [app_path](#method-app-path)
- [base_path](#method-base-path)
- [config_path](#method-config-path)
- [database_path](#method-database-path)
- [lang_path](#method-lang-path)
- [mix](#method-mix)
- [public_path](#method-public-path)
- [resource_path](#method-resource-path)
- [storage_path](#method-storage-path)

</div>

<a name="urls-method-list"></a>
### URL-адреси

<div class="docs-column-list" markdown="1">

- [action](#method-action)
- [asset](#method-asset)
- [route](#method-route)
- [secure_asset](#method-secure-asset)
- [secure_url](#method-secure-url)
- [to_route](#method-to-route)
- [url](#method-url)

</div>

<a name="miscellaneous-method-list"></a>
### Різне

<div class="docs-column-list" markdown="1">

- [abort](#method-abort)
- [abort_if](#method-abort-if)
- [abort_unless](#method-abort-unless)
- [app](#method-app)
- [auth](#method-auth)
- [back](#method-back)
- [bcrypt](#method-bcrypt)
- [blank](#method-blank)
- [broadcast](#method-broadcast)
- [cache](#method-cache)
- [class_uses_recursive](#method-class-uses-recursive)
- [collect](#method-collect)
- [config](#method-config)
- [context](#method-context)
- [cookie](#method-cookie)
- [csrf_field](#method-csrf-field)
- [csrf_token](#method-csrf-token)
- [decrypt](#method-decrypt)
- [dd](#method-dd)
- [dispatch](#method-dispatch)
- [dispatch_sync](#method-dispatch-sync)
- [dump](#method-dump)
- [encrypt](#method-encrypt)
- [env](#method-env)
- [event](#method-event)
- [fake](#method-fake)
- [filled](#method-filled)
- [info](#method-info)
- [literal](#method-literal)
- [logger](#method-logger)
- [method_field](#method-method-field)
- [now](#method-now)
- [old](#method-old)
- [once](#method-once)
- [optional](#method-optional)
- [policy](#method-policy)
- [redirect](#method-redirect)
- [report](#method-report)
- [report_if](#method-report-if)
  [report_unless](#method-report-unless)
- [request](#method-request)
- [rescue](#method-rescue)
- [resolve](#method-resolve)
- [response](#method-response)
- [retry](#method-retry)
- [session](#method-session)
- [tap](#method-tap)
- [throw_if](#method-throw-if)
- [throw_unless](#method-throw-unless)
- [today](#method-today)
- [trait_uses_recursive](#method-trait-uses-recursive)
- [transform](#method-transform)
- [validator](#method-validator)
- [value](#method-value)
- [view](#method-view)
- [with](#method-with)
- [when](#method-when)

</div>

<a name="arrays"></a>
## Масиви та об'єкти

<a name="method-array-accessible"></a>
#### `Arr::accessible()`

Метод `Arr::accessible` визначає, чи доступне передане значення масиву:

    use Illuminate\Support\Arr;
    use Illuminate\Support\Collection;

    $isAccessible = Arr::accessible(['a' => 1, 'b' => 2]);

    // true

    $isAccessible = Arr::accessible(new Collection);

    // true

    $isAccessible = Arr::accessible('abc');

    // false

    $isAccessible = Arr::accessible(new stdClass);

    // false

<a name="method-array-add"></a>
#### `Arr::add()`

Метод `Arr::add` додає передану пару ключ/значення в масив, якщо вказаний ключ ще не існує в масиві або встановлений як `null`:

    use Illuminate\Support\Arr;

    $array = Arr::add(['name' => 'Desk'], 'price', 100);

    // ['name' => 'Desk', 'price' => 100]

    $array = Arr::add(['name' => 'Desk', 'price' => null], 'price', 100);

    // ['name' => 'Desk', 'price' => 100]

<a name="method-array-collapse"></a>
#### `Arr::collapse()`

Метод `Arr::collapse` згортає масив масивів у один масив:

    use Illuminate\Support\Arr;

    $array = Arr::collapse([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);

    // [1, 2, 3, 4, 5, 6, 7, 8, 9]

<a name="method-array-crossjoin"></a>
#### `Arr::crossJoin()`

Метод `Arr::crossJoin` перехресно з'єднує зазначені масиви, повертаючи декартовий добуток з усіма можливими перестановками:

    use Illuminate\Support\Arr;

    $matrix = Arr::crossJoin([1, 2], ['a', 'b']);

    /*
        [
            [1, 'a'],
            [1, 'b'],
            [2, 'a'],
            [2, 'b'],
        ]
    */

    $matrix = Arr::crossJoin([1, 2], ['a', 'b'], ['I', 'II']);

    /*
        [
            [1, 'a', 'I'],
            [1, 'a', 'II'],
            [1, 'b', 'I'],
            [1, 'b', 'II'],
            [2, 'a', 'I'],
            [2, 'a', 'II'],
            [2, 'b', 'I'],
            [2, 'b', 'II'],
        ]
    */

<a name="method-array-divide"></a>
#### `Arr::divide()`

Метод `Arr::divide` повертає два масиви: один містить ключі, а інший - значення переданого масиву:

    use Illuminate\Support\Arr;

    [$keys, $values] = Arr::divide(['name' => 'Desk']);

    // $keys: ['name']

    // $values: ['Desk']

<a name="method-array-dot"></a>
#### `Arr::dot()`

Метод `Arr::dot` об'єднує багатовимірний масив в однорівневий, що використовує «точкову нотацію» для позначення глибини:

    use Illuminate\Support\Arr;

    $array = ['products' => ['desk' => ['price' => 100]]];

    $flattened = Arr::dot($array);

    // ['products.desk.price' => 100]

<a name="method-array-except"></a>
#### `Arr::except()`

Метод `Arr::except` видаляє передані пари ключ / значення з масиву:

    use Illuminate\Support\Arr;

    $array = ['name' => 'Desk', 'price' => 100];

    $filtered = Arr::except($array, ['price']);

    // ['name' => 'Desk']

<a name="method-array-exists"></a>
#### `Arr::exists()`

Метод `Arr::exists` перевіряє, чи існує переданий ключ у вказаному масиві:

    use Illuminate\Support\Arr;

    $array = ['name' => 'John Doe', 'age' => 17];

    $exists = Arr::exists($array, 'name');

    // true

    $exists = Arr::exists($array, 'salary');

    // false

<a name="method-array-first"></a>
#### `Arr::first()`

Метод `Arr::first` повертає перший елемент масиву, що пройшов тест переданого замикання на істинність:

    use Illuminate\Support\Arr;

    $array = [100, 200, 300];

    $first = Arr::first($array, function (int $value, int $key) {
        return $value >= 150;
    });

    // 200

Значення за замовчуванням може бути передано як третій аргумент методу. Це значення буде повернуто, якщо жодне зі значень не пройде перевірку на істинність:

    use Illuminate\Support\Arr;

    $first = Arr::first($array, $callback, $default);

<a name="method-array-flatten"></a>
#### `Arr::flatten()`

Метод `Arr::flatten` об'єднує багатовимірний масив в однорівневий:

    use Illuminate\Support\Arr;

    $array = ['name' => 'Joe', 'languages' => ['PHP', 'Ruby']];

    $flattened = Arr::flatten($array);

    // ['Joe', 'PHP', 'Ruby']

<a name="method-array-forget"></a>
#### `Arr::forget()`

Метод `Arr::forget` видаляє передану пару ключ / значення з глибоко вкладеного масиву, використовуючи «точкову нотацію»:

    use Illuminate\Support\Arr;

    $array = ['products' => ['desk' => ['price' => 100]]];

    Arr::forget($array, 'products.desk');

    // ['products' => []]

<a name="method-array-get"></a>
#### `Arr::get()`

Метод `Arr::get` витягує значення з глибоко вкладеного масиву, використовуючи «точкову нотацію»:

    use Illuminate\Support\Arr;

    $array = ['products' => ['desk' => ['price' => 100]]];

    $price = Arr::get($array, 'products.desk.price');

    // 100

Метод `Arr::get` також приймає значення за замовчуванням, яке буде повернуто, якщо вказаний ключ відсутній у масиві:

    use Illuminate\Support\Arr;

    $discount = Arr::get($array, 'products.desk.discount', 0);

    // 0

<a name="method-array-has"></a>
#### `Arr::has()`

Метод `Arr::has` перевіряє, чи існує переданий елемент або елементи в масиві, використовуючи «точкову нотацію»:

    use Illuminate\Support\Arr;

    $array = ['product' => ['name' => 'Desk', 'price' => 100]];

    $contains = Arr::has($array, 'product.name');

    // true

    $contains = Arr::has($array, ['product.price', 'product.discount']);

    // false

<a name="method-array-hasany"></a>
#### `Arr::hasAny()`

Метод `Arr::hasAny` перевіряє, чи існує будь-який елемент у переданому наборі в масиві, використовуючи «точкову нотацію»:

    use Illuminate\Support\Arr;

    $array = ['product' => ['name' => 'Desk', 'price' => 100]];

    $contains = Arr::hasAny($array, 'product.name');

    // true

    $contains = Arr::hasAny($array, ['product.name', 'product.discount']);

    // true

    $contains = Arr::hasAny($array, ['category', 'product.discount']);

    // false

<a name="method-array-isassoc"></a>
#### `Arr::isAssoc()`

Метод `Arr::isAssoc` повертає `true`, якщо переданий масив є асоціативним. Масив вважається асоціативним, якщо в ньому немає послідовних цифрових ключів, що починаються з нуля:

    use Illuminate\Support\Arr;

    $isAssoc = Arr::isAssoc(['product' => ['name' => 'Desk', 'price' => 100]]);

    // true

    $isAssoc = Arr::isAssoc([1, 2, 3]);

    // false

<a name="method-array-islist"></a>
#### `Arr::isList()`

Метод `Arr::isList` повертає true, якщо ключі заданого масиву являють собою послідовні цілі числа, починаючи з нуля:


    use Illuminate\Support\Arr;

    $isList = Arr::isList(['foo', 'bar', 'baz']);

    // true

    $isList = Arr::isList(['product' => ['name' => 'Desk', 'price' => 100]]);

    // false

<a name="method-array-join"></a>
#### `Arr::join()`

Метод `Arr::join` об'єднує елементи масиву в рядок. Використовуючи другий аргумент цього методу ви також можете вказати рядок для з'єднання останнього елемента масиву:

    use Illuminate\Support\Arr;

    $array = ['Tailwind', 'Alpine', 'Laravel', 'Livewire'];

    $joined = Arr::join($array, ', ');

    // Tailwind, Alpine, Laravel, Livewire

    $joined = Arr::join($array, ', ', ' and ');

    // Tailwind, Alpine, Laravel and Livewire

<a name="method-array-keyby"></a>
#### `Arr::keyBy()`

Метод `Arr::keyBy` присвоює ключі елементам базового масиву на основі зазначеного ключа.  Якщо у кількох елементів один і той самий ключ, у новому масиві з'явиться тільки останній:

    use Illuminate\Support\Arr;

    $array = [
        ['product_id' => 'prod-100', 'name' => 'Desk'],
        ['product_id' => 'prod-200', 'name' => 'Chair'],
    ];

    $keyed = Arr::keyBy($array, 'product_id');

    /*
        [
            'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
            'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]
    */

<a name="method-array-last"></a>
#### `Arr::last()`

Метод `Arr::last` повертає останній елемент масиву, що пройшов тест переданого замикання на істинність:

    use Illuminate\Support\Arr;

    $array = [100, 200, 300, 110];

    $last = Arr::last($array, function (int $value, int $key) {
        return $value >= 150;
    });

    // 300

Значення за замовчуванням може бути передано як третій аргумент методу. Це значення буде повернуто, якщо жодне зі значень не пройде перевірку на істинність:

    use Illuminate\Support\Arr;

    $last = Arr::last($array, $callback, $default);

<a name="method-array-map"></a>
#### `Arr::map()`

Метод `Arr::map` проходить по масиву і передає кожне значення і ключ зазначеної функції зворотного виклику. Значення масиву замінюється значенням, що повертається зворотним викликом:

    use Illuminate\Support\Arr;

    $array = ['first' => 'james', 'last' => 'kirk'];

    $mapped = Arr::map($array, function (string $value, string $key) {
        return ucfirst($value);
    });

    // ['first' => 'James', 'last' => 'Kirk']

<a name="method-array-map-spread"></a>
#### `Arr::mapSpread()`

Метод `Arr::mapSpread` виконує ітерацію по масиву, передаючи кожне значення вкладеного елемента в дане замикання. Замикання може змінювати елемент і повертати його, формуючи таким чином новий масив змінених елементів:

    use Illuminate\Support\Arr;

    $array = [
        [0, 1],
        [2, 3],
        [4, 5],
        [6, 7],
        [8, 9],
    ];

    $mapped = Arr::mapSpread($array, function (int $even, int $odd) {
        return $even + $odd;
    });

    /*
        [1, 5, 9, 13, 17]
    */

<a name="method-array-map-with-keys"></a>
#### `Arr::mapWithKeys()`


Метод `Arr::mapWithKeys` проходить по масиву і передає кожне значення зазначеній функції зворотного виклику, яка повинна повертати асоціативний масив, що містить одну пару ключ / значення:

use Illuminate\Support\Arr;

    $array = [
        [
            'name' => 'John',
            'department' => 'Sales',
            'email' => 'john@example.com',
        ],
        [
            'name' => 'Jane',
            'department' => 'Marketing',
            'email' => 'jane@example.com',
        ]
    ];

    $mapped = Arr::mapWithKeys($array, function (array $item, int $key) {
        return [$item['email'] => $item['name']];
    });

    /*
        [
            'john@example.com' => 'John',
            'jane@example.com' => 'Jane',
        ]
    */

<a name="method-array-only"></a>
#### `Arr::only()`

Метод `Arr::only` повертає тільки зазначені пари ключ / значення з переданого масиву:

    use Illuminate\Support\Arr;

    $array = ['name' => 'Desk', 'price' => 100, 'orders' => 10];

    $slice = Arr::only($array, ['name', 'price']);

    // ['name' => 'Desk', 'price' => 100]

<a name="method-array-pluck"></a>
#### `Arr::pluck()`

Метод `Arr::pluck` витягує всі значення для вказаного ключа з масиву:

    use Illuminate\Support\Arr;

    $array = [
        ['developer' => ['id' => 1, 'name' => 'Taylor']],
        ['developer' => ['id' => 2, 'name' => 'Abigail']],
    ];

    $names = Arr::pluck($array, 'developer.name');

    // ['Taylor', 'Abigail']

Ви також можете задати ключ результуючого списку:

    use Illuminate\Support\Arr;

    $names = Arr::pluck($array, 'developer.name', 'developer.id');

    // [1 => 'Taylor', 2 => 'Abigail']

<a name="method-array-prepend"></a>
#### `Arr::prepend()`

Метод `Arr::prepend` поміщає елемент на початок масиву:

    use Illuminate\Support\Arr;

    $array = ['one', 'two', 'three', 'four'];

    $array = Arr::prepend($array, 'zero');

    // ['zero', 'one', 'two', 'three', 'four']

За необхідності ви можете вказати ключ, який слід використовувати для значення:

    use Illuminate\Support\Arr;

    $array = ['price' => 100];

    $array = Arr::prepend($array, 'Desk', 'name');

    // ['name' => 'Desk', 'price' => 100]

<a name="method-array-prependkeyswith"></a>
#### `Arr::prependKeysWith()`


Метод `Arr::prependKeysWith` додає вказаний префікс до всіх імен ключів асоціативного масиву:

    use Illuminate\Support\Arr;

    $array = [
        'name' => 'Desk',
        'price' => 100,
    ];

    $keyed = Arr::prependKeysWith($array, 'product.');

    /*
        [
            'product.name' => 'Desk',
            'product.price' => 100,
        ]
    */

<a name="method-array-pull"></a>
#### `Arr::pull()`

Метод `Arr::pull` повертає і видаляє пару ключ / значення з масиву:

    use Illuminate\Support\Arr;

    $array = ['name' => 'Desk', 'price' => 100];

    $name = Arr::pull($array, 'name');

    // $name: Desk

    // $array: ['price' => 100]

Значення за замовчуванням може бути передано як третій аргумент методу. Це значення буде повернуто, якщо ключ не існує:

    use Illuminate\Support\Arr;

    $value = Arr::pull($array, $key, $default);

<a name="method-array-query"></a>
#### `Arr::query()`

Метод `Arr::query` перетворює масив на рядок запиту:

    use Illuminate\Support\Arr;

    $array = [
        'name' => 'Taylor',
        'order' => [
            'column' => 'created_at',
            'direction' => 'desc'
        ]
    ];

    Arr::query($array);

    // name=Taylor&order[column]=created_at&order[direction]=desc

<a name="method-array-random"></a>
#### `Arr::random()`

Метод `Arr::random` повертає випадкове значення з масиву:

    use Illuminate\Support\Arr;

    $array = [1, 2, 3, 4, 5];

    $random = Arr::random($array);

    // 4 - (retrieved randomly)

Ви також можете вказати кількість елементів для повернення як необов'язковий другий аргумент. Зверніть увагу, що при зазначенні цього аргументу, буде повернуто масив, навіть якщо потрібен тільки один елемент:

    use Illuminate\Support\Arr;

    $items = Arr::random($array, 2);

    // [2, 5] - (retrieved randomly)

<a name="method-array-set"></a>
#### `Arr::set()`

Метод `Arr::set` встановлює значення за допомогою «точкової нотації» у вкладеному масиві:

    use Illuminate\Support\Arr;

    $array = ['products' => ['desk' => ['price' => 100]]];

    Arr::set($array, 'products.desk.price', 200);

    // ['products' => ['desk' => ['price' => 200]]]

<a name="method-array-shuffle"></a>
#### `Arr::shuffle()`

Метод `Arr::shuffle` випадковим чином перемішує елементи в масиві:

    use Illuminate\Support\Arr;

    $array = Arr::shuffle([1, 2, 3, 4, 5]);

    // [3, 2, 5, 1, 4] - (generated randomly)

<a name="method-array-sort"></a>
#### `Arr::sort()`

Метод `Arr::sort` сортує масив за його значеннями:

    use Illuminate\Support\Arr;

    $array = ['Desk', 'Table', 'Chair'];

    $sorted = Arr::sort($array);

    // ['Chair', 'Desk', 'Table']

Ви також можете відсортувати масив за результатами переданого замикання:

    use Illuminate\Support\Arr;

    $array = [
        ['name' => 'Desk'],
        ['name' => 'Table'],
        ['name' => 'Chair'],
    ];

    $sorted = array_values(Arr::sort($array, function (array $value) {
        return $value['name'];
    }));

    /*
        [
            ['name' => 'Chair'],
            ['name' => 'Desk'],
            ['name' => 'Table'],
        ]
    */

<a name="method-array-sort-desc"></a>
#### `Arr::sortDesc()`

Метод `Arr::sortDesc` сортує масив за спаданням значень:

    use Illuminate\Support\Arr;

    $array = ['Desk', 'Table', 'Chair'];

    $sorted = Arr::sortDesc($array);

    // ['Table', 'Desk', 'Chair']

Ви також можете відсортувати масив за результатами переданого замикання:

    use Illuminate\Support\Arr;

    $array = [
        ['name' => 'Desk'],
        ['name' => 'Table'],
        ['name' => 'Chair'],
    ];

    $sorted = array_values(Arr::sortDesc($array, function (array $value) {
        return $value['name'];
    }));

    /*
        [
            ['name' => 'Table'],
            ['name' => 'Desk'],
            ['name' => 'Chair'],
        ]
    */

<a name="method-array-sort-recursive"></a>
#### `Arr::sortRecursive()`

Метод `Arr::sortRecursive` рекурсивно сортує масив за допомогою методу `sort` для числових підмасивів і `ksort` для асоціативних підмасивів:

    use Illuminate\Support\Arr;

    $array = [
        ['Roman', 'Taylor', 'Li'],
        ['PHP', 'Ruby', 'JavaScript'],
        ['one' => 1, 'two' => 2, 'three' => 3],
    ];

    $sorted = Arr::sortRecursive($array);

    /*
        [
            ['JavaScript', 'PHP', 'Ruby'],
            ['one' => 1, 'three' => 3, 'two' => 2],
            ['Li', 'Roman', 'Taylor'],
        ]
    */

Якщо ви хочете, щоб результати були відсортовані за зменшенням, ви можете використати метод` Arr::sortRecursiveDesc`.

    $sorted = Arr::sortRecursiveDesc($array);

<a name="method-array-take"></a>
#### `Arr::take()` {.collection-method}

Метод `Arr::take` повертає новий масив із зазначеною кількістю елементів:

    use Illuminate\Support\Arr;

    $array = [0, 1, 2, 3, 4, 5];

    $chunk = Arr::take($array, 3);

    // [0, 1, 2]

Ви також можете передати від'ємне ціле число, щоб отримати вказану кількість елементів з кінця масиву:

    $array = [0, 1, 2, 3, 4, 5];

    $chunk = Arr::take($array, -2);

    // [4, 5]

<a name="method-array-to-css-classes"></a>
#### `Arr::toCssClasses()`

Метод `Arr::toCssClasses` складає рядок класів CSS виходячи із заданих умов. Метод приймає масив класів, де ключ масиву містить клас або класи, які ви хочете додати, а значення є булевим виразом. Якщо елемент масиву не має рядкового ключа, він завжди буде включений до списку відмальованих класів:

    use Illuminate\Support\Arr;

    $isActive = false;
    $hasError = true;

    $array = ['p-4', 'font-bold' => $isActive, 'bg-red' => $hasError];

    $classes = Arr::toCssClasses($array);

    /*
        'p-4 bg-red'
    */

<a name="method-array-to-css-styles"></a>
#### `Arr::toCssStyles()`

Метод `Arr::toCssStyles` умовно компілює рядок стилів CSS. Метод приймає масив класів, де ключ масиву містить клас або класи, які ви хочете додати, а значення - логічний вираз. Якщо елемент масиву має числовий ключ, його завжди буде включено до списку відображуваних класів:

```php
use Illuminate\Support\Arr;

$hasColor = true;

$array = ['background-color: blue', 'color: blue' => $hasColor];

$classes = Arr::toCssStyles($array);

/*
    'background-color: blue; color: blue;'
*/
```

За допомогою цього методу здійснюється [об'єднання css-класів у Blade](/docs/{{version}}/blade#conditionally-merge-classes), а також [у директиві](/docs/{{{version}}/blade#conditional-classes) `@class`.

<a name="method-array-undot"></a>
#### `Arr::undot()`

Метод `Arr::undot` розширює одновимірний масив, що використовує «точкову нотацію», у багатовимірний масив:

    use Illuminate\Support\Arr;

    $array = [
        'user.name' => 'Kevin Malone',
        'user.occupation' => 'Accountant',
    ];

    $array = Arr::undot($array);

    // ['user' => ['name' => 'Kevin Malone', 'occupation' => 'Accountant']]

<a name="method-array-where"></a>
#### `Arr::where()`

Метод `Arr::where` фільтрує масив, використовуючи передане замикання:

    use Illuminate\Support\Arr;

    $array = [100, '200', 300, '400', 500];

    $filtered = Arr::where($array, function (string|int $value, int $key) {
        return is_string($value);
    });

    // [1 => '200', 3 => '400']

<a name="method-array-where-not-null"></a>
#### `Arr::whereNotNull()`

Метод `Arr::whereNotNull` видаляє всі значення `null` з даного масиву:

    use Illuminate\Support\Arr;

    $array = [0, null];

    $filtered = Arr::whereNotNull($array);

    // [0 => 0]

<a name="method-array-wrap"></a>
#### `Arr::wrap()`

Метод `Arr::wrap` обертає передане значення в масив. Якщо передане значення вже є масивом, то воно буде повернуто без змін:

    use Illuminate\Support\Arr;

    $string = 'Laravel';

    $array = Arr::wrap($string);

    // ['Laravel']

Якщо передане значення дорівнює `null`, то буде повернуто порожній масив:

    use Illuminate\Support\Arr;

    $array = Arr::wrap(null);

    // []

<a name="method-data-fill"></a>
#### `data_fill()`

Функція `data_fill` встановлює відсутнє значення за допомогою «точкової нотації» у вкладеному масиві або об'єкті:

    $data = ['products' => ['desk' => ['price' => 100]]];

    data_fill($data, 'products.desk.price', 200);

    // ['products' => ['desk' => ['price' => 100]]]

    data_fill($data, 'products.desk.discount', 10);

    // ['products' => ['desk' => ['price' => 100, 'discount' => 10]]]

Допускається використання метасимволу підстановки `*`:

    $data = [
        'products' => [
            ['name' => 'Desk 1', 'price' => 100],
            ['name' => 'Desk 2'],
        ],
    ];

    data_fill($data, 'products.*.price', 200);

    /*
        [
            'products' => [
                ['name' => 'Desk 1', 'price' => 100],
                ['name' => 'Desk 2', 'price' => 200],
            ],
        ]
    */

<a name="method-data-get"></a>
#### `data_get()`

Функція `data_get` повертає значення за допомогою «точкової нотації» з вкладеного масиву або об'єкта:

    $data = ['products' => ['desk' => ['price' => 100]]];

    $price = data_get($data, 'products.desk.price');

    // 100

Функція `data_get` також приймає значення за замовчуванням, яке буде повернуто, якщо вказаний ключ не знайдено:

    $discount = data_get($data, 'products.desk.discount', 0);

    // 0

Допускається використання метасимволу підстановки `*`, призначений для будь-якого ключа масиву або об'єкта:

    $data = [
        'product-one' => ['name' => 'Desk 1', 'price' => 100],
        'product-two' => ['name' => 'Desk 2', 'price' => 150],
    ];

    data_get($data, '*.name');

    // ['Desk 1', 'Desk 2'];

Заповнювачі `{first}` і `{last}` можуть використовуватися для отримання першого або останнього елемента масиву:

    $flight = [
        'segments' => [
            ['from' => 'LHR', 'departure' => '9:00', 'to' => 'IST', 'arrival' => '15:00'],
            ['from' => 'IST', 'departure' => '16:00', 'to' => 'PKX', 'arrival' => '20:00'],
        ],
    ];

    data_get($flight, 'segments.{first}.arrival');

    // 15:00

<a name="method-data-set"></a>
#### `data_set()`

Функція `data_set` встановлює значення за допомогою «точкової нотації» у вкладеному масиві або об'єкті:

    $data = ['products' => ['desk' => ['price' => 100]]];

    data_set($data, 'products.desk.price', 200);

    // ['products' => ['desk' => ['price' => 200]]]

Допускається використання метасимволу підстановки `*`:

    $data = [
        'products' => [
            ['name' => 'Desk 1', 'price' => 100],
            ['name' => 'Desk 2', 'price' => 150],
        ],
    ];

    data_set($data, 'products.*.price', 200);

    /*
        [
            'products' => [
                ['name' => 'Desk 1', 'price' => 200],
                ['name' => 'Desk 2', 'price' => 200],
            ],
        ]
    */

За замовчуванням усі наявні значення перезаписуються. Якщо ви хочете, щоб значення було встановлено тільки в тому випадку, якщо воно не існує, ви можете передати `false` як четвертий аргумент:

    $data = ['products' => ['desk' => ['price' => 100]]];

    data_set($data, 'products.desk.price', 200, overwrite: false);

    // ['products' => ['desk' => ['price' => 100]]]

<a name="method-data-forget"></a>
#### `data_forget()`

Функція `data_forget` видаляє значення всередині вкладеного масиву або об'єкта, використовуючи «точкову» нотацію:

    $data = ['products' => ['desk' => ['price' => 100]]];

    data_forget($data, 'products.desk.price');

    // ['products' => ['desk' => []]]

Ця функція також приймає маски з використанням зірочок і видаляє відповідні значення з цілі:

    $data = [
        'products' => [
            ['name' => 'Desk 1', 'price' => 100],
            ['name' => 'Desk 2', 'price' => 150],
        ],
    ];

    data_forget($data, 'products.*.price');

    /*
        [
            'products' => [
                ['name' => 'Desk 1'],
                ['name' => 'Desk 2'],
            ],
        ]
    */

<a name="method-head"></a>
#### `head()`

Функція `head` повертає перший елемент переданого масиву:

    $array = [100, 200, 300];

    $first = head($array);

    // 100

<a name="method-last"></a>
#### `last()`

Функція `last` повертає останній елемент переданого масиву:

    $array = [100, 200, 300];

    $last = last($array);

    // 300

<a name="numbers"></a>
## Числа

<a name="method-number-abbreviate"></a>
#### `Number::abbreviate()`

Метод `Number::abbreviate` повертає числове значення в читабельному форматі зі скороченням для одиниць виміру:

    use Illuminate\Support\Number;

    $number = Number::abbreviate(1000);

    // 1K

    $number = Number::abbreviate(489939);

    // 490K

    $number = Number::abbreviate(1230000, precision: 2);

    // 1.23M

<a name="method-number-clamp"></a>
#### `Number::clamp()` {.collection-method}

Метод `Number::clamp` гарантує, що задане число залишиться в заданому діапазоні. Якщо число менше мінімуму, повертається мінімальне значення. Якщо число більше максимуму, повертається максимальне значення:

    use Illuminate\Support\Number;

    $number = Number::clamp(105, min: 10, max: 100);

    // 100

    $number = Number::clamp(5, min: 10, max: 100);

    // 10

    $number = Number::clamp(10, min: 10, max: 100);

    // 10

    $number = Number::clamp(20, min: 10, max: 100);

    // 20

<a name="method-number-currency"></a>
#### `Number::currency()`

Метод `Number::currency` повертає представлення зазначеного значення у валюті у вигляді рядка:

    use Illuminate\Support\Number;

    $currency = Number::currency(1000);

    // $1,000.00

    $currency = Number::currency(1000, in: 'EUR');

    // €1,000.00

    $currency = Number::currency(1000, in: 'EUR', locale: 'de');

    // 1.000,00 €

<a name="method-default-currency"></a>
#### `Number::defaultCurrency()`

Метод `Number::defaultCurrency` повертає валюту за замовчуванням, використовувану класом `Number`:

    use Illuminate\Support\Number;

    $currency = Number::defaultCurrency();

    // USD

<a name="method-default-locale"></a>
#### `Number::defaultLocale()`

Метод `Number::defaultLocale` повертає локаль за замовчуванням, використовувану класом `Number`:

    use Illuminate\Support\Number;

    $locale = Number::defaultLocale();

    // en

<a name="method-number-file-size"></a>
#### `Number::fileSize()`

Метод `Number::fileSize` для вказаного значення в байтах повертає подання розміру файлу у вигляді рядка:

    use Illuminate\Support\Number;

    $size = Number::fileSize(1024);

    // 1 KB

    $size = Number::fileSize(1024 * 1024);

    // 1 MB

    $size = Number::fileSize(1024, precision: 2);

    // 1.00 KB

<a name="method-number-for-humans"></a>
#### `Number::forHumans()`

Метод Number::forHumans повертає числове значення в читабельному форматі:

    use Illuminate\Support\Number;

    $number = Number::forHumans(1000);

    // 1 thousand

    $number = Number::forHumans(489939);

    // 490 thousand

    $number = Number::forHumans(1230000, precision: 2);

    // 1.23 million

<a name="method-number-format"></a>
#### `Number::format()`

Метод `Number::format` форматує надане число в рядок з урахуванням локалізації:

use Illuminate\Support\Number;

    $number = Number::format(100000);

    // 100,000

    $number = Number::format(100000, precision: 2);

    // 100,000.00

    $number = Number::format(100000.123, maxPrecision: 2);

    // 100,000.12

    $number = Number::format(100000, locale: 'de');

    // 100.000

<a name="method-number-ordinal"></a>
#### `Number::ordinal()` {.collection-method}

Метод `Number::ordinal` повертає порядкове представлення числа:

    use Illuminate\Support\Number;

    $number = Number::ordinal(1);

    // 1st

    $number = Number::ordinal(2);

    // 2nd

    $number = Number::ordinal(21);

    // 21st

<a name="method-number-pairs"></a>
#### `Number::pairs()`

Метод `Number::pairs` генерує масив пар чисел (піддіапазонів) на основі зазначеного діапазону і значення кроку. Цей метод може бути корисний для поділу більшого діапазону чисел на більш дрібні, керовані піддіапазони для таких завдань, як розбивка на сторінки або пакетна обробка. Метод `pairs` повертає масив масивів, де кожен внутрішній масив представляє пару (піддіапазон) чисел:

```php
use Illuminate\Support\Number;

$result = Number::pairs(25, 10);

// [[1, 10], [11, 20], [21, 25]]

$result = Number::pairs(25, 10, offset: 0);

// [[0, 10], [10, 20], [20, 25]]
```

<a name="method-number-percentage"></a>
#### `Number::percentage()`

Метод `Number::percentage` повертає відсоткове представлення вказаного значення у вигляді рядка:

    use Illuminate\Support\Number;

    $percentage = Number::percentage(10);

    // 10%

    $percentage = Number::percentage(10, precision: 2);

    // 10.00%

    $percentage = Number::percentage(10.123, maxPrecision: 2);

    // 10.12%

    $percentage = Number::percentage(10, precision: 2, locale: 'de');

    // 10,00%

<a name="method-number-spell"></a>
#### `Number::spell()` {.collection-method}

Метод `Number::spell` повертає задане число прописом:

    use Illuminate\Support\Number;

    $number = Number::spell(102);

    // one hundred and two

    $number = Number::spell(88, locale: 'fr');

    // quatre-vingt-huit

Аргумент `after` дозволяє вказати значення, після якого всі числа мають бути прописом:

    $number = Number::spell(10, after: 10);

    // 10

    $number = Number::spell(11, after: 10);

    // eleven

Аргумент `until` дозволяє вказати значення, до якого всі числа мають бути прописом:

    $number = Number::spell(5, until: 10);

    // five

    $number = Number::spell(10, until: 10);

    // 10

<a name="method-number-trim"></a>
#### `Number::trim()`

Метод `Number::trim` видаляє всі кінцеві нульові цифри після десяткової крапки заданого числа:

    use Illuminate\Support\Number;

    $number = Number::trim(12.0);

    // 12

    $number = Number::trim(12.30);

    // 12.3

<a name="method-number-use-locale"></a>
#### `Number::useLocale()` {.collection-method}

Метод `Number::useLocale` глобально встановлює мовний стандарт чисел за замовчуванням, що впливає на форматування чисел і валюти під час наступних звернень до методів класу `Number`:

    use Illuminate\Support\Number;

    /**
     * Загрузка любых служб пакета.
     */
    public function boot(): void
    {
        Number::useLocale('de');
    }

<a name="method-number-with-locale"></a>
#### `Number::withLocale()` {.collection-method}

Метод `Number::withLocale` виконує задане замикання з використанням зазначеного мовного стандарту, а потім відновлює вихідний мовний стандарт після виконання замикання:

    use Illuminate\Support\Number;

    $number = Number::withLocale('de', function () {
        return Number::format(1500);
    });

<a name="method-number-use-currency"></a>
#### `Number::useCurrency()`

Метод `Number::useCurrency` встановлює глобальну числову валюту за замовчуванням, що впливає на форматування валюти при наступних викликах методів класу `Number`:

    use Illuminate\Support\Number;

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Number::useCurrency('GBP');
    }

<a name="method-number-with-currency"></a>
#### `Number::withCurrency()`

Метод `Number::withCurrency` виконує дане замикання, використовуючи зазначену валюту, а потім відновлює вихідну валюту після виконання зворотного виклику:

    use Illuminate\Support\Number;

    $number = Number::withCurrency('GBP', function () {
        // ...
    });

<a name="paths"></a>
## Шляхи

<a name="method-app-path"></a>
#### `app_path()`

Функція `app_path` повертає повний шлях до каталогу вашого додатка `app`. Ви також можете використовувати функцію `app_path` для створення повного шляху до файлу відносно каталогу додатка:

    $path = app_path();

    $path = app_path('Http/Controllers/Controller.php');

<a name="method-base-path"></a>
#### `base_path()`

Функція `base_path` повертає повний шлях до кореневого каталогу вашого додатка. Ви також можете використовувати функцію `base_path` для генерації повного шляху до заданого файлу відносно кореневого каталогу проекту:

    $path = base_path();

    $path = base_path('vendor/bin');

<a name="method-config-path"></a>
#### `config_path()`

Функція `config_path` повертає повний шлях до каталогу `config` вашого додатка. Ви також можете використовувати функцію `config_path` для створення повного шляху до заданого файлу в каталозі конфігурації програми:

    $path = config_path();

    $path = config_path('app.php');

<a name="method-database-path"></a>
#### `database_path()`

Функція `database_path` повертає повний шлях до каталогу `database` вашого додатка. Ви також можете використовувати функцію `database_path` для генерації повного шляху до заданого файлу в каталозі бази даних:

    $path = database_path();

    $path = database_path('factories/UserFactory.php');

<a name="method-lang-path"></a>
#### `lang_path()`

Функція `lang_path` повертає повний шлях до каталогу `lang` вашого додатка. Ви також можете використовувати функцію `lang_path` для генерації повного шляху до вказаного файлу всередині цього каталогу:

    $path = lang_path();

    $path = lang_path('en/messages.php');

> [!NOTE]
> За замовчуванням у структурі програми Laravel відсутній каталог `lang`. Якщо ви хочете налаштувати мовні файли Laravel, ви можете опублікувати їх за допомогою команди Artisan `lang:publish`.

<a name="method-mix"></a>
#### `mix()`

Функція `mix` повертає шлях до [версіонованого файлу Mix](/docs/{{version}}/mix#versioning-and-cache-busting):

    $path = mix('css/app.css');

<a name="method-public-path"></a>
#### `public_path()`

Функція `public_path` повертає повний шлях до каталогу `public` вашого додатка. Ви також можете використовувати функцію `public_path` для генерації повного шляху до заданого файлу в публічному каталозі:

    $path = public_path();

    $path = public_path('css/app.css');

<a name="method-resource-path"></a>
#### `resource_path()`

Функція `resource_path` повертає повний шлях до каталогу `resources` вашого додатка. Ви також можете використовувати функцію `resource_path`, щоб згенерувати повний шлях до заданого файлу в каталозі вихідних кодів:

    $path = resource_path();

    $path = resource_path('sass/app.scss');

<a name="method-storage-path"></a>
#### `storage_path()`

Функція `storage_path` повертає повний шлях до каталогу `storage` вашого додатка. Ви також можете використовувати функцію `storage_path` для генерації повного шляху до заданого файлу в каталозі сховища:

    $path = storage_path();

    $path = storage_path('app/file.txt');

<a name="urls"></a>
## URL-адреси

<a name="method-action"></a>
#### `action()`

Функція `action` генерує URL-адресу для переданої дії контролера:

    use App\Http\Controllers\HomeController;

    $url = action([HomeController::class, 'index']);

Якщо метод приймає параметри маршруту, ви можете передати їх як другий аргумент методу:

    $url = action([UserController::class, 'profile'], ['id' => 1]);

<a name="method-asset"></a>
#### `asset()`

Функція `asset` генерує URL для вихідника (прим. перекл.: директорія `resources`), використовуючи поточну схему запиту (HTTP або HTTPS):

    $url = asset('img/photo.jpg');

Ви можете налаштувати хост URL вихідних кодів, встановивши змінну `ASSET_URL` у вашому файлі `.env`. Це може бути корисно, якщо ви розміщуєте свої вихідні коди на зовнішньому сервісі, такому як Amazon S3 або інший CDN:

    // ASSET_URL=http://example.com/assets

    $url = asset('img/photo.jpg'); // http://example.com/assets/img/photo.jpg

<a name="method-route"></a>
#### `route()`

Функція `route` генерує URL для переданого [іменованого маршруту](/docs/{{version}}/routing#named-routes):

    $url = route('route.name');

Якщо маршрут приймає параметри, ви можете передати їх як другий аргумент методу:

    $url = route('route.name', ['id' => 1]);

За замовчуванням функція `route` генерує абсолютний URL. Якщо ви хочете створити відносний URL, ви можете передати `false` як третій аргумент:

    $url = route('route.name', ['id' => 1], false);

<a name="method-secure-asset"></a>
#### `secure_asset()`

Функція `secure_asset` генерує URL для вихідника, використовуючи HTTPS:

    $url = secure_asset('img/photo.jpg');

<a name="method-secure-url"></a>
#### `secure_url()`

Функція `secure_url` генерує повну URL-адресу для вказаного шляху, використовуючи HTTPS. Додаткові сегменти URL можуть бути передані в другому аргументі функції:

    $url = secure_url('user/profile');

    $url = secure_url('user/profile', [1]);

<a name="method-to-route"></a>
#### `to_route()`

Функція `to_route` генерує [HTTP-відповідь перенаправлення](/docs/{{version}}}/responses#redirects) для заданого [іменованого маршруту](/docs/{{{version}}}/routing#named-routes) :

    return to_route('users.show', ['user' => 1]);

За необхідності ви можете передати код статусу HTTP, який має бути присвоєний перенаправленню, і будь-які додаткові заголовки відповіді як третій і четвертий аргументи методу `to_route`:

    return to_route('users.show', ['user' => 1], 302, ['X-Framework' => 'Laravel']);

<a name="method-url"></a>
#### `url()`

Функція `url` генерує повну URL-адресу для вказаного шляху:

    $url = url('user/profile');

    $url = url('user/profile', [1]);

Якщо шлях не вказано, буде повернуто екземпляр `Illuminate\Routing\UrlGenerator`:

    $current = url()->current();

    $full = url()->full();

    $previous = url()->previous();

<a name="miscellaneous"></a>
## Різне

<a name="method-abort"></a>
#### `abort()`

Функція `abort` генерує [HTTP-виняток](/docs/{{version}}/errors#http-exceptions), який буде опрацьований [обробником винятку](/docs/{{{version}}}/errors#handling-exceptions):

    abort(403);

Ви також можете вказати текст відповіді винятку та користувацькі заголовки відповіді, які мають бути надіслані в браузер:

    abort(403, 'Unauthorized.', $headers);

<a name="method-abort-if"></a>
#### `abort_if()`

Функція `abort_if` генерує виняток HTTP, якщо переданий логічний вираз має значення `true`:

    abort_if(! Auth::user()->isAdmin(), 403);

Подібно до методу `abort`, ви також можете вказати текст відповіді винятку третім аргументом і масив користувацьких заголовків відповіді як четвертий аргумент.

<a name="method-abort-unless"></a>
#### `abort_unless()`

Функція `abort_unless` генерує виняток HTTP, якщо переданий логічний вираз оцінюється як `false`:

    abort_unless(Auth::user()->isAdmin(), 403);

Подібно до методу `abort`, ви також можете вказати текст відповіді винятку третім аргументом і масив користувацьких заголовків відповіді як четвертий аргумент.

<a name="method-app"></a>
#### `app()`

Функція `app` повертає екземпляр [контейнера служб](/docs/{{version}}/container):

    $container = app();

Ви можете передати ім'я класу або інтерфейсу для вилучення його з контейнера:

    $api = app('HelpSpot\API');

<a name="method-auth"></a>
#### `auth()`

Функція `auth` повертає екземпляр [аутентифікатора](authentication). Ви можете використовувати його замість фасаду `Auth` для зручності:

    $user = auth()->user();

За необхідності ви можете вказати, до якого примірника охоронця ви хочете отримати доступ:

    $user = auth('admin')->user();

<a name="method-back"></a>
#### `back()`

Функція `back` генерує [HTTP-відповідь перенаправлення](responses#redirects) у попереднє розташування користувача:

    return back($status = 302, $headers = [], $fallback = '/');

    return back();

<a name="method-bcrypt"></a>
#### `bcrypt()`

Функція `bcrypt` [хешує](/docs/{{version}}/hashing) передане значення, використовуючи Bcrypt. Ви можете використовувати його як альтернативу фасаду `Hash`:

    $password = bcrypt('my-secret-password');

<a name="method-blank"></a>
#### `blank()`

Функція `blank` перевіряє, чи є передане значення «порожнім»:

    blank('');
    blank(' ');
    blank(null);
    blank(collect());

    // true

    blank(0);
    blank(true);
    blank(false);

    // false

Зворотною функції `blank` є функція [`filled`](#method-filled).

<a name="method-broadcast"></a>
#### `broadcast()`

Функція `broadcast` [транслює](/docs/{{version}}/broadcasting) передану [подію](/docs/{{version}}/events) своїм слухачам:

    broadcast(new UserRegistered($user));

    broadcast(new UserRegistered($user))->toOthers();

<a name="method-cache"></a>
#### `cache()`

Функція `cache` використовується для отримання значень з [кеша](/docs/{{version}}/cache). Якщо переданий ключ не існує в кеші, буде повернуто необов'язкове значення за замовчуванням:

    $value = cache('key');

    $value = cache('key', 'default');

Ви можете додавати елементи в кеш, передаючи масив пар ключ / значення у функцію. Ви також повинні передати кількість секунд або тривалість актуальності кешованого значення:

    cache(['key' => 'value'], 300);

    cache(['key' => 'value'], now()->addSeconds(10));

<a name="method-class-uses-recursive"></a>
#### `class_uses_recursive()`

Функція `class_uses_recursive` повертає всі трейти, що використовуються класом, включно з трейти, що використовуються всіма його батьківськими класами:

    $traits = class_uses_recursive(App\Models\User::class);

<a name="method-collect"></a>
#### `collect()`

Функція `collect` створює екземпляр [колекції](/docs/{{version}}/collections) переданого значення:

    $collection = collect(['taylor', 'abigail']);

<a name="method-config"></a>
#### `config()`

Функція `config` отримує значення змінної [конфігурації](/docs/{{version}}/configuration). Доступ до значень конфігурації можна отримати за допомогою «точкової нотації», що включає ім'я файлу і параметр, до якого ви хочете отримати доступ. Значення за замовчуванням може бути вказано і повертається, якщо опція конфігурації не існує:

    $value = config('app.timezone');

    $value = config('app.timezone', $default);

Ви можете встановити змінні конфігурації на час виконання скрипта, передавши масив пар ключ / значення. Однак зверніть увагу, що ця функція впливає тільки на значення конфігурації для поточного запиту і не оновлює фактичні значення конфігурації:

    config(['app.debug' => true]);

<a name="method-context"></a>
#### `context()`

Функція `context` отримує значення з [поточного контексту](/docs/{{version}}/context). Може бути вказано значення за замовчуванням, яке повертається, якщо ключ контексту не існує:

    $value = context('trace_id');

    $value = context('trace_id', $default);

Ви можете встановити значення контексту, передавши масив пар ключ/значення:

    use Illuminate\Support\Str;

    context(['trace_id' => Str::uuid()->toString()]);

<a name="method-cookie"></a>
#### `cookie()`

Функція `cookie` створює новий екземпляр [Cookie](/docs/{{version}}/requests#cookies):

    $cookie = cookie('name', 'value', $minutes);

<a name="method-csrf-field"></a>
#### `csrf_field()`

Функція `csrf_field` генерує HTML «прихованого» поля введення, що містить значення токена CSRF. Наприклад, використовуючи [синтаксис Blade](/docs/{{version}}/blade):

    {{ csrf_field() }}

<a name="method-csrf-token"></a>
#### `csrf_token()`

Функція `csrf_token` повертає значення поточного токена CSRF:

    $token = csrf_token();

<a name="method-decrypt"></a>
#### `decrypt()`

Функція `decrypt` [розшифровує](/docs/{{version}}/encryption) надане значення. Ви можете використовувати цю функцію як альтернативу фасаду `Crypt`.

$password = decrypt($value);

<a name="method-dd"></a>
#### `dd()`

Функція `dd` виводить передані змінні та завершує виконання скрипта:

    dd($value);

    dd($value1, $value2, $value3, ...);

Якщо ви не хочете зупиняти виконання вашого скрипта, використовуйте замість цього функцію [`dump`](#method-dump).

<a name="method-dispatch"></a>
#### `dispatch()`

Функція `dispatch` поміщає передане [завдання](/docs/{{version}}/queues#creating-jobs) у [чергу завдань](/docs/{{version}}/queues) Laravel:

    dispatch(new App\Jobs\SendEmails);

<a name="method-dispatch-sync"></a>
#### `dispatch_sync()`

Функція `dispatch_sync` поміщає надане завдання в чергу [синхронно](/docs/{{version}}/queues#synchronous-dispatching) для негайного опрацювання:

    dispatch_sync(new App\Jobs\SendEmails);

<a name="method-dump"></a>
#### `dump()`

Функція `dump` виводить передані змінні:

    dump($value);

    dump($value1, $value2, $value3, ...);

Якщо ви хочете припинити виконання скрипта після виведення змінних, використовуйте замість цього функцію [`dd`](#method-dd).

<a name="method-encrypt"></a>
#### `encrypt()`

Функція `encrypt` [шифрує](/docs/{{version}}/encryption) надане значення. Ви можете використовувати цю функцію як альтернативу фасаду `Crypt`.

    $secret = encrypt('my-secret-value');

<a name="method-env"></a>
#### `env()`

Функція `env` повертає значення [змінної оточення](/docs/{{version}}/configuration#environment-configuration) або значення за замовчуванням:

    $env = env('APP_ENV');

    $env = env('APP_ENV', 'production');


> [!WARNING]  
> Якщо ви виконали команду `config:cache` під час процесу розгортання, ви маєте бути певні, що викликаєте функцію `env` лише з файлів конфігурації. Як тільки конфігурації будуть кешовані, файл `.env` не буде завантажуватися, і всі виклики функції `env` повертатимуть `null`.

<a name="method-event"></a>
#### `event()`

Функція `event` надсилає передану [подію](/docs/{{version}}/events) своїм слухачам:

    event(new UserRegistered($user));

<a name="method-fake"></a>
#### `fake()`

Функція `fake` отримує екземпляр [Faker](https://github.com/FakerPHP/Faker) з контейнера, що може бути корисно при створенні фіктивних даних у фабриках моделей, наповненні бази даних, тестуванні та створенні макетів подань:

```blade
@for($i = 0; $i < 10; $i++)
    <dl>
        <dt>Name</dt>
        <dd>{{ fake()->name() }}</dd>
        <dt>Email</dt>
        <dd>{{ fake()->unique()->safeEmail() }}</dd>
    </dl>
@endfor
```

За замовчуванням функція `fake` буде використовувати опцію `app.faker_locale` з файлу конфігурації `config/app.php`. Зазвичай цей параметр конфігурації задається через змінну середовища `APP_FAKER_LOCALE`. Ви також можете вказати локалізацію, передавши її у функцію `fake`. Для кожної локалізації буде створено свій власний екземпляр:

    fake('nl_NL')->name()

<a name="method-filled"></a>
#### `filled()`

Функція `filled` перевіряє, чи є передане значення не «порожнім»:

    filled(0);
    filled(true);
    filled(false);

    // true

    filled('');
    filled('   ');
    filled(null);
    filled(collect());

    // false

Зворотною функції `filled` є функція [`blank`](#method-blank).

<a name="method-info"></a>
#### `info()`

Функція `info` запише інформацію в [журнал](/docs/{{version}}/logging):

    info('Some helpful information!');

Також функції може бути передано масив контекстних даних:

    info('User login attempt failed.', ['id' => $user->id]);

<a name="method-literal"></a>
#### `literal()`

Функція `literal` створює новий екземпляр [stdClass](https://www.php.net/manual/en/class.stdclass.php) із заданими іменованими аргументами як властивостями:

    $obj = literal(
        name: 'Joe',
        languages: ['PHP', 'Ruby'],
    );

    $obj->name; // 'Joe'
    $obj->languages; // ['PHP', 'Ruby']

<a name="method-logger"></a>
#### `logger()`

Функцію `logger` можна використовувати для запису повідомлення рівня `debug` у [журнал](/docs/{{version}}/logging):

    logger('Debug message');

Також функції може бути передано масив контекстних даних:

    logger('User has logged in.', ['id' => $user->id]);

Якщо функції не передано значення, то буде повернуто екземпляр [реєстратора](/docs/{{version}}/errors#logging):

    logger()->error('You are not allowed here.');

<a name="method-method-field"></a>
#### `method_field()`

Функція `method_field` генерує HTML «прихованого» поля введення, що містить підроблене значення HTTP-методу форми. Наприклад, використовуючи [синтаксис Blade](/docs/{{version}}/blade):

    <form method="POST">
        {{ method_field('DELETE') }}
    </form>

<a name="method-now"></a>
#### `now()`

Функція `now` створює новий екземпляр `Illuminate\Support\Carbon` для поточного часу:

    $now = now();

<a name="method-old"></a>
#### `old()`

Функція `old` [повертає](/docs/{{version}}}/requests#retrieving-input) значення [попереднього введення](/docs/{{{version}}}/requests#old-input), короткостроково збережене в сесії:

    $value = old('value');

    $value = old('value', 'default');

Оскільки значення за замовчуванням, що надається другим аргументом функції `old`, часто є атрибутом моделі Eloquent, Laravel дозволяє вам просто передати всю модель Eloquent як другий аргумент функції `old`. При цьому Laravel припускає, що перший аргумент, наданий функції `old`, - це ім'я атрибута Eloquent, яке слід вважати значенням за замовчуванням:

    {{ old('name', $user->name) }}

    // Is equivalent to...

    {{ old('name', $user) }}

<a name="method-once"></a>
#### `once()`

Функція `once` виконує заданий зворотний виклик і кешує результат у пам'яті на час запиту. Будь-які наступні виклики функції `once` з тим самим зворотним викликом повертатимуть раніше кешований результат:

    function random(): int
    {
        return once(function () {
            return random_int(1, 1000);
        });
    }

    random(); // 123
    random(); // 123 (cached result)
    random(); // 123 (cached result)

Коли функція `once` виконується з екземпляра об'єкта, кешований результат буде унікальним для цього екземпляра об'єкта:

```php
<?php

class NumberService
{
    public function all(): array
    {
        return once(fn () => [1, 2, 3]);
    }
}

$service = new NumberService;

$service->all();
$service->all(); // (cached result)

$secondService = new NumberService;

$secondService->all();
$secondService->all(); // (cached result)
```

<a name="method-optional"></a>
#### `optional()`

Функція `optional` приймає будь-який аргумент і дозволяє вам отримувати доступ до властивостей або викликати методи цього об'єкта. Якщо переданий об'єкт має значення `null`, властивості та методи повертатимуть також `null` замість виклику помилки:

    return optional($user->address)->street;

    {!! old('name', optional($user)->name) !!}

Функція `optional` також приймає замикання як другий аргумент. Замикання буде викликано, якщо значення, вказане як перший аргумент, не дорівнює `null`:

    return optional(User::find($id), function (User $user) {
        return $user->name;
    });

<a name="method-policy"></a>
#### `policy()`

Функція `policy` витягує екземпляр [політики](authorization#creating-policies) для переданого класу:

    $policy = policy(App\Models\User::class);

<a name="method-redirect"></a>
#### `redirect()`

Функція `redirect` повертає [HTTP-відповідь перенаправлення](responses#redirects) або повертає екземпляр перенаправника, якщо викликається без аргументів:

    return redirect($to = null, $status = 302, $headers = [], $https = null);

    return redirect('/home');

    return redirect()->route('route.name');

<a name="method-report"></a>
#### `report()`

Функція `report` повідомить про виняток, використовуючи ваш [обробник винятків](/docs/{{version}}/errors#handling-exceptions):

    report($e);

Функція `report` також приймає рядок як аргумент. Коли у функцію передається рядок, вона створює виняток із переданим рядком як повідомлення:

    report('Something went wrong.');

<a name="method-report-if"></a>
#### `report_if()`

Функція `report_if` повідомлятиме про виняток із використанням вашого [обробника винятків](/docs/{{version}}}/errors#handling-exceptions), якщо задана умова є `true`:

    report_if($shouldReport, $e);

    report_if($shouldReport, 'Something went wrong.');

<a name="method-report-unless"></a>
#### `report_unless()`

Функція `report_unless` повідомлятиме про виняток із використанням вашого [обробника винятків](/docs/{{version}}}/errors#handling-exceptions), якщо задана умова є `false`:

    report_unless($reportingDisabled, $e);

    report_unless($reportingDisabled, 'Something went wrong.');

<a name="method-request"></a>
#### `request()`

Функція `request` повертає екземпляр поточного [запиту](/docs/{{version}}}/requests) або отримує значення поля введення з поточного запиту:

    $request = request();

    $value = request('key', $default);

<a name="method-rescue"></a>
#### `rescue()`

Функція `rescue` виконує передане замикання і перехоплює будь-які винятки, що виникають під час його виконання. Усі перехоплені винятки буде надіслано до вашого [обробника винятків](/docs/{{version}}/errors#handling-exceptions); однак, обробку запиту буде продовжено:

    return rescue(function () {
        return $this->method();
    });

Ви також можете передати другий аргумент функції `rescue`. Цей аргумент буде значенням «за замовчуванням», яке має бути повернуто, якщо під час виконання замикання виникне виняток:

    return rescue(function () {
        return $this->method();
    }, false);

    return rescue(function () {
        return $this->method();
    }, function () {
        return $this->failure();
    });

Функції `rescue` може бути надано аргумент `report`, щоб визначити, чи слід повідомляти про виключення через функцію `report`:


    return rescue(function () {
        return $this->method();
    }, report: function (Throwable $throwable) {
        return $throwable instanceof InvalidArgumentException;
    });

<a name="method-resolve"></a>
#### `resolve()`

Функція `resolve` витягує екземпляр пов'язаного з переданим класом або інтерфейсом, використовуючи [контейнер служб](/docs/{{version}}/container):

    $api = resolve('HelpSpot\API');

<a name="method-response"></a>
#### `response()`

Функція `response` створює екземпляр [відповіді](responses) або отримує екземпляр фабрики відповідей:

    return response('Hello World', 200, $headers);

    return response()->json(['foo' => 'bar'], 200, $headers);

<a name="method-retry"></a>
#### `retry()`

Функція `retry` намагається виконати передану функцію, доки не буде досягнуто зазначеного ліміту спроб. Якщо функція не викине виняток, то буде повернуто її значення. Якщо функція викине виняток, то буде автоматично повторена. Якщо максимальну кількість спроб перевищено, буде викинуто виняток

    return retry(5, function () {
        // Attempt 5 times while resting 100ms between attempts...
    }, 100);

Якщо ви хочете вручну обчислити кількість мілісекунд, яка має пройти між спробами, ви можете передати функцію як третій аргумент функції `retry`:

    use Exception;

    return retry(5, function () {
        // ...
    }, function (int $attempt, Exception $exception) {
        return $attempt * 100;
    });

Для зручності ви можете передати функції `retry` як перший аргумент масив. Цей масив буде використовуватися для визначення інтервалу в мілісекундах між наступними спробами:

    return retry([100, 200], function () {
        // Sleep for 100ms on first retry, 200ms on second retry...
    });

Щоб повторити спробу тільки за певних умов, ви можете передати функцію, що визначає цю умову, як четвертий аргумент функції `retry`:

    use Exception;

    return retry(5, function () {
        // ...
    }, 100, function ($exception) {
        return $exception instanceof RetryException;
    });

<a name="method-session"></a>
#### `session()`

Функція`session` використовується для отримання або завдання значень [сесії](/docs/{{version}}/session):

    $value = session('key');

Ви можете встановити значення, передавши масив пар ключ / значення у функцію:

    session(['chairs' => 7, 'instruments' => 3]);

Якщо у функцію не передано значення, то буде повернуто екземпляр сховища сесій:

    $value = session()->get('key');

    session()->put('key', $value);

<a name="method-tap"></a>
#### `tap()`

Функція `tap` приймає два аргументи: довільне значення і замикання. Значення буде передано в замикання, а потім повернуто функцією `tap`. Повернуте значення замикання не має значення:

    $user = tap(User::first(), function (User $user) {
        $user->name = 'taylor';

        $user->save();
    });

Якщо замикання не передано функції `tap`, то ви можете викликати будь-який метод із зазначеним значенням. Значення, що повертається, методу, що викликається, завжди буде спочатку вказане, незалежно від того, що метод фактично повертає у своєму визначенні. Наприклад, метод Eloquent `update` зазвичай повертає цілочисельне значення. Однак, ми можемо змусити метод повертати саму модель, пов'язавши виклик методу `update` за допомогою функції `tap`:

    $user = tap($user)->update([
        'name' => $name,
        'email' => $email,
    ]);

Щоб додати до свого класу метод `tap`, використовуйте трейт `Illuminate\Support\Traits\Tappable` у вашому класі. Метод `tap` цього трейта приймає замикання як єдиний аргумент. Сам екземпляр об'єкта буде передано замиканню, а потім буде повернуто методом `tap`:

    return $user->tap(function (User $user) {
        //
    });

<a name="method-throw-if"></a>
#### `throw_if()`

Функція `throw_if` викидає переданий виняток, якщо вказаний логічний вираз оцінюється як `true`:

    throw_if(! Auth::user()->isAdmin(), AuthorizationException::class);

    throw_if(
        ! Auth::user()->isAdmin(),
        AuthorizationException::class,
        'You are not allowed to access this page.'
    );

<a name="method-throw-unless"></a>
#### `throw_unless()`

Функція `throw_unless` викидає переданий виняток, якщо вказаний логічний вираз оцінюється як `false`:

    throw_unless(Auth::user()->isAdmin(), AuthorizationException::class);

    throw_unless(
        Auth::user()->isAdmin(),
        AuthorizationException::class,
        'You are not allowed to access this page.'
    );

<a name="method-today"></a>
#### `today()`

Функція `today` створює новий екземпляр `Illuminate\Support\Carbon` для поточної дати:

    $today = today();

<a name="method-trait-uses-recursive"></a>
#### `trait_uses_recursive()`

Функція `trait_uses_recursive` повертає всі трейти, що використовуються трейтом:

    $traits = trait_uses_recursive(\Illuminate\Notifications\Notifiable::class);

<a name="method-transform"></a>
#### `transform()`

Функція `transform` виконує замикання для переданого значення, якщо значення не [порожнє](#method-blank), і повертає результат замикання:

    $callback = function (int $value) {
        return $value * 2;
    };

    $result = transform(5, $callback);

    // 10

Як третій параметр можуть бути вказані значення за замовчуванням або замикання. Це значення буде повернуто, якщо передане значення порожнє:

    $result = transform(null, $callback, 'The value is blank');

    // The value is blank

<a name="method-validator"></a>
#### `validator()`

Функція `validator` створює новий екземпляр [валідатора](/docs/{{version}}{{version}}/validation) із зазначеними аргументами. Ви можете використовувати його для зручності замість фасаду `Validator`:

    $validator = validator($data, $rules, $messages);

<a name="method-value"></a>
#### `value()`

Функція `value` повертає передане значення. Однак, якщо ви передасте замикання у функцію, то замикання буде виконано, і буде повернуто його результат:

    $result = value(true);

    // true

    $result = value(function () {
        return false;
    });

    // false

Функції `value` можуть бути передані додаткові аргументи. Якщо перший аргумент є замиканням, то додаткові параметри будуть передані в замикання як аргументи, в іншому випадку вони будуть проігноровані:

    $result = value(function (string $name) {
        return $name;
    }, 'Taylor');

    // 'Taylor'

<a name="method-view"></a>
#### `view()`

Функція `view` повертає екземпляр [подання](/docs/{{version}}/views):

    return view('auth.login');

<a name="method-with"></a>
#### `with()`

Функція `with` повертає передане значення. Якщо ви передасте замикання у функцію як другий аргумент, то замикання буде виконано і буде повернуто результат його виконання:

    $callback = function (mixed $value) {
        return is_numeric($value) ? $value * 2 : 0;
    };

    $result = with(5, $callback);

    // 10

    $result = with(null, $callback);

    // 0

    $result = with(5, null);

    // 5

<a name="method-when"></a>
#### `when()`

Функція `when` повертає задане їй значення, якщо задана умова має значення `true`. В іншому випадку повертається `null`. Якщо замикання передається як другий аргумент функції, замикання буде виконано і буде повернуто його значення, що повертається:

    $value = when(true, 'Hello World');

    $value = when(true, fn () => 'Hello World');

Функція `when` насамперед корисна для умовного рендерингу атрибутів HTML:

```blade
<div {!! when($condition, 'wire:poll="calculate"') !!}>
    ...
</div>
```

<a name="other-utilities"></a>
## Інші утиліти

<a name="benchmarking"></a>
### Benchmark

Іноді вам може знадобитися швидко оцінити продуктивність певних частин вашого додатка. У таких випадках ви можете скористатися класом `Benchmark` для вимірювання часу виконання переданих зворотних викликів у мілісекундах:


    <?php

    use App\Models\User;
    use Illuminate\Support\Benchmark;

    Benchmark::dd(fn () => User::find(1)); // 0.1 ms

    Benchmark::dd([
        'Scenario 1' => fn () => User::count(), // 0.5 ms
        'Scenario 2' => fn () => User::all()->count(), // 20.0 ms
    ]);


За замовчуванням передані зворотні виклики будуть виконані один раз (одна ітерація), і їхня тривалість буде відображена в браузері / консолі.

Щоб виконати зворотний виклик більше одного разу, ви можете вказати кількість ітерацій другим аргументом методу. При виконанні зворотного виклику більше одного разу клас `Benchmark` поверне середню кількість мілісекунд, витрачених на виконання зворотного виклику за всі ітерації:

    Benchmark::dd(fn () => User::count(), iterations: 10); // 0.5 ms

Іноді вам може знадобитися виміряти час виконання зворотного виклику, зберігаючи при цьому значення, що повертається зворотним викликом. Метод `value` поверне кортеж, що містить значення, що повертається зворотним викликом, і кількість мілісекунд, витрачених на виконання зворотного виклику:

    [$count, $duration] = Benchmark::value(fn () => User::count());

<a name="dates"></a>
### Дати

Laravel включає в себе [Carbon](https://carbon.nesbot.com/docs/), потужну бібліотеку для маніпулювання датою і часом. Щоб створити новий екземпляр `Carbon`, ви можете викликати функцію `now`. Ця функція доступна глобально у вашому додатку Laravel:

```php
$now = now();
```

Або ж ви можете створити новий екземпляр `Carbon`, використовуючи клас `Illuminate\Support\Carbon`:

```php
use Illuminate\Support\Carbon;

$now = Carbon::now();
```

Детальний опис `Carbon` і його функцій можна знайти в [офіційній документації Carbon](https://carbon.nesbot.com/docs/).

<a name="deferred-functions"></a>
### Відкладені функції

> [!WARNING]
> Відкладені функції наразі перебувають на стадії бета-тестування, поки ми збираємо відгуки спільноти.

Хоча [завдання в черзі](/docs/{{version}}}/queues) Laravel дають змогу ставити завдання в чергу для фонового опрацювання, іноді у вас можуть виникнути прості завдання, які ви хотіли б відкласти без настроювання або обслуговування обробника черги, що довго працює.

Відкладені функції дають змогу відкласти виконання закриття доти, доки HTTP-відповідь не буде надіслано користувачеві, що дає змогу вашій програмі відчувати себе швидкою та чуйною. Щоб відкласти виконання замикання, просто передайте його функції `Illuminate\Support\defer`:

```php
use App\Services\Metrics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use function Illuminate\Support\defer;

Route::post('/orders', function (Request $request) {
    // Create order...

    defer(fn () => Metrics::reportOrder($order));

    return $order;
});
```

За замовчуванням відкладені функції будуть виконуватися тільки в тому випадку, якщо HTTP-відповідь, команда Artisan або завдання в черзі, з якого викликається `Illuminate\Support\defer`, завершуються успішно. Це означає, що відкладені функції не будуть виконуватися, якщо запит призведе до HTTP-відповіді `4xx` або `5xx`. Якщо ви хочете, щоб відкладена функція виконувалася завжди, ви можете пов'язати метод `always` з вашою відкладеною функцією:

```php
defer(fn () => Metrics::reportOrder($order))->always();
```

<a name="cancelling-deferred-functions"></a>
#### Скасування відкладених функцій

Якщо вам потрібно скасувати відкладену функцію до її виконання, ви можете використовувати метод `forget`, щоб скасувати функцію за її іменем. Щоб назвати відкладену функцію, вкажіть другий аргумент функції `Illuminate\Support\defer`:

```php
defer(fn () => Metrics::report(), 'reportMetrics');

defer()->forget('reportMetrics');
```

<a name="deferred-function-compatibility"></a>
#### Сумісність відкладених функцій

Якщо ви оновилися до Laravel 11.x із додатка Laravel 10.x і скелет вашого додатка все ще містить файл `app/Http/Kernel.php`, вам слід додати проміжне програмне забезпечення `InvokeDeferredCallbacks` у початок властивості `$middleware` ядра:

```php
protected $middleware = [
    \Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class, // [tl! add]
    \App\Http\Middleware\TrustProxies::class,
    // ...
];
```

<a name="disabling-deferred-functions-in-tests"></a>
#### Вимкнення відкладених функцій у тестах

Під час написання тестів може бути корисно вимкнути відкладені функції. Ви можете викликати `withoutDefer` у своєму тесті, щоб вказати Laravel негайно викликати всі відкладені функції:

```php tab=Pest
test('without defer', function () {
    $this->withoutDefer();

    // ...
});
```

```php tab=PHPUnit
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_without_defer(): void
    {
        $this->withoutDefer();

        // ...
    }
}
```

Якщо ви хочете відключити відкладені функції для всіх тестів у тестовому прикладі, ви можете викликати метод `withoutDefer` з методу `setUp` вашого базового класу `TestCase`:

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void// [tl! add:start]
    {
        parent::setUp();

        $this->withoutDefer();
    }// [tl! add:end]
}
```

<a name="lottery"></a>
### Лотерея

Клас лотереї Laravel може використовуватися для виконання зворотних викликів на основі заданих шансів. Це може бути особливо корисно, коли ви хочете виконати код тільки для певного відсотка ваших вхідних запитів:

    use Illuminate\Support\Lottery;

    Lottery::odds(1, 20)
        ->winner(fn () => $user->won())
        ->loser(fn () => $user->lost())
        ->choose();

Ви можете комбінувати клас лотереї Laravel з іншими функціями Laravel. Наприклад, ви можете захотіти повідомляти обробнику винятків тільки про невеликий відсоток повільних запитів. А оскільки клас лотереї є викликаним, ми можемо передати екземпляр класу в будь-який метод, який приймає викликані об'єкти:

    use Carbon\CarbonInterval;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Lottery;

    DB::whenQueryingForLongerThan(
        CarbonInterval::seconds(2),
        Lottery::odds(1, 100)->winner(fn () => report('Querying > 2 seconds.')),
    );

<a name="testing-lotteries"></a>
#### Тестування лотерей

Laravel надає кілька простих методів, які дають змогу легко тестувати виклики лотереї у вашому додатку:

    // Лотерея завжди виграшна...
    Lottery::alwaysWin();

    // Лотерея завжди програшна...
    Lottery::alwaysLose();

    // Виграш, програш, потім повернутися до нормальної поведінки...
    Lottery::fix([true, false]);

    // Повернутися до нормальної поведінки...
    Lottery::determineResultsNormally();

<a name="pipeline"></a>
### Pipeline

Фасад `Pipeline` у Laravel надає зручний спосіб «прокидання» введення через серію викликів класів, замикань або об'єктів, що викликаються, надаючи кожному класу можливість перевірити або змінити вхідні дані та викликати наступний елемент у ланцюжку викликів пайплайна:

```php
use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Pipeline;

$user = Pipeline::send($user)
            ->through([
                function (User $user, Closure $next) {
                    // ...

                    return $next($user);
                },
                function (User $user, Closure $next) {
                    // ...

                    return $next($user);
                },
            ])
            ->then(fn (User $user) => $user);
```


Як бачите, кожен клас, що викликається, або замикання, вказане в pipeline, отримує вхідні дані та замикання `$next`. Виклик замикання `$next` призведе до виклику наступного об'єкта, що викликається, у пайплайні. Як ви могли помітити, це дуже схоже на [middleware](/docs/{{version}}/middleware).

Коли останній викликаний об'єкт у пайплайні викликає `$next`, буде виконано об'єкт, наданий методу `then`. Зазвичай цей об'єкт, що викликається, просто повертає надані вхідні дані.

Як було описано раніше, ви не обмежені наданням тільки замикань у свій пайплайн. Ви також можете використовувати класи, що викликаються. Якщо надано ім'я класу, екземпляр класу буде створено з використанням [контейнера служб Laravel](/docs/{{version}}}/container), що дає змогу впроваджувати залежності в клас, що викликається:

```php
$user = Pipeline::send($user)
            ->through([
                GenerateProfilePhoto::class,
                ActivateSubscription::class,
                SendWelcomeEmail::class,
            ])
            ->then(fn (User $user) => $user);
```

<a name="sleep"></a>
### Sleep

Клас `Sleep` у Laravel являє собою легковажну обгортку навколо нативних функцій PHP `sleep` і `usleep`, надаючи більшу тестованість і зручний API для роботи з часом:


    use Illuminate\Support\Sleep;

    $waiting = true;

    while ($waiting) {
        Sleep::for(1)->second();

        $waiting = /* ... */;
    }

Клас `Sleep` надає різноманітні методи, що дають змогу вам працювати з різними одиницями часу:

    // Повернути значення після сну...
    $result = Sleep::for(1)->second()->then(fn () => 1 + 1);

    // Спати, поки задане значення істинне...
    Sleep::for(1)->second()->while(fn () => shouldKeepSleeping());

    //Призупиніть виконання на 90 секунд...
    Sleep::for(1.5)->minutes();

    // Призупиніть виконання на 2 секунди...
    Sleep::for(2)->seconds();

    // Призупиніть виконання на 500 мілісекунд...
    Sleep::for(500)->milliseconds();

    // Призупиніть виконання на 500 мілісекунд...
    Sleep::for(5000)->microseconds();

    // Призупинити виконання до заданого часу...
    Sleep::until(now()->addMinute());

    // Псевдонім функції PHP «sleep»...
    Sleep::sleep(2);

    // Псевдонім функції PHP «usleep»
    Sleep::usleep(5000);

Щоб легко об'єднувати одиниці часу, ви можете використовувати метод `and`:

    Sleep::for(1)->second()->and(10)->milliseconds();

<a name="testing-sleep"></a>
#### Тестування Sleep

При тестуванні коду, що використовує клас `Sleep` або функції PHP `sleep` , виконання вашого тесту буде призупинено. Як можна очікувати, це робить ваш пакет тестів значно повільнішим. Наприклад, уявіть, що ви тестуєте наступний код:

    $waiting = /* ... */;

    $seconds = 1;

    while ($waiting) {
        Sleep::for($seconds++)->seconds();

        $waiting = /* ... */;
    }

Зазвичай тестування цього коду займе щонайменше одну секунду. На щастя, клас `Sleep` дозволяє нам «підробляти» затримку, щоб наш тестовий набір залишався швидким:

```php tab=Pest
it('waits until ready', function () {
    Sleep::fake();

    // ...
});
```

```php tab=PHPUnit
public function test_it_waits_until_ready()
{
    Sleep::fake();

    // ...
}
```

При підробці класу `Sleep` реальна затримка виконання обходиться, що призводить до швидшого тестування.

Як тільки клас `Sleep` було підроблено, можна робити твердження щодо очікуваних «пауз». Для ілюстрації давайте уявимо, що ми тестуємо код, який призупиняє виконання три рази, при цьому кожна затримка збільшується на одну секунду. Використовуючи метод `assertSequence`, ми можемо перевірити, що наш код «спав» потрібну кількість часу, зберігаючи при цьому швидкість виконання тесту:

```php tab=Pest
it('checks if ready three times', function () {
    Sleep::fake();

    // ...

    Sleep::assertSequence([
        Sleep::for(1)->second(),
        Sleep::for(2)->seconds(),
        Sleep::for(3)->seconds(),
    ]);
}
```

```php tab=PHPUnit
public function test_it_checks_if_ready_three_times()
{
    Sleep::fake();

    // ...

    Sleep::assertSequence([
        Sleep::for(1)->second(),
        Sleep::for(2)->seconds(),
        Sleep::for(3)->seconds(),
    ]);
}
```

Звичайно ж, клас Sleep надає й інші твердження, які ви можете використовувати під час тестування:


    use Carbon\CarbonInterval as Duration;
    use Illuminate\Support\Sleep;

    // Твердження, що sliip викликали 3 рази...
    Sleep::assertSleptTimes(3);

    // Твердження, що тривалість сну... 
    Sleep::assertSlept(function (Duration $duration): bool {
        return /* ... */;
    }, times: 1);

    // Твердження, що клас Sleep ніколи не викликався...
    Sleep::assertNeverSlept();

    // Твердження, що, навіть якщо було викликано Sleep, пауза у виконанні не настала...
    Sleep::assertInsomniac();

Іноді буває корисно виконувати дію при кожному імітованому очікуванні в коді вашої програми. Для цього ви можете надати зворотний виклик методу `whenFakingSleep`. У наступному прикладі ми використовуємо помічники Laravel з [маніпулювання часом](/docs/{{version}}/mocking#interacting-with-time), щоб миттєво просунути час на тривалість кожного очікування:

```php
use Carbon\CarbonInterval as Duration;

$this->freezeTime();

Sleep::fake();

Sleep::whenFakingSleep(function (Duration $duration) {
    // Progress time when faking sleep...
    $this->travel($duration->totalMilliseconds)->milliseconds();
});
```

Оскільки прогресування часу є загальною вимогою, метод `fake` приймає аргумент `syncWithCarbon`, щоб синхронізувати Carbon під час сну в тесті:

```php
Sleep::fake(syncWithCarbon: true);

$start = now();

Sleep::for(1)->second();

$start->diffForHumans(); // 1 second ago
```

Клас `Sleep` використовується всередині Laravel під час призупинення виконання. Наприклад, помічник [retry](#method-retry) використовує клас `Sleep` під час затримки, що забезпечує кращу тестуваність під час використання цього помічника.
