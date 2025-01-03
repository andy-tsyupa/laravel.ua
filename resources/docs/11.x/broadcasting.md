# Мовлення

- [Вступ](#introduction)
- [Встановлення на стороні сервера](#server-side-installation)
    - [Налаштування](#configuration)
    - [Reverb](#reverb)
    - [Pusher Channels](#pusher-channels)
    - [Ably](#ably)
- [Встановлення на стороні клієнта](#client-side-installation)
    - [Reverb](#client-reverb)
    - [Pusher канал](#client-pusher-channels)
    - [Ably](#client-ably)
- [Огляд концепції](#concept-overview)
    - [Приклад використання](#using-example-application)
- [Визначення транслюваних подій](#defining-broadcast-events)
    - [Ім'я транслюваної події](#broadcast-name)
    - [Дані трансляції](#broadcast-data)
    - [Черга трансляції](#broadcast-queue)
    - [Умови трансляції](#broadcast-conditions)
    - [Трансляція та транзакції бази даних](#broadcasting-and-database-transactions)
- [Авторизація каналів](#authorizing-channels)
    - [Прослуховування трансляцій подій](#listening-for-event-broadcasts)
    - [Визначення авторизації канала](#defining-authorization-callbacks)
    - [Визначення класу канала](#defining-channel-classes)
- [Трансляція подій](#broadcasting-events)
    - [Трансляція подій тільки іншим користувачам](#only-to-others)
    - [Налаштування з'єднання](#customizing-the-connection)
- [Прийом трансляцій](#receiving-broadcasts)
    - [Зупинка прослуховування подій](#listening-for-events)
    - [Покидання каналу](#leaving-a-channel)
    - [Простори імен](#namespaces)
- [Канали присутності](#presence-channels)
    - [Авторизація каналів присутності](#authorizing-presence-channels)
    - [Приєднання до каналів присутності](#joining-presence-channels)
    - [Трансляція на канали присутності](#broadcasting-to-presence-channels)
- [Трансляція моделей](#model-broadcasting)
    - [Model Broadcasting Conventions](#model-broadcasting-conventions)
    - [Listening for Model Broadcasts](#listening-for-model-broadcasts)
- [Клієнтські події](#client-events)
- [Повідомлення](#notifications)

# Трансляція (broadcast) подій

<a name="introduction"></a>
## Вступ

У багатьох сучасних веб-додатках веб-сокети використовуються для реалізації користувацьких інтерфейсів, які оновлюються в реальному часі. Коли деякі дані оновлюються на сервері, зазвичай надсилається повідомлення через з'єднання WebSocket для обробки клієнтом. Веб-сокети забезпечують більш ефективну альтернативу постійному опитуванню сервера вашого застосунку на предмет змін даних, які повинні бути відображені в вашому користувацькому інтерфейсі.

Наприклад, уявіть, що ваше додаток може експортувати дані користувача у файл CSV і надсилати цей файл йому електронною поштою. Проте створення цього CSV-файлу займає кілька хвилин, отже, ви можете створити і надіслати CSV-файл поштою, помістивши [завдання в чергу](/docs/{{version}}/queues). Коли файл CSV буде створено і надіслано користувачу, ми можемо використовувати **мовлення** для надсилання події `App\Events\UserDataExported`, яка буде отримана в JavaScript нашого додатку. Як тільки подія буде отримана, ми можемо відобразити повідомлення користувачу про те, що його файл CSV був надісланий йому електронною поштою без необхідності оновлення сторінки.

Щоб допомогти вам у створенні подібного роду функціоналу, Laravel спрощує «мовлення» серверних [подій](/docs/{{version}}/events) Laravel через з'єднання WebSocket. Трансляція ваших подій Laravel дозволяє вам використовувати одні й ті ж імена подій і дані між серверним додатком Laravel і клієнтським JavaScript-додатком.

Основні концепції широкомовлення прості: клієнти підключаються до іменованих каналів у зовнішньому інтерфейсі, тоді як ваше додаток Laravel транслює події на ці канали у внутрішньому інтерфейсі. Ці події можуть містити будь-які додаткові дані, які ви хочете зробити доступними для зовнішнього інтерфейсу.

<a name="supported-drivers"></a>
#### Підтримувані драйвери

За замовчуванням Laravel містить три серверних драйвера трансляції на вибір: [Laravel Reverb](https://reverb.laravel.com), [Pusher Channels](https://pusher.com/channels) та [Ably](https://ably.com)

> [!NOTE]
> Перед тим, як ближче ознайомитися з трансляцією подій, переконайтеся, що ви прочитали документацію Laravel про [події та слухачі](/docs/{{version}}/events).

<a name="server-side-installation"></a>
## Встановлення на стороні сервера

Щоб почати використовувати трансляцію подій Laravel, нам потрібно виконати деякі налаштування в додатку Laravel, а також встановити деякі пакети.

Трансляція подій виконується серверним драйвером трансляції, який транслює ваші події Laravel, отримані браузером клієнта через Laravel Echo (бібліотека JavaScript). Не хвилюйтеся – ми розглянемо кожну частину процесу встановлення крок за кроком.

<a name="configuration"></a>
### Налаштування

Вся конфігурація трансляцій подій вашого додатку зберігається у конфігураційному файлі `config/broadcasting.php`. Не хвилюйтеся, якщо цей каталог не існує у вашому додатку; він буде створений при запуску Artisan-команди `install:broadcasting`.

Laravel з коробки підтримує кілька драйверів трансляції: [Laravel Reverb](/docs/{{version}}/reverb), [Pusher Channels](https://pusher.com/channels), [Ably](https://ably.com), а також драйвер `log` для локальної розробки та налагодження. Крім того, підтримується драйвер `null`, який дозволяє повністю відключити трансляцію під час тестування. У конфігураційному файлі `config/broadcasting.php` міститься приклад конфігурації для кожного з цих драйверів.

<a name="installation"></a>
#### Встановлення

За замовчуванням трансляція не включена в нових додатках Laravel. Ви можете включити трансляцію за допомогою Artisan-команди `install:broadcasting`:

```shell
php artisan install:broadcasting
```

Команда `install:broadcasting` створить файл конфігурації `config/broadcasting.php`. Крім того, команда створить файл `routes/channels.php`, в якому ви можете зареєструвати маршрути авторизації трансляції та колбеків вашого додатка.

<a name="queue-configuration"></a>
#### Налаштування черги

Перш ніж транслювати будь-які події, вам слід спочатку налаштувати і запустити [обробник черги](/docs/{{version}}/queues). Вся трансляція подій виконується через завдання в черзі, тому транслювані події не впливають на час відгуку вашого додатка.

<a name="reverb"></a>
### Reverb

При запуску команди `install:broadcasting` вам буде запропоновано встановити [Laravel Reverb](/docs/{{version}}/reverb). Звичайно, ви також можете встановити Reverb вручну, використовуючи менеджер пакетів Composer.

```sh
composer require laravel/reverb
```

Після встановлення пакета ви можете запустити команду встановлення Reverb, щоб опублікувати конфігурацію, додати необхідні змінні середовища Reverb і включити трансляцію подій у вашому додатку:

```sh
php artisan reverb:install
```

Докладні інструкції з встановлення та використання Reverb можна знайти в [документації Reverb](/docs/{{version}}/reverb).

<a name="pusher-channels"></a>
### Pusher канал

Якщо ви плануєте транслювати свої події за допомогою [Pusher Channels](https://pusher.com/channels), то вам слід встановити PHP SDK Pusher Channels з допомогою менеджера пакетів Composer:

```shell
composer require pusher/pusher-php-server
```

Далі, ви повинні налаштувати свої облікові дані Pusher Channels у конфігураційному файлі `config/broadcasting.php`. Приклад конфігурації Pusher Channels вже міститься у цьому файлі, що дозволяє швидко вказати параметри `key`, `secret` і `app_id`. Зазвичай вам слід налаштувати облікові дані Pusher Channels у файлі `.env` вашого додатку:

```ini
PUSHER_APP_ID="your-pusher-app-id"
PUSHER_APP_KEY="your-pusher-key"
PUSHER_APP_SECRET="your-pusher-secret"
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME="https"
PUSHER_APP_CLUSTER="mt1"
```

Конфігурація `pusher` у файлі `config/broadcasting.php` також дозволяє вам вказувати додаткові параметри, які підтримуються Pusher, наприклад, `cluster`.

Після цього встановити для змінної середовища `BROADCAST_CONNECTION` значення `pusher` у файлі `.env` вашого додатку:

```ini
BROADCAST_CONNECTION=pusher
```

І, нарешті, ви готові встановити та налаштувати [Laravel Echo](#client-side-installation), який буде отримувати транслювані події на клієнтській стороні.

<a name="ably"></a>
### Ably

> [!NOTE]  
> Нижче наведено опис того, як використовувати Ably в режимі "сумісності з Pusher". Однак команда Ably рекомендує та підтримує ведучий і клієнт Echo, здатні використовувати унікальні можливості, що пропонуються Ably. Для отримання додаткової інформації про використання підтримуваних драйверів Ably зверніться до [документації Ably по Laravel мовленні](https://github.com/ably/laravel-broadcaster).

Якщо ви плануєте транслювати свої події за допомогою [Ably](https://ably.io), вам слід встановити PHP SDK Ably за допомогою менеджера пакетів Composer:

```shell
composer require ably/ably-php
```

Далі, ви повинні налаштувати свої облікові дані Ably у конфігураційному файлі `config/broadcasting.php`. Приклад конфігурації Ably вже міститься у цьому файлі, що дозволяє швидко вказати параметр `key`. Як правило, це значення має бути встановлено через [змінну середовища](/docs/{{version}}/configuration#environment-configuration) `ABLY_KEY`:

```ini
ABLY_KEY=your-ably-key
```

Після цього встановити для змінної середовища `BROADCAST_CONNECTION` значення `ably` у файлі .env вашого додатку:

```ini
BROADCAST_CONNECTION=ably
```

І, нарешті, ви готові встановити та налаштувати [Laravel Echo](#client-side-installation), який буде отримувати транслювані події на клієнтській стороні.

<a name="client-side-installation"></a>
## Встановлення на стороні клієнта

<a name="client-reverb"></a>
### Reverb

[Laravel Echo](https://github.com/laravel/echo) — це бібліотека JavaScript, яка дозволяє без зусиль підписуватися на канали та прослуховувати події, транслювані вашим серверним драйвером трансляції. Ви можете встановити Echo через менеджер пакетів NPM. У цьому прикладі ми також встановимо пакет `pusher-js`, оскільки Reverb використовує протокол Pusher для підписок, каналів та повідомлень WebSocket:

```shell
npm install --save-dev laravel-echo pusher-js
```

Після встановлення Echo ви готові створити новий екземпляр Echo у JavaScript вашого додатку. Чудове місце для цього — внизу файлу `resources/js/bootstrap.js`, який входить до складу фреймворку Laravel. За замовчуванням у цей файл вже включений приклад конфігурації Echo — вам просто потрібно розкоментувати його та оновити параметр конфігурації `broadcaster` на `reverb`:

```js
import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

Далі вам слід скомпілювати ресурси вашого додатку:

```shell
npm run build
```

> [!WARNING]  
> Для трансляції Laravel Echo `reverb` вимагає laravel-echo v1.16.0+.

<a name="client-pusher-channels"></a>
### Pusher Channels

[Laravel Echo](https://github.com/laravel/echo) — це JavaScript-бібліотека, яка спрощує підписку на канали та прослуховування подій, транслюваних вашим серверним драйвером трансляції. Echo також використовує пакет NPM `pusher-js` для реалізації протоколу Pusher для підписок, каналів і повідомлень WebSocket.

Команда Artisan `install:broadcasting` автоматично встановлює для вас пакети `laravel-echo` і `pusher-js`; однак ви також можете встановити ці пакети вручну через NPM:

```shell
npm install --save-dev laravel-echo pusher-js
```

Після встановлення Echo ви готові створити новий екземпляр Echo у JavaScript вашого додатку. Команда `install:broadcasting` створює файл конфігурації Echo за адресою `resources/js/echo.js`; однак конфігурація за замовчуванням у цьому файлі призначена для Laravel Reverb. Ви можете скопіювати конфігурацію нижче, щоб перенести вашу конфігурацію на Pusher:

```js
import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

Далі вам слід визначити відповідні значення для змінних середовища Pusher у файлі `.env` вашого додатку. Якщо ці змінні ще не існують у вашому файлі `.env`, вам слід додати їх:

```ini
PUSHER_APP_ID="your-pusher-app-id"
PUSHER_APP_KEY="your-pusher-key"
PUSHER_APP_SECRET="your-pusher-secret"
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME="https"
PUSHER_APP_CLUSTER="mt1"

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

Після того, як ви налаштували конфігурацію Echo відповідно до потреб вашого додатку, ви можете скомпілювати ресурси вашого додатку:

```shell
npm run build
```

> [!NOTE]
> Щоб дізнатися більше про компіляцію JavaScript-джерел вашого додатку, зверніться до документації [Vite](/docs/{{version}}/vite).

<a name="using-an-existing-client-instance"></a>
#### Використання існуючого екземпляра клієнта

Якщо у вас вже є попередньо налаштований екземпляр клієнта Pusher Channels, який ви хочете використовувати в Echo, ви можете передати його Echo за допомогою властивості конфігурації `client`:

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const options = {
    broadcaster: 'pusher',
    key: 'your-pusher-channels-key'
}

window.Echo = new Echo({
    ...options,
    client: new Pusher(options.key, options)
});
```

<a name="client-ably"></a>
### Ably

> [!NOTE]  
> Нижче наведено опис того, як використовувати Ably в режимі "сумісності з Pusher". Однак команда Ably рекомендує та підтримує ведучий і клієнт Echo, здатні використовувати унікальні можливості, що пропонуються Ably. Для отримання додаткової інформації про використання підтримуваних Ably драйверів зверніться до [документації Ably по Laravel мовлення](https://github.com/ably/laravel-broadcaster).

[Laravel Echo](https://github.com/laravel/echo) — це JavaScript-бібліотека, яка дозволяє без зусиль підписуватися на канали та прослуховувати події, транслювані вашим серверним драйвером трансляції. Echo також використовує пакет NPM `pusher-js` для реалізації протоколу Pusher для підписок, каналів і повідомлень WebSocket.

Команда Artisan `install:broadcasting` автоматично встановлює для вас пакети `laravel-echo` і `pusher-js`; однак ви також можете встановити ці пакети вручну через NPM:

```shell
npm install --save-dev laravel-echo pusher-js
```

**Перш ніж продовжити, ви повинні включити підтримку протоколу Pusher в налаштуваннях вашого додатку Ably. Ви можете включити цю функцію в розділі налаштувань «Protocol Adapter Settings» панелі вашого додатку Ably.**

Після установки Echo ви готові створити новий екземпляр Echo у JavaScript вашого додатку. Команда `install:broadcasting` створює файл конфігурації Echo за адресою `resources/js/echo.js`; однак конфігурація за замовчуванням у цьому файлі призначена для Laravel Reverb. Ви можете скопіювати конфігурацію нижче, щоб перенести її в Ably:

```js
import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_ABLY_PUBLIC_KEY,
    wsHost: 'realtime-pusher.ably.io',
    wsPort: 443,
    disableStats: true,
    encrypted: true,
});
```

Ви, мабуть, помітили, що наша конфігурація Echo для Ably посилається на змінну середовища `VITE_ABLY_PUBLIC_KEY`. Значення цієї змінної повинно бути вашим публічним ключем Ably. Ваш публічний ключ – це частина ключа Ably перед символом `:`.

Після того, як ви налаштували конфігурацію Echo відповідно до ваших потреб, ви можете скомпілювати джерела вашого додатку:

```shell
npm run dev
```

> [!NOTE]  
> Щоб дізнатися більше про компіляцію JavaScript-джерел вашого додатку, зверніться до документації [Vite](/docs/{{version}}/vite).

<a name="concept-overview"></a>
## Огляд концепції

Трансляція подій Laravel дозволяє транслювати серверні події Laravel у JavaScript-додаток на клієнтській стороні, використовуючи драйверний підхід до WebSockets. Наразі Laravel постачається з драйверами [Pusher Channels](https://pusher.com/channels) і [Ably](https://ably.com). Події можуть бути легко оброблені на стороні клієнта за допомогою JavaScript-пакета [Laravel Echo](#client-side-installation).

Події транслюються по «каналах», які можуть бути публічними або приватними. Будь-який відвідувач вашого додатку може підписатися на публічний канал без жодної аутентифікації або авторизації; однак, щоб підписатися на приватний канал, користувач повинен бути аутентифікованим і авторизованим для прослуховування подій на цьому каналі.

<a name="using-example-application"></a>
### Приклад використання

Перш ніж заглибитися в кожен аспект трансляції подій, давайте зробимо загальний огляд на прикладі інтернет-магазину.

Припустимо, що в нашому додатку у нас є сторінка, яка дозволяє користувачам переглядати статус доставки своїх замовлень. Припустимо також, що подія `OrderShipmentStatusUpdated` запускається, коли застосунок обробляє оновлення статусу доставки:

    use App\Events\OrderShipmentStatusUpdated;

    OrderShipmentStatusUpdated::dispatch($order);

<a name="the-shouldbroadcast-interface"></a>
#### Інтерфейс `ShouldBroadcast`

Коли користувач переглядає одне з своїх замовлень, ми не хочемо, щоб йому доводилося оновлювати сторінку для перегляду статусу оновлень. Замість цього ми хочемо транслювати оновлення в додаток, як тільки вони будуть створені. Отже, нам потрібно позначити подію `OrderShipmentStatusUpdated` інтерфейсом `ShouldBroadcast`. Це проінструктує Laravel транслювати подію при її запуску:

    <?php

    namespace App\Events;

    use App\Models\Order;
    use Illuminate\Broadcasting\Channel;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Broadcasting\PresenceChannel;
    use Illuminate\Broadcasting\PrivateChannel;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    use Illuminate\Queue\SerializesModels;

    class OrderShipmentStatusUpdated implements ShouldBroadcast
    {
        /**
         * Екземпляр замовлення.
         *
         * @var \App\Models\Order
         */
        public $order;
    }

Інтерфейс `ShouldBroadcast` вимагає, щоб у нашому класі події був визначений метод `broadcastOn`. Цей метод відповідає за повернення каналів, по яких повинна транслюватися подія. Порожня заглушка цього методу вже визначена в згенерованих класах подій, тому нам потрібно лише заповнити її реалізацію. Ми хочемо, щоб тільки творець замовлення міг переглядати статус оновлення, тому будемо транслювати подію на приватному каналі, прив'язаному до конкретного замовлення:

    use Illuminate\Broadcasting\Channel;
    use Illuminate\Broadcasting\PrivateChannel;

    /**
     * Отримати канали трансляції події.
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('orders.'.$this->order->id);
    }

Якщо ви хочете, щоб подія передавалася по кільком каналам, ви можете повернути замість цього `array`:

    use Illuminate\Broadcasting\PrivateChannel;

    /**
     * Отримати канали, на які має транслюватися подія.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('orders.'.$this->order->id),
            // ...
        ];
    }

<a name="example-application-authorizing-channels"></a>
#### Авторизація каналів

Пам'ятайте, що користувачі повинні мати дозвіл на прослуховування приватних каналів. Ми можемо визначити наші правила авторизації каналів у файлі `routes/channels.php` нашого додатку. У цьому прикладі нам потрібно впевнитися, що будь-який користувач, який намагається прослуховувати приватний канал `orders.1`, насправді є творцем замовлення:

    use App\Models\Order;
    use App\Models\User;

    Broadcast::channel('orders.{orderId}', function (User $user, int $orderId) {
        return $user->id === Order::findOrNew($orderId)->user_id;
    });

Метод `channel` приймає два аргументи: ім'я канала та замикання, яке повертає `true` або `false`, вказуючи тим самим, чи має користувач право прослуховувати канал.

Всі замикання авторизації отримують поточного аутентифікованого користувача як свій перший аргумент і будь-які додаткові параметри як свої подальші аргументи. У цьому прикладі ми використовуємо заповнювач `{orderId}`, щоб вказати, що частина «ID» імені канала є параметром.

<a name="listening-for-event-broadcasts"></a>
#### Прослуховування трансляцій подій

Далі все, що залишається – це прослуховувати подію в нашому JavaScript-додатку. Ми можемо зробити це за допомогою [Laravel Echo](#client-side-installation). Спочатку ми будемо використовувати метод `private` для підписки на приватний канал. Потім ми можемо використовувати метод `listen` для прослуховування події `OrderShipmentStatusUpdated`. За замовчуванням всі публічні властивості події будуть включені до трансляції події:

```js
Echo.private(`orders.${orderId}`)
    .listen('OrderShipmentStatusUpdated', (e) => {
        console.log(e.order);
    });
```

<a name="defining-broadcast-events"></a>
## Визначення транслюваних подій

Щоб проінформувати Laravel про те, що певна подія повинна транслюватися, ви повинні реалізувати інтерфейс `Illuminate\Contracts\Broadcasting\ShouldBroadcast` у класі події. Цей інтерфейс вже імпортований у всі класи подій, згенеровані фреймворком, тому ви з легкістю можете додати його до будь-яких ваших подій.

Інтерфейс `ShouldBroadcast` вимагає, щоб ви реалізували єдиний метод: `broadcastOn`. Метод `broadcastOn` має повертати канал або масив каналів, по яких має транслюватися подія. Канали мають бути екземплярами `Channel`, `PrivateChannel` або `PresenceChannel`. Екземпляри `Channel` представляють собою публічні канали, на які може підписатися будь-який користувач, в той час як `PrivateChannels` і `PresenceChannels` представляють собою приватні канали, для яких потрібна [авторизація каналу](#authorizing-channels):

    <?php

    namespace App\Events;

    use App\Models\User;
    use Illuminate\Broadcasting\Channel;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Broadcasting\PresenceChannel;
    use Illuminate\Broadcasting\PrivateChannel;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    use Illuminate\Queue\SerializesModels;

    class ServerCreated implements ShouldBroadcast
    {
        use SerializesModels;

        /**
         * Створити новий екземпляр події.
         */
        public function __construct(
            public User $user,
        ) {}

        /**
         * Отримати канали трансляції події.
         *
         * @return array<int, \Illuminate\Broadcasting\Channel>
         */
        public function broadcastOn(): array
        {
            return [
                new PrivateChannel('user.'.$this->user->id),
            ];
        }
    }

Після реалізації інтерфейсу `ShouldBroadcast` вам потрібно лише [запустити подію](/docs/{{version}}/events), як зазвичай. Після того, як подія буде запущена, [завдання в черзі](/docs/{{version}}/queues) автоматично транслює подію, використовуючи зазначений вами драйвер трансляції.

<a name="broadcast-name"></a>
### Ім'я транслюваної події

За замовчуванням Laravel буде транслювати подію, використовуючи ім'я класу події. Проте ви можете змінити ім'я транслюваної події, визначивши для події метод `broadcastAs`:

    /**
     * Ім'я транслюваної події.
     */
    public function broadcastAs(): string
    {
        return 'server.created';
    }

Якщо ви зміните ім'я транслюваної події за допомогою методу `broadcastAs`, тоді ви повинні переконатися, що зареєстрували ваш слухач із ведучим символом `.`. Це проінструктує Echo не додавати простір імені додатку до події:

    .listen('.server.created', function (e) {
        ....
    });

<a name="broadcast-data"></a>
### Дані трансляції

При трансляції події, всі її публічні властивості автоматично серіалізуються і транслюються як корисна навантаження події, що дозволяє вам отримувати доступ до будь-яких її публічних даних з вашого JavaScript-додатку. Так, наприклад, якщо ваше подія має єдине публічне властивість `$user`, що представляє собою модель Eloquent, то корисна навантаження при трансляції події буде:

```json
{
    "user": {
        "id": 1,
        "name": "Patrick Stewart"
        ...
    }
}
```

Проте якщо ви хочете мати більше контролю над корисною навантаженням трансляції, то ви можете визначити метод `broadcastWith` вашої події. Цей метод має повертати масив даних, які ви хочете використовувати як корисну навантаження при трансляції події:

    /**
     * Отримати дані для трансляції.
     *
     * @return array string, mixed
     */
    public function broadcastWith(): array
    {
        return ['id' => $this->user->id];
    }

<a name="broadcast-queue"></a>
### Черга трансляції

За замовчуванням кожне транслюване подія поміщається в чергу за замовчуванням і з'єднання черги за замовчуванням, зазначені у вашому конфігураційному файлі `config/queue.php`. Ви можете змінити з'єднання черги та ім'я, яке використовує ведучий, визначивши властивості `connection` та `queue` у вашому класі події:

    /**
     * Ім'я з'єднання черги, яке буде використовуватися при трансляції події.
     *
     * @var string
     */
    public $connection = 'redis';

    /**
     * Ім'я черги, в яку потрібно помістити завдання трансляції.
     *
     * @var string
     */
    public $queue = 'default';

В якості альтернативи, ви можете налаштувати ім'я черги, визначивши в методі `broadcastQueue` вашої події:

    /**
     * Ім'я черги, в яку треба помістити завдання трансляції.
     */
    public function broadcastQueue(): string
    {
        return 'default';
    }

Якщо ви хочете транслювати вашу подію за допомогою черги `sync` замість драйвера черги за замовчуванням, тоді ви можете реалізувати інтерфейс `ShouldBroadcastNow` замість `ShouldBroadcast`:

    <?php

    use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

    class OrderShipmentStatusUpdated implements ShouldBroadcastNow
    {
        // ...
    }

<a name="broadcast-conditions"></a>
### Умови трансляції

Іноді необхідно транслювати подію лише у разі виконання певної умови. Ви можете визначити ці умови, додавши метод `broadcastWhen` у ваш клас події:

    /**
     * Визначити, умови трансляції події.
     */
    public function broadcastWhen(): bool
    {
        return $this->order->value > 100;
    }

<a name="broadcasting-and-database-transactions"></a>
#### Трансляція та транзакції бази даних

Коли транслюємi події надсилаються в транзакціях бази даних, вони можуть бути оброблені чергою до того, як транзакція бази даних буде зафіксована. Коли це відбувається, будь-які оновлення, внесені вами в моделі або записи бази даних під час транзакції бази даних, можуть ще не бути відображені в базі даних. Крім того, будь-які моделі або записи бази даних, створені в рамках транзакції, можуть не існувати в базі даних. Якщо ваше подія залежить від цих моделей, можуть виникнути непередбачувані помилки при обробці завдання, що транслює подію.

Якщо для параметра `after_commit` конфігурації вашого з'єднання з чергою встановлено значення `false`, ви все ще можете вказати, що конкретна транслююча подія повинна бути надіслана після того, як усі відкриті транзакції бази даних були зафіксовані, реалізувавши інтерфейс `ShouldDispatchAfterCommit` у класі події:

    <?php

    namespace App\Events;

    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
    use Illuminate\Queue\SerializesModels;

    class ServerCreated implements ShouldBroadcast, ShouldDispatchAfterCommit
    {
        use SerializesModels;
    }

> [!NOTE]
> Щоб дізнатися більше про те, як обійти ці проблеми, перегляньте документацію, що стосується [завдань у черзі та транзакцій бази даних](/docs/{{version}}/queues#jobs-and-database-transactions).

<a name="authorizing-channels"></a>
## Авторизація каналів

Приватні канали вимагають, щоб поточний аутентифікований користувач був авторизований і дійсно міг прослуховувати канал. Це досягається шляхом відправки HTTP-запиту вашому додатку Laravel з ім'ям каналу, що дозволить вашому додатку визначити, чи може користувач прослуховувати цей канал. При використанні [Laravel Echo](#client-side-installation) HTTP-запит на авторизацію підписок на приватні канали буде виконано автоматично.

Коли ведення трансляції включено, Laravel автоматично реєструє маршрут `/broadcasting/auth` для обробки запитів на авторизацію. Маршрут `/broadcasting/auth` автоматично поміщається в групу проміжників `web`.

<a name="defining-authorization-callbacks"></a>
### Визначення авторизації канала

Потім нам потрібно визначити логіку, яка фактично буде визначати, чи може поточний аутентифікований користувач прослуховувати вказаний канал. Це робиться у файлі `routes/channels.php`, створеному командою Artisan `install:broadcasting`. У цьому файлі ви можете використовувати метод `Broadcast::channel` для реєстрації замикань авторизації канала:

    use App\Models\User;

    Broadcast::channel('orders.{orderId}', function (User $user, int $orderId) {
        return $user->id === Order::findOrNew($orderId)->user_id;
    });

Метод `channel` приймає два аргументи: ім'я канала та замикання, яке повертає `true` або `false`, вказуючи тим самим, чи має користувач право прослуховувати канал.

Всі замикання авторизації отримують поточного аутентифікованого користувача як свій перший аргумент і будь-які додаткові параметри як свої подальші аргументи. У цьому прикладі ми використовуємо заповнювач `{orderId}`, щоб вказати, що частина «ID» імені канала є параметром.

Ви можете переглянути список замикань авторизації ведення вашого додатку, використовуючи команду Artisan `channel:list`:

```shell
php artisan channel:list
```

<a name="authorization-callback-model-binding"></a>
#### Прив'язка моделі до авторизації

Як і HTTP-маршрути, для маршрутів каналів також можуть використовуватися неявні та явні [прив'язки моделі до маршруту](/docs/{{version}}/routing#route-model-binding). Наприклад, замість отримання рядкового або числового ідентифікатора замовлення ви можете запитати фактичний екземпляр моделі `Order`:

    use App\Models\Order;
    use App\Models\User;

    Broadcast::channel('orders.{order}', function (User $user, Order $order) {
        return $user->id === $order->user_id;
    });


> [!WARNING]  
> На відміну від прив'язки моделі до HTTP-маршруту, прив'язка моделі канала не підтримує [обмеження неявної прив'язки моделі](/docs/{{version}}/routing#implicit-model-binding-scoping). Однак це рідко представляє собою проблему, оскільки більшість каналів можна обмежити на основі унікального первинного ключа однієї моделі.

<a name="authorization-callback-authentication"></a>
#### Попередня аутентифікація авторизації канала

Приватні канали та канали присутності аутентифікують поточного користувача через стандартного охоронця аутентифікації вашого додатку. Якщо користувач не аутентифікований, тоді авторизація каналу автоматично відклоняється, і зворотний виклик авторизації ніколи не виконується. Однак ви можете призначити кілька своїх охоронців, які повинні при необхідності аутентифікувати вхідний запит:

    Broadcast::channel('channel', function () {
        // ...
    }, ['guards' => ['web', 'admin']]);

<a name="defining-channel-classes"></a>
### Визначення класу канала

Якщо ваше додаток використовує багато різних каналів, ваш файл `routes/channels.php` може стати громіздким. Таким чином, замість використання замикань для авторизації каналів ви можете використовувати класи каналів. Щоб згенерувати новий канал, використовуйте команду `make:channel` [Artisan](artisan). Ця команда помістить новий клас канала в каталог `app/Broadcasting` вашого додатку:

```shell
php artisan make:channel OrderChannel
```

Потім зареєструйте свій канал у файлі `routes/channels.php`:

    use App\Broadcasting\OrderChannel;

    Broadcast::channel('orders.{order}', OrderChannel::class);

Нарешті, ви можете помістити логіку авторизації для свого канала в метод `join` класу канала. Цей метод буде містити ту ж логіку, яку ви зазвичай використовували б у замиканні при авторизації вашого канала. Ви також можете скористатися перевагами прив'язки моделі канала:

    <?php

    namespace App\Broadcasting;

    use App\Models\Order;
    use App\Models\User;

    class OrderChannel
    {
        /**
         * Створити новий екземпляр канала.
         */
        public function __construct() {}

        /**
         * Підтвердити доступ користувача до канала.
         */
        public function join(User $user, Order $order): array|bool
        {
            return $user->id === $order->user_id;
        }
    }


> [!NOTE]  
> Як і багато інших класів у Laravel, класи каналів будуть автоматично розв’язані [контейнером служб](/docs/{{version}}/container). Таким чином, ви можете вказати будь-які залежності, необхідні для вашого канала, у його конструкторі.

<a name="broadcasting-events"></a>
## Трансляція подій

Після того, як ви визначили подію та позначили її інтерфейсом `ShouldBroadcast`, вам потрібно лише запустити подію, використовуючи метод надсилання події. Диспетчер подій помітить, що подія помічена інтерфейсом `ShouldBroadcast`, і поставить подію в чергу для подальшої трансляції:

    use App\Events\OrderShipmentStatusUpdated;

    OrderShipmentStatusUpdated::dispatch($order);

<a name="only-to-others"></a>
### Трансляція подій тільки іншим користувачам

При створенні додатка, що використовує трансляцію подій, іноді може знадобитися транслювати подію всім підписникам каналу, крім поточного користувача. Ви можете зробити це за допомогою допоміжного методу `broadcast` та методу `toOthers`:

    use App\Events\OrderShipmentStatusUpdated;

    broadcast(new OrderShipmentStatusUpdated($update))->toOthers();

Щоб краще зрозуміти необхідність використання методу `toOthers`, давайте уявимо додаток зі списком завдань, у якому користувач може створити нове завдання, введення назви завдання. Щоб створити завдання, ваше додаток може зробити запит до URL-адреси `/task`, яка транслює створення завдання і повертає JSON-представлення нової завдання. Коли ваше JavaScript-додаток отримує відповідь від кінцевої точки, воно може безпосередньо вставити нове завдання до свого списку завдань наступним чином:

```js
axios.post('/task', task)
    .then((response) => {
        this.tasks.push(response.data);
    });
```
Проте пам'ятайте, що ми також транслюємо створення завдання. Якщо ваше JavaScript-додаток також прослуховує цю подію, щоб додати завдання до списку завдань, у вас будуть дубльовані завдання у вашому списку: одне з кінцевої точки та одне з трансляції. Ви можете вирішити цю проблему, використовуючи метод `toOthers`, щоб вказати ведучому не транслювати подію поточному користувачу.

> [!WARNING]  
> Ваше подія повинно використовувати трейт `Illuminate\Broadcasting\InteractsWithSockets` для виклику методу `toOthers`.

<a name="only-to-others-configuration"></a>
#### Конфігурування при використанні методу `toOthers`

Коли ви ініціалізуєте екземпляр Laravel Echo, з'єднанню призначається ідентифікатор сокета. Якщо ви використовуєте глобальний екземпляр [Axios](https://github.com/axios/axios) для виконання HTTP-запитів з вашого JavaScript-додатку, то ідентифікатор сокета буде автоматично прикріплюватися до кожного вихідного запиту в заголовку `X-Socket-ID`. Потім, коли ви викликаєте метод `toOthers`, Laravel витягне ідентифікатор сокета з заголовка і проінструктує ведучого не транслювати жодні з'єднання з цим ідентифікатором сокета.

Якщо ви не використовуєте глобальний екземпляр Axios, тоді вам потрібно вручну налаштувати JavaScript-додаток для відправлення заголовка `X-Socket-ID` з усіма вихідними запитами. Ви можете отримати ідентифікатор сокета, використовуючи метод `Echo.socketId`:

```js
var socketId = Echo.socketId();
```

<a name="customizing-the-connection"></a>
### Налаштування з'єднання

Якщо ваше додаток взаємодіє з кількома широковещальними з'єднаннями, і ви хочете транслювати подію за допомогою ведучого, відмінного від використовуваного за замовчуванням, ви можете вказати, на яке з'єднання надсилати подію, використовуючи метод `via`:

    use App\Events\OrderShipmentStatusUpdated;

    broadcast(new OrderShipmentStatusUpdated($update))->via('pusher');

В якості альтернативи ви можете вказати широковещальне з'єднання події, викликавши метод `broadcastVia` у конструкторі події. Проте перед цим ви повинні переконатися, що клас подій використовує трейт `InteractsWithBroadcasting`:

    <?php

    namespace App\Events;

    use Illuminate\Broadcasting\Channel;
    use Illuminate\Broadcasting\InteractsWithBroadcasting;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Broadcasting\PresenceChannel;
    use Illuminate\Broadcasting\PrivateChannel;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    use Illuminate\Queue\SerializesModels;

    class OrderShipmentStatusUpdated implements ShouldBroadcast
    {
        use InteractsWithBroadcasting;

        /**
         * Створити новий екземпляр події.
         */
        public function __construct()
        {
            $this->broadcastVia('pusher');
        }
    }

<a name="anonymous-events"></a>
### Анонімні події

Іноді вам може знадобитися транслювати просту подію у зовнішній інтерфейс вашого додатку без створення спеціального класу подій. Щоб забезпечити це, фасад `Broadcast` дозволяє транслювати «анонімні події»:

```php
Broadcast::on('orders.'.$order->id)->send();
```

У наведеному вище прикладі буде транслюватися наступна подія:

```json
{
    "event": "AnonymousEvent",
    "data": "[]",
    "channel": "orders.1"
}
```

Використовуючи методи `as` та `with`, ви можете налаштувати ім'я та дані події:

```php
Broadcast::on('orders.'.$order->id)
    ->as('OrderPlaced')
    ->with($order)
    ->send();
```

У наведеному вище прикладі буде транслюватися подія, подібна до наступної:

```json
{
    "event": "OrderPlaced",
    "data": "{ id: 1, total: 100 }",
    "channel": "orders.1"
}
```

Якщо ви хочете транслювати анонімну подію на приватному каналі або каналі присутності, ви можете використовувати методи `private` та `presence`:

```php
Broadcast::private('orders.'.$order->id)->send();
Broadcast::presence('channels.'.$channel->id)->send();
```

При відправці анонімної події з допомогою методу `send` вона відправляється в [чергу](/docs/{{version}}/queues) вашого додатку для обробки. Однак, якщо ви бажаєте негайно транслювати подію, ви можете використовувати метод `sendNow`:

```php
Broadcast::on('orders.'.$order->id)->sendNow();
```

Щоб транслювати подію всім підписникам каналу, крім поточного аутентифікованого користувача, ви можете викликати метод `toOthers`:

```php
Broadcast::on('orders.'.$order->id)
    ->toOthers()
    ->send();
```

<a name="receiving-broadcasts"></a>
## Прийом трансляцій

<a name="listening-for-events"></a>
### Прослуховування подій

Після того, як ви [встановили та створили екземпляр Laravel Echo](#client-side-installation), ви готові до прослуховування подій, які транслюються з вашого додатку Laravel. Спершу використовуйте метод `channel` для отримання екземпляра каналу, потім викликайте метод `listen` для прослуховування конкретної події:

```js
Echo.channel(`orders.${this.order.id}`)
    .listen('OrderShipmentStatusUpdated', (e) => {
        console.log(e.order.name);
    });
```

Якщо ви хочете прослуховувати події на приватному каналі, то використовуйте замість цього метод `private`. Ви можете продовжити ланцюг викликів методу `listen` для прослуховування кількох подій на одному каналі:

```js
Echo.private(`orders.${this.order.id}`)
    .listen(/* ... */)
    .listen(/* ... */)
    .listen(/* ... */);
```

<a name="stop-listening-for-events"></a>
#### Зупинка прослуховування подій

Якщо ви хочете припинити прослуховування даної події, не [покидаючи канал](#leaving-a-channel), ви можете використовувати метод `stopListening`:

```js
Echo.private(`orders.${this.order.id}`)
    .stopListening('OrderShipmentStatusUpdated')
```

<a name="leaving-a-channel"></a>
### Покидання каналу

Щоб покинути канал, ви можете викликати метод `leaveChannel` вашого екземпляра Echo:

```js
Echo.leaveChannel(`orders.${this.order.id}`);
```

Якщо ви хочете покинути канал, а також пов'язані з ним приватні канали та канали присутності, ви можете викликати метод `leave`:

```js
Echo.leave(`orders.${this.order.id}`);
```
<a name="namespaces"></a>
### Простори імен

Ви могли помітити в наведених вище прикладах, що ми не вказали повне простір імені `App\Events` для класів подій. Це пов'язано з тим, що Echo автоматично припускає, що події знаходяться у просторі імені `App\Events`. Однак ви можете змінити кореневе простір імені при створенні екземпляра Echo, передавши параметр конфігурації `namespace`:

```js
window.Echo = new Echo({
    broadcaster: 'pusher',
    // ...
    namespace: 'App.Other.Namespace'
});
```

В якості альтернативи ви можете додати до класів подій префікс `.` при підписуванні на них за допомогою Echo. Це дозволить вам завжди вказувати повне ім'я класу:

```js
Echo.channel('orders')
    .listen('.Namespace\\Event\\Class', (e) => {
        // ...
    });
```

<a name="presence-channels"></a>
## Канали присутності

Канали присутності базуються на безпеці приватних каналів, але одночасно надають додаткову функцію обізнаності про те, хто підписаний на канал. Це спрощує створення потужних функцій додатку для співпраці, таких як повідомлення користувачів про те, коли інший користувач переглядає ту ж сторінку, або перерахування користувачів кімнати чату.

<a name="authorizing-presence-channels"></a>
### Авторизація каналів присутності

Усі канали присутності також є приватними; отже, користувачі повинні бути [авторизовані для доступу до них](#authorizing-channels). Однак при визначенні замикань авторизації для каналів присутності ви не повинні повертати `true`, якщо користувач авторизований для приєднання до каналу. Замість цього ви повинні повернути масив даних про користувача.

Дані, що повертаються замиканням авторизації, будуть доступні для слухачів подій каналу присутності у вашому JavaScript-додатку. Якщо користувач не авторизований для приєднання до каналу присутності, тоді ви повинні повернути `false` або `null`:

    use App\Models\User;

    Broadcast::channel('chat.{roomId}', function (User $user, int $roomId) {
        if ($user->canJoinRoom($roomId)) {
            return ['id' => $user->id, 'name' => $user->name];
        }
    });

<a name="joining-presence-channels"></a>
### Приєднання до каналів присутності

Щоб приєднатися до каналу присутності, ви можете використовувати метод `join` Echo. Метод `join` поверне реалізацію `PresenceChannel`, яка, разом із методом `listen`, дозволяє вам підписатися на події `here`, `joining` та `leave`.

```js
Echo.join(`chat.${roomId}`)
    .here((users) => {
        // ...
    })
    .joining((user) => {
        console.log(user.name);
    })
    .leaving((user) => {
        console.log(user.name);
    })
    .error((error) => {
        console.error(error);
    });
```

Замикання `here` буде виконано відразу після успішного приєднання до каналу і отримає масив, що містить інформацію про користувача для всіх інших користувачів, які в даний момент підписані на канал. Метод `joining` буде виконуватись, коли новий користувач приєднується до каналу, а метод `leaving` буде виконуватись при покиданні користувачем каналу. Метод `error` буде виконуватись, коли при аутентифікації повертається код HTTP-статусу, відмінний від 200, або якщо виникає проблема з парсингом повернутого JSON.

<a name="broadcasting-to-presence-channels"></a>
### Трансляція на канали присутності

Канали присутності можуть отримувати події так само, як публічні або приватні канали. Використовуючи приклад чату, ми можемо захотіти транслювати події `NewMessage` на канал присутності кімнати. Для цього ми повернемо екземпляр `PresenceChannel` з методу `broadcastOn` події:

    /**
     * Отримати канали трансляції події.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return new PresenceChannel('room.'.$this->message->room_id);
    }

Як і в разі інших подій, ви можете використовувати допоміжник `broadcast` і метод `toOthers`, щоб виключити поточного користувача з отримання трансляції:

    broadcast(new NewMessage($message));

    broadcast(new NewMessage($message))->toOthers();

Як і для інших типів подій, ви можете прослуховувати події, надіслані в канали присутності, використовуючи метод `listen` Echo:

```js
Echo.join(`chat.${roomId}`)
    .here(/* ... */)
    .joining(/* ... */)
    .leaving(/* ... */)
    .listen('NewMessage', (e) => {
        // ...
    });
```

<a name="model-broadcasting"></a>
## Трансляція моделей

> [!WARNING]  
> Перед тим як читати наступну документацію про трансляцію моделей, ми рекомендуємо вам ознайомитися із загальними концепціями модельних широкомовних служб Laravel, а також із тим, як вручну створювати і прослуховувати широкомовні події.

Зазвичай транслюються події, коли [моделі Eloquent](/docs/{{version}}/eloquent) створюються, оновлюються або видаляються. Звісно, це легко можна зробити вручну, [визначивши користувацькі події для змін стану моделі Eloquent](/docs/{{version}}/eloquent#events) і позначивши ці події за допомогою інтерфейсу `ShouldBroadcast`.

Проте, якщо ви не використовуєте ці події для яких-небудь інших цілей у своєму додатку, може виявитися обтяжливим створення класів подій з єдиною метою їх широкомовної передачі. Щоб це виправити, Laravel дозволяє вам вказати, що модель Eloquent повинна автоматично транслювати зміни свого стану.

Для початку ваша модель Eloquent повинна використовувати трейт `Illuminate\Database\Eloquent\BroadcastsEvents`. Крім того, модель повинна визначати метод `broadcastOn`, що повертає масив каналів, по яких повинні транслюватися події моделі:

```php
<?php

namespace App\Models;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use BroadcastsEvents, HasFactory;

    /**
     * Отримайте користувача, якому належить повідомлення.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Отримайте канали, по яких повинні транслюватися події моделі.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel|\Illuminate\Database\Eloquent\Model>
     */
    public function broadcastOn(string $event): array
    {
        return [$this, $this->user];
    }
}
```

Після того, як ваша модель включає цей трейт і визначає свої канали ведення, вона почне автоматично транслювати події при створенні, оновленні, видаленні, знищенні або відновленні екземпляра моделі.

Крім того, ви могли помітити, що метод `broadcastOn` отримує строковий аргумент `$event`. Цей аргумент містить тип події, що сталося в моделі, і матиме значення `created`, `updated`, `deleted`, `trashed` або `restored`. Перевіряючи значення цієї змінної, ви можете визначити, на які канали (якщо є) модель повинна транслювати конкретну подію:

```php
/**
 * Отримайте канали, по яких повинні транслюватися події моделі.
 *
 * @return array<string, array<int, \Illuminate\Broadcasting\Channel|\Illuminate\Database\Eloquent\Model>>
 */
public function broadcastOn(string $event): array
{
    return match ($event) {
        'deleted' => [],
        default => [$this, $this->user],
    };
}
```

<a name="customizing-model-broadcasting-event-creation"></a>
#### Налаштування створення події трансляції моделі

Іноді ви можете захотіти налаштувати те, як Laravel створює базове подія трансляції моделі. Ви можете досягти цього, визначивши метод `newBroadcastableEvent` у вашій моделі Eloquent. Цей метод має повертати екземпляр `Illuminate\Database\Eloquent\BroadcastableModelEventOccurred`:

```php
use Illuminate\Database\Eloquent\BroadcastableModelEventOccurred;

/**
 * Створіть нове транслююче подію для моделі.
 */
protected function newBroadcastableEvent(string $event): BroadcastableModelEventOccurred
{
    return (new BroadcastableModelEventOccurred(
        $this, $event
    ))->dontBroadcastToCurrentUser();
}
```

<a name="model-broadcasting-conventions"></a>
### Угода про трансляцію моделей

<a name="model-broadcasting-channel-conventions"></a>
#### Угода про канали

Як ви могли помітити, метод `broadcastOn` у наведеному вище прикладі моделі не повертав екземпляри `Channel`. Замість цього моделі Eloquent поверталися безпосередньо. Якщо екземпляр моделі Eloquent повертається методом `broadcastOn` вашої моделі (або міститься в масиві, повернутому методом), Laravel автоматично створить екземпляр приватного каналу для моделі, використовуючи ім'я класу моделі та ідентифікатор первинного ключа як ім'я каналу.

Отже, модель `App\Models\User` з `id`, що дорівнює `1`, буде перетворена на екземпляр `Illuminate\Broadcasting\PrivateChannel` з іменем `App.Models.User.1`. Звичайно, на додаток до повернення екземплярів моделі Eloquent з методу `broadcastOn` вашої моделі, ви можете повертати повні екземпляри `Channel`, щоб мати повний контроль над іменами каналів моделі:

```php
use Illuminate\Broadcasting\PrivateChannel;

/**
 * Отримайте канали, за якими мають транслюватися події моделі.
 *
 * @return array<int, \Illuminate\Broadcasting\Channel>
 */
public function broadcastOn(string $event): array
{
    return [
        new PrivateChannel('user.'.$this->id)
    ];
}
```

Якщо ви плануєте явно повертати екземпляр каналу з методу `broadcastOn` вашої моделі, ви можете передати екземпляр моделі Eloquent у конструктор каналу. При цьому Laravel буде використовувати описані вище угоди про канали моделі, щоб перетворити модель Eloquent в рядок імені каналу:

```php
return [new Channel($this->user)];
```

Якщо вам потрібно визначити ім'я каналу моделі, ви можете викликати метод `broadcastChannel` для будь-якого екземпляра моделі. Наприклад, цей метод повертає рядок `App.Models.User.1` для моделі `App\Models\User` з `id`, що дорівнює `1`:

```php
$user->broadcastChannel()
```

<a name="model-broadcasting-event-conventions"></a>
#### Угода про події

Оскільки події трансляції моделі не пов'язані з «фактичною» подією в каталозі `App\Events` вашого додатка, їм присвоюються ім'я та корисне навантаження на основі угод. Угода Laravel полягає в тому, щоб транслювати подію, використовуючи ім'я класу моделі (без урахування простору імен) та ім'я події моделі, яка ініціювала трансляцію.

Так, наприклад, оновлення моделі `App\Models\Post` транслюватиме подію у ваш клієнтський застосунок як `PostUpdated` з наступним корисним навантаженням:

```json
{
    "model": {
        "id": 1,
        "title": "My first post"
        ...
    },
    ...
    "socket": "someSocketId",
}
```

Видалення моделі `App\Models\User` призведе до трансляції події з іменем `UserDeleted`.

Якщо ви бажаєте, ви можете визначити власне ім'я трансляції та корисне навантаження, додавши до вашої моделі методи `broadcastAs` і `broadcastWith`. Ці методи отримують ім'я події / операції моделі, що відбувається, що дає змогу вам налаштувати ім'я події та корисне навантаження для кожної операції моделі. Якщо `null` повертається з методу `broadcastAs`, Laravel буде використовувати угоди про імена широкомовних подій моделі, обговорені вище:

```php
/**
 * Ім'я події моделі, що транслюється.
 */
public function broadcastAs(string $event): string|null
{
    return match ($event) {
        'created' => 'post.created',
        default => null,
    };
}

/**
 * Отримайте дані для трансляції моделі.
 *
 * @return array<string, mixed>
 */
public function broadcastWith(string $event): array
{
    return match ($event) {
        'created' => ['title' => $this->title],
        default => ['model' => $this],
    };
}
```

<a name="listening-for-model-broadcasts"></a>
### Прослуховування трансляцій моделей

Після того, як ви додали до моделі трейт `BroadcastsEvents` і визначили метод `broadcastOn` моделі, ви готові почати прослуховувати транслювані події моделі у своєму клієнтському додатку. Перед тим як почати, ви можете ознайомитися з повною документацією про [прослуховування подій](#listening-for-events).

Спочатку використовуйте метод `private` для отримання екземпляра каналу, потім викликайте метод `listen` для прослуховування вказаної події. Як правило, ім'я канала, присвоєне методу `private`, повинно відповідати [угоді про трансляцію моделей](#model-broadcasting-conventions).

Як тільки ви отримали екземпляр каналу, ви можете використовувати метод `listen` для прослуховування певної події. Оскільки події трансляції моделей не пов’язані з «фактичним» подією в каталозі вашого додатку `App\Events`, [ім'я події](#model-broadcasting-event-conventions) має мати префікс `.`, щоб вказати, що воно не належить конкретному простору імен. Кожна подія трансляції моделі має властивість `model`, яка містить усі транслювані властивості моделі:

```js
Echo.private(`App.Models.User.${this.user.id}`)
    .listen('.PostUpdated', (e) => {
        console.log(e.model);
    });
```

<a name="client-events"></a>
## Клієнтські події

> [!NOTE]
> У разі використання [Pusher Channels](https://pusher.com/channels) ви маєте ввімкнути опцію «Client Events» у розділі «App Settings» вашої [панелі керування застосунком](https://dashboard.pusher.com/) для надсилання клієнтських подій.

За бажанням можна транслювати подію іншим підключеним клієнтам, взагалі не зачіпаючи ваш додаток Laravel. Це може бути особливо корисно для таких речей, як «введення» сповіщень, коли ви хочете попередити користувачів вашої програми про те, що інший користувач друкує повідомлення.

Щоб транслювати клієнтські події, ви можете використовувати метод `whisper` Echo:

```js
Echo.private(`chat.${roomId}`)
    .whisper('typing', {
        name: this.user.name
    });
```

Щоб прослуховувати клієнтські події, ви можете використовувати метод `listenForWhisper`:

```js
Echo.private(`chat.${roomId}`)
    .listenForWhisper('typing', (e) => {
        console.log(e.name);
    });
```

<a name="notifications"></a>
## Повідомлення

Якщо пов'язати трансляцію подій з [сповіщеннями](notifications), то ваш JavaScript-додаток може отримувати нові сповіщення в міру їхньої появи без необхідності в оновленні сторінки. Перед початком роботи обов'язково прочитайте документацію з використання [каналу трансльованих повідомлень](notifications#broadcast-notifications).

Після того як ви налаштували повідомлення для використання трансляції каналу, ви можете прослуховувати трансльовані події, використовуючи метод `notification` Echo. Пам'ятайте, що ім'я каналу має відповідати імені класу об'єкта, який отримує повідомлення:

```js
Echo.private(`App.Models.User.${userId}`)
    .notification((notification) => {
        console.log(notification.type);
    });
```

У цьому прикладі всі повідомлення, надіслані екземплярам `App\Models\User` через канал `broadcast`, будуть отримані в замиканні. Авторизація каналу `App.Models.User.{id}` включена у файл `routes/channels.php` вашого застосунку.
