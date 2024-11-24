# Стартові шаблони

- [Вступ](#introduction)
- [Laravel Breeze](#laravel-breeze)
    - [Встановлення](#laravel-breeze-installation)
    - [Breeze і Blade](#breeze-and-blade)
    - [Breeze і Livewire](#breeze-and-livewire)
    - [Breeze і React / Vue](#breeze-and-inertia)
    - [Breeze і Next.js / API](#breeze-and-next)
- [Laravel Jetstream](#laravel-jetstream)

<a name="introduction"></a>
## Вступ

Щоб дати вам старт у створенні вашого нового додатку на Laravel, ми раді запропонувати стартові набори для автентифікації та створення додатків. Ці набори автоматично створюють ваш додаток з маршрутами, контролерами та поданнями, необхідними для реєстрації та автентифікації користувачів вашого додатку.

Хоча ви можете використовувати ці стартові набори, вони не є обов'язковими. Ви можете створити свій власний додаток з нуля, просто встановивши свіжу копію Laravel. У будь-якому випадку, ми знаємо, що ви створите щось чудове!

<a name="laravel-breeze"></a>
## Laravel Breeze

[Laravel Breeze](https://github.com/laravel/breeze) є мінімальною, простою реалізацією всіх можливостей Laravel [автентифікації](/docs/{{version}}/authentication), включаючи вхід, реєстрацію, скидання пароля, перевірку електронної пошти та підтвердження пароля. Крім того, Breeze включає просту сторінку "профілю", де користувач може оновити своє ім'я, адресу електронної пошти та пароль.

Шар перегляду за замовчуванням у Laravel Breeze складається з простих [Blade шаблонів](/docs/{{version}}/blade) стилізовано під [Tailwind CSS](https://tailwindcss.com). Крім того, Breeze пропонує варіанти риштування на основі [Livewire](https://livewire.laravel.com) або [Inertia](https://inertiajs.com), з можливістю використання Vue або React для інерційного риштування.

<img src="https://laravel.com/img/docs/breeze-register.png">

#### Laravel Bootcamp

Якщо ви новачок в Laravel, сміливо переходьте в розділ [Laravel Bootcamp](https://bootcamp.laravel.com). Laravel Bootcamp допоможе вам створити свій перший додаток Laravel за допомогою Breeze. Це чудовий спосіб ознайомитися з усім, що можуть запропонувати Laravel та Breeze.

<a name="laravel-breeze-installation"></a>
### Встановлення

По-перше, ви повинні [створити новий Laravel-додаток](/docs/{{version}}/installation). Якщо ви створюєте свій додаток з використанням [Інсталятор Laravel](/docs/{{version}}/installation#creating-a-laravel-project) вам буде запропоновано встановити Laravel Breeze під час процесу інсталяції. В іншому випадку вам потрібно буде слідувати інструкціям по встановленню, наведеним нижче.

Якщо ви вже створили новий додаток Laravel без стартового набору, ви можете встановити Laravel Breeze вручну за допомогою Composer:

```shell
composer require laravel/breeze --dev
```

Після того, як Composer встановив пакунок Laravel Breeze, вам слід виконати команду `breeze:install` Artisan. Ця команда публікує подання автентифікації, маршрути, контролери та інші ресурси у вашому додатку. Laravel Breeze публікує весь свій код у вашому додатку, так що ви маєте повний контроль і видимість над його можливостями і реалізацією.

Команда `breeze:install` запитає вас про бажаний стек інтерфейсу та фреймворк для тестування:

```shell
php artisan breeze:install

php artisan migrate
npm install
npm run dev
```

<a name="breeze-and-blade"></a>
### Breeze і Blade

"Стеком" Breeze за замовчуванням є стек Blade, який використовує прості [Blade templates](/docs/{{version}}/blade) для рендерингу інтерфейсу вашої програми. Стек Blade можна встановити, викликавши команду `breeze:install` без інших додаткових аргументів і вибравши стек інтерфейсу Blade. Після встановлення стеку Breeze вам також слід скомпілювати ресурси інтерфейсу вашої програми:

```shell
php artisan breeze:install

php artisan migrate
npm install
npm run dev
```

Далі ви можете перейти на URL-адреси `/login` або `/register` вашого додатку у вашому веб-браузері. Всі маршрути Breeze визначені у файлі `routes/auth.php`.

> [!NOTE]  
> Щоб дізнатися більше про компіляцію CSS і JavaScript вашого додатка, перегляньте статтю Laravel [Vite документацію](/docs/{{version}}/vite#running-vite).

<a name="breeze-and-livewire"></a>
### Breeze і Livewire

Laravel Breeze також пропонує [Livewire](https://livewire.laravel.com) риштування. Livewire - це потужний спосіб створення динамічного, реактивного інтерфейсу користувача, використовуючи лише PHP.

Livewire чудово підходить для команд, які переважно використовують шаблони Blade і шукають простішу альтернативу JavaScript-фреймворкам SPA, таким як Vue та React.

Щоб використовувати стек Livewire, ви можете вибрати стек інтерфейсу Livewire при виконанні команди `breeze:install` Artisan. Після встановлення стеку Breeze вам слід виконати міграцію баз даних:

```shell
php artisan breeze:install

php artisan migrate
```

<a name="breeze-and-inertia"></a>
### Breeze і React / Vue

Laravel Breeze також пропонує риштування React і Vue через [Inertia](https://inertiajs.com) фронтенд-реалізація. Inertia дозволяє створювати сучасні односторінкові React та Vue додатки з використанням класичної серверної маршрутизації та контролерів.

Inertia дозволяє вам насолоджуватися потужністю фронтенду React та Vue у поєднанні з неймовірною продуктивністю бекенду Laravel та блискавичною [Vite](https://vitejs.dev) компіляція. Щоб використовувати стек Inertia, ви можете вибрати стек інтерфейсу Vue або React при виконанні команди `breeze:install` Artisan.

При виборі стека фронтенду Vue або React інсталятор Breeze також запропонує вам визначити, чи бажаєте ви [Inertia SSR](https://inertiajs.com/server-side-rendering) або підтримку TypeScript. Після того, як Breeze буде встановлено, вам також слід скомпілювати інтерфейсні ресурси вашого додатку:

```shell
php artisan breeze:install

php artisan migrate
npm install
npm run dev
```

Далі ви можете перейти на URL-адреси `/login` або `/register` вашого додатку у вашому веб-браузері. Всі маршрути Breeze визначені у файлі `routes/auth.php`.

<a name="breeze-and-next"></a>
### Breeze і Next.js / API

Laravel Breeze також може створити API для автентифікації, який готовий до автентифікації сучасних JavaScript-додатків, таких як ті, що працюють на [Next](https://nextjs.org), [Nuxt](https://nuxt.com), та інші. Щоб почати, виберіть стек API як бажаний стек при виконанні команди `breeze:install` Artisan:

```shell
php artisan breeze:install

php artisan migrate
```

Під час встановлення Breeze додасть змінну середовища `FRONTEND_URL` до файлу `.env` вашої програми. Ця адреса має бути URL-адресою вашої програми на JavaScript. Зазвичай це буде `http://localhost:3000` під час локальної розробки. Крім того, вам слід переконатися, що ваш `APP_URL` має значення `http://localhost:8000`, що є URL за замовчуванням, який використовується командою `erve` Artisan.

<a name="next-reference-implementation"></a>
#### Еталонна реалізація Next.js

Нарешті, ви готові підключити цей бекенд до обраного вами фронтенду. Наступною еталонною реалізацією інтерфейсу Breeze є [доступний на GitHub](https://github.com/laravel/breeze-next). Цей фронтенд підтримується Laravel і містить такий самий користувацький інтерфейс, як і традиційні стеки Blade та Inertia, що надаються Breeze.

<a name="laravel-jetstream"></a>
## Laravel Jetstream

У той час як Laravel Breeze забезпечує просту і мінімальну відправну точку для створення додатків Laravel, Jetstream розширює цю функціональність за рахунок більш надійних функцій і додаткових стеків фронтенд-технологій. **Для тих, хто не знайомий з Laravel, ми рекомендуємо спочатку освоїти Laravel Breeze, перш ніж переходити на Laravel Jetstream.**.

Jetstream надає гарно розроблений каркас додатків для Laravel і включає в себе вхід, реєстрацію, перевірку електронної пошти, двофакторну автентифікацію, управління сесіями, підтримку API через Laravel Sanctum і додаткове управління командами. Jetstream розроблений з використанням [Tailwind CSS](https://tailwindcss.com) і пропонує на вибір [Livewire](https://livewire.laravel.com) або [Inertia](https://inertiajs.com) привідні фасадні риштування.

Повну документацію по встановленню Laravel Jetstream можна знайти в розділі [офіційна документація Jetstream](https://jetstream.laravel.com).
