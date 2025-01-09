# Події (Events)

- [Вступ](#introduction)
- [Генерація подій і слухачів](#generating-events-and-listeners)
- [Реєстрація подій і слухачів](#registering-events-and-listeners)
    - [Автопошук подій](#event-discovery)
    - [Ручна реєстрація подій](#manually-registering-events)
    - [Слухачі на основі замикання](#closure-listeners)
- [Визначення подій](#defining-events)
- [Визначення слухачів](#defining-listeners)
- [Слухачі подій у черзі](#queued-event-listeners)
    - [Взаємодія з чергою вручну](#manually-interacting-with-the-queue)
    - [Слухачі подій у черзі та транзакції бази даних](#queued-event-listeners-and-database-transactions)
    - [Обробка невиконаних завдань](#handling-failed-jobs)
- [Надсилання подій](#dispatching-events)
    - [Надсилання подій після транзакцій у базі даних](#dispatching-events-after-database-transactions)
- [Підписники подій](#event-subscribers)
    - [Написання підписників на події](#writing-event-subscribers)
    - [Реєстрація підписників на події](#registering-event-subscribers)
- [Тестування](#testing)
    - [Підміна певного набору подій](#faking-a-subset-of-events)
    - [Підміна подій в обмеженій області видимості](#scoped-event-fakes)

<a name="introduction"></a>
## Вступ

Події Laravel забезпечують просту реалізацію шаблону Спостерігач, даючи вам змогу підписуватися і відстежувати різні події, що відбуваються у вашому додатку. Класи подій зазвичай зберігаються в каталозі `app/Events`, а їхні слухачі - в `app/Listeners`. Не хвилюйтеся, якщо ви не бачите цих каталогів у своєму застосунку, оскільки їх буде створено для вас, коли ви генеруватимете події та слухачів за допомогою команд консолі Artisan.

Події слугують чудовим способом розділення різних аспектів вашого застосунку, оскільки одна подія може мати кілька слухачів, які не залежать один від одного. Наприклад, буває необхідно надсилати повідомлення Slack своєму користувачеві щоразу, коли замовлення буде відправлено. Замість того щоб пов'язувати код обробки замовлення з кодом сповіщення Slack, ви можете викликати подію `App\Events\OrderShipped`, яку слухач може отримати та використати для надсилання сповіщення Slack.

<a name="generating-events-and-listeners"></a>
## Генерація подій і слухачів

Щоб швидко генерувати події та слухачів, ви можете використовувати Artisan-команди `make:event` і `make:listener`:

```shell
php artisan make:event PodcastProcessed

php artisan make:listener SendPodcastNotification --event=PodcastProcessed
```

Для зручності ви також можете викликати команди Artisan `make:event` і `make:listener` без додаткових аргументів. Коли ви це зробите, Laravel автоматично запропонує вам ввести ім'я класу і, при створенні слухача, подію, яку він повинен прослуховувати:

```shell
php artisan make:event

php artisan make:listener
```

<a name="registering-events-and-listeners"></a>
## Реєстрація подій і слухачів

<a name="event-discovery"></a>
### Автопошук подій

За замовчуванням Laravel автоматично знайде і зареєструє ваших слухачів подій, просканувавши каталог `Listeners` вашого додатка. Коли Laravel знаходить будь-який метод класу слухача, який починається з `handle` або `__invoke`, Laravel реєструє ці методи як слухачі подій для події, тип якої вказано в сигнатурі методу:

    use App\Events\PodcastProcessed;

    class SendPodcastNotification
    {
        /**
         * Обробіть дану подію.
         */
        public function handle(PodcastProcessed $event): void
        {
            // ...
        }
    }

Ви можете прослуховувати кілька подій, використовуючи типи об'єднання PHP:

    /**
     * Handle the given event.
     */
    public function handle(PodcastProcessed|PodcastPublished $event): void
    {
        // ...
    }

Якщо ви плануєте зберігати свої слухачі в іншому каталозі або в декількох каталогах, ви можете доручити Laravel сканувати ці каталоги за допомогою методу `withEvents` у файлі `bootstrap/app.php` вашого додатка:

    ->withEvents(discover: [
        __DIR__.'/../app/Domain/Orders/Listeners',
    ])

Команда `event:list` може використовуватися для виведення списку всіх слухачів, зареєстрованих у вашому додатку:

```shell
php artisan event:list
```

<a name="event-discovery-in-production"></a>
#### Кешування подій

Щоб підвищити швидкість вашого додатка, вам слід кешувати маніфест усіх прослуховувачів вашого додатка за допомогою Artisan-команд `optimize` або `event:cache`. Зазвичай цю команду слід запускати як частину [процесу розгортання](/docs/{{version}}/deployment#optimization). Цей маніфест буде використовуватися платформою для прискорення процесу реєстрації подій. Команда `event:clear` може використовуватися для знищення кешу подій.

<a name="manually-registering-events"></a>
### Ручна реєстрація подій

Використовуючи фасад `Event`, ви можете вручну реєструвати події та відповідних їм слухачів у методі `boot` `AppServiceProvider` вашого застосунку:

    use App\Domain\Orders\Events\PodcastProcessed;
    use App\Domain\Orders\Listeners\SendPodcastNotification;
    use Illuminate\Support\Facades\Event;

    /**
     * Запуск будь-яких служб програми.
     */
    public function boot(): void
    {
        Event::listen(
            PodcastProcessed::class,
            SendPodcastNotification::class,
        );
    }

Команда `event:list` може використовуватися для виведення списку всіх слухачів, зареєстрованих у вашому додатку:

```shell
php artisan event:list
```

<a name="closure-listeners"></a>
### Слухачі на основі замикання

Зазвичай слухачі визначаються як класи; однак ви також можете вручну зареєструвати слухачів подій на основі замикань у методі `boot` вашого додатка `AppServiceProvider`:

    use App\Events\PodcastProcessed;
    use Illuminate\Support\Facades\Event;

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (PodcastProcessed $event) {
            // ...
        });
    }

<a name="queuable-anonymous-event-listeners"></a>
#### Анонімні слухачі подій у черзі

Під час реєстрації слухачів подій на основі замикання ви можете обернути замикання слухача у функцію `Illuminate\Events\queueable`, щоб вказати Laravel виконати слухача з використанням [черги](/docs/{{version}}}/queues):

    use App\Events\PodcastProcessed;
    use function Illuminate\Events\queueable;
    use Illuminate\Support\Facades\Event;

    /**
     * Запуск будь-яких служб програми.
     */
    public function boot(): void
    {
        Event::listen(queueable(function (PodcastProcessed $event) {
            // ...
        }));
    }

Як і у випадку із завданнями в чергах, ви можете використовувати методи `onConnection`, `onQueue` і `delay` для деталізації виконання слухача в черзі:

    Event::listen(queueable(function (PodcastProcessed $event) {
        // ...
    })->onConnection('redis')->onQueue('podcasts')->delay(now()->addSeconds(10)));

Якщо ви хочете обробляти збої анонімного слухача в черзі, то ви можете передати замикання методу `catch` при визначенні слухача `queueable`. Це замикання отримає екземпляр події та екземпляр `Throwable`, що викликав збій слухача:

    use App\Events\PodcastProcessed;
    use function Illuminate\Events\queueable;
    use Illuminate\Support\Facades\Event;
    use Throwable;

    Event::listen(queueable(function (PodcastProcessed $event) {
        // ...
    })->catch(function (PodcastProcessed $event, Throwable $e) {
        // Подія в черзі завершилася невдало ...
    }));

<a name="wildcard-event-listeners"></a>
#### Анонімні слухачі групи подій

Ви також можете зареєструвати слухачів, використовуючи символ `*` як підстановний параметр, що дасть вам змогу перехоплювати кілька подій на одному слухачі. Слухачі, зареєстровані за допомогою цього синтаксису, отримують ім'я події як перший аргумент і весь масив даних події як другий аргумент:

    Event::listen('event.*', function (string $eventName, array $data) {
        // ...
    });

<a name="defining-events"></a>
## Визначення подій

Клас подій - це, по суті, контейнер даних, який містить інформацію, що стосується події. Наприклад, припустимо, що подія `App\Events\OrderShipped` отримує об'єкт [Eloquent ORM](/docs/{{version}}}/eloquent):

    <?php

    namespace App\Events;

    use App\Models\Order;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Queue\SerializesModels;

    class OrderShipped
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        /**
         * Створити новий екземпляр події.
         */
        public function __construct(
            public Order $order,
        ) {}
    }

Як бачите, у цьому класі подій немає логіки. Це контейнер для екземпляра `App\Models\Order` замовлення, яке було виконано. Трейт `SerializesModels`, який використовується подією, буде витончено серіалізувати будь-які моделі Eloquent, якщо об'єкт події серіалізується з використанням функції `serialize` PHP, наприклад, під час використання [слухачів у черзі](#queued-event-listeners).

<a name="defining-listeners"></a>
## Визначення слухачів

Потім, давайте подивимося на слухача для нашого прикладу події. Слухачі подій отримують екземпляри подій у своєму методі `handle`. Команда Artisan `make:listener` при виклику з опцією `--event` автоматично імпортує відповідний клас події та вказує тип події в методі `handle`. У методі `handle` ви можете виконувати будь-які дії, необхідні для реагування на подію:

    <?php

    namespace App\Listeners;

    use App\Events\OrderShipped;

    class SendShipmentNotification
    {
        /**
         * Створити слухача подій.
         */
        public function __construct() {}

        /**
         * Обробити подію.
         */
        public function handle(OrderShipped $event): void
        {
             // Доступ до замовлення за допомогою $event->order ...
        }
    }

> [!NOTE]   
> У конструкторі ваших слухачів подій можуть бути оголошені будь-які необхідні типи залежностей. Усі слухачі подій дозволяються через [контейнер служб](/docs/{{version}}/container) Laravel, тому залежності будуть впроваджені автоматично.

<a name="stopping-the-propagation-of-an-event"></a>
#### Зупинення поширення події

За бажанням можна зупинити поширення події серед інших слухачів. Ви можете зробити це, повернувши `false` з методу `handle` вашого слухача.

<a name="queued-event-listeners"></a>
## Слухачі подій у черзі

Слухачі в черзі можуть бути корисними, якщо ваш слухач збирається виконувати повільне завдання, як-от надсилання електронної пошти або виконання HTTP-запиту. Перед використанням слухачів у черзі переконайтеся, що ви [сконфігурували чергу](/docs/{{version}}/queues) і запустили обробник черги на вашому сервері або в локальному середовищі розробки.

Щоб вказати, що слухач має бути поставлений у чергу, додайте інтерфейс `ShouldQueue` у клас слухача. Слухачі, згенеровані командами `event:generate` і `make:listener` Artisan, вже матимуть цей інтерфейс, що імпортується до поточного простору імен, тому ви можете використовувати його негайно:

    <?php

    namespace App\Listeners;

    use App\Events\OrderShipped;
    use Illuminate\Contracts\Queue\ShouldQueue;

    class SendShipmentNotification implements ShouldQueue
    {
        // ...
    }

Це все! Тепер, коли надсилається подія, що обробляється цим слухачем, слухач автоматично ставиться в чергу диспетчером подій з використанням [системи черг](/docs/{{version}}}/queues) Laravel. Якщо під час виконання слухача в черзі не виникає жодних винятків, завдання в черзі буде автоматично видалено після завершення обробки.

<a name="customizing-the-queue-connection-queue-name"></a>
#### Налаштування з'єднання черги, імені та часу затримки

Якщо ви хочете налаштувати з'єднання черги, ім'я черги або час затримки черги для слухача подій, то ви можете визначити властивості `$connection`, `$queue`, або `$delay` у своєму класі слухача:

    <?php

    namespace App\Listeners;

    use App\Events\OrderShipped;
    use Illuminate\Contracts\Queue\ShouldQueue;

    class SendShipmentNotification implements ShouldQueue
    {
        /**
         * Ім'я з'єднання, на яке має бути надіслано завдання.
         *
         * @var string|null
         */
        public $connection = 'sqs';

        /**
         * Ім'я черги, в яку має бути відправлено завдання.
         *
         * @var string|null
         */
        public $queue = 'listeners';

        /**
         * Час (у секундах) до обробки завдання.
         *
         * @var int
         */
        public $delay = 60;
    }

Якщо ви хочете визначити з'єднання черги слухача або ім'я черги слухача під час виконання, ви можете визначити методи `viaConnection`, `viaQueue` або `withDelay` слухача:

    /**
     * Отримати ім'я підключення черги слухача.
     */
    public function viaConnection(): string
    {
        return 'sqs';
    }

    /**
     * Отримати ім'я черги слухача.
     */
    public function viaQueue(): string
    {
        return 'listeners';
    }

    /**
     * Отримати кількість секунд до того, як завдання має бути виконано.
     */
    public function withDelay(OrderShipped $event): int
    {
        return $event->highPriority ? 0 : 60;
    }

<a name="conditionally-queueing-listeners"></a>
#### Умовне відправлення слухачів у чергу

Іноді потрібно визначити, чи слід ставити слухача в чергу на основі деяких даних, доступних тільки під час виконання. Для цього до слухача може бути доданий метод `shouldQueue`, щоб визначити, чи слід поставити слухача в чергу. Якщо метод `shouldQueue` повертає `false`, то слухач не буде поставлений у чергу:

    <?php

    namespace App\Listeners;

    use App\Events\OrderCreated;
    use Illuminate\Contracts\Queue\ShouldQueue;

    class RewardGiftCard implements ShouldQueue
    {
        /**
         * Нагородити покупця подарунковою карткою.
         */
        public function handle(OrderCreated $event): void
        {
            // ...
        }

        /**
         * Визначити, чи слід ставити слухача в чергу.
         */
        public function shouldQueue(OrderCreated $event): bool
        {
            return $event->order->subtotal >= 5000;
        }
    }

<a name="manually-interacting-with-the-queue"></a>
### Взаємодія з чергою вручну

Якщо вам потрібно вручну отримати доступ до методів `delete` і `release` базового завдання в черзі слухача, ви можете зробити це за допомогою трейта `Illuminate\Queue\InteractsWithQueue`. Цей трейт за замовчуванням імпортується в згенеровані слухачі та забезпечує доступ до цих методів:

    <?php

    namespace App\Listeners;

    use App\Events\OrderShipped;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Queue\InteractsWithQueue;

    class SendShipmentNotification implements ShouldQueue
    {
        use InteractsWithQueue;

        /**
         * Обробити подію.
         */
        public function handle(OrderShipped $event): void
        {
            if (true) {
                $this->release(30);
            }
        }
    }

<a name="queued-event-listeners-and-database-transactions"></a>
### Слухачі подій у черзі та транзакції бази даних

Коли слухачі в черзі відправляються в транзакціях бази даних, вони можуть бути оброблені чергою до того, як транзакція бази даних буде зафіксована. Коли це відбувається, будь-які оновлення, внесені вами в моделі або записи бази даних під час транзакції бази даних, можуть ще не бути відображені в базі даних. Крім того, будь-які моделі або записи бази даних, створені в рамках транзакції, можуть не існувати в базі даних. Якщо ваш слухач залежить від цих моделей, можуть виникнути непередбачувані помилки під час опрацювання завдання, яке відправляє поставлений у чергу слухач.

Якщо опція `after_commit` вашого з'єднання з чергою встановлена в значення `false`, то ви все одно можете вказати, що конкретний слухач у черзі має бути виконаний після того, як усі відкриті транзакції в базі даних будуть завершені, реалізувавши інтерфейс `ShouldQueueAfterCommit` у класі слухача:

    <?php

    namespace App\Listeners;

    use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
    use Illuminate\Queue\InteractsWithQueue;

    class SendShipmentNotification implements ShouldQueueAfterCommit
    {
        use InteractsWithQueue;
    }

> [!NOTE]   
> Щоб дізнатися більше про те, як обійти ці проблеми, перегляньте документацію, що стосується [завдань у черзі та транзакцій бази даних](/docs/{{version}}}/queues#jobs-and-database-transactions).

<a name="handling-failed-jobs"></a>
### Обробка невиконаних завдань

Іноді ваші слухачі подій у черзі можуть дати збій. Якщо слухач у черзі перевищує максимальну кількість спроб, визначену вашим обробником черги, для вашого слухача буде викликано метод `failed`. Метод `failed` отримує екземпляр події та `Throwable`, що викликав збій:

    <?php

    namespace App\Listeners;

    use App\Events\OrderShipped;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Queue\InteractsWithQueue;
    use Throwable;

    class SendShipmentNotification implements ShouldQueue
    {
        use InteractsWithQueue;

        /**
         * Обробити подію.
         */
        public function handle(OrderShipped $event): void
        {
            // ...
        }

        /**
         * Обробити провал завдання.
         */
        public function failed(OrderShipped $event, Throwable $exception): void
        {
            // ...
        }
    }

<a name="specifying-queued-listener-maximum-attempts"></a>
#### Зазначення максимальної кількості спроб слухача в черзі

Якщо один із ваших слухачів у черзі виявляє помилку, ви, ймовірно, не хочете, щоб він продовжував повторювати спроби нескінченно. Таким чином, Laravel пропонує різні способи вказати, скільки разів і як довго може виконуватися спроба прослуховування.

Ви можете визначити властивість `$tries` у своєму класі слухача, щоб вказати, скільки разів можна спробувати виконати слухач, перш ніж його вважатимуть невдалим:

    <?php

    namespace App\Listeners;

    use App\Events\OrderShipped;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Queue\InteractsWithQueue;

    class SendShipmentNotification implements ShouldQueue
    {
        use InteractsWithQueue;

        /**
         * Кількість спроб слухача в черзі.
         *
         * @var int
         */
        public $tries = 5;
    }

Як альтернативу визначенню того, скільки разів можна спробувати виконати прослуховування, перш ніж воно зазнає невдачі, ви можете визначити час, через який прослуховування більше не повинно виконуватися. Це дозволяє спробувати виконати прослуховування будь-яку кількість разів протягом заданого періоду часу. Щоб визначити час, через який більше не слід робити спроби прослуховування, додайте метод `retryUntil` у свій клас слухача. Цей метод повинен повертати екземпляр `DateTime`:

    use DateTime;

    /**
     * Визначити час, через який слухач має відключитися.
     *
     * @return \DateTime
     */
    public function retryUntil(): DateTime
    {
        return now()->addMinutes(5);
    }

<a name="dispatching-events"></a>
## Надсилання подій

Щоб відправити подію, ви можете викликати статичний метод `dispatch` події. Цей метод доступний у події за допомогою трейта `Illuminate\Foundation\Events\Dispatchable`. Будь-які аргументи, передані методу `dispatch`, будуть передані конструктору події:

    <?php

    namespace App\Http\Controllers;

    use App\Events\OrderShipped;
    use App\Http\Controllers\Controller;
    use App\Models\Order;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;

    class OrderShipmentController extends Controller
    {
        /**
         * Відправити замовлення.
         */
        public function store(Request $request): RedirectResponse
        {
            $order = Order::findOrFail($request->order_id);

            // Логіка відправлення замовлення ...

            OrderShipped::dispatch($order);

            return redirect('/orders');
        }
    }

Якщо ви хочете умовно надіслати подію, ви можете використовувати методи `dispatchIf` і `dispatchUnless`:

```php
OrderShipped::dispatchIf($condition, $order);

OrderShipped::dispatchUnless($condition, $order);
```

> [!NOTE]    
> Під час тестування може бути корисним стверджувати, що певні події було надіслано, не активуючи їхніх слухачів. У Laravel це легко зробити за допомогою [вбудованих засобів тестування](#testing).

<a name="dispatching-events-after-database-transactions"></a>
### Надсилання подій після транзакцій у базі даних

Іноді вам може знадобитися вказати Laravel відправляти подію тільки після завершення активної транзакції в базі даних. Для цього ви можете реалізувати інтерфейс `ShouldDispatchAfterCommit` у класі події.

Цей інтерфейс вказує Laravel не надсилати подію, поки поточну транзакцію в базі даних не буде завершено. Якщо транзакція завершиться з помилкою, подію буде відкинуто. Якщо в момент відправлення події немає активної транзакції в базі даних, подію буде відправлено негайно.

    <?php

    namespace App\Events;

    use App\Models\Order;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Queue\SerializesModels;

    class OrderShipped implements ShouldDispatchAfterCommit
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        /**
         * Create a new event instance.
         */
        public function __construct(
            public Order $order,
        ) {}
    }

<a name="event-subscribers"></a>
## Підписники подій

<a name="writing-event-subscribers"></a>
### Написання підписників на події

Передплатники подій - це класи, які можуть підписуватися на кілька подій, що дає змогу вам визначати кілька обробників подій в одному класі. Передплатники повинні визначити метод `subscribe`, якому буде передано екземпляр диспетчера подій. Ви можете викликати метод `listen` даного диспетчера для реєстрації слухачів подій:

    <?php

    namespace App\Listeners;

    use Illuminate\Auth\Events\Login;
    use Illuminate\Auth\Events\Logout;

    class UserEventSubscriber
    {
        /**
         * Обробити подію входу користувача в систему.
         */
        public function handleUserLogin(Login $event): void {}

        /**
         * Обробити подію виходу користувача із системи.
         */
        public function handleUserLogout(Logout $event): void {}

        /**
         * Зареєструвати слухачів для передплатника.
         *
         * @param  \Illuminate\Events\Dispatcher  $events
         * @return void
         */
        public function subscribe(Dispatcher $events): void
        {
            $events->listen(
                Login::class,
                [UserEventSubscriber::class, 'handleUserLogin']
            );

            $events->listen(
                Logout::class,
                [UserEventSubscriber::class, 'handleUserLogout']
            );
        }
    }

Якщо ваші методи слухачів подій визначені в самому передплатнику, вам може бути зручніше повертати масив подій та імен методів із методу передплатника `subscribe`. Laravel автоматично визначить ім'я класу передплатника під час реєстрації слухачів подій:

    <?php

    namespace App\Listeners;

    use Illuminate\Auth\Events\Login;
    use Illuminate\Auth\Events\Logout;
    use Illuminate\Events\Dispatcher;

    class UserEventSubscriber
    {
        /**
         * Обробити подію входу користувача в систему.
         */
        public function handleUserLogin(Login $event): void {}

        /**
         * Обробити подію виходу користувача із системи.
         */
        public function handleUserLogout(Logout $event): void {}

        /**
         * Реєстрація слухачів для абонента.
         */
        public function subscribe(Dispatcher $events): array
        {
            return [
                Login::class => 'handleUserLogin',
                Logout::class => 'handleUserLogout',
            ];
        }
    }

<a name="registering-event-subscribers"></a>
### Реєстрація підписників на події

Після написання передплатника Laravel автоматично зареєструє методи-обробники всередині передплатника, якщо вони відповідають [угодам про виявлення подій](#event-discovery) Laravel. В іншому випадку ви можете вручну зареєструвати свого передплатника, використовуючи метод `subscribe` фасаду `Event`. Зазвичай це слід робити в методі `boot` `AppServiceProvider` вашого додатка:

    <?php

    namespace App\Providers;

    use App\Listeners\UserEventSubscriber;
    use Illuminate\Support\Facades\Event;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Завантаження будь-яких сервісів додатка.
         */
        public function boot(): void
        {
            Event::subscribe(UserEventSubscriber::class);
        }
    }

<a name="testing"></a>
## Тестування

Під час тестування коду, який надсилає події, може знадобитися вказати Laravel не виконувати фактично слухачів подій, оскільки код слухачів можна тестувати безпосередньо й окремо від коду, який надсилає відповідну подію. Звичайно, для тестування самого слухача ви можете створити екземпляр слухача і викликати метод `handle` безпосередньо у вашому тесті.

Використовуючи метод `fake` фасаду `Event`, ви можете запобігти виконанню слухачів, виконати код, який потрібно протестувати, і потім стверджувати, які події були надіслані вашим додатком за допомогою методів `assertDispatched`, `assertNotDispatched` і `assertNothingDispatched`:

```php tab=Pest
<?php

use App\Events\OrderFailedToShip;
use App\Events\OrderShipped;
use Illuminate\Support\Facades\Event;

test('orders can be shipped', function () {
    Event::fake();

    // Виконайте процес доставки замовлення...

    // Затвердіть, що подію було відправлено...
    Event::assertDispatched(OrderShipped::class);

    // Ствердіть, що подію було надіслано двічі...
    Event::assertDispatched(OrderShipped::class, 2);

    // Ствердіть, що подію не було відправлено...
    Event::assertNotDispatched(OrderFailedToShip::class);

    // Ствердіть, що не було відправлено жодної події...
    Event::assertNothingDispatched();
});
```

```php tab=PHPUnit
<?php

namespace Tests\Feature;

use App\Events\OrderFailedToShip;
use App\Events\OrderShipped;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test order shipping.
     */
    public function test_orders_can_be_shipped(): void
    {
        Event::fake();

        // Виконайте процес доставки замовлення...

        // Затвердіть, що подію було відправлено...
        Event::assertDispatched(OrderShipped::class);

        // Ствердіть, що подію було надіслано двічі...
        Event::assertDispatched(OrderShipped::class, 2);

        // Ствердіть, що подію не було відправлено...
        Event::assertNotDispatched(OrderFailedToShip::class);

        // Ствердіть, що не було відправлено жодної події...
        Event::assertNothingDispatched();
    }
}
```

Ви можете передати замикання в методи `assertDispatched` або `assertNotDispatched`, щоб стверджувати, що було відправлено подію, яка проходить заданий «тест істинності». Якщо хоча б одна подія була відправлена і пройшла заданий тест істинності, то твердження буде успішним:

```php
Event::assertDispatched(function (OrderShipped $event) use ($order) {
    return $event->order->id === $order->id;
});
```

Якщо ви хочете просто затвердити, що слухач події слухає певну подію, ви можете використовувати метод `assertListening`:

```php
Event::assertListening(
    OrderShipped::class,
    SendShipmentNotification::class
);
```

> [!WARNING]
> Після виклику `Event::fake()`, слухачі подій не будуть виконані. Тому, якщо ваші тести використовують фабрики моделей, які залежать від подій, наприклад, створення UUID під час події `creating` моделі, ви повинні викликати `Event::fake()` **після** використання ваших фабрик.

<a name="faking-a-subset-of-events"></a>
### Підміна певного набору подій

Якщо ви хочете підмінити слухачів подій тільки для певного набору подій, ви можете передати їх у метод `fake` або `fakeFor`:

```php tab=Pest
test('orders can be processed', function () {
    Event::fake([
        OrderCreated::class,
    ]);

    $order = Order::factory()->create();

    Event::assertDispatched(OrderCreated::class);

    // Інші події відправляються як зазвичай...
    $order->update([...]);
});
```

```php tab=PHPUnit
/**
 * Test order process.
 */
public function test_orders_can_be_processed(): void
{
    Event::fake([
        OrderCreated::class,
    ]);

    $order = Order::factory()->create();

    Event::assertDispatched(OrderCreated::class);

    // Інші події відправляються як зазвичай...
    $order->update([...]);
}
```

Ви можете підмінити всі події, крім зазначених подій, використовуючи метод `except`:

```php
Event::fake()->except([
    OrderCreated::class,
]);
```

<a name="scoped-event-fakes"></a>
### Підміна подій в обмеженій області видимості

Якщо ви хочете підмінити слухачів подій тільки в певній частині вашого тесту, ви можете використовувати метод `fakeFor`:

```php tab=Pest
<?php

use App\Events\OrderCreated;
use App\Models\Order;
use Illuminate\Support\Facades\Event;

test('orders can be processed', function () {
    $order = Event::fakeFor(function () {
        $order = Order::factory()->create();

        Event::assertDispatched(OrderCreated::class);

        return $order;
    });

    // Події відправляються у звичайному режимі і спостерігачі запускаються ...
    $order->update([...]);
});
```

```php tab=PHPUnit
<?php

namespace Tests\Feature;

use App\Events\OrderCreated;
use App\Models\Order;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test order process.
     */
    public function test_orders_can_be_processed(): void
    {
        $order = Event::fakeFor(function () {
            $order = Order::factory()->create();

            Event::assertDispatched(OrderCreated::class);

            return $order;
        });

        // Події відправляються як зазвичай, і спостерігачі будуть виконані...
        $order->update([...]);
    }
}
```
