# Колекції

- [Вступ](#introduction)
    - [Створення колекцій](#creating-collections)
    - [Розширення колекцій](#extending-collections)
- [Доступні методи](#available-methods)
- [Повідомлення вищого порядку](#higher-order-messages)
- [Відкладених колекцій](#lazy-collections)
    - [Вступ до відкладених колекцій](#lazy-collection-introduction)
    - [Створення відкладених колекцій](#creating-lazy-collections)
    - [Контракт `Enumerable`](#the-enumerable-contract)
    - [Методи відкладених колекцій](#lazy-collection-methods)

<a name="introduction"></a>
## Вступ

Клас `Illuminate\Support\Collection` забезпечує гнучку і зручну обгортку для роботи з масивами даних. Наприклад, подивіться на наступний код. Тут ми будемо використовувати хелпер `collect`, щоб створити новий екземпляр колекції з масиву, запустимо функцію `strtoupper` для кожного елемента, а потім видалимо всі порожні елементи:

    $collection = collect(['taylor', 'abigail', null])->map(function (?string $name) {
        return strtoupper($name);
    })->reject(function (string $name) {
        return empty($name);
    });

Як бачите, клас `Collection` дозволяє об'єднувати необхідні вам методи в ланцюжок для виконання послідовного перебору і скорочення базового масиву. В основному колекції незмінні, тобто кожен метод колекції повертає абсолютно новий екземпляр `Collection`.

<a name="creating-collections"></a>
### Створення колекцій

Як згадувалося вище, помічник `collect` повертає новий екземпляр `Illuminate\Support\Collection` для переданого масиву. Отже, створити колекцію дуже просто:

    $collection = collect([1, 2, 3]);

> [!NOTE]
> Результати запитів [Eloquent](/docs/{{version}}}/eloquent) завжди повертаються як екземпляри `Collection`.

<a name="extending-collections"></a>
### Розширення колекцій

Клас `Collection` є «макропрограмованим», що дозволяє вам додавати додаткові методи до класу під час виконання. Метод `macro` класу `Illuminate\Support\Collection` приймає функцію, яка буде виконана під час виклику вашого макросу. Ця функція може звертатися до інших методів колекції через `$this`, так, наче це був би реальний метод класу колекції. Наприклад, наступний код додає метод `toUpper` класу `Collection`

    use Illuminate\Support\Collection;
    use Illuminate\Support\Str;

    Collection::macro('toUpper', function () {
        return $this->map(function (string $value) {
            return Str::upper($value);
        });
    });

    $collection = collect(['first', 'second']);

    $upper = $collection->toUpper();

    // ['FIRST', 'SECOND']

Зазвичай макроси колекцій оголошуються в методі `boot` [сервіс-провайдера](/docs/{{version}}/providers).

<a name="macro-arguments"></a>
#### Макроси з аргументами

За необхідності ви можете визначити макроси, які приймають додаткові аргументи:

    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\Lang;

    Collection::macro('toLocale', function (string $locale) {
        return $this->map(function (string $value) use ($locale) {
            return Lang::get($value, [], $locale);
        });
    });

    $collection = collect(['first', 'second']);

    $translated = $collection->toLocale('es');

<a name="available-methods"></a>
## Доступні методи

У більшій частині решти документації по колекціях ми обговоримо кожен метод, доступний у класі `Collection`. Пам'ятайте, що всі ці методи можна об'єднати в ланцюжок для послідовного управління базовим масивом. Ба більше, майже кожен метод повертає новий екземпляр `Collection`, даючи змогу вам за потреби зберегти вихідну копію колекції:

 <div class="docs-column-list" markdown="1">

- [`after`](#method-after)
- [`all()`](#method-all)
- [`average()`](#method-average)
- [`avg()`](#method-avg)
- [`before`](#method-before)
- [`chunk()`](#method-chunk)
- [`chunkWhile()`](#method-chunkwhile)
- [`collapse()`](#method-collapse)
- [`combine()`](#method-combine)
- [`collect()`](#method-collect)
- [`concat()`](#method-concat)
- [`contains()`](#method-contains)
- [`containsOneItem()`](#method-containsoneitem)
- [`containsStrict()`](#method-containsstrict)
- [`count()`](#method-count)
- [`countBy()`](#method-countby)
- [`crossJoin()`](#method-crossjoin)
- [`dd()`](#method-dd)
- [`diff()`](#method-diff)
- [`diffAssoc()`](#method-diffassoc)
- [`diffAssocUsing()`](#method-diffassocusing)
- [`diffKeys()`](#method-diffkeys)
- [`doesntContain`](#method-doesntcontain)
- [`dot()`](#method-dot)
- [`dump()`](#method-dump)
- [`duplicates()`](#method-duplicates)
- [`duplicatesStrict()`](#method-duplicatesstrict)
- [`each()`](#method-each)
- [`eachSpread()`](#method-eachspread)
- [`ensure()`](#method-ensure)
- [`every()`](#method-every)
- [`except()`](#method-except)
- [`filter()`](#method-filter)
- [`first()`](#method-first)
- [`firstOrFail()`](#method-first-or-fail)
- [`firstWhere()`](#method-firstwhere)
- [`flatMap()`](#method-flatmap)
- [`flatten()`](#method-flatten)
- [`flip()`](#method-flip)
- [`forget()`](#method-forget)
- [`forPage()`](#method-forpage)
- [`get()`](#method-get)
- [`groupBy()`](#method-groupby)
- [`has()`](#method-has)
- [`hasAny()`](#method-hasany)
- [`implode()`](#method-implode)
- [`intersect()`](#method-intersect)
- [`intersectAssoc()`](#method-intersectAssoc)
- [`intersectByKeys()`](#method-intersectbykeys)
- [`isEmpty()`](#method-isempty)
- [`isNotEmpty()`](#method-isnotempty)
- [`join()`](#method-join)
- [`keyBy()`](#method-keyby)
- [`keys()`](#method-keys)
- [`last()`](#method-last)
- [`lazy()`](#method-lazy)
- [`macro()`](#method-macro)
- [`make()`](#method-make)
- [`map()`](#method-map)
- [`mapInto()`](#method-mapinto)
- [`mapSpread()`](#method-mapspread)
- [`mapToGroups()`](#method-maptogroups)
- [`mapWithKeys()`](#method-mapwithkeys)
- [`max()`](#method-max)
- [`median()`](#method-median)
- [`merge()`](#method-merge)
- [`mergeRecursive()`](#method-mergerecursive)
- [`min()`](#method-min)
- [`mode()`](#method-mode)
- [`multiply`](#method-multiply)
- [`nth()`](#method-nth)
- [`only()`](#method-only)
- [`pad()`](#method-pad)
- [`partition()`](#method-partition)
- [`percentage()`](#method-percentage)
- [`pipe()`](#method-pipe)
- [`pipeInto()`](#method-pipeinto)
- [`pipeThrough()`](#method-pipethrough)
- [`pluck()`](#method-pluck)
- [`pop()`](#method-pop)
- [`prepend()`](#method-prepend)
- [`pull()`](#method-pull)
- [`push()`](#method-push)
- [`put()`](#method-put)
- [`random()`](#method-random)
- [`range`](#method-range)
- [`reduce()`](#method-reduce)
- [`reduceSpread`](#method-reduce-spread)
- [`reject()`](#method-reject)
- [`replace()`](#method-replace)
- [`replaceRecursive()`](#method-replacerecursive)
- [`reverse()`](#method-reverse)
- [`search()`](#method-search)
- [select](#method-select)
- [`shift()`](#method-shift)
- [`shuffle()`](#method-shuffle)
- [`skip()`](#method-skip)
- [`skipUntil()`](#method-skipuntil)
- [`skipWhile()`](#method-skipwhile)
- [`slice()`](#method-slice)
- [`sliding`](#method-sliding)
- [`sole`](#method-sole)
- [`some()`](#method-some)
- [`sort()`](#method-sort)
- [`sortBy()`](#method-sortby)
- [`sortByDesc()`](#method-sortbydesc)
- [`sortDesc()`](#method-sortdesc)
- [`sortKeys()`](#method-sortkeys)
- [`sortKeysDesc()`](#method-sortkeysdesc)
- [`sortKeysUsing()`](#method-sortkeysusing)
- [`splice()`](#method-splice)
- [`split()`](#method-split)
- [`splitIn()`](#method-splitin)
- [`sum()`](#method-sum)
- [`take()`](#method-take)
- [`takeUntil()`](#method-takeuntil)
- [`takeWhile()`](#method-takewhile)
- [`tap()`](#method-tap)
- [`times()`](#method-times)
- [`toArray()`](#method-toarray)
- [`toJson()`](#method-tojson)
- [`transform()`](#method-transform)
- [`undot()`](#method-undot)
- [`union()`](#method-union)
- [`unique()`](#method-unique)
- [`uniqueStrict()`](#method-uniquestrict)
- [`unless()`](#method-unless)
- [`unlessEmpty()`](#method-unlessempty)
- [`unlessNotEmpty()`](#method-unlessnotempty)
- [`unwrap()`](#method-unwrap)
- [`value()`](#method-value)
- [`values()`](#method-values)
- [`when()`](#method-when)
- [`whenEmpty()`](#method-whenempty)
- [`whenNotEmpty()`](#method-whennotempty)
- [`where()`](#method-where)
- [`whereStrict()`](#method-wherestrict)
- [`whereBetween()`](#method-wherebetween)
- [`whereIn()`](#method-wherein)
- [`whereInStrict()`](#method-whereinstrict)
- [`whereInstanceOf()`](#method-whereinstanceof)
- [`whereNotBetween()`](#method-wherenotbetween)
- [`whereNotIn()`](#method-wherenotin)
- [`whereNotInStrict()`](#method-wherenotinstrict)
- [`whereNotNull()`](#method-wherenotnull)
- [`whereNull()`](#method-wherenull)
- [`wrap()`](#method-wrap)
- [`zip()`](#method-zip)

 </div>

<a name="method-listing"></a>
## Список методів

<a name="method-after"></a>
#### `after()`

Метод `after` повертає елемент після даного елемента. `null` повертається, якщо цей елемент не знайдено або він є останнім елементом:

    $collection = collect([1, 2, 3, 4, 5]);

    $collection->after(3);

    // 4

    $collection->after(5);

    // null

Цей метод шукає цей елемент, використовуючи «вільне» порівняння, тобто рядок, що містить цілочисельне значення, вважатимуть таким, що дорівнює цілому числу того самого значення. Щоб використовувати «суворе» порівняння, ви можете надати методу аргумент `strict`:

    collect([2, 4, 6, 8])->after('4', strict: true);

    // null

Як альтернативу ви можете надати власне замикання для пошуку першого елемента, який проходить заданий тест на істинність:

    collect([2, 4, 6, 8])->after(function (int $item, int $key) {
        return $item > 5;
    });

    // 8

<a name="method-all"></a>
#### `all()`

Метод `all` повертає базовий масив, представлений колекцією:

    collect([1, 2, 3])->all();

    // [1, 2, 3]

<a name="method-average"></a>
#### `average()`

Псевдонім для методу [`avg`](#method-avg).

<a name="method-avg"></a>
#### `avg()`

Метод `avg` повертає [середнє значення](https://en.wikipedia.org/wiki/Average) переданого ключа:

    $average = collect([
        ['foo' => 10],
        ['foo' => 10],
        ['foo' => 20],
        ['foo' => 40]
    ])->avg('foo');

    // 20

    $average = collect([1, 1, 2, 4])->avg();

    // 2

<a name="method-before"></a>
#### `before()`

Метод `before` є протилежністю методу [`after`](#method-after). Він повертає елемент перед даним елементом. `null` повертається, якщо цей елемент не знайдено або він є першим елементом:

    $collection = collect([1, 2, 3, 4, 5]);

    $collection->before(3);

    // 2

    $collection->before(1);

    // null

    collect([2, 4, 6, 8])->before('4', strict: true);

    // null

    collect([2, 4, 6, 8])->before(function (int $item, int $key) {
        return $item > 5;
    });

    // 4

<a name="method-chunk"></a>
#### `chunk()`

Метод `chunk` розбиває колекцію на кілька менших колекцій зазначеного розміру:

    $collection = collect([1, 2, 3, 4, 5, 6, 7]);

    $chunks = $collection->chunk(4);

    $chunks->all();

    // [[1, 2, 3, 4], [5, 6, 7]]

Цей метод особливо корисний у [шаблонах](/docs/{{version}}/views) під час роботи з сіткою, такою як [Bootstrap](https://getbootstrap.com/docs/5.3/layout/grid/). Наприклад, уявіть, що у вас є колекція моделей [Eloquent](/docs/{{version}}/eloquent), які ви хочете відобразити в сітці:

```blade
@foreach ($products->chunk(3) as $chunk)
    <div class="row">
        @foreach ($chunk as $product)
            <div class="col-xs-4">{{ $product->name }}</div>
        @endforeach
    </div>
@endforeach
```

<a name="method-chunkwhile"></a>
#### `chunkWhile()`

Метод `chunkWhile` розбиває колекцію на кілька менших за розміром колекцій на основі результату переданого замикання. Змінна `$chunk`, передана в замикання, може використовуватися для перевірки попереднього елемента:

    $collection = collect(str_split('AABBCCCD'));

    $chunks = $collection->chunkWhile(function (string $value, int $key, Collection $chunk) {
        return $value === $chunk->last();
    });

    $chunks->all();

    // [['A', 'A'], ['B', 'B'], ['C', 'C', 'C'], ['D']]

<a name="method-collapse"></a>
#### `collapse()`

Метод `collapse` згортає колекцію масивів у єдину плоску колекцію:

    $collection = collect([
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9],
    ]);

    $collapsed = $collection->collapse();

    $collapsed->all();

    // [1, 2, 3, 4, 5, 6, 7, 8, 9]

<a name="method-collect"></a>
#### `collect()`

Метод `collect` повертає новий екземпляр `Collection` з елементами, що знаходяться в поточній колекції:

    $collectionA = collect([1, 2, 3]);

    $collectionB = $collectionA->collect();

    $collectionB->all();

    // [1, 2, 3]

Метод `collect` насамперед корисний для перетворення [відкладених колекцій](#lazy-collections) у стандартні екземпляри `Collection`:

    $lazyCollection = LazyCollection::make(function () {
        yield 1;
        yield 2;
        yield 3;
    });

    $collection = $lazyCollection->collect();

    $collection::class;

    // 'Illuminate\Support\Collection'

    $collection->all();

    // [1, 2, 3]

> [!NOTE]
> Метод `collect` особливо корисний, коли у вас є екземпляр `Enumerable` і вам потрібен «не-відкладений» екземпляр колекції. Оскільки `collect()` є частиною контракту `Enumerable`, ви можете безпечно використовувати його для отримання екземпляра `Collection`.

<a name="method-combine"></a>
#### `combine()`

Метод `combine` об'єднує значення колекції як ключі зі значеннями іншого масиву або колекції:

    $collection = collect(['name', 'age']);

    $combined = $collection->combine(['George', 29]);

    $combined->all();

    // ['name' => 'George', 'age' => 29]

<a name="method-concat"></a>
#### `concat()`

Метод `concat` додає значення переданого масиву або колекції в кінець іншої колекції:

    $collection = collect(['John Doe']);

    $concatenated = $collection->concat(['Jane Doe'])->concat(['name' => 'Johnny Doe']);

    $concatenated->all();

    // ['John Doe', 'Jane Doe', 'Johnny Doe']

Метод `concat` чисельно переіндексує ключі для елементів, доданих до вихідної колекції. Щоб зберегти ключі в асоціативних колекціях, див. метод [merge](#method-merge).

<a name="method-contains"></a>
#### `contains()`

Метод `contains` визначає, чи містить колекція даний елемент. Ви можете передати в `contains` функцію, щоб визначити, чи існує в колекції елемент, який відповідає зазначеному критерію істинності:

    $collection = collect([1, 2, 3, 4, 5]);

    $collection->contains(function (int $value, int $key) {
        return $value > 5;
    });

    // false

Ви також можете передати рядок методу `contains`, щоб визначити, чи містить колекція вказане значення елемента:

    $collection = collect(['name' => 'Desk', 'price' => 100]);

    $collection->contains('Desk');

    // true

    $collection->contains('New York');

    // false

Ви також можете передати пару ключ / значення методу `contains`, який визначить, чи існує ця пара в колекції:

    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Chair', 'price' => 100],
    ]);

    $collection->contains('product', 'Bookcase');

    // false

Метод `contains` використовує «гнучке» порівняння під час перевірки значень елементів, тобто рядок із цілочисельним значенням вважатиметься таким, що дорівнює цілому числу того самого значення. Використовуйте метод [`containsStrict`](#method-containsstrict) для фільтрації з використанням «жорсткого» порівняння.

Протилежним для методу `contains`, є метод [`doesntContain`](#method-doesntcontain).

<a name="method-containsoneitem"></a>
#### `containsOneItem()`

Метод `containsOneItem` визначає, чи містить колекція тільки один елемент:

    collect([])->containsOneItem();

    // false

    collect(['1'])->containsOneItem();

    // true

    collect(['1', '2'])->containsOneItem();

    // false

<a name="method-containsstrict"></a>
#### `containsStrict()`

Цей метод має ту саму сигнатуру, що й метод [`contains`](#method-contains); однак, усі значення порівнюються з використанням «жорсткого» порівняння.

> [!NOTE]
> Поведінка цього методу змінюється при використанні [колекцій Eloquent](/docs/{{version}}/eloquent-collections#method-contains).

<a name="method-count"></a>
#### `count()`

Метод `count` повертає загальну кількість елементів у колекції:

    $collection = collect([1, 2, 3, 4]);

    $collection->count();

    // 4

<a name="method-countBy"></a>
#### `countBy()`

Метод `countBy` підраховує входження значень у колекцію. За замовчуванням метод підраховує входження кожного елемента, що дає змогу підрахувати певні «типи» елементів у колекції:

    $collection = collect([1, 2, 2, 2, 3]);

    $counted = $collection->countBy();

    $counted->all();

    // [1 => 1, 2 => 3, 3 => 1]

Ви можете передати замикання методу `countBy` для підрахунку всіх елементів за власними критеріями:

    $collection = collect(['alice@gmail.com', 'bob@yahoo.com', 'carlos@gmail.com']);

    $counted = $collection->countBy(function (string $email) {
        return substr(strrchr($email, "@"), 1);
    });

    $counted->all();

    // ['gmail.com' => 2, 'yahoo.com' => 1]

<a name="method-crossjoin"></a>
#### `crossJoin()`

Метод `crossJoin` перехресно з'єднує значення колекції серед переданих масивів або колекцій, повертаючи декартовий добуток з усіма можливими перестановками:

    $collection = collect([1, 2]);

    $matrix = $collection->crossJoin(['a', 'b']);

    $matrix->all();

    /*
        [
            [1, 'a'],
            [1, 'b'],
            [2, 'a'],
            [2, 'b'],
        ]
    */

    $collection = collect([1, 2]);

    $matrix = $collection->crossJoin(['a', 'b'], ['I', 'II']);

    $matrix->all();

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

<a name="method-dd"></a>
#### `dd()`

Метод `dd` виводить елементи колекції і завершує виконання скрипта:

    $collection = collect(['John Doe', 'Jane Doe']);

    $collection->dd();

    /*
        Collection {
            #items: array:2 [
                0 => "John Doe"
                1 => "Jane Doe"
            ]
        }
    */

Якщо ви не хочете зупиняти виконання вашого скрипта, використовуйте замість цього метод [`dump`](#method-dump).

<a name="method-diff"></a>
#### `diff()`

Метод `diff` порівнює колекцію з іншою колекцією або простим масивом PHP на основі його значень. Цей метод поверне значення з вихідної колекції, яких немає в переданій колекції:

    $collection = collect([1, 2, 3, 4, 5]);

    $diff = $collection->diff([2, 4, 6, 8]);

    $diff->all();

    // [1, 3, 5]

> [!NOTE]
> Поведінка цього методу змінюється при використанні [колекцій Eloquent](/docs/{{version}}/eloquent-collections#method-diff).

<a name="method-diffassoc"></a>
#### `diffAssoc()`

Метод `diffAssoc` порівнює колекцію з іншою колекцією або простим масивом PHP на основі його ключів і значень. Цей метод поверне пари ключ / значення з вихідної колекції, яких немає в переданій колекції:

    $collection = collect([
        'color' => 'orange',
        'type' => 'fruit',
        'remain' => 6,
    ]);

    $diff = $collection->diffAssoc([
        'color' => 'yellow',
        'type' => 'fruit',
        'remain' => 3,
        'used' => 6,
    ]);

    $diff->all();

    // ['color' => 'orange', 'remain' => 6]

<a name="method-diffassocusing"></a>
#### `diffAssocUsing()`

На відміну від `diffAssoc`, `diffAssocUsing` приймає користувацьку функцію зворотного виклику для порівняння індексів:

    $collection = collect([
        'color' => 'orange',
        'type' => 'fruit',
        'remain' => 6,
    ]);

    $diff = $collection->diffAssocUsing([
        'Color' => 'yellow',
        'Type' => 'fruit',
        'Remain' => 3,
    ], 'strnatcasecmp');

    $diff->all();

    // ['color' => 'orange', 'remain' => 6]


Зворотний виклик має бути функцією порівняння, яка повертає ціле число менше, рівне або більше нуля. Додаткову інформацію можна знайти в документації PHP щодо array_diff_uassoc, функції PHP, яку внутрішньо використовує метод diffAssocUsing.

<a name="method-diffkeys"></a>
#### `diffKeys()`

Метод `diffKeys` порівнює колекцію з іншою колекцією або простим масивом PHP на основі його ключів. Цей метод поверне пари ключ / значення з вихідної колекції, яких немає в переданій колекції:

    $collection = collect([
        'one' => 10,
        'two' => 20,
        'three' => 30,
        'four' => 40,
        'five' => 50,
    ]);

    $diff = $collection->diffKeys([
        'two' => 2,
        'four' => 4,
        'six' => 6,
        'eight' => 8,
    ]);

    $diff->all();

    // ['one' => 10, 'three' => 30, 'five' => 50]

<a name="method-doesntcontain"></a>
#### `doesntContain()`

Метод `doesntContain` визначає, чи не містить колекція даний елемент. Ви можете передати замикання методу `doesntContain`, щоб визначити, чи не існує елемента в колекції, що відповідає заданому критерію:

    $collection = collect([1, 2, 3, 4, 5]);

    $collection->doesntContain(function (int $value, int $key) {
        return $value < 5;
    });

    // false

Як альтернативу ви можете передати рядок методу `doesntContain`, щоб визначити, чи не містить колекція заданого значення елемента:

    $collection = collect(['name' => 'Desk', 'price' => 100]);

    $collection->doesntContain('Table');

    // true

    $collection->doesntContain('Desk');

    // false

Ви також можете передати пару ключ/значення методу `doesntContain`, який визначить, чи не існує ця пара в колекції:

    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Chair', 'price' => 100],
    ]);

    $collection->doesntContain('product', 'Bookcase');

    // true

Метод `doesntContain` використовує «не суворе» порівняння при перевірці значень елементів, що означає, що рядок з цілим значенням буде вважатися рівним цілому числу того ж значення.

<a name="method-dot"></a>
#### `dot()`

Метод `dot` згладжує багатовимірну колекцію в колекцію одного рівня, використовуючи «точкову» нотацію для зазначення глибини:

    $collection = collect(['products' => ['desk' => ['price' => 100]]]);

    $flattened = $collection->dot();

    $flattened->all();

    // ['products.desk.price' => 100]

<a name="method-dump"></a>
#### `dump()`

Метод `dump` виводить елементи колекції:

    $collection = collect(['John Doe', 'Jane Doe']);

    $collection->dump();

    /*
        Collection {
            #items: array:2 [
                0 => "John Doe"
                1 => "Jane Doe"
            ]
        }
    */

Якщо ви хочете припинити виконання скрипта після виведення елементів колекції, використовуйте замість цього метод [`dd`](#method-dd).

<a name="method-duplicates"></a>
#### `duplicates()`

Метод `duplicates` витягує і повертає повторювані значення з колекції:

    $collection = collect(['a', 'b', 'a', 'c', 'b']);

    $collection->duplicates();

    // [2 => 'a', 4 => 'b']

Якщо колекція містить масиви або об'єкти, ви можете передати ключ атрибутів, які ви хочете перевірити на наявність повторюваних значень:

    $employees = collect([
        ['email' => 'abigail@example.com', 'position' => 'Developer'],
        ['email' => 'james@example.com', 'position' => 'Designer'],
        ['email' => 'victoria@example.com', 'position' => 'Developer'],
    ]);

    $employees->duplicates('position');

    // [2 => 'Developer']

<a name="method-duplicatesstrict"></a>
#### `duplicatesStrict()`

Цей метод має ту саму сигнатуру, що й метод [`duplicates`](#method-duplicates); однак, усі значення порівнюються з використанням «жорсткого» порівняння.

<a name="method-each"></a>
#### `each()`

Метод `each` перебирає елементи в колекції та передає кожен елемент у замикання:

    $collection = collect([1, 2, 3, 4]);

    $collection->each(function (int $item, int $key) {
        // ...
    });

Якщо ви хочете припинити ітерацію за елементами, ви можете повернути `false` з вашого замикання:

    $collection->each(function (int $item, int $key) {
        if (/* condition */) {
            return false;
        }

<a name="method-eachspread"></a>
#### `eachSpread()`

Метод `eachSpread` виконує ітерацію за елементами колекції, передаючи значення кожного вкладеного елемента в замикання:

    $collection = collect([['John Doe', 35], ['Jane Doe', 33]]);

    $collection->eachSpread(function (string $name, int $age) {
        // ...
    });

Якщо ви хочете припинити ітерацію за елементами, ви можете повернути `false` з вашого замикання:

    $collection->eachSpread(function (string $name, int $age) {
        return false;
    });

<a name="method-ensure"></a>
#### `ensure()`

Метод `ensure` може використовуватися для перевірки того, що всі елементи колекції мають певний тип або список типів. В іншому випадку буде викинуто виняток `UnexpectedValueException`:

    return $collection->ensure(User::class);
    
    return $collection->ensure([User::class, Customer::class]);

Примітивні типи, такі як `string`, `int`, `float`, `bool` і `array`, також можуть бути вказані:

    return $collection->ensure('int');

> [!WARNING]
> Метод `ensure` не гарантує, що елементи різних типів не будуть додані в колекцію в майбутньому.

<a name="method-every"></a>
#### `every()`

Метод `every` використовується для перевірки того, що всі елементи колекції проходять зазначений тест істинності:

    collect([1, 2, 3, 4])->every(function (int $value, int $key) {
        return $value > 2;
    });

    // false

Якщо колекція порожня, метод `every` поверне `true`:

    $collection = collect([]);

    $collection->every(function (int $value, int $key) {
        return $value > 2;
    });

    // true

<a name="method-except"></a>
#### `except()`

Метод `except` повертає всі елементи з колекції, окрім тих, які мають зазначені ключі:

    $collection = collect(['product_id' => 1, 'price' => 100, 'discount' => false]);

    $filtered = $collection->except(['price', 'discount']);

    $filtered->all();

    // ['product_id' => 1]

Протилежним методу `except` є метод [only](#method-only).

> [!NOTE]  
> Поведінка цього методу змінюється при використанні [колекцій Eloquent](/docs/{{version}}/eloquent-collections#method-except).

<a name="method-filter"></a>
#### `filter()`

Метод `filter` фільтрує колекцію, використовуючи передане замикання, зберігаючи тільки ті елементи, які проходять вказаний тест істинності:

    $collection = collect([1, 2, 3, 4]);

    $filtered = $collection->filter(function (int $value, int $key) {
        return $value > 2;
    });

    $filtered->all();

    // [3, 4]

Якщо замикання не вказано, то всі записи колекції, еквівалентні `false`, будуть видалені:

    $collection = collect([1, 2, 3, null, false, '', 0, []]);

    $collection->filter()->all();

    // [1, 2, 3]

Протилежним методу `filter` є метод [reject](#method-reject).

<a name="method-first"></a>
#### `first()`

Метод `first` повертає перший елемент з колекції, який проходить зазначену перевірку істинності:

    collect([1, 2, 3, 4])->first(function (int $value, int $key) {
        return $value > 2;
    });

    // 3

Ви також можете викликати метод `first` без аргументів, щоб отримати перший елемент з колекції. Якщо колекція порожня, повертається `null`:

    collect([1, 2, 3, 4])->first();

    // 1

<a name="method-first-or-fail"></a>
#### `firstOrFail()`

Метод `firstOrFail` ідентичний методу `first`; однак, якщо результат не знайдено, буде згенеровано виняток `Illuminate\Support\ItemNotFoundException`:

    collect([1, 2, 3, 4])->firstOrFail(function (int $value, int $key) {
        return $value > 5;
    });
    
    // Генерує виняток ItemNotFoundException...

Ви також можете викликати метод `firstOrFail` без аргументів, щоб отримати перший елемент у колекції. Якщо колекція порожня, буде згенеровано виняток `Illuminate\Support\ItemNotFoundException`:

    collect([])->firstOrFail();
    
    // Генерує виняток ItemNotFoundException...

<a name="method-first-where"></a>
#### `firstWhere()`

Метод `firstWhere` повертає перший елемент колекції з переданою парою ключ / значення:

    $collection = collect([
        ['name' => 'Regena', 'age' => null],
        ['name' => 'Linda', 'age' => 14],
        ['name' => 'Diego', 'age' => 23],
        ['name' => 'Linda', 'age' => 84],
    ]);

    $collection->firstWhere('name', 'Linda');

    // ['name' => 'Linda', 'age' => 14]

Ви також можете викликати метод `firstWhere` з оператором порівняння:

    $collection->firstWhere('age', '>=', 18);

    // ['name' => 'Diego', 'age' => 23]

Подібно до методу [where](#method-where), ви можете передати один аргумент методу `firstWhere`. У цьому сценарії метод `firstWhere` поверне перший елемент, для якого значення даного ключа елемента є «істинним»:

    $collection->firstWhere('age');

    // ['name' => 'Linda', 'age' => 14]

<a name="method-flatmap"></a>
#### `flatMap()`

Метод `flatMap` виконує ітерацію по колекції і передає кожне значення переданому замиканню. Замикання може змінити елемент і повернути його, таким чином формуючи нову колекцію змінених елементів. Потім масив перетворюється на плоску структуру:

    $collection = collect([
        ['name' => 'Sally'],
        ['school' => 'Arkansas'],
        ['age' => 28]
    ]);

    $flattened = $collection->flatMap(function (array $values) {
        return array_map('strtoupper', $values);
    });

    $flattened->all();

    // ['name' => 'SALLY', 'school' => 'ARKANSAS', 'age' => '28'];

<a name="method-flatten"></a>
#### `flatten()`

Метод `flatten` об'єднує багатовимірну колекцію в однорівневу:

    $collection = collect([
        'name' => 'taylor',
        'languages' => [
            'php', 'javascript'
        ]
    ]);

    $flattened = $collection->flatten();

    $flattened->all();

    // ['taylor', 'php', 'javascript'];

Якщо необхідно, ви можете передати методу `flatten` аргумент «глибини»:

    $collection = collect([
        'Apple' => [
            [
                'name' => 'iPhone 6S',
                'brand' => 'Apple'
            ],
        ],
        'Samsung' => [
            [
                'name' => 'Galaxy S7',
                'brand' => 'Samsung'
            ],
        ],
    ]);

    $products = $collection->flatten(1);

    $products->values()->all();

    /*
        [
            ['name' => 'iPhone 6S', 'brand' => 'Apple'],
            ['name' => 'Galaxy S7', 'brand' => 'Samsung'],
        ]
    */

У цьому прикладі виклик `flatten` без зазначення глибини також призвів би до згладжування вкладених масивів, що призвело б до `['iPhone 6S', 'Apple', 'Galaxy S7', 'Samsung']`. Надання глибини дозволяє вказати кількість рівнів, на які будуть згладжені вкладені масиви.

<a name="method-flip"></a>
#### `flip()`

Метод `flip` міняє місцями ключі колекції на їхні відповідні значення:

    $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);

    $flipped = $collection->flip();

    $flipped->all();

    // ['taylor' => 'name', 'laravel' => 'framework']

<a name="method-forget"></a>
#### `forget()`

Метод `forget` видаляє елемент із колекції за його ключем:

    $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);

     // Забути один ключ...
    $collection->forget('name');

    // ['framework' => 'laravel']

    // Забути кілька ключів...
    $collection->forget(['name', 'framework']);

    // []

> [!WARNING]
> На відміну від більшості інших методів колекції, `forget` модифікує колекцію.

<a name="method-forpage"></a>
#### `forPage()`

Метод `forPage` повертає нову колекцію, що містить елементи, які будуть присутні на зазначеному номері сторінки. Метод приймає номер сторінки як перший аргумент і кількість елементів, що відображаються на сторінці, як другий аргумент:

    $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

    $chunk = $collection->forPage(2, 3);

    $chunk->all();

    // [4, 5, 6]

<a name="method-get"></a>
#### `get()`

Метод `get` повертає елемент за вказаним ключем. Якщо ключ не існує, повертається `null`:

    $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);

    $value = $collection->get('name');

    // taylor

За бажання ви можете передати значення за замовчуванням як другий аргумент:

    $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);

    $value = $collection->get('age', 34);

    // 34

Ви навіть можете передати замикання як значення методу за замовчуванням. Результат замикання буде повернуто, якщо вказаний ключ не існує:

    $collection->get('email', function () {
        return 'taylor@example.com';
    });

    // taylor@example.com

<a name="method-groupby"></a>
#### `groupBy()`

Метод `groupBy` групує елементи колекції за вказаним ключем:

    $collection = collect([
        ['account_id' => 'account-x10', 'product' => 'Chair'],
        ['account_id' => 'account-x10', 'product' => 'Bookcase'],
        ['account_id' => 'account-x11', 'product' => 'Desk'],
    ]);

    $grouped = $collection->groupBy('account_id');

    $grouped->all();

    /*
        [
            'account-x10' => [
                ['account_id' => 'account-x10', 'product' => 'Chair'],
                ['account_id' => 'account-x10', 'product' => 'Bookcase'],
            ],
            'account-x11' => [
                ['account_id' => 'account-x11', 'product' => 'Desk'],
            ],
        ]
    */

Замість передачі рядкового ключа ви можете передати замикання. Замикання повинно повернути значення, що використовується як ключ для групування:

    $grouped = $collection->groupBy(function (array $item, int $key) {
        return substr($item['account_id'], -3);
    });

    $grouped->all();

    /*
        [
            'x10' => [
                ['account_id' => 'account-x10', 'product' => 'Chair'],
                ['account_id' => 'account-x10', 'product' => 'Bookcase'],
            ],
            'x11' => [
                ['account_id' => 'account-x11', 'product' => 'Desk'],
            ],
        ]
    */

У вигляді масиву можна передати кілька критеріїв групування. Кожен елемент масиву буде застосовано до відповідного рівня в багатовимірному масиві:

    $data = new Collection([
        10 => ['user' => 1, 'skill' => 1, 'roles' => ['Role_1', 'Role_3']],
        20 => ['user' => 2, 'skill' => 1, 'roles' => ['Role_1', 'Role_2']],
        30 => ['user' => 3, 'skill' => 2, 'roles' => ['Role_1']],
        40 => ['user' => 4, 'skill' => 2, 'roles' => ['Role_2']],
    ]);

    $result = $data->groupBy(['skill', function (array $item) {
        return $item['roles'];
    }], preserveKeys: true);

    /*
    [
        1 => [
            'Role_1' => [
                10 => ['user' => 1, 'skill' => 1, 'roles' => ['Role_1', 'Role_3']],
                20 => ['user' => 2, 'skill' => 1, 'roles' => ['Role_1', 'Role_2']],
            ],
            'Role_2' => [
                20 => ['user' => 2, 'skill' => 1, 'roles' => ['Role_1', 'Role_2']],
            ],
            'Role_3' => [
                10 => ['user' => 1, 'skill' => 1, 'roles' => ['Role_1', 'Role_3']],
            ],
        ],
        2 => [
            'Role_1' => [
                30 => ['user' => 3, 'skill' => 2, 'roles' => ['Role_1']],
            ],
            'Role_2' => [
                40 => ['user' => 4, 'skill' => 2, 'roles' => ['Role_2']],
            ],
        ],
    ];
    */

<a name="method-has"></a>
#### `has()`

Метод `has` визначає, чи існує переданий ключ у колекції:

    $collection = collect(['account_id' => 1, 'product' => 'Desk', 'amount' => 5]);

    $collection->has('product');

    // true

    $collection->has(['product', 'amount']);

    // true

    $collection->has(['amount', 'price']);

    // false

<a name="method-hasany"></a>
#### `hasAny()`

Метод `hasAny` визначає, чи існує хоча б один із заданих ключів у колекції:

    $collection = collect(['account_id' => 1, 'product' => 'Desk', 'amount' => 5]);

    $collection->hasAny(['product', 'price']);

    // true

    $collection->hasAny(['name', 'price']);

    // false

<a name="method-implode"></a>
#### `implode()`

Метод `implode` об'єднує елементи колекції. Його аргументи залежать від типу елементів у колекції. Якщо колекція містить масиви або об'єкти, ви повинні передати ключ об'єднуються атрибутів, і «сполучний рядок», що розміщується між значеннями:

    $collection = collect([
        ['account_id' => 1, 'product' => 'Desk'],
        ['account_id' => 2, 'product' => 'Chair'],
    ]);

    $collection->implode('product', ', ');

    // Desk, Chair

Якщо колекція містить прості рядки або числові значення, ви повинні передати «сполучний рядок» як єдиний аргумент методу:

    collect([1, 2, 3, 4, 5])->implode('-');

    // '1-2-3-4-5'

Ви можете передати замикання методу `implode`, якщо хочете форматувати значення, які об'єднуються:

    $collection->implode(function (array $item, int $key) {
        return strtoupper($item['product']);
    }, ', ');

    // DESK, CHAIR

<a name="method-intersect"></a>
#### `intersect()`

Метод `intersect` видаляє будь-які значення з вихідної колекції, яких немає в зазначеному масиві або колекції. Отримана колекція збереже ключі вихідної колекції:

    $collection = collect(['Desk', 'Sofa', 'Chair']);

    $intersect = $collection->intersect(['Desk', 'Chair', 'Bookcase']);

    $intersect->all();

    // [0 => 'Desk', 2 => 'Chair']

> [!NOTE]
> Поведінка цього методу змінюється при використанні [колекцій Eloquent](/docs/{{version}}/eloquent-collections#method-intersect).

<a name="method-intersectAssoc"></a>
#### `intersectAssoc()`

Метод `intersectAssoc` порівнює вихідну колекцію з іншою колекцією або `array`, повертаючи пари ключ/значення, які присутні у всіх заданих колекціях:

    $collection = collect([
        'color' => 'red',
        'size' => 'M',
        'material' => 'cotton'
    ]);

    $intersect = $collection->intersectAssoc([
        'color' => 'blue',
        'size' => 'M',
        'material' => 'polyester'
    ]);

    $intersect->all();

    // ['size' => 'M']

<a name="method-intersectbykeys"></a>
#### `intersectByKeys()`

Метод `intersectByKeys` видаляє всі ключі та відповідні їм значення з вихідної колекції, ключі яких відсутні в зазначеному масиві або колекції:

    $collection = collect([
        'serial' => 'UX301', 'type' => 'screen', 'year' => 2009,
    ]);

    $intersect = $collection->intersectByKeys([
        'reference' => 'UX404', 'type' => 'tab', 'year' => 2011,
    ]);

    $intersect->all();

    // ['type' => 'screen', 'year' => 2009]

<a name="method-isempty"></a>
#### `isEmpty()`

Метод `isEmpty` повертає `true`, якщо колекція порожня; в іншому випадку повертається `false`:

    collect([])->isEmpty();

    // true

<a name="method-isnotempty"></a>
#### `isNotEmpty()`

Метод `isNotEmpty` повертає `true`, якщо колекція не порожня; в іншому випадку повертається `false`:

    collect([])->isNotEmpty();

    // false

<a name="method-join"></a>
#### `join()`

Метод `join` об'єднує значення колекції в рядок. Використовуючи другий аргумент цього методу, ви також можете вказати, як останній елемент має бути доданий до рядка:

    collect(['a', 'b', 'c'])->join(', '); // 'a, b, c'
    collect(['a', 'b', 'c'])->join(', ', ', and '); // 'a, b, and c'
    collect(['a', 'b'])->join(', ', ' and '); // 'a and b'
    collect(['a'])->join(', ', ' and '); // 'a'
    collect([])->join(', ', ' and '); // ''

<a name="method-keyby"></a>
#### `keyBy()`

Метод `keyBy` повертає колекцію, елементи якої будуть утворені шляхом присвоєння ключів елементам базової колекції. Якщо у кількох елементів один і той самий ключ, у новій колекції з'явиться тільки останній:

    $collection = collect([
        ['product_id' => 'prod-100', 'name' => 'Desk'],
        ['product_id' => 'prod-200', 'name' => 'Chair'],
    ]);

    $keyed = $collection->keyBy('product_id');

    $keyed->all();

    /*
        [
            'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
            'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]
    */

Ви також можете передати методу замикання. Замикання має повертати ім'я для ключа колекції:

    $keyed = $collection->keyBy(function (array $item, int $key) {
        return strtoupper($item['product_id']);
    });

    $keyed->all();

    /*
        [
            'PROD-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
            'PROD-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]
    */

<a name="method-keys"></a>
#### `keys()`

Метод `keys` повертає всі ключі колекції:

    $collection = collect([
        'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
        'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
    ]);

    $keys = $collection->keys();

    $keys->all();

    // ['prod-100', 'prod-200']

<a name="method-last"></a>
#### `last()`

Метод `last` повертає останній елемент у колекції, який проходить зазначену перевірку істинності:

    collect([1, 2, 3, 4])->last(function (int $value, int $key) {
        return $value < 3;
    });

    // 2

Ви також можете викликати метод `last` без аргументів, щоб отримати останній елемент колекції. Якщо колекція порожня, повертається `null`:

    collect([1, 2, 3, 4])->last();

    // 4

<a name="method-lazy"></a>
#### `lazy()`

Метод `lazy` повертає новий екземпляр [`LazyCollection`](#lazy-collections) з базового масиву елементів:

    $lazyCollection = collect([1, 2, 3, 4])->lazy();

    $lazyCollection::class;

    // Illuminate\Support\LazyCollection

    $lazyCollection->all();

    // [1, 2, 3, 4]

Це особливо корисно, коли вам потрібно виконувати перетворення у величезній `Collection`, що містить безліч елементів:

    $count = $hugeCollection
        ->lazy()
        ->where('country', 'FR')
        ->where('balance', '>', '100')
        ->count();

Перетворивши колекцію в `LazyCollection`, ми уникаємо необхідності виділяти величезну кількість додаткової пам'яті. Хоча вихідна колекція все ще зберігає свої значення в пам'яті, наступні фільтри цього не роблять. Таким чином, при фільтрації результатів колекції практично не виділяється додаткової пам'яті.

<a name="method-macro"></a>
#### `macro()`

Статичний метод `macro` дозволяє вам додавати методи до класу `Collection` під час виконання. Зверніться до документації по [розширенню колекцій](#extending-collections) для отримання додаткової інформації.

<a name="method-make"></a>
#### `make()`

Статичний метод `make` створює новий екземпляр колекції. Див. розділ [Створення колекцій](#creating-collections).

<a name="method-map"></a>
#### `map()`

Метод `map` виконує ітерацію по колекції і передає кожне значення вказаному замиканню. Замикання може змінити елемент і повернути його, утворюючи нову колекцію змінених елементів:

    $collection = collect([1, 2, 3, 4, 5]);

    $multiplied = $collection->map(function (int $item, int $key) {
        return $item * 2;
    });

    $multiplied->all();

    // [2, 4, 6, 8, 10]

> [!WARNING]
> Як і більшість інших методів колекції, `map` повертає новий екземпляр колекції; він не модифікує колекцію. Якщо ви хочете перетворити вихідну колекцію, використовуйте метод [`transform`](#method-transform).

<a name="method-mapinto"></a>
#### `mapInto()`

Метод `mapInto()` виконує ітерацію колекції, створюючи новий екземпляр зазначеного класу, і передаючи значення в його конструктор:

    class Currency
    {
        /**
         * Створити новий екземпляр валюти.
         */
        function __construct(
            public string $code
        ) {}
    }

    $collection = collect(['USD', 'EUR', 'GBP']);

    $currencies = $collection->mapInto(Currency::class);

    $currencies->all();

    // [Currency('USD'), Currency('EUR'), Currency('GBP')]

<a name="method-mapspread"></a>
#### `mapSpread()`

Метод `mapSpread` виконує ітерацію за елементами колекції, передаючи значення кожного вкладеного елемента в зазначене замикання. Замикання може змінити елемент і повернути його, таким чином формуючи нову колекцію змінених елементів:

    $collection = collect([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]);

    $chunks = $collection->chunk(2);

    $sequence = $chunks->mapSpread(function (int $even, int $odd)
        return $even + $odd;
    });

    $sequence->all();

    // [1, 5, 9, 13, 17]

<a name="method-maptogroups"></a>
#### `mapToGroups()`

Метод `mapToGroups` групує елементи колекції за вказаним замиканням. Замикання повинно повертати асоціативний масив, що містить одну пару ключ / значення, таким чином формуючи нову колекцію згрупованих значень:

    $collection = collect([
        [
            'name' => 'John Doe',
            'department' => 'Sales',
        ],
        [
            'name' => 'Jane Doe',
            'department' => 'Sales',
        ],
        [
            'name' => 'Johnny Doe',
            'department' => 'Marketing',
        ]
    ]);

    $grouped = $collection->mapToGroups(function (array $item, int $key) {
        return [$item['department'] => $item['name']];
    });

    $grouped->all();

    /*
        [
            'Sales' => ['John Doe', 'Jane Doe'],
            'Marketing' => ['Johnny Doe'],
        ]
    */

    $grouped->get('Sales')->all();

    // ['John Doe', 'Jane Doe']

<a name="method-mapwithkeys"></a>
#### `mapWithKeys()`

Метод `mapWithKeys` виконує ітерацію по колекції і передає кожне значення в зазначене замикання. Замикання повинно повертати асоціативний масив, що містить одну пару ключ / значення:

    $collection = collect([
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
    ]);

    $keyed = $collection->mapWithKeys(function (array $item, int $key) {
        return [$item['email'] => $item['name']];
    });

    $keyed->all();

    /*
        [
            'john@example.com' => 'John',
            'jane@example.com' => 'Jane',
        ]
    */

<a name="method-max"></a>
#### `max()`

Метод `max` повертає максимальне значення переданого ключа:

    $max = collect([
        ['foo' => 10],
        ['foo' => 20]
    ])->max('foo');

    // 20

    $max = collect([1, 2, 3, 4, 5])->max();

    // 5

<a name="method-median"></a>
#### `median()`

Метод `median` повертає [медіану](https://en.wikipedia.org/wiki/Median) переданого ключа:

    $median = collect([
        ['foo' => 10],
        ['foo' => 10],
        ['foo' => 20],
        ['foo' => 40]
    ])->median('foo');

    // 15

    $median = collect([1, 1, 2, 4])->median();

    // 1.5

<a name="method-merge"></a>
#### `merge()`

Метод `merge` об'єднує переданий масив або колекцію з вихідною колекцією. Якщо строковий ключ у переданих елементах відповідає рядковому ключу у вихідній колекції, значення переданого елемента перезапише значення у вихідній колекції:

    $collection = collect(['product_id' => 1, 'price' => 100]);

    $merged = $collection->merge(['price' => 200, 'discount' => false]);

    $merged->all();

    // ['product_id' => 1, 'price' => 200, 'discount' => false]

Якщо ключі переданих елементів є числовими, значення будуть додані в кінець колекції:

    $collection = collect(['Desk', 'Chair']);

    $merged = $collection->merge(['Bookcase', 'Door']);

    $merged->all();

    // ['Desk', 'Chair', 'Bookcase', 'Door']

<a name="method-mergerecursive"></a>
#### `mergeRecursive()`

Метод `mergeRecursive` рекурсивно об'єднує переданий масив або колекцію з вихідною колекцією. Якщо строковий ключ у переданих елементах збігається з строковим ключем у вихідній колекції, тоді значення цих ключів об'єднуються в масив, і це робиться рекурсивно:

    $collection = collect(['product_id' => 1, 'price' => 100]);

    $merged = $collection->mergeRecursive([
        'product_id' => 2,
        'price' => 200,
        'discount' => false
    ]);

    $merged->all();

    // ['product_id' => [1, 2], 'price' => [100, 200], 'discount' => false]

<a name="method-min"></a>
#### `min()`

Метод `min` повертає мінімальне значення переданого ключа:

    $min = collect([['foo' => 10], ['foo' => 20]])->min('foo');

    // 10

    $min = collect([1, 2, 3, 4, 5])->min();

    // 1

<a name="method-mode"></a>
#### `mode()`

Метод `mode` повертає [значення моди](https://en.wikipedia.org/wiki/Mode_(statistics)) вказаного ключа:

    $mode = collect([
        ['foo' => 10],
        ['foo' => 10],
        ['foo' => 20],
        ['foo' => 40]
    ])->mode('foo');

    // [10]

    $mode = collect([1, 1, 2, 4])->mode();

    // [1]

    $mode = collect([1, 1, 2, 2])->mode();

    // [1, 2]

<a name="method-multiply"></a>
#### `multiply()`

Метод `multiply` створює вказану кількість копій усіх елементів колекції:

```php
$users = collect([
    ['name' => 'User #1', 'email' => 'user1@example.com'],
    ['name' => 'User #2', 'email' => 'user2@example.com'],
])->multiply(3);

/*
    [
        ['name' => 'User #1', 'email' => 'user1@example.com'],
        ['name' => 'User #2', 'email' => 'user2@example.com'],
        ['name' => 'User #1', 'email' => 'user1@example.com'],
        ['name' => 'User #2', 'email' => 'user2@example.com'],
        ['name' => 'User #1', 'email' => 'user1@example.com'],
        ['name' => 'User #2', 'email' => 'user2@example.com'],
    ]
*/
```

<a name="method-nth"></a>
#### `nth()`

Метод `nth` створює нову колекцію, що складається з кожного `n`-го елемента:

    $collection = collect(['a', 'b', 'c', 'd', 'e', 'f']);

    $collection->nth(4);

    // ['a', 'e']

За бажання ви можете передати початкове зміщення як другий аргумент:

    $collection->nth(4, 1);

    // ['b', 'f']

<a name="method-only"></a>
#### `only()`

Метод `only` повертає елементи колекції тільки із зазначеними ключами:

    $collection = collect([
        'product_id' => 1,
        'name' => 'Desk',
        'price' => 100,
        'discount' => false
    ]);

    $filtered = $collection->only(['product_id', 'name']);

    $filtered->all();

    // ['product_id' => 1, 'name' => 'Desk']

Протилежним методу `only` є метод [except](#method-except).

> [!NOTE]
> Поведінка цього методу змінюється при використанні [колекцій Eloquent](/docs/{{version}}/eloquent-collections#method-only).

<a name="method-pad"></a>
#### `pad()`

Метод `pad` доповнить колекцію певним значенням, поки колекція не досягне зазначеного розміру. Цей метод поводиться як функція [array_pad](https://www.php.net/manual/ru/function.array-pad.php) PHP.

Для доповнення зліва слід вказати від'ємний розмір. Якщо абсолютне значення вказаного розміру менше або дорівнює довжині масиву, заповнення не відбудеться:

    $collection = collect(['A', 'B', 'C']);

    $filtered = $collection->pad(5, 0);

    $filtered->all();

    // ['A', 'B', 'C', 0, 0]

    $filtered = $collection->pad(-5, 0);

    $filtered->all();

    // [0, 0, 'A', 'B', 'C']

<a name="method-partition"></a>
#### `partition()`

Метод `partition` використовується у зв'язці з деструктуризацією масивів PHP (замість функції list у попередніх версіях), щоб відокремити елементи, які пройшли зазначену перевірку істинності, від тих, які її не пройшли:

    $collection = collect([1, 2, 3, 4, 5, 6]);

    [$underThree, $equalOrAboveThree] = $collection->partition(function (int $i) {
        return $i < 3;
    });

    $underThree->all();

    // [1, 2]

    $equalOrAboveThree->all();

    // [3, 4, 5, 6]

<a name="method-percentage"></a>
#### `percentage()`

Метод `percentage` може бути використаний для швидкого визначення відсотка елементів у колекції, які проходять задану умову:

```php
$collection = collect([1, 1, 2, 2, 2, 3]);

$percentage = $collection->percentage(fn ($value) => $value === 1);

// 33.33
```

За замовчуванням відсоток буде округлено до двох знаків після коми. Однак, ви можете налаштувати цю поведінку, вказавши другий аргумент методу:

```php
$percentage = $collection->percentage(fn ($value) => $value === 1, precision: 3);

// 33.333
```

<a name="method-pipe"></a>
#### `pipe()`

Метод `pipe` передає колекцію вказаному замиканню і повертає результат виконаного замикання:

    $collection = collect([1, 2, 3]);

    $piped = $collection->pipe(function (Collection $collection) {
        return $collection->sum();
    });

    // 6

<a name="method-pipeinto"></a>
#### `pipeInto()`

Метод `pipeInto` створює новий екземпляр зазначеного класу і передає колекцію в конструктор:

    class ResourceCollection
    {

        /**
         * Створити новий екземпляр ResourceCollection.
         */
        public function __construct(
            public Collection $collection,
        ) {}
    }

    $collection = collect([1, 2, 3]);

    $resource = $collection->pipeInto(ResourceCollection::class);

    $resource->collection->all();

    // [1, 2, 3]

<a name="method-pipethrough"></a>
#### `pipeThrough()`

Метод `pipeThrough` передає колекцію заданому масиву замикань і повертає результат виконаних замикань:

    use Illuminate\Support\Collection;

    $collection = collect([1, 2, 3]);

    $result = $collection->pipeThrough([
        function (Collection $collection) {
            return $collection->merge([4, 5]);
        },
        function (Collection $collection) {
            return $collection->sum();
        },
    ]);

    // 15

<a name="method-pluck"></a>
#### `pluck()`

Метод `pluck` витягує всі значення для зазначеного ключа:

    $collection = collect([
        ['product_id' => 'prod-100', 'name' => 'Desk'],
        ['product_id' => 'prod-200', 'name' => 'Chair'],
    ]);

    $plucked = $collection->pluck('name');

    $plucked->all();

    // ['Desk', 'Chair']

Ви також можете задати ключ результуючої колекції:

    $plucked = $collection->pluck('name', 'product_id');

    $plucked->all();

    // ['prod-100' => 'Desk', 'prod-200' => 'Chair']

Метод `pluck` також підтримує отримання вкладених значень з використанням «точкової нотації»:

    $collection = collect([
        [
            'name' => 'Laracon',
            'speakers' => [
                'first_day' => ['Rosa', 'Judith'],
            ],
        ],
        [
            'name' => 'VueConf',
            'speakers' => [
                'first_day' => ['Abigail', 'Joey'],
            ],
        ],
    ]);

    $plucked = $collection->pluck('speakers.first_day');

    $plucked->all();

    // [['Rosa', 'Judith'], ['Abigail', 'Joey']]

Якщо існують ключі, що повторюються, останній відповідний елемент буде вставлено в результуючу колекцію:

    $collection = collect([
        ['brand' => 'Tesla',  'color' => 'red'],
        ['brand' => 'Pagani', 'color' => 'white'],
        ['brand' => 'Tesla',  'color' => 'black'],
        ['brand' => 'Pagani', 'color' => 'orange'],
    ]);

    $plucked = $collection->pluck('color', 'brand');

    $plucked->all();

    // ['Tesla' => 'black', 'Pagani' => 'orange']

<a name="method-pop"></a>
#### `pop()`

Метод `pop` видаляє і повертає останній елемент з колекції:

    $collection = collect([1, 2, 3, 4, 5]);

    $collection->pop();

    // 5

    $collection->all();

    // [1, 2, 3, 4]

Ви можете передати ціле число в метод `pop`, щоб видалити і повернути кілька елементів з кінця колекції:

    $collection = collect([1, 2, 3, 4, 5]);

    $collection->pop(3);

    // collect([5, 4, 3])

    $collection->all();

    // [1, 2]

<a name="method-prepend"></a>
#### `prepend()`

Метод `prepend` додає елемент на початок колекції:

    $collection = collect([1, 2, 3, 4, 5]);

    $collection->prepend(0);

    $collection->all();

    // [0, 1, 2, 3, 4, 5]

Ви також можете передати другий аргумент, щоб вказати ключ елемента, що додається:

    $collection = collect(['one' => 1, 'two' => 2]);

    $collection->prepend(0, 'zero');

    $collection->all();

    // ['zero' => 0, 'one' => 1, 'two' => 2]

<a name="method-pull"></a>
#### `pull()`

Метод `pull` видаляє і повертає елемент з колекції за його ключем:

    $collection = collect(['product_id' => 'prod-100', 'name' => 'Desk']);

    $collection->pull('name');

    // 'Desk'

    $collection->all();

    // ['product_id' => 'prod-100']

<a name="method-push"></a>
#### `push()`

Метод `push` додає елемент у кінець колекції:

    $collection = collect([1, 2, 3, 4]);

    $collection->push(5);

    $collection->all();

    // [1, 2, 3, 4, 5]

<a name="method-put"></a>
#### `put()`

Метод `put` поміщає зазначені ключ і значення в колекцію:

    $collection = collect(['product_id' => 1, 'name' => 'Desk']);

    $collection->put('price', 100);

    $collection->all();

    // ['product_id' => 1, 'name' => 'Desk', 'price' => 100]

<a name="method-random"></a>
#### `random()`

Метод `random` повертає випадковий елемент із колекції:

    $collection = collect([1, 2, 3, 4, 5]);

    $collection->random();

    // 4 - (retrieved randomly)

Ви можете передати ціле число в `random`, щоб вказати, скільки випадкових елементів ви хочете отримати. Колекція елементів завжди повертається при явній передачі кількості елементів, які ви хочете отримати:

    $random = $collection->random(3);

    $random->all();

    // [2, 4, 5] - (retrieved randomly)

Якщо в екземплярі колекції менше елементів, ніж запитано, метод `random` згенерує виняток `InvalidArgumentException`.

Метод `random` також приймає замикання, яке буде отримувати поточний екземпляр колекції:

    use Illuminate\Support\Collection;

    $random = $collection->random(fn (Collection $items) => min(10, count($items)));

    $random->all();

    // [1, 2, 3, 4, 5] - (retrieved randomly)

<a name="method-range"></a>
#### `range()`

Метод `range` повертає колекцію, що містить цілі числа в зазначеному діапазоні:

    $collection = collect()->range(3, 6);

    $collection->all();

    // [3, 4, 5, 6]


<a name="method-reduce"></a>
#### `reduce()`

Метод `reduce` скорочує колекцію до одного значення, передаючи результат кожної ітерації наступній ітерації:

    $collection = collect([1, 2, 3]);

    $total = $collection->reduce(function (?int $carry, int $item) {
        return $carry + $item;
    });

    // 6

Значення `$carry` першої ітерації дорівнює `null`; однак ви можете вказати його початкове значення, передавши другий аргумент методу `reduce`:

    $collection->reduce(function (int $carry, int $item) {
        return $carry + $item;
    }, 4);

    // 10

Метод `reduce` також передає ключі масиву асоціативних колекцій массиву асоціативних колекцій зазначеному замиканню:

    $collection = collect([
        'usd' => 1400,
        'gbp' => 1200,
        'eur' => 1000,
    ]);

    $ratio = [
        'usd' => 1,
        'gbp' => 1.37,
        'eur' => 1.22,
    ];

    $collection->reduce(function (int $carry, int $value, int $key) use ($ratio) {
        return $carry + ($value * $ratio[$key]);
    });

    // 4264

<a name="method-reduce-spread"></a>
#### `reduceSpread()`

Метод `reduceSpread` скорочує колекцію до масиву значень, передаючи результати кожної ітерації в наступну ітерацію. Цей метод схожий на метод `reduce`, однак він може приймати кілька початкових значень:

    [$creditsRemaining, $batch] = Image::where('status', 'unprocessed')
        ->get()
        ->reduceSpread(function (int $creditsRemaining, Collection $batch, Image $image) {
            if ($creditsRemaining >= $image->creditsRequired()) {
                $batch->push($image);

                $creditsRemaining -= $image->creditsRequired();
            }

            return [$creditsRemaining, $batch];
        }, $creditsAvailable, collect());

<a name="method-reject"></a>
#### `reject()`

Метод `reject` фільтрує колекцію, використовуючи передане замикання. Замикання повинно повертати `true`, якщо елемент повинен бути видалений з результуючої колекції:

    $collection = collect([1, 2, 3, 4]);

    $filtered = $collection->reject(function (int $value, int $key) {
        return $value > 2;
    });

    $filtered->all();

    // [1, 2]

Протилежним методу `reject` є метод [`filter`](#method-filter).

<a name="method-replace"></a>
#### `replace()`

Метод `replace` поводиться аналогічно методу `merge`; однак, окрім перезапису елементів, що збігаються, які мають строкові ключі, метод `replace` також перезаписує елементи в колекції, які мають збігові числові ключі:

    $collection = collect(['Taylor', 'Abigail', 'James']);

    $replaced = $collection->replace([1 => 'Victoria', 3 => 'Finn']);

    $replaced->all();

    // ['Taylor', 'Victoria', 'James', 'Finn']

<a name="method-replacerecursive"></a>
#### `replaceRecursive()`

Цей метод працює як і `replace`, але він буде повторюватися в масивах і застосовувати той самий процес заміни до внутрішніх значень:

    $collection = collect([
        'Taylor',
        'Abigail',
        [
            'James',
            'Victoria',
            'Finn'
        ]
    ]);

    $replaced = $collection->replaceRecursive([
        'Charlie',
        2 => [1 => 'King']
    ]);

    $replaced->all();

    // ['Charlie', 'Abigail', ['James', 'King', 'Finn']]

<a name="method-reverse"></a>
#### `reverse()`

Метод `reverse` змінює порядок елементів колекції на зворотний, зберігаючи вихідні ключі:

    $collection = collect(['a', 'b', 'c', 'd', 'e']);

    $reversed = $collection->reverse();

    $reversed->all();

    /*
        [
            4 => 'e',
            3 => 'd',
            2 => 'c',
            1 => 'b',
            0 => 'a',
        ]
    */

<a name="method-search"></a>
#### `search()`

Метод `search` шукає в колекції вказане значення і повертає його ключ, якщо він знайдений. Якщо елемент не знайдено, повертається `false`:

    $collection = collect([2, 4, 6, 8]);

    $collection->search(4);

    // 1

Пошук виконується з використанням «гнучкого» порівняння, тобто рядок із цілим значенням вважатиметься рівним цілому числу того самого значення. Щоб використовувати «жорстке» порівняння, передайте `true` як другий аргумент методу:

    collect([2, 4, 6, 8])->search('4', strict: true);

    // false

В якості альтернативи ви можете передати власне замикання для пошуку першого елемента, який проходить вказаний тест на істинність:

    collect([2, 4, 6, 8])->search(function (int $item, int $key) {
        return $item > 5;
    });

    // 2

<a name="method-select"></a>
#### `select()` {.collection-method}

Метод `select` вибирає задані ключі з колекції, подібно до SQL-оператора `SELECT`:

```php
$users = collect([
    ['name' => 'Taylor Otwell', 'role' => 'Developer', 'status' => 'active'],
    ['name' => 'Victoria Faith', 'role' => 'Researcher', 'status' => 'active'],
]);

$users->select(['name', 'role']);

/*
    [
        ['name' => 'Taylor Otwell', 'role' => 'Developer'],
        ['name' => 'Victoria Faith', 'role' => 'Researcher'],
    ],
*/
```

<a name="method-shift"></a>
#### `shift()`

Метод `shift` видаляє і повертає перший елемент з колекції:

    $collection = collect([1, 2, 3, 4, 5]);

    $collection->shift();

    // 1

    $collection->all();

    // [2, 3, 4, 5]

Ви можете передати ціле число в метод `shift`, щоб видалити і повернути кілька елементів з початку колекції:

    $collection = collect([1, 2, 3, 4, 5]);

    $collection->shift(3);

    // collect([1, 2, 3])

    $collection->all();

    // [4, 5]

<a name="method-shuffle"></a>
#### `shuffle()`

Метод `shuffle` випадковим чином перемішує елементи в колекції:

    $collection = collect([1, 2, 3, 4, 5]);

    $shuffled = $collection->shuffle();

    $shuffled->all();

    // [3, 2, 5, 1, 4] - (послідовність випадкова)

<a name="method-skip"></a>
#### `skip()`

Метод `skip` повертає нову колекцію із зазначеною кількістю елементів, що видаляються з початку колекції:

    $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

    $collection = $collection->skip(4);

    $collection->all();

    // [5, 6, 7, 8, 9, 10]

<a name="method-skipuntil"></a>
#### `skipUntil()`

Метод `skipUntil` пропускає елементи з колекції доти, доки передане замикання не поверне `true`, а потім поверне елементи, що залишилися в колекції, як новий екземпляр колекції:

    $collection = collect([1, 2, 3, 4]);

    $subset = $collection->skipUntil(function (int $item) {
        return $item >= 3;
    });

    $subset->all();

    // [3, 4]

Ви також можете передати просте значення методу `skipUntil`, щоб пропустити всі елементи, поки не буде знайдено вказане значення:

    $collection = collect([1, 2, 3, 4]);

    $subset = $collection->skipUntil(3);

    $subset->all();

    // [3, 4]

> [!WARNING]
> Якщо вказане значення не знайдено або замикання ніколи не повертає `true`, то метод `skipUntil` поверне порожню колекцію.

<a name="method-skipwhile"></a>
#### `skipWhile()`

Метод `skipWhile` пропускає елементи з колекції, поки вказане замикання повертає `true`, а потім повертає елементи, що залишилися в колекції, як нову колекцію:

    $collection = collect([1, 2, 3, 4]);

    $subset = $collection->skipWhile(function (int $item) {
        return $item <= 3;
    });

    $subset->all();

    // [4]

> [!WARNING]
> Якщо замикання ніколи не повертає `false`, то метод `skipWhile` поверне порожню колекцію.

<a name="method-slice"></a>
#### `slice()`

Метод `slice` повертає фрагмент колекції, починаючи із зазначеного індексу:

    $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

    $slice = $collection->slice(4);

    $slice->all();

    // [5, 6, 7, 8, 9, 10]

Якщо ви хочете обмежити розмір фрагмента, що повертається, то передайте бажаний розмір як другий аргумент методу:

    $slice = $collection->slice(4, 2);

    $slice->all();

    // [5, 6]

Повернутий фрагмент за замовчуванням збереже ключі. Якщо ви не хочете зберігати вихідні ключі, ви можете використовувати метод [`values`](#method-values), щоб переіндексувати їх.

<a name="method-sliding"></a>
#### `sliding()`

Метод `sliding` повертає нову колекцію фрагментів (chunks), що представляють представлення елементів колекції у вигляді «ковзного вікна»:

    $collection = collect([1, 2, 3, 4, 5]);

    $chunks = $collection->sliding(2);

    $chunks->toArray();

    // [[1, 2], [2, 3], [3, 4], [4, 5]]

Це особливо корисно в поєднанні з методом [`eachSpread`](#method-eachspread):

    $transactions->sliding(2)->eachSpread(function (Collection $previous, Collection $current) {
        $current->total = $previous->total + $current->amount;
    });

За бажанням другим аргументом можна передати «крок», який визначає відстань між першим елементом кожного фрагмента:

    $collection = collect([1, 2, 3, 4, 5]);

    $chunks = $collection->sliding(3, step: 2);

    $chunks->toArray();

    // [[1, 2, 3], [3, 4, 5]]


<a name="method-sole"></a>
#### `sole()`

Метод `sole` повертає перший елемент у колекції, який проходить заданий тест на істинність, але тільки якщо тест на істинність відповідає рівно одному елементу:

    collect([1, 2, 3, 4])->sole(function (int $value, int $key) {
        return $value === 2;
    });

    // 2

Ви також можете передати пару ключ/значення в метод `sole`, який поверне перший елемент колекції, що відповідає даній парі, але тільки в тому випадку, якщо збігається рівно один елемент:

    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Chair', 'price' => 100],
    ]);

    $collection->sole('product', 'Chair');

    // ['product' => 'Chair', 'price' => 100]

Як альтернативу ви також можете викликати метод `sole` без аргументу, щоб отримати перший елемент у колекції, якщо в ній тільки один елемент:

    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
    ]);

    $collection->sole();

    // ['product' => 'Desk', 'price' => 200]

Якщо в колекції немає елементів, які мають бути повернуті методом `sole`, буде кинуто виняток `\Illuminate\Collections\ItemNotFoundException`. Якщо є більше одного елемента, який має бути повернутий, то буде кинуто виняток `\Illuminate\Collections\MultipleItemsFoundException`.

<a name="method-some"></a>
#### `some()`

Псевдонім для методу [`contains`](#method-contains).

<a name="method-sort"></a>
#### `sort()`

Метод `sort` сортує колекцію. У відсортованій колекції зберігаються вихідні ключі масиву, тому в наступному прикладі ми будемо використовувати метод [`values`](#method-values) для скидання ключів для послідовної нумерації індексів:

    $collection = collect([5, 3, 1, 2, 4]);

    $sorted = $collection->sort();

    $sorted->values()->all();

    // [1, 2, 3, 4, 5]

Якщо ваші потреби в сортуванні складніші, ви можете передати замикання методу `sort` з вашим власним алгоритмом. Зверніться до документації PHP щодо [`uasort`](https://www.php.net/manual/ru/function.uasort.php#refsect1-function.uasort-parameters), який використовується всередині методу `sort`.

> [!NOTE]  
> Якщо вам потрібно відсортувати колекцію вкладених масивів або об'єктів, то див. методи [`sortBy`](#method-sortby) і [`sortByDesc`](#method-sortbydesc).

<a name="method-sortby"></a>
#### `sortBy()`

Метод `sortBy` сортує колекцію за вказаним ключем. У відсортованій колекції зберігаються вихідні ключі масиву, тому в наступному прикладі ми будемо використовувати метод [`values`](#method-values) для скидання ключів для послідовної нумерації індексів:

    $collection = collect([
        ['name' => 'Desk', 'price' => 200],
        ['name' => 'Chair', 'price' => 100],
        ['name' => 'Bookcase', 'price' => 150],
    ]);

    $sorted = $collection->sortBy('price');

    $sorted->values()->all();

    /*
        [
            ['name' => 'Chair', 'price' => 100],
            ['name' => 'Bookcase', 'price' => 150],
            ['name' => 'Desk', 'price' => 200],
        ]
    */

Метод `sortBy` приймає [прапори типу сортування](https://www.php.net/manual/ru/function.sort.php) як другий аргумент:

    $collection = collect([
        ['title' => 'Item 1'],
        ['title' => 'Item 12'],
        ['title' => 'Item 3'],
    ]);

    $sorted = $collection->sortBy('title', SORT_NATURAL);

    $sorted->values()->all();

    /*
        [
            ['title' => 'Item 1'],
            ['title' => 'Item 3'],
            ['title' => 'Item 12'],
        ]
    */

Як альтернативу ви можете передати власне замикання, щоб визначити, як сортувати значення колекції:

    $collection = collect([
        ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
        ['name' => 'Chair', 'colors' => ['Black']],
        ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
    ]);

    $sorted = $collection->sortBy(function (array $product, int $key) {
        return count($product['colors']);
    });

    $sorted->values()->all();

    /*
        [
            ['name' => 'Chair', 'colors' => ['Black']],
            ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
            ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
        ]
    */

Якщо ви хочете відсортувати свою колекцію за кількома атрибутами, ви можете передати масив операцій сортування методу `sortBy`. Кожна операція сортування має бути масивом, що складається з атрибута, за яким ви хочете сортувати, і напрямку бажаного сортування:

    $collection = collect([
        ['name' => 'Taylor Otwell', 'age' => 34],
        ['name' => 'Abigail Otwell', 'age' => 30],
        ['name' => 'Taylor Otwell', 'age' => 36],
        ['name' => 'Abigail Otwell', 'age' => 32],
    ]);

    $sorted = $collection->sortBy([
        ['name', 'asc'],
        ['age', 'desc'],
    ]);

    $sorted->values()->all();

    /*
        [
            ['name' => 'Abigail Otwell', 'age' => 32],
            ['name' => 'Abigail Otwell', 'age' => 30],
            ['name' => 'Taylor Otwell', 'age' => 36],
            ['name' => 'Taylor Otwell', 'age' => 34],
        ]
    */

Під час сортування колекції за кількома атрибутами ви також можете вказати замикання, що визначають кожну операцію сортування:

    $collection = collect([
        ['name' => 'Taylor Otwell', 'age' => 34],
        ['name' => 'Abigail Otwell', 'age' => 30],
        ['name' => 'Taylor Otwell', 'age' => 36],
        ['name' => 'Abigail Otwell', 'age' => 32],
    ]);

    $sorted = $collection->sortBy([
        fn (array $a, array $b) => $a['name'] <=> $b['name'],
        fn (array $a, array $b) => $b['age'] <=> $a['age'],
    ]);

    $sorted->values()->all();

    /*
        [
            ['name' => 'Abigail Otwell', 'age' => 32],
            ['name' => 'Abigail Otwell', 'age' => 30],
            ['name' => 'Taylor Otwell', 'age' => 36],
            ['name' => 'Taylor Otwell', 'age' => 34],
        ]
    */

<a name="method-sortbydesc"></a>
#### `sortByDesc()`

Цей метод має ту саму сигнатуру, що й метод [`sortBy`](#method-sortby), але відсортує колекцію у зворотному порядку.

<a name="method-sortdesc"></a>
#### `sortDesc()`

Цей метод сортує колекцію в порядку, зворотному методу [`sort`](#method-sort):

    $collection = collect([5, 3, 1, 2, 4]);

    $sorted = $collection->sortDesc();

    $sorted->values()->all();

    // [5, 4, 3, 2, 1]

На відміну від `sort`, ви не можете передавати замикання в `sortDesc`. Замість цього ви повинні використовувати метод [`sort`](#method-sort) та інвертувати ваше порівняння.

<a name="method-sortkeys"></a>
#### `sortKeys()`

Метод `sortKeys` сортує колекцію за ключами базового асоціативного масиву:

    $collection = collect([
        'id' => 22345,
        'first' => 'John',
        'last' => 'Doe',
    ]);

    $sorted = $collection->sortKeys();

    $sorted->all();

    /*
        [
            'first' => 'John',
            'id' => 22345,
            'last' => 'Doe',
        ]
    */

<a name="method-sortkeysdesc"></a>
#### `sortKeysDesc()`

Цей метод має ту саму сигнатуру, що й метод [`sortKeys`](#method-sortkeys), але відсортує колекцію у зворотному порядку.

<a name="method-sortkeysusing"></a>
#### `sortKeysUsing()`

Метод `sortKeysUsing` сортує колекцію за ключами базового асоціативного масиву за допомогою зворотного виклику:

    $collection = collect([
        'ID' => 22345,
        'first' => 'John',
        'last' => 'Doe',
    ]);

    $sorted = $collection->sortKeysUsing('strnatcasecmp');

    $sorted->all();

    /*
        [
            'first' => 'John',
            'ID' => 22345,
            'last' => 'Doe',
        ]
    */

Зворотний виклик повинен бути функцією порівняння, яка повертає ціле число, менше, рівне або більше нуля. Для отримання додаткової інформації зверніться до документації по PHP [`uksort`](https://www.php.net/manual/ru/function.uksort.php#refsect1-function.uksort-parameters), яка являє собою функцію PHP, що використовується всередині методу `sortKeysUsing`.

<a name="method-splice"></a>
#### `splice()`

Метод `splice` видаляє і повертає фрагмент елементів, починаючи із зазначеного індексу:

    $collection = collect([1, 2, 3, 4, 5]);

    $chunk = $collection->splice(2);

    $chunk->all();

    // [3, 4, 5]

    $collection->all();

    // [1, 2]

Ви можете передати другий аргумент, щоб обмежити розмір результуючої колекції:

    $collection = collect([1, 2, 3, 4, 5]);

    $chunk = $collection->splice(2, 1);

    $chunk->all();

    // [3]

    $collection->all();

    // [1, 2, 4, 5]

Крім того, ви можете передати третій аргумент, що містить нові елементи, щоб замінити елементи, видалені з колекції:

    $collection = collect([1, 2, 3, 4, 5]);

    $chunk = $collection->splice(2, 1, [10, 11]);

    $chunk->all();

    // [3]

    $collection->all();

    // [1, 2, 10, 11, 4, 5]

<a name="method-split"></a>
#### `split()`

Метод `split` розбиває колекцію на вказану кількість груп:

    $collection = collect([1, 2, 3, 4, 5]);

    $groups = $collection->split(3);

    $groups->all();

    // [[1, 2], [3, 4], [5]]

<a name="method-splitin"></a>
#### `splitIn()`

Метод `splitIn` розбиває колекцію на вказану кількість груп, повністю заповнюючи нетермінальні групи перед тим, як виділити залишок останній групі:

    $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

    $groups = $collection->splitIn(3);

    $groups->all();

    // [[1, 2, 3, 4], [5, 6, 7, 8], [9, 10]]

<a name="method-sum"></a>
#### `sum()`

Метод `sum` повертає суму всіх елементів у колекції:

    collect([1, 2, 3, 4, 5])->sum();

    // 15

Якщо колекція містить вкладені масиви або об'єкти, ви маєте передати ключ, який буде використовуватися для визначення підсумовування значень:

    $collection = collect([
        ['name' => 'JavaScript: The Good Parts', 'pages' => 176],
        ['name' => 'JavaScript: The Definitive Guide', 'pages' => 1096],
    ]);

    $collection->sum('pages');

    // 1272

Крім того, ви можете передати власне замикання, щоб визначити, які значення колекції підсумовувати:

    $collection = collect([
        ['name' => 'Chair', 'colors' => ['Black']],
        ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
        ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
    ]);

    $collection->sum(function (array $product) {
        return count($product['colors']);
    });

    // 6

<a name="method-take"></a>
#### `take()`

Метод `take` повертає нову колекцію із зазначеною кількістю елементів:

    $collection = collect([0, 1, 2, 3, 4, 5]);

    $chunk = $collection->take(3);

    $chunk->all();

    // [0, 1, 2]

Ви також можете передати від'ємне ціле число, щоб отримати вказану кількість елементів з кінця колекції:

    $collection = collect([0, 1, 2, 3, 4, 5]);

    $chunk = $collection->take(-2);

    $chunk->all();

    // [4, 5]

<a name="method-takeuntil"></a>
#### `takeUntil()`

Метод `takeUntil` повертає елементи колекції, поки вказане замикання не поверне `true`:

    $collection = collect([1, 2, 3, 4]);

    $subset = $collection->takeUntil(function (int $item) {
        return $item >= 3;
    });

    $subset->all();

    // [1, 2]

Ви також можете передати просте значення методу `takeUntil`, щоб отримувати елементи, поки не буде знайдено вказане значення:

    $collection = collect([1, 2, 3, 4]);

    $subset = $collection->takeUntil(3);

    $subset->all();

    // [1, 2]

> [!WARNING]
> Якщо вказане значення не знайдено або замикання ніколи не повертає `true`, то метод `takeUntil` поверне всі елементи колекції.

<a name="method-takewhile"></a>
#### `takeWhile()`

Метод `takeWhile` повертає елементи колекції доти, доки вказане замикання не поверне `false`:

    $collection = collect([1, 2, 3, 4]);

    $subset = $collection->takeWhile(function (int $item) {
        return $item < 3;
    });

    $subset->all();

    // [1, 2]

> [!WARNING]
> Якщо замикання ніколи не повертає `false`, метод `takeWhile` поверне всі елементи колекції.

<a name="method-tap"></a>
#### `tap()`

Метод `tap` передає колекцію зазначеному замиканню, даючи змогу вам «перехопити» колекцію в певний момент і зробити щось з елементами, не зачіпаючи саму колекцію. Потім колекція повертається методом `tap`:

    collect([2, 4, 3, 1, 5])
        ->sort()
        ->tap(function (Collection $collection) {
            Log::debug('Values after sorting', $collection->values()->all());
        })
        ->shift();

    // 1

<a name="method-times"></a>
#### `times()`

Статичний метод `times` створює нову колекцію, викликаючи передане замикання вказану кількість разів:

    $collection = Collection::times(10, function (int $number) {
        return $number * 9;
    });

    $collection->all();

    // [9, 18, 27, 36, 45, 54, 63, 72, 81, 90]

<a name="method-toarray"></a>
#### `toArray()`

Метод `toArray` перетворює колекцію на простий масив PHP. Якщо значеннями колекції є моделі [Eloquent](/docs/{{version}}{{version}}/eloquent), то моделі також будуть перетворені в масиви:

    $collection = collect(['name' => 'Desk', 'price' => 200]);

    $collection->toArray();

    /*
        [
            ['name' => 'Desk', 'price' => 200],
        ]
    */

> [!WARNING]  
> Метод `toArray` також перетворює всі вкладені об'єкти колекції, які є екземпляром `Arrayable`, на масив. Якщо ви хочете отримати необроблений масив, що лежить в основі колекції, використовуйте замість цього метод [`all`](#method-all).

<a name="method-tojson"></a>
#### `toJson()`

Метод `toJson` перетворює колекцію в серіалізований рядок JSON:

    $collection = collect(['name' => 'Desk', 'price' => 200]);

    $collection->toJson();

    // '{"name":"Desk", "price":200}'

<a name="method-transform"></a>
#### `transform()`

Метод `transform` виконує ітерацію колекції і викликає вказане замикання для кожного елемента в колекції. Елементи в колекції будуть замінені значеннями, що повертаються замиканням:

    $collection = collect([1, 2, 3, 4, 5]);

    $collection->transform(function (int $item, int $key) {
        return $item * 2;
    });

    $collection->all();

    // [2, 4, 6, 8, 10]

> [!WARNING]
> На відміну від більшості інших методів колекції, `transform` модифікує колекцію. Якщо ви хочете замість цього створити нову колекцію, використовуйте метод [`map`](#method-map).

<a name="method-undot"></a>
#### `undot()`

Метод `undot` розширює одновимірну колекцію, що використовує «точкову» нотацію, у багатовимірну колекцію:

    $person = collect([
        'name.first_name' => 'Marie',
        'name.last_name' => 'Valentine',
        'address.line_1' => '2992 Eagle Drive',
        'address.line_2' => '',
        'address.suburb' => 'Detroit',
        'address.state' => 'MI',
        'address.postcode' => '48219'
    ]);

    $person = $person->undot();

    $person->toArray();

    /*
        [
            "name" => [
                "first_name" => "Marie",
                "last_name" => "Valentine",
            ],
            "address" => [
                "line_1" => "2992 Eagle Drive",
                "line_2" => "",
                "suburb" => "Detroit",
                "state" => "MI",
                "postcode" => "48219",
            ],
        ]
    */

<a name="method-union"></a><a name="method-union"></a>
#### `union()`

Метод `union` додає переданий масив у колекцію. Якщо переданий масив містить ключі, які вже перебувають у вихідній колекції, кращими будуть значення вихідної колекції:

    $collection = collect([1 => ['a'], 2 => ['b']]);

    $union = $collection->union([3 => ['c'], 1 => ['d']]);

    $union->all();

    // [1 => ['a'], 2 => ['b'], 3 => ['c']]

<a name="method-unique"></a>
#### `unique()`

Метод `unique` повертає всі унікальні елементи колекції. Повернута колекція зберігає вихідні ключі масиву, тому в наступному прикладі ми будемо використовувати метод [`values`](#method-values) для скидання ключів для послідовної нумерації індексів:

    $collection = collect([1, 1, 2, 2, 3, 4, 2]);

    $unique = $collection->unique();

    $unique->values()->all();

    // [1, 2, 3, 4]

Під час роботи з вкладеними масивами або об'єктами ви можете вказати ключ, який використовується для визначення унікальності:

    $collection = collect([
        ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
        ['name' => 'iPhone 5', 'brand' => 'Apple', 'type' => 'phone'],
        ['name' => 'Apple Watch', 'brand' => 'Apple', 'type' => 'watch'],
        ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
        ['name' => 'Galaxy Gear', 'brand' => 'Samsung', 'type' => 'watch'],
    ]);

    $unique = $collection->unique('brand');

    $unique->values()->all();

    /*
        [
            ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
            ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
        ]
    */

Нарешті, ви також можете передати власне замикання методу `unique`, щоб вказати, яке значення має визначати унікальність елемента:

    $unique = $collection->unique(function (array $item) {
        return $item['brand'].$item['type'];
    });

    $unique->values()->all();

    /*
        [
            ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
            ['name' => 'Apple Watch', 'brand' => 'Apple', 'type' => 'watch'],
            ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
            ['name' => 'Galaxy Gear', 'brand' => 'Samsung', 'type' => 'watch'],
        ]
    */

Метод `unique` використовує «гнучке» порівняння при перевірці значень елементів, тобто рядок з цілим значенням буде вважатися рівним цілому числу того ж значення. Використовуйте метод [`uniqueStrict`](#method-uniquestrict) для фільтрації з використанням «жорсткого» порівняння.

> [!NOTE]  
> Поведінка цього методу змінюється при використанні [колекцій Eloquent](/docs/{{version}}/eloquent-collections#method-unique).

<a name="method-uniquestrict"></a>
#### `uniqueStrict()`

Цей метод має ту саму сигнатуру, що й метод [`unique`](#method-unique); однак, усі значення порівнюються з використанням «жорсткого» порівняння.

<a name="method-unless"></a>
#### `unless()`

Метод `unless` виконає вказане замикання, якщо перший аргумент, переданий методу, не матиме значення `true`:

    $collection = collect([1, 2, 3]);

    $collection->unless(true, function (Collection $collection) {
        return $collection->push(4);
    });

    $collection->unless(false, function (Collection $collection) {
        return $collection->push(5);
    });

    $collection->all();

    // [1, 2, 3, 5]

Друге замикання може бути передано методу `unless`. Друге замикання буде виконано, коли перший аргумент, переданий методу `unless` матиме значення `true`:

    $collection = collect([1, 2, 3]);

    $collection->unless(true, function (Collection $collection) {
        return $collection->push(4);
    }, function (Collection $collection) {
        return $collection->push(5);
    });

    $collection->all();

    // [1, 2, 3, 5]

Протилежним методу `unless` є метод [`when`](#method-when).

<a name="method-unlessempty"></a>
#### `unlessEmpty()`

Псевдонім для методу [`whenNotEmpty`](#method-whennotempty).

<a name="method-unlessnotempty"></a>
#### `unlessNotEmpty()`

Псевдонім для методу [`whenEmpty`](#method-whenempty).

<a name="method-unwrap"></a>
#### `unwrap()`

Статичний метод `unwrap` повертає базові елементи колекції із зазначеного значення, коли це застосовно:

    Collection::unwrap(collect('John Doe'));

    // ['John Doe']

    Collection::unwrap(['John Doe']);

    // ['John Doe']

    Collection::unwrap('John Doe');

    // 'John Doe'

<a name="method-value"></a>
#### `value()`

Метод `value` витягує задане значення з першого елемента колекції:

    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Speaker', 'price' => 400],
    ]);

    $value = $collection->value('price');

    // 200

<a name="method-values"></a>
#### `values()`

Метод `values` повертає нову колекцію з ключами, скинутими на послідовні цілі числа:

    $collection = collect([
        10 => ['product' => 'Desk', 'price' => 200],
        11 => ['product' => 'Desk', 'price' => 200],
    ]);

    $values = $collection->values();

    $values->all();

    /*
        [
            0 => ['product' => 'Desk', 'price' => 200],
            1 => ['product' => 'Desk', 'price' => 200],
        ]
    */

<a name="method-when"></a>
#### `when()`

Метод `when` виконає вказане замикання, коли перший аргумент, переданий методу, оцінюється як `true`. Примірник колекції та перший аргумент, переданий методу `when`, будуть надані в замикання:

    $collection = collect([1, 2, 3]);

    $collection->when(true, function (Collection $collection, int $value) {
        return $collection->push(4);
    });

    $collection->when(false, function (Collection $collection, int $value) {
        return $collection->push(5);
    });

    $collection->all();

    // [1, 2, 3, 4]

Друге замикання може бути передано методу `when`. Друге замикання буде виконано, коли перший аргумент, переданий методу `when` матиме значення `false`:

    $collection = collect([1, 2, 3]);

    $collection->when(false, function (Collection $collection, int $value) {
        return $collection->push(4);
    }, function (Collection $collection) {
        return $collection->push(5);
    });

    $collection->all();

    // [1, 2, 3, 5]

Протилежним методу `when` є метод [`unless`](#method-unless).

<a name="method-whenempty"></a>
#### `whenEmpty()`

Метод `whenEmpty` виконає вказане замикання, коли колекція порожня:

    $collection = collect(['Michael', 'Tom']);

    $collection->whenEmpty(function (Collection $collection) {
        return $collection->push('Adam');
    });

    $collection->all();

    // ['Michael', 'Tom']


    $collection = collect();

    $collection->whenEmpty(function (Collection $collection) {
        return $collection->push('Adam');
    });

    $collection->all();

    // ['Adam']

Друге замикання може бути передано методу `whenEmpty`, яке буде виконуватися, якщо колекція не порожня:

    $collection = collect(['Michael', 'Tom']);

    $collection->whenEmpty(function (Collection $collection) {
        return $collection->push('Adam');
    }, function (Collection $collection) {
        return $collection->push('Taylor');
    });

    $collection->all();

    // ['Michael', 'Tom', 'Taylor']

Протилежним методу `whenEmpty` є метод [`whenNotEmpty`](#method-whennotempty).

<a name="method-whennotempty"></a>
#### `whenNotEmpty()`

Метод `whenNotEmpty` виконає вказане замикання, якщо колекція не порожня:

    $collection = collect(['michael', 'tom']);

    $collection->whenNotEmpty(function (Collection $collection) {
        return $collection->push('adam');
    });

    $collection->all();

    // ['michael', 'tom', 'adam']


    $collection = collect();

    $collection->whenNotEmpty(function (Collection $collection) {
        return $collection->push('adam');
    });

    $collection->all();

    // []

Друге замикання може бути передано методу `whenNotEmpty`, яке буде виконуватися, якщо колекція порожня:

    $collection = collect();

    $collection->whenNotEmpty(function (Collection $collection) {
        return $collection->push('adam');
    }, function (Collection $collection) {
        return $collection->push('taylor');
    });

    $collection->all();

    // ['taylor']

Протилежним методу `whenNotEmpty` є метод [`whenEmpty`](#method-whenempty).

<a name="method-where"></a>
#### `where()`

Метод `where` фільтрує колекцію за вказаною парою ключ / значення:

    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Chair', 'price' => 100],
        ['product' => 'Bookcase', 'price' => 150],
        ['product' => 'Door', 'price' => 100],
    ]);

    $filtered = $collection->where('price', 100);

    $filtered->all();

    /*
        [
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Door', 'price' => 100],
        ]
    */

Метод `where` використовує «гнучке» порівняння при перевірці значень елементів, що означає, що рядок із цілим значенням вважатиметься таким, що дорівнює цілому числу того самого значення. Використовуйте метод [`whereStrict`](#method-wherestrict) для фільтрації з використанням «жорсткого» порівняння.

За бажання ви можете передати оператор порівняння як другий параметр. Підтримувані оператори: '===', '!==', '!=', '==', '=', '=', '<>', '>', '<', '>=', і '<=':

    $collection = collect([
        ['name' => 'Jim', 'deleted_at' => '2019-01-01 00:00:00'],
        ['name' => 'Sally', 'deleted_at' => '2019-01-02 00:00:00'],
        ['name' => 'Sue', 'deleted_at' => null],
    ]);

    $filtered = $collection->where('deleted_at', '!=', null);

    $filtered->all();

    /*
        [
            ['name' => 'Jim', 'deleted_at' => '2019-01-01 00:00:00'],
            ['name' => 'Sally', 'deleted_at' => '2019-01-02 00:00:00'],
        ]
    */

<a name="method-wherestrict"></a>
#### `whereStrict()`

Цей метод має ту саму сигнатуру, що й метод [`where`](#method-where); однак, усі значення порівнюються з використанням «жорсткого» порівняння.

<a name="method-wherebetween"></a>
#### `whereBetween()`

Метод `whereBetween` фільтрує колекцію, визначаючи, чи знаходиться передане значення елемента в зазначеному діапазоні:

    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Chair', 'price' => 80],
        ['product' => 'Bookcase', 'price' => 150],
        ['product' => 'Pencil', 'price' => 30],
        ['product' => 'Door', 'price' => 100],
    ]);

    $filtered = $collection->whereBetween('price', [100, 200]);

    $filtered->all();

    /*
        [
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Door', 'price' => 100],
        ]
    */

<a name="method-wherein"></a>
#### `whereIn()`

Метод `whereIn` видаляє елементи з колекції, у яких значення відсутні в зазначеному масиві:

    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Chair', 'price' => 100],
        ['product' => 'Bookcase', 'price' => 150],
        ['product' => 'Door', 'price' => 100],
    ]);

    $filtered = $collection->whereIn('price', [150, 200]);

    $filtered->all();

    /*
        [
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Bookcase', 'price' => 150],
        ]
    */

Метод `whereIn` використовує «гнучке» порівняння при перевірці значень елементів, що означає, що рядок із цілим значенням вважатиметься таким, що дорівнює цілому числу того самого значення. Використовуйте метод [`whereInStrict`](#method-whereinstrict) для фільтрації з використанням «жорсткого» порівняння.

<a name="method-whereinstrict"></a>
#### `whereInStrict()`

Цей метод має ту саму сигнатуру, що й метод [`whereIn`](#method-wherein); однак, усі значення порівнюються з використанням «жорсткого» порівняння.

<a name="method-whereinstanceof"></a>
#### `whereInstanceOf()`

Метод `whereInstanceOf` фільтрує колекцію за вказаним типом класу:

    use App\Models\User;
    use App\Models\Post;

    $collection = collect([
        new User,
        new User,
        new Post,
    ]);

    $filtered = $collection->whereInstanceOf(User::class);

    $filtered->all();

    // [App\Models\User, App\Models\User]

<a name="method-wherenotbetween"></a>
#### `whereNotBetween()`

Метод `whereNotBetween` фільтрує колекцію, визначаючи, чи знаходиться передане значення елемента за межами зазначеного діапазону:

    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Chair', 'price' => 80],
        ['product' => 'Bookcase', 'price' => 150],
        ['product' => 'Pencil', 'price' => 30],
        ['product' => 'Door', 'price' => 100],
    ]);

    $filtered = $collection->whereNotBetween('price', [100, 200]);

    $filtered->all();

    /*
        [
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Pencil', 'price' => 30],
        ]
    */

<a name="method-wherenotin"></a>
#### `whereNotIn()`

Метод `whereNotIn` видаляє елементи з колекції, у яких значення присутні в зазначеному масиві:

    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Chair', 'price' => 100],
        ['product' => 'Bookcase', 'price' => 150],
        ['product' => 'Door', 'price' => 100],
    ]);

    $filtered = $collection->whereNotIn('price', [150, 200]);

    $filtered->all();

    /*
        [
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Door', 'price' => 100],
        ]
    */

Метод `whereNotIn` використовує «гнучке» порівняння при перевірці значень елементів, що означає, що рядок з цілим значенням буде вважатися рівним цілому числу того ж значення. Використовуйте метод [`whereNotInStrict`](#method-wherenotinstrict) для фільтрації з використанням «жорсткого» порівняння.

<a name="method-wherenotinstrict"></a>
#### `whereNotInStrict()`

Цей метод має ту саму сигнатуру, що й метод [`whereNotIn`](#method-wherenotin); однак, усі значення порівнюються з використанням «жорсткого» порівняння.

<a name="method-wherenotnull"></a>
#### `whereNotNull()`

Метод `whereNotNull` повертає елементи з колекції, для яких значення зазначеного ключа не дорівнює `null`:

    $collection = collect([
        ['name' => 'Desk'],
        ['name' => null],
        ['name' => 'Bookcase'],
    ]);

    $filtered = $collection->whereNotNull('name');

    $filtered->all();

    /*
        [
            ['name' => 'Desk'],
            ['name' => 'Bookcase'],
        ]
    */

<a name="method-wherenull"></a>
#### `whereNull()`

Метод `whereNull` повертає елементи з колекції, для яких значення зазначеного ключа дорівнює `null`:

    $collection = collect([
        ['name' => 'Desk'],
        ['name' => null],
        ['name' => 'Bookcase'],
    ]);

    $filtered = $collection->whereNull('name');

    $filtered->all();

    /*
        [
            ['name' => null],
        ]
    */

<a name="method-wrap"></a>
#### `wrap()`

Статичний метод `wrap` обертає вказане значення в колекцію, якщо це застосовно:

    use Illuminate\Support\Collection;

    $collection = Collection::wrap('John Doe');

    $collection->all();

    // ['John Doe']

    $collection = Collection::wrap(['John Doe']);

    $collection->all();

    // ['John Doe']

    $collection = Collection::wrap(collect('John Doe'));

    $collection->all();

    // ['John Doe']

<a name="method-zip"></a>
#### `zip()`

Метод `zip` об'єднує значення переданого масиву зі значеннями вихідної колекції за їхнім відповідним індексом:

    $collection = collect(['Chair', 'Desk']);

    $zipped = $collection->zip([100, 200]);

    $zipped->all();

    // [['Chair', 100], ['Desk', 200]]

<a name="higher-order-messages"></a>
## Повідомлення вищого порядку

Колекції також забезпечують підтримку «повідомлень вищого порядку», які є скороченнями для виконання загальних дій з колекціями. Методи колекції, які надають повідомлення вищого порядку: [`average`](#method-average), [`avg`](#method-avg), [`contains`](#method-contains), [`each`](#method-each), [`every`](#method-every), [`filter`](#method-filter), [`first`](#метод-first), [`flatMap`](#метод-flatmap), [`groupBy`](#метод-groupby), [`keyBy`](#метод-keyby), [`map`](#метод-map), [`max`](#метод-max), [`min`](#метод-min), [`partition`](#метод-partition), [`reject`](#метод-reject), [`skipUntil`](#метод-skipuntil), [`skipWhile`](#метод-skipwhile), [`some`](#метод-some), [`sortBy`](#метод-sortby), [`sortByDesc`](#method-sortbydesc), [`sum`](#method-sum), [`takeUntil`](#method-takeuntil), [`takeWhile`](#method-takewhile), і [`unique`](#method-unique).

До кожного повідомлення вищого порядку можна отримати доступ як до динамічної властивості екземпляра колекції. Наприклад, давайте використовувати повідомлення вищого порядку `each`, викликаючи метод для кожного об'єкта колекції:

    use App\Models\User;

    $users = User::where('votes', '>', 500)->get();

    $users->each->markAsVip();

Так само ми можемо використовувати повідомлення вищого порядку `sum`, щоб зібрати загальну кількість «голосів» для колекції користувачів:

    $users = User::where('group', 'Development')->get();

    return $users->sum->votes;

<a name="lazy-collections"></a>
## Відкладені колекції

<a name="lazy-collection-introduction"></a>
### Вступ до відкладених колекцій

> [!WARNING]  
> Перш ніж дізнатися більше про відкладені колекції Laravel, витратьте деякий час на те, щоб ознайомитися з [генераторами PHP](https://www.php.net/manual/ru/language.generators.overview.php).

На додаток до потужного класу `Collection`, клас `LazyCollection` використовує [генератори](https://www.php.net/manual/ru/language.generators.overview.php) PHP, щоб ви могли працювати з дуже великими наборами даних за низького споживання пам'яті.

Наприклад, уявіть, що ваш додаток має обробляти файл журналу розміром у кілька гігабайт, використовуючи при цьому методи колекцій Laravel для аналізу журналів. Замість одночасного читання всього файлу в пам'ять можна використовувати відкладені колекції, щоб зберегти в пам'яті тільки невелику частину файлу в поточний момент:

    use App\Models\LogEntry;
    use Illuminate\Support\LazyCollection;

    LazyCollection::make(function () {
        $handle = fopen('log.txt', 'r');

        while (($line = fgets($handle)) !== false) {
            yield $line;
        }
    })->chunk(4)->map(function (array $lines) {
        return LogEntry::fromLines($lines);
    })->each(function (LogEntry $logEntry) {
        // Process the log entry...
    });

Або уявіть, що вам потрібно перебрати 10 000 моделей Eloquent. При використанні традиційних колекцій Laravel всі 10 000 моделей Eloquent повинні бути завантажені в пам'ять одночасно:

    use App\Models\User;

    $users = User::all()->filter(function (User $user) {
        return $user->id > 500;
    });

Однак, метод `cursor` побудовника запитів повертає екземпляр `LazyCollection`. Це дозволяє вам, як і раніше, виконувати тільки один запит до бази даних, але при цьому одночасно завантажувати в пам'ять тільки одну модель Eloquent. У цьому прикладі замикання методу `filter` не виконається до тих пір, поки ми насправді не переберемо кожного користувача індивідуально, що дозволяє значно скоротити використання пам'яті:

    use App\Models\User;

    $users = User::cursor()->filter(function (User $user) {
        return $user->id > 500;
    });

    foreach ($users as $user) {
        echo $user->id;
    }

<a name="creating-lazy-collections"></a>
### Створення відкладених колекцій

Щоб створити екземпляр відкладеної колекції, ви повинні передати функцію генератора PHP методу `make` колекції:

    use Illuminate\Support\LazyCollection;

    LazyCollection::make(function () {
        $handle = fopen('log.txt', 'r');

        while (($line = fgets($handle)) !== false) {
            yield $line;
        }
    });

<a name="the-enumerable-contract"></a>
### Контракт `Enumerable`

Майже всі методи, доступні в класі `Collection`, також доступні в класі `LazyCollection`. Обидва класи реалізують контракт `Illuminate\Support\Enumerable`, який визначає такі методи:

<div class="docs-column-list" markdown="1"> 

- [`all()`](#method-all)
- [`average()`](#method-average)
- [`avg()`](#method-avg)
- [`chunk()`](#method-chunk)
- [`chunkWhile()`](#method-chunkwhile)
- [`collapse()`](#method-collapse)
- [`combine()`](#method-combine)
- [`collect()`](#method-collect)
- [`concat()`](#method-concat)
- [`contains()`](#method-contains)
- [`containsStrict()`](#method-containsstrict)
- [`count()`](#method-count)
- [`countBy()`](#method-countby)
- [`crossJoin()`](#method-crossjoin)
- [`dd()`](#method-dd)
- [`diff()`](#method-diff)
- [`diffAssoc()`](#method-diffassoc)
- [`diffKeys()`](#method-diffkeys)
- [`dump()`](#method-dump)
- [`duplicates()`](#method-duplicates)
- [`duplicatesStrict()`](#method-duplicatesstrict)
- [`each()`](#method-each)
- [`eachSpread()`](#method-eachspread)
- [`every()`](#method-every)
- [`except()`](#method-except)
- [`filter()`](#method-filter)
- [`first()`](#method-first)
- [`firstOrFail()`](#method-first-or-fail)
- [`firstWhere()`](#method-firstwhere)
- [`flatMap()`](#method-flatmap)
- [`flatten()`](#method-flatten)
- [`flip()`](#method-flip)
- [`forget()`](#method-forget)
- [`forPage()`](#method-forpage)
- [`get()`](#method-get)
- [`groupBy()`](#method-groupby)
- [`has()`](#method-has)
- [`implode()`](#method-implode)
- [`intersect()`](#method-intersect)
- [`intersectAssoc()`](#method-intersectAssoc)
- [`intersectByKeys()`](#method-intersectbykeys)
- [`isEmpty()`](#method-isempty)
- [`isNotEmpty()`](#method-isnotempty)
- [`join()`](#method-join)
- [`keyBy()`](#method-keyby)
- [`keys()`](#method-keys)
- [`last()`](#method-last)
- [`macro()`](#method-macro)
- [`make()`](#method-make)
- [`map()`](#method-map)
- [`mapInto()`](#method-mapinto)
- [`mapSpread()`](#method-mapspread)
- [`mapToGroups()`](#method-maptogroups)
- [`mapWithKeys()`](#method-mapwithkeys)
- [`max()`](#method-max)
- [`median()`](#method-median)
- [`merge()`](#method-merge)
- [`mergeRecursive()`](#method-mergerecursive)
- [`min()`](#method-min)
- [`mode()`](#method-mode)
- [`nth()`](#method-nth)
- [`only()`](#method-only)
- [`pad()`](#method-pad)
- [`partition()`](#method-partition)
- [`pipe()`](#method-pipe)
- [`pipeInto()`](#method-pipeinto)
- [`pluck()`](#method-pluck)
- [`pop()`](#method-pop)
- [`prepend()`](#method-prepend)
- [`pull()`](#method-pull)
- [`push()`](#method-push)
- [`put()`](#method-put)
- [`random()`](#method-random)
- [`reduce()`](#method-reduce)
- [`reject()`](#method-reject)
- [`replace()`](#method-replace)
- [`replaceRecursive()`](#method-replacerecursive)
- [`reverse()`](#method-reverse)
- [`search()`](#method-search)
- [`shift()`](#method-shift)
- [`shuffle()`](#method-shuffle)
- [`skip()`](#method-skip)
- [`skipUntil()`](#method-skipuntil)
- [`skipWhile()`](#method-skipwhile)
- [`slice()`](#method-slice)
- [`sole()`](#method-sole)
- [`some()`](#method-some)
- [`sort()`](#method-sort)
- [`sortBy()`](#method-sortby)
- [`sortByDesc()`](#method-sortbydesc)
- [`sortDesc()`](#method-sortdesc)
- [`sortKeys()`](#method-sortkeys)
- [`sortKeysDesc()`](#method-sortkeysdesc)
- [`splice()`](#method-splice)
- [`split()`](#method-split)
- [`splitIn()`](#method-splitin)
- [`sum()`](#method-sum)
- [`take()`](#method-take)
- [`takeUntil()`](#method-takeuntil)
- [`takeWhile()`](#method-takewhile)
- [`tap()`](#method-tap)
- [`times()`](#method-times)
- [`toArray()`](#method-toarray)
- [`toJson()`](#method-tojson)
- [`transform()`](#method-transform)
- [`union()`](#method-union)
- [`unique()`](#method-unique)
- [`uniqueStrict()`](#method-uniquestrict)
- [`unless()`](#method-unless)
- [`unlessEmpty()`](#method-unlessempty)
- [`unlessNotEmpty()`](#method-unlessnotempty)
- [`unwrap()`](#method-unwrap)
- [`values()`](#method-values)
- [`when()`](#method-when)
- [`whenEmpty()`](#method-whenempty)
- [`whenNotEmpty()`](#method-whennotempty)
- [`where()`](#method-where)
- [`whereStrict()`](#method-wherestrict)
- [`whereBetween()`](#method-wherebetween)
- [`whereIn()`](#method-wherein)
- [`whereInStrict()`](#method-whereinstrict)
- [`whereInstanceOf()`](#method-whereinstanceof)
- [`whereNotBetween()`](#method-wherenotbetween)
- [`whereNotIn()`](#method-wherenotin)
- [`whereNotInStrict()`](#method-wherenotinstrict)
- [`whereNotNull()`](#method-wherenotnull)
- [`whereNull()`](#method-wherenull)
- [`wrap()`](#method-wrap)
- [`zip()`](#method-zip)

</div>

> [!WARNING]
> Методи, які змінюють колекцію (такі, як `shift`, `pop`, `prepend` тощо), **недоступні** у класі `LazyCollection`.

<a name="lazy-collection-methods"></a>
### Методи відкладених колекцій

На додаток до методів, визначених у контракті `Enumerable`, клас `LazyCollection` містить такі методи:

<a name="method-takeUntilTimeout"></a>
#### `takeUntilTimeout()`

Метод `takeUntilTimeout` повертає нову відкладену колекцію, яка буде перераховувати значення до зазначеного часу. Після закінчення цього часу колекція перестане перераховувати:

    $lazyCollection = LazyCollection::times(INF)
        ->takeUntilTimeout(now()->addMinute());

    $lazyCollection->each(function (int $number) {
        dump($number);

        sleep(1);
    });

    // 1
    // 2
    // ...
    // 58
    // 59

Щоб проілюструвати використання цього методу, уявіть застосунок, який надсилає рахунки з бази даних за допомогою курсору. Ви можете визначити [заплановану задачу](/docs/{{version}}/scheduling), яка запускається кожні 15 хвилин і обробляє рахунки максимум 14 хвилин:

    use App\Models\Invoice;
    use Illuminate\Support\Carbon;

    Invoice::pending()->cursor()
        ->takeUntilTimeout(
            Carbon::createFromTimestamp(LARAVEL_START)->add(14, 'minutes')
        )
        ->each(fn (Invoice $invoice) => $invoice->submit());

<a name="method-tapEach"></a>
#### `tapEach()`

Тоді як метод `each` викликає передане замикання для кожного елемента в колекції одразу ж, метод `tapEach` викликає передане замикання тільки тоді, коли елементи витягуються зі списку один за одним:

    // Поки нічого не виведено ...
    $lazyCollection = LazyCollection::times(INF)->tapEach(function (int $value) {
        dump($value);
    });

    // Три елементи виведено ...
    $array = $lazyCollection->take(3)->all();

    // 1
    // 2
    // 3

<a name="method-throttle"></a>
#### `throttle()`

Метод `throttle` регулюватиме ледачу колекцію таким чином, щоб кожне значення поверталося через вказану кількість секунд. Цей метод особливо корисний у ситуаціях, коли ви можете взаємодіяти із зовнішніми API, які обмежують швидкість вхідних запитів:

```php
use App\Models\User;

User::where('vip', true)
    ->cursor()
    ->throttle(seconds: 1)
    ->each(function (User $user) {
        // Call external API...
    });
```

<a name="method-remember"></a>
#### `remember()`

Метод `remember` повертає нову відкладену колекцію, яка запам'ятовує будь-які значення, що вже були перелічені, і не буде витягувати їх знову під час наступних перерахувань колекції:

    // Запит ще не виконано ...
    $users = User::cursor()->remember();

    // Запит виконано ...
    // Перші 5 користувачів із бази даних включено до результуючого набору ...
    $users->take(5)->all();

    // Перші 5 користувачів прийшли з кешу колекції ...
    // Решту з бази даних включено до результуючого набору ...
    $users->take(20)->all();
