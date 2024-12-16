# Збірка ресурсів (Vite)

- [Вступ](#introduction)
- [Встановлення та налаштування](#installation)
    - [Встановлення вузла](#installing-node)
    - [Встановлення Vite та плагіна Laravel](#installing-vite-and-laravel-plugin)
    - [Налаштування Vite](#configuring-vite)
    - [Завантаження ваших скриптів і стилів](#loading-your-scripts-and-styles)
- [Запуск Vite](#running-vite)
- [Робота з JavaScript](#working-with-scripts)
    - [Псевдоніми](#aliases)
    - [Vue](#vue)
    - [React](#react)
    - [Inertia](#inertia)
    - [Обробка URL](#url-processing)
- [Working With Stylesheets](#working-with-stylesheets)
- [Робота з Blade маршрутами](#working-with-blade-and-routes)
    - [Обробка статичних ресурсів Vite](#blade-processing-static-assets)
        - [Оновлення при збереженні](#blade-refreshing-on-save)
    - [Псевдоніми](#blade-aliases)
- [Попередня вибірка активів](#custom-base-urls)
- [Змінні середовища](#environment-variables)
- [Вимкнення Vite в тестах](#disabling-vite-in-tests)
- [Серверний рендеринг (SSR)](#ssr)
- [Атрибути тегів скриптів і стилів](#script-and-style-attributes)
    - [Політика безпеки вмісту (CSP) Nonce](#content-security-policy-csp-nonce)
    - [Цілісність субресурсів (SRI)](#subresource-integrity-sri)
    - [Довільні атрибути](#arbitrary-attributes)
- [Розширене налаштування](#advanced-customization)
    - [Виправлення URL-адрес серверів розробки](#correcting-dev-server-urls)

<a name="introduction"></a>

## Введение

[Vite](https://vitejs.dev) - це сучасний інструмент збірки фронтенду, який забезпечує надзвичайно швидке оточення розробки та збирає ваш код для продакшена. При створенні додатків з використанням Laravel ви зазвичай використовуєте Vite для складання файлів CSS і JavaScript вашого додатка в готові до продакшену ресурси.

Laravel інтегрується з Vite без проблем, надаючи офіційний плагін і директиву Blade для завантаження ваших ресурсів як для розробки, так і для продакшена.

> [!NOTE]  
> Ви використовуєте Laravel Mix? Vite замінив Laravel Mix у нових установках Laravel. Для документації щодо Mix відвідайте веб-сайт [Laravel Mix](https://laravel-mix.com/). Якщо ви хочете перейти на Vite, ознайомтеся з нашим [керівництвом з міграції](https://github.com/laravel/vite-plugin/blob/main/UPGRADE.md#migrating-from-laravel-mix-to-vite).

<a name="vite-or-mix"></a>
<a name="vite-or-mix"></a>
#### Вибір між Vite і Laravel Mix

Перш ніж перейти на Vite, нові додатки Laravel використовували [Mix](https://laravel-mix.com/) під час збирання ресурсів, який працює на основі [webpack](https://webpack.js.org/). Vite зосереджений на наданні більш швидкого і продуктивного досвіду при створенні потужних JavaScript-додатків. Якщо ви розробляєте односторінковий додаток (SPA), включно з тими, що розроблені з використанням інструментів, таких як [Inertia](https://inertiajs.com), то Vite буде ідеальним вибором.

Vite також добре працює з традиційними додатками з серверним рендерингом з «домішкою» JavaScript, включно з тими, що використовують [Livewire](https://livewire.laravel.com). Однак у нього немає деяких функцій, які підтримує Laravel Mix, таких як можливість копіювання довільних ресурсів, на які немає прямих посилань у вашому додатку JavaScript, у збірку.

<a name="migrating-back-to-mix"></a>
#### Повернення до Mix

Ви розпочали новий додаток Laravel, використовуючи нашу структуру Vite, але вам потрібно повернутися до Laravel Mix і webpack? Немає проблем. Будь ласка, зверніться до нашого [офіційного керівництва з міграції з Vite на Mix](https://github.com/laravel/vite-plugin/blob/main/UPGRADE.md#migrating-from-vite-to-laravel-mix).

<a name="installation"></a>
## Встановлення та налаштування

> [!NOTE]  
> У наступній документації розглядається процес ручного встановлення та налаштування плагіна Laravel Vite. Однак стартові комплекти Laravel вже містять у собі всю цю структуру і є найшвидшим способом почати роботу з Laravel і Vite.

<a name="installing-node"></a>
### Встановлення Node

Перед запуском Vite і плагіна Laravel переконайтеся, що у вас встановлені Node.js (версії 16 і вище) і NPM:

```sh
node -v
npm -v
```

Ви можете легко встановити останню версію Node і NPM, використовуючи прості інсталятори з [офіційного сайту Node](https://nodejs.org/en/download/). Або, якщо ви використовуєте [Laravel Sail](https://laravel.com/docs/{{version}}/sail), ви можете викликати Node і NPM через Sail:

```sh
./vendor/bin/sail node -v
./vendor/bin/sail npm -v
```

<a name="installing-vite-and-laravel-plugin"></a>
### Встановлення Vite і плагіна Laravel

У новій інсталяції Laravel у корені структури каталогів вашого додатка ви знайдете файл `package.json`. У файлі `package.json` вже міститься все необхідне для початку роботи з Vite і плагіном Laravel. Ви можете встановити залежності фронтенду вашого додатка через NPM:

```sh
npm install
```

<a name="configuring-vite"></a>
### Налаштування Vite

Vite налаштовується за допомогою файлу `vite.config.js` у корені вашого проекту. Ви можете налаштовувати цей файл на свій розсуд, а також встановлювати будь-які інші плагіни, необхідні для вашого додатка, такі як `@vitejs/plugin-vue` або `@vitejs/plugin-react`.

Плагін Laravel Vite вимагає вказівки точок входу для вашого додатка. Це можуть бути файли JavaScript або CSS, включно з попередньо обробленими мовами, такими як TypeScript, JSX, TSX і Sass.

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
    ],
});
```

Якщо ви створюєте SPA, включно з додатками, побудованими з використанням Inertia, то Vite найкраще працюватиме без точок входу CSS:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css', // [tl! remove]
            'resources/js/app.js',
        ]),
    ],
});
```

Замість цього ви повинні імпортувати свій CSS через JavaScript. Зазвичай це робиться у файлі `resources/js/app.js` вашого застосунку:

```js
import './bootstrap';
import '../css/app.css'; // [tl! add]
```

Плагін Laravel також підтримує кілька точок входу і розширені параметри конфігурації, як-от [точки входу SSR](#ssr).

<a name="working-with-a-secure-development-server"></a>
#### Робота із захищеним сервером розробки

Якщо ваш локальний веб-сервер розробки обслуговує ваш додаток через HTTPS, у вас можуть виникнути проблеми з підключенням до сервера розробки Vite.

Якщо ви використовуєте [Laravel Herd](https://herd.laravel.com) і зашифрували сайт, або ви використовуєте [Laravel Valet](/docs/{{version}}/valet) і запустили [команду secure](/docs/{{{version}}}/valet#securing-sites) для вашого додатка, плагін Laravel Vite автоматично виявить і використає згенерований TLS-сертифікат.

Якщо ви зашифрували сайт з використанням хоста, який не відповідає імені каталогу застосунку, ви можете вручну вказати хост у файлі `vite.config.js` вашого застосунку:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // ...
            detectTls: 'my-app.test', // [tl! add]
        }),
    ],
});
```

Коли ви використовуєте інший веб-сервер, ви повинні згенерувати довірений сертифікат і вручну налаштувати Vite для використання згенерованих сертифікатів:

```js
// ...
import fs from 'fs'; // [tl! add]

const host = 'my-app.test'; // [tl! add]

export default defineConfig({
    // ...
    server: { // [tl! add]
        host, // [tl! add]
        hmr: { host }, // [tl! add]
        https: { // [tl! add]
            key: fs.readFileSync(`/path/to/${host}.key`), // [tl! add]
            cert: fs.readFileSync(`/path/to/${host}.crt`), // [tl! add]
        }, // [tl! add]
    }, // [tl! add]
});
```

Якщо ви не можете згенерувати довірений сертифікат для вашої системи, ви можете встановити та налаштувати плагін `@vitejs/plugin-basic-ssl`. У разі використання ненадійних сертифікатів вам потрібно буде прийняти попередження про сертифікат для сервера розробки Vite у вашому браузері, перейшовши за посиланням «Local» у консолі під час виконання команди `npm run dev`.

<a name="configuring-hmr-in-sail-on-wsl2"></a>
#### Запуск сервера розробки в Sail на WSL2

Під час запуску сервера розробки Vite у [Laravel Sail](/docs/{{version}}}/sail) на Windows Subsystem for Linux 2 (WSL2), вам слід додати таку конфігурацію до вашого файлу `vite.config.js`, щоб забезпечити зв'язок браузера із сервером розробки:

```js
// ...

export default defineConfig({
    // ...
    server: { // [tl! add:start]
        hmr: {
            host: 'localhost',
        },
    }, // [tl! add:end]
});
```

Якщо зміни у файлах не відображаються в браузері під час запущеного сервера розробки, вам також може знадобитися налаштувати опцію [`server.watch.usePolling`](https://vitejs.dev/config/server-options.html#server-watch) у Vite.
<a name="loading-your-scripts-and-styles"></a>
### Завантаження ваших скриптів і стилів

Після налаштування точок входу Vite ви можете посилатися на них за допомогою директиви `@vite()` у Blade, яку ви маєте додати в `<head>` кореневого шаблону вашого застосунку:

```blade
<!DOCTYPE html>
<head>
    {{-- ... --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

Якщо ви імпортуєте свій CSS через JavaScript, вам потрібно ввімкнути тільки точку входу JavaScript:

```blade
<!DOCTYPE html>
<head>
    {{-- ... --}}

    @vite('resources/js/app.js')
</head>
```

Директива `@vite` автоматично виявляє сервер розробки Vite і впроваджує клієнт Vite для можливості гарячої заміни модулів. У режимі складання директива завантажить ваші скомпільовані та пронумеровані ресурси, включно з будь-яким імпортованим CSS.

За необхідності ви також можете вказати шлях складання ваших скомпільованих ресурсів під час виклику директиви `@vite`.

```blade
<!doctype html>
<head>
    {{-- Given build path is relative to public path. --}}

    @vite('resources/js/app.js', 'vendor/courier/build')
</head>
```

<a name="inline-assets"></a>
#### Вбудовування ресурсів

Іноді може бути необхідно включити сирий вміст ресурсів, а не посилатися на версійний URL ресурсу. Наприклад, вам може знадобитися включити вміст ресурсу безпосередньо на сторінку, коли передаєте HTML-контент генератору PDF. Ви можете виводити вміст ресурсів Vite за допомогою методу `content`, наданого фасадом `Vite`:

```blade
@use('Illuminate\Support\Facades\Vite')

<!doctype html>
<head>
    {{-- ... --}}

    <style>
        {!! Vite::content('resources/css/app.css') !!}
    </style>
    <script>
        {!! Vite::content('resources/js/app.js') !!}
    </script>
</head>
```

<a name="running-vite"></a>
## Запуск Vite

Існує два способи запуску Vite. Ви можете запустити сервер розробки за допомогою команди `dev`, що корисно під час локальної розробки. Сервер розробки автоматично виявляє зміни у ваших файлах і миттєво відображає їх у будь-яких відкритих вікнах браузера.

Або виконати команду `build`, яка версіонує і збере ресурси вашого додатка, підготувавши їх до розгортання у виробничому середовищі:

```shell
# Запустіть сервер розробки Vite...
npm run dev

# Створюйте та версіонуйте ресурси для виробництва...
npm run build
```

Якщо ви запускаєте сервер розробки в [Sail](/docs/{{version}}}/sail) на WSL2, вам можуть знадобитися додаткові [опції конфігурації](#configuring-hmr-in-sail-on-wsl2).

<a name="working-with-scripts"></a>
## Работа с JavaScript

<a name="aliases"></a>
### Псевдоніми

За замовчуванням плагін Laravel надає загальний псевдонім, щоб ви могли почати роботу одразу і зручно імпортувати ресурси вашого додатка:

```js
{
    '@' => '/resources/js'
}
```

Ви можете перевизначити псевдонім `'@'`, додавши власний у файл конфігурації `vite.config.js`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel(['resources/ts/app.tsx']),
    ],
    resolve: {
        alias: {
            '@': '/resources/ts',
        },
    },
});
```

<a name="vue"></a>
### Vue

Якщо ви хочете зібрати свій фронтенд, використовуючи фреймворк [Vue](https://vuejs.org/), вам також потрібно встановити плагін `@vitejs/plugin-vue`:

```sh
npm install --save-dev @vitejs/plugin-vue
```

Потім ви можете включити плагін у ваш файл конфігурації `vite.config.js`. Під час використання плагіна Vue з Laravel вам знадобляться кілька додаткових параметрів:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel(['resources/js/app.js']),
        vue({
            template: {
                transformAssetUrls: {
                    // Плагін Vue перепише URL-адреси ресурсів, коли вони 
                    // будуть використовуватися в однофайлових компонентах, 
                    // щоб вказувати на веб-сервер Laravel. Встановлення цього 
                    // значення в `null` дозволяє замість цього плагіну Laravel 
                    // переписувати URL-адреси ресурсів, щоб вони вказували на сервер Vite.
                    base: null,

                    // Плагін Vue аналізуватиме абсолютні URL-адреси і розглядатиме 
                    // їх як абсолютні шляхи до файлів на диску. Встановлення цього значення в 
                    // `false` залишить абсолютні URL-адреси недоторканими, щоб вони могли 
                    // посилатися на ресурси в папці public, як очікується.
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
```

> [!NOTE]  
> Стартові набори Laravel ([starter kits](/docs/{{version}}}/starter-kits)) вже включають правильну конфігурацію Laravel, Vue і Vite. Подивіться [Laravel Breeze](/docs/{{version}}/starter-kits#breeze-and-inertia) для найшвидшого способу почати роботу з Laravel, Vue і Vite.

<a name="react"></a>
### React

Якщо ви хочете зібрати свій фронтенд, використовуючи фреймворк [React](https://reactjs.org/), вам також необхідно встановити плагін `@vitejs/plugin-react`:

```sh
npm install --save-dev @vitejs/plugin-react
```

Потім ви можете включити плагін у ваш файл конфігурації `vite.config.js`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel(['resources/js/app.jsx']),
        react(),
    ],
});
```

Ви повинні переконатися, що будь-які файли, що містять JSX, мають розширення `.jsx` або `.tsx`, пам'ятаючи про необхідність оновлення вашої точки входу, якщо це потрібно, як показано [вище](#configuring-vite).

Вам також потрібно буде включити додаткову директиву `@viteReactRefresh` разом із вашою поточною директивою `@vite` у Blade.

```blade
@viteReactRefresh
@vite('resources/js/app.jsx')
```

Директива `@viteReactRefresh` має бути викликана перед директивою `@vite`.

> [!NOTE]  
> Стартові набори Laravel ([starter kits](/docs/{{version}}}/starter-kits)) вже включають правильну конфігурацію Laravel, React і Vite. Ознайомтеся з [Laravel Breeze](/docs/{{version}}}/starter-kits#breeze-and-inertia) - найшвидшим способом почати роботу з Laravel, React і Vite.

<a name="inertia"></a>
### Inertia

Плагін Laravel Vite надає зручну функцію `resolvePageComponent`, яка допоможе вам визначити ваші компоненти сторінок Inertia. Нижче наведено приклад використання помічника з Vue 3; однак, ви також можете використовувати цю функцію в інших фреймворках, таких як React:

```js
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

createInertiaApp({
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
});
```

Якщо ви використовуєте функцію поділу коду Vite з Inertia, ми рекомендуємо налаштувати [попередню вибірку активу](#asset-prefetching).

> [!NOTE]  
> Стартові набори Laravel ([starter kits](/docs/{{version}}}/starter-kits)) вже включають правильну конфігурацію Laravel, Inertia і Vite. Ознайомтеся з [Laravel Breeze](/docs/{{version}}}/starter-kits#breeze-and-inertia), щоб швидко почати роботу з Laravel, Inertia і Vite.

<a name="url-processing"></a>
### Обработка URL

Під час використання Vite і посилань на ресурси в HTML, CSS або JS вашого додатка, слід враховувати кілька моментів. По-перше, якщо ви посилаєтеся на ресурси з абсолютним шляхом, Vite не включить ресурс у збірку; тому переконайтеся, що ресурс доступний у вашій публічній директорії. Вам слід уникати використання абсолютних шляхів під час використання [виділеної точки входу CSS](#configuring-vite), оскільки під час розроблення браузери намагатимуться завантажити ці шляхи із сервера розроблення Vite, де розміщено CSS, а не з вашого загальнодоступного каталогу.

Під час використання відносних шляхів до ресурсів пам'ятайте, що шляхи відносні до файлу, в якому вони використовуються. Будь-які ресурси, посилання на які здійснюються через відносний шлях, будуть переписані, версіоновані та зібрані Vite.

Розглянемо таку структуру проєкту:

```nothing
public/
  taylor.png
resources/
  js/
    Pages/
      Welcome.vue
  images/
    abigail.png
```

Наступний приклад демонструє, як Vite оброблятиме відносні й абсолютні URL-адреси:

```html
<!-- Цей ресурс не обробляється Vite і не буде включений до збірки. -->
<img src="/taylor.png">

<!-- Цей ресурс буде переписаний, пронумерований і зібраний Vite. -->
<img src="../../images/abigail.png">
```

<a name="working-with-stylesheets"></a>
## Робота з таблицями стилів

Ви можете дізнатися більше про підтримку CSS у Vite в [документації Vite](https://vitejs.dev/guide/features.html#css). Якщо ви використовуєте плагіни PostCSS, як-от [Tailwind](https://tailwindcss.com), ви можете створити файл `postcss.config.js` у корені вашого проєкту, і Vite автоматично його застосує.
```js
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
```

> [!NOTE]  
> Стартові набори Laravel ([starter kits](/docs/{{version}}}/starter-kits)) вже включають правильну конфігурацію Tailwind, PostCSS і Vite. Або, якщо ви хочете використовувати Tailwind і Laravel без використання одного з наших стартових наборів, ознайомтеся з [посібником зі встановлення Tailwind для Laravel](https://tailwindcss.com/docs/guides/laravel).

<a name="working-with-blade-and-routes"></a>
## Робота з Blade і маршрутами

<a name="blade-processing-static-assets"></a>
### Обробка статичних ресурсів з Vite

При посиланні на ресурси у вашому JavaScript або CSS, Vite автоматично обробляє і версіонує їх. Крім того, під час побудови застосунків на основі Blade, Vite також може обробляти та версіонувати статичні ресурси, на які ви посилаєтесь виключно в шаблонах Blade.

Однак для цього необхідно поінформувати Vite про ваші ресурси, імпортувавши статичні ресурси в точку входу вашої програми. Наприклад, якщо ви хочете обробити і версіонувати всі зображення, що зберігаються в `resources/images`, і всі шрифти, що зберігаються в `resources/fonts`, вам слід додати наступне в точку входу вашого додатка `resources/js/app.js`:

```js
import.meta.glob([
  '../images/**',
  '../fonts/**',
]);
```

Тепер ці ресурси будуть оброблятися Vite під час запуску `npm run build`. Потім ви можете посилатися на ці ресурси в шаблонах Blade, використовуючи метод `Vite::asset`, який поверне пронумерований URL для зазначеного ресурсу:

```blade
<img src="{{ Vite::asset('resources/images/logo.png') }}">
```

<a name="blade-refreshing-on-save"></a>
### Оновлення під час збереження

Коли ваш застосунок побудовано з використанням традиційного візуалізації на стороні сервера за допомогою Blade, Vite може поліпшити ваш робочий процес розробки, автоматично оновлюючи браузер при внесенні змін до файлів подань вашого застосунку. Щоб почати, просто вкажіть параметр `refresh` як `true`.
```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // ...
            refresh: true,
        }),
    ],
});
```

Коли параметр `refresh` встановлений в `true`, збереження файлів в наступних каталогах викличе повне оновлення сторінки в браузері при виконанні команди `npm run dev`:

- `app/Livewire/**`
- `app/View/Components/**`
- `lang/**`
- `resources/lang/**`
- `resources/views/**`
- `routes/**`

Відстеження каталогу `routes/**` корисно, якщо ви використовуєте [Ziggy](https://github.com/tighten/ziggy) для створення посилань на маршрути у фронтенді вашого додатка.

Якщо ці стандартні шляхи не відповідають вашим потребам, ви можете вказати власний список шляхів для відстеження:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // ...
            refresh: ['resources/views/**'],
        }),
    ],
});
```

В основі плагіна Laravel Vite використовується пакет [`vite-plugin-full-reload`](https://github.com/ElMassimo/vite-plugin-full-reload), який пропонує деякі додаткові параметри конфігурації для налаштування поведінки цієї функції. Якщо вам потрібен такий рівень налаштування, ви можете надати визначення `config`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // ...
            refresh: [{
                paths: ['path/to/watch/**'],
                config: { delay: 300 }
            }],
        }),
    ],
});
```

<a name="blade-aliases"></a>
### Псевдоніми

Часто в JavaScript-додатках створюють [псевдоніми](#aliases) для часто використовуваних каталогів. Однак, ви також можете створювати псевдоніми для використання в Blade, використовуючи метод `macro` класу `Illuminate\Support\Facades\Vite`. Зазвичай «макроси» визначаються в методі `boot` [сервіс-провайдера](/docs/{{version}}/providers):

    /**
     * Завантажуйте будь-які сервіси додатків.
     */
    public function boot(): void
    {
        Vite::macro('image', fn (string $asset) => $this->asset("resources/images/{$asset}"));
    }

Після визначення макроса його можна викликати у ваших шаблонах. Наприклад, ми можемо використовувати визначений вище макрос `image`, щоб посилатися на ресурс, розташований за шляхом `resources/images/logo.png`:

```blade
<img src="{{ Vite::image('logo.png') }}" alt="Laravel Logo">
```

<a name="asset-prefetching"></a>
## Попередня вибірка активів

При створенні SPA з використанням функції поділу коду Vite необхідні ресурси витягуються під час навігації по кожній сторінці. Така поведінка може призвести до затримки рендерингу користувацького інтерфейсу. Якщо це проблема для обраного вами середовища зовнішнього інтерфейсу, Laravel пропонує можливість попереднього завантаження ресурсів JavaScript і CSS вашої програми під час початкового завантаження сторінки.

Ви можете доручити Laravel виконати попередню вибірку ваших ресурсів, викликавши метод `Vite::prefetch` у методі `boot` [постачальника послуг](/docs/{{version}}/providers):

```php
<?php
namespace App\Providers;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);
    }
}
```

У наведеному вище прикладі ресурси будуть попередньо завантажені з максимум `3` одночасними завантаженнями при кожному завантаженні сторінки. Ви можете змінити паралелізм відповідно до потреб вашого додатка або не вказувати обмеження паралелізму, якщо додаток повинен завантажувати всі ресурси одночасно:

```php
/**
 * Завантажуйте будь-які сервіси додатків.
 */
public function boot(): void
{
    Vite::prefetch();
}
```

За замовчуванням попередня вибірка починається при виникненні події [_load_](https://developer.mozilla.org/en-US/docs/Web/API/Window/load_event). Якщо ви хочете налаштувати час початку попереднього завантаження, ви можете вказати подію, яку Vite буде прослуховувати.

```php
/**
 * Завантажуйте будь-які сервіси додатків.
 */
public function boot(): void
{
    Vite::prefetch(event: 'vite:prefetch');
}
```

З огляду на наведений вище код, попередня вибірка тепер почнеться, коли ви вручну надішлете подію `vite:prefetch` для об'єкта `window`. Наприклад, ви можете почати попереднє завантаження через три секунди після завантаження сторінки:

```html
<script>
    addEventListener('load', () => setTimeout(() => {
        dispatchEvent(new Event('vite:prefetch'))
    }, 3000))
</script>
```

<a name="custom-base-urls"></a>
## Користувацькі базові URL

Якщо ваші скомпільовані ресурси Vite розгорнуті на домені, відмінному від вашого додатка, наприклад, через CDN, ви маєте вказати змінну оточення `ASSET_URL` у файлі `.env` вашого додатка:

```env
ASSET_URL=https://cdn.example.com
```

Після налаштування URL ресурсу в початок усіх переписаних URL-адрес ваших ресурсів буде додано вказане значення:

```nothing
https://cdn.example.com/build/assets/app.9dce8d17.js
```

Пам'ятайте, що [абсолютні URL-адреси не переписуються Vite](#url-processing), тому вони не будуть змінені.

<a name="environment-variables"></a>
## Змінні середовища

Ви можете впровадити змінні середовища у ваш JavaScript, додавши їм префікс `VITE_` у файлі `.env` вашої програми:

```env
VITE_SENTRY_DSN_PUBLIC=http://example.com
```

Ви можете отримати доступ до впроваджених змінних середовища через об'єкт `import.meta.env`:

```js
import.meta.env.VITE_SENTRY_DSN_PUBLIC
```

<a name="disabling-vite-in-tests"></a>
## Вимкнення Vite у тестах

Інтеграція Vite в Laravel намагатиметься дозволити ваші ресурси під час виконання ваших тестів, що вимагає запуску сервера розробки Vite або збірки ваших ресурсів.

Якщо ви віддаєте перевагу використанню імітації Vite під час тестування, ви можете викликати метод `withoutVite`, який доступний для всіх тестів, що розширюють клас `TestCase` Laravel:

```php tab=Pest
test('without vite example', function () {
    $this->withoutVite();
    // ...
});
```

```php tab=PHPUnit
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_without_vite_example(): void
    {
        $this->withoutVite();

        // ...
    }
}
```

Якщо ви хочете відключити Vite для всіх тестів, ви можете викликати метод `withoutVite` з методу `setUp` вашого базового класу `TestCase`:

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void// [tl! add:start]
    {
        parent::setUp();

        $this->withoutVite();
    }// [tl! add:end]
}
```

<a name="ssr"></a>
## Рендеринг на стороні сервера (SSR)

Плагін Laravel Vite полегшує налаштування рендерингу на стороні сервера з використанням Vite. Щоб почати, створіть точку входу SSR в `resources/js/ssr.js` і вкажіть її в плагіні Laravel, передавши конфігураційну опцію:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
        }),
    ],
});
```

Щоб не забути перебудувати точку входу SSR, ми рекомендуємо змінити скрипт «build» у файлі `package.json` вашого застосунку для створення збірки SSR:

```json
"scripts": {
     "dev": "vite",
     "build": "vite build" // [tl! remove]
     "build": "vite build && vite build --ssr" // [tl! add]
}
```

Потім, щоб зібрати і запустити сервер SSR, ви можете виконати такі команди:

```sh
npm run build
node bootstrap/ssr/ssr.js
```

Якщо ви використовуєте [SSR з Inertia](https://inertiajs.com/server-side-rendering), ви можете замість цього використовувати команду Artisan `inertia:start-ssr` для запуску сервера SSR:

```sh
php artisan inertia:start-ssr
```

> [!NOTE]  
> Стартові набори Laravel ([starter kits](/docs/{{version}}}/starter-kits)) вже включають правильну конфігурацію Laravel, SSR Inertia і Vite. Ознайомтеся з [Laravel Breeze](/docs/{{{version}}}/starter-kits#breeze-and-inertia) для найшвидшого способу почати роботу з Laravel, SSR Inertia і Vite.

<a name="script-and-style-attributes"></a>
## Атрибути тегів Style b Script

<a name="content-security-policy-csp-nonce"></a>
### Політика безпеки вмісту (CSP) Nonce

Якщо ви хочете ввімкнути атрибут [`nonce`](https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/nonce) у ваших тегах script і style як частину [ політики безпеки контенту (Content Security Policy)](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP), ви можете згенерувати або вказати nonce, використовуючи метод `useCspNonce` всередині власного [посередників](/docs/{{version}}}/middleware):

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class AddContentSecurityPolicyHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        return $next($request)->withHeaders([
            'Content-Security-Policy' => "script-src 'nonce-".Vite::cspNonce()."'",
        ]);
    }
}
```

Після виклику методу `useCspNonce`, Laravel автоматично включить атрибути `nonce` в усі згенеровані теги script і style.

Якщо вам потрібно вказати nonce в іншому місці, включно з [директивою `@route` Ziggy](https://github.com/tighten/ziggy#using-routes-with-a-content-security-policy), що входить до стартових наборів Laravel, ви можете зробити це, використовуючи метод `cspNonce`:
```blade
@routes(nonce: Vite::cspNonce())
```

Якщо у вас вже є nonce, який ви хотіли б використовувати, ви можете передати його методу `useCspNonce`:

```php
Vite::useCspNonce($nonce);
```

<a name="subresource-integrity-sri"></a>
### Subresource Integrity (SRI) (Цілісність підресурсів)

Якщо ваш маніфест Vite включає хеші `integrity` для ваших ресурсів, Laravel автоматично додасть атрибут `integrity` до всіх тегів script і style, які він генерує, щоб забезпечити [цілісність підресурсів](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity). За замовчуванням Vite не включає хеш `integrity` у свій маніфест, але ви можете ввімкнути його, встановивши плагін [`vite-plugin-manifest-sri`](https://www.npmjs.com/package/vite-plugin-manifest-sri) з NPM:
```shell
npm install --save-dev vite-plugin-manifest-sri
```

Потім ви можете включити цей плагін у вашому файлі `vite.config.js`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import manifestSRI from 'vite-plugin-manifest-sri';// [tl! add]

export default defineConfig({
    plugins: [
        laravel({
            // ...
        }),
        manifestSRI(),// [tl! add]
    ],
});
```

За необхідності ви також можете налаштувати ключ маніфесту, де міститиметься хеш цілісності:

```php
use Illuminate\Support\Facades\Vite;

Vite::useIntegrityKey('custom-integrity-key');
```

Якщо ви хочете повністю вимкнути це автовиявлення, ви можете передати `false` методу `useIntegrityKey`:

```php
Vite::useIntegrityKey(false);
```

<a name="arbitrary-attributes"></a>
### Довільні атрибути

Якщо вам потрібно додати додаткові атрибути до ваших тегів script і style, такі як атрибут [`data-turbo-track`](https://turbo.hotwired.dev/handbook/drive#reloading-when-assets-change), ви можете вказати їх за допомогою методів `useScriptTagAttributes` і `useStyleTagAttributes`. Зазвичай ці методи викликаються з [сервіс-провайдера](/docs/{{version}}/providers):

```php
use Illuminate\Support\Facades\Vite;

Vite::useScriptTagAttributes([
    'data-turbo-track' => 'reload', // Вказати значення для атрибуту...
    'async' => true, // Вказати атрибут без значення...
    'integrity' => false, // Виключити атрибут, який в іншому випадку буде включено...
]);

Vite::useStyleTagAttributes([
    'data-turbo-track' => 'reload',
]);
```

Якщо вам потрібно використовувати умову для додавання атрибутів, ви можете передати зворотний виклик, який отримуватиме вихідний шлях ресурсу, його URL, його фрагмент маніфесту і весь маніфест:

```php
use Illuminate\Support\Facades\Vite;

Vite::useScriptTagAttributes(fn (string $src, string $url, array|null $chunk, array|null $manifest) => [
    'data-turbo-track' => $src === 'resources/js/app.js' ? 'reload' : false,
]);

Vite::useStyleTagAttributes(fn (string $src, string $url, array|null $chunk, array|null $manifest) => [
    'data-turbo-track' => $chunk && $chunk['isEntry'] ? 'reload' : false,
]);
```

> [!WARNING]  
> Аргументи `$chunk` і `$manifest` дорівнюватимуть `null`, якщо сервер розробки Vite запущено.

<a name="advanced-customization"></a>
## Розширене налаштування

За замовчуванням плагін Vite Laravel використовує угоди, які повинні підходити для більшості додатків; однак іноді вам може знадобитися налаштувати поведінку Vite. Для активації додаткових параметрів налаштування ми пропонуємо такі методи та параметри, які можуть використовуватися замість директиви Blade `@vite`:

```blade
<!doctype html>
<head>
    {{-- ... --}}

    {{
        Vite::useHotFile(storage_path('vite.hot')) // Customize the "hot" file...
            ->useBuildDirectory('bundle') // Customize the build directory...
            ->useManifestFilename('assets.json') // Customize the manifest filename...
            ->withEntryPoints(['resources/js/app.js']) // Specify the entry points...
            ->createAssetPathsUsing(function (string $path, ?bool $secure) { // Налаштуйте внутрішню генерацію шляхів для створених активів.
                return "https://cdn.example.com/{$path}";
            })
    }}
</head>
```

Потім у файлі `vite.config.js` ви повинні вказати ту саму конфігурацію:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            hotFile: 'storage/vite.hot', // Налаштування «гарячого» файлу...
            buildDirectory: 'bundle', // Налаштуйте каталог збірки...
            input: ['resources/js/app.js'], // Вкажіть точки входу...
        }),
    ],
    build: {
      manifest: 'assets.json', // Налаштуйте ім'я файлу маніфесту...
    },
});
```

<a name="correcting-dev-server-urls"></a>
### Корекція URL-адрес сервера розробки

Деякі плагіни в екосистемі Vite припускають, що URL-адреси, які починаються з косої риски, завжди вказуватимуть на сервер розробки Vite. Однак через характер інтеграції з Laravel це не так.

Наприклад, плагін `vite-imagetools` виводить URL-адреси, подібні до таких, поки Vite обслуговує ваші ресурси:

```html
<img src="/@imagetools/f0b2f404b13f052c604e632f2fb60381bf61a520">
```

Плагін `vite-imagetools` очікує, що вихідний URL буде перехоплений Vite, і потім плагін зможе обробляти всі URL-адреси, які починаються з `/@imagetools`. Якщо ви використовуєте плагіни, які очікують на таку поведінку, вам доведеться вручну скоригувати URL-адреси. Ви можете зробити це у вашому файлі `vite.config.js`, використовуючи опцію `transformOnServe`.

У цьому конкретному прикладі ми додамо префікс URL сервера розробки до всіх входжень `/@imagetools` у згенерованому коді:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { imagetools } from 'vite-imagetools';

export default defineConfig({
    plugins: [
        laravel({
            // ...
            transformOnServe: (code, devServerUrl) => code.replaceAll('/@imagetools', devServerUrl+'/@imagetools'),
        }),
        imagetools(),
    ],
});
```

Тепер, коли Vite обслуговує ресурси, він буде виводити URL-адреси, що вказують на сервер розробки Vite:

```html
- <img src="/@imagetools/f0b2f404b13f052c604e632f2fb60381bf61a520"><!-- [tl! remove] -->
+ <img src="http://[::1]:5173/@imagetools/f0b2f404b13f052c604e632f2fb60381bf61a520"><!-- [tl! add] -->
```
