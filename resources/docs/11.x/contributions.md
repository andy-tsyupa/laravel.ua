# Рекомендації щодо участі

- [Звіти про помилки](#bug-reports)
- [Питання до служби підтримки](#support-questions)
- [Обговорення основних питань розвитку](#core-development-discussion)
- [Яке відділення?](#which-branch)
- [Складені активи](#compiled-assets)
- [Вразливості безпеки](#security-vulnerabilities)
- [Стиль кодування](#coding-style)
    - [PHPDoc](#phpdoc)
    - [StyleCI](#styleci)
- [Кодекс поведінки](#code-of-conduct)

<a name="bug-reports"></a>
## Звіти про помилки

Щоб заохотити активну співпрацю, Laravel наполегливо рекомендує надсилати запити, а не тільки повідомлення про помилки. Pull-запити будуть розглянуті тільки тоді, коли вони будуть позначені як "готові до розгляду" (а не в стані "чернетка") і пройдуть всі тести для нових функцій. Затяжні, неактивні запити, залишені в стані "чернетка", будуть закриті через кілька днів.

Однак, якщо ви створюєте звіт про помилку, ваша проблема повинна містити назву і чіткий опис проблеми. Ви також повинні додати якомога більше релевантної інформації та приклад коду, який демонструє проблему. Мета повідомлення про ваду полягає в тому, щоб полегшити собі - і іншим - відтворення помилки і розробку виправлення.

Пам'ятайте, що повідомлення про вади створюються в надії на те, що інші користувачі з такою ж проблемою зможуть співпрацювати з вами над її вирішенням. Не сподівайтеся, що звіт про помилку автоматично побачить будь-яку активність або що інші люди поспішать її виправити. Створення повідомлення про ваду слугує для того, щоб допомогти собі та іншим стати на шлях виправлення проблеми. Якщо ви хочете зробити свій внесок, ви можете допомогти, виправивши [будь-які вади, перелічені в наших трекерах випусків](https://github.com/issues?q=is%3Aopen+is%3Aissue+label%3Abug+user%3Alaravel). Ви повинні бути авторизовані на GitHub, щоб переглядати всі проблеми Laravel.

Якщо ви помітили неправильні попередження DocBlock, PHPStan або IDE під час використання Laravel, не створюйте проблему на GitHub. Замість цього, будь ласка, надішліть запит для виправлення проблеми.

Вихідний код Laravel управляється на GitHub, і там є репозиторії для кожного з проектів Laravel:

<div class="content-list" markdown="1">

- [Laravel Application](https://github.com/laravel/laravel)
- [Laravel Art](https://github.com/laravel/art)
- [Laravel Documentation](https://github.com/laravel/docs)
- [Laravel Dusk](https://github.com/laravel/dusk)
- [Laravel Cashier Stripe](https://github.com/laravel/cashier)
- [Laravel Cashier Paddle](https://github.com/laravel/cashier-paddle)
- [Laravel Echo](https://github.com/laravel/echo)
- [Laravel Envoy](https://github.com/laravel/envoy)
- [Laravel Folio](https://github.com/laravel/folio)
- [Laravel Framework](https://github.com/laravel/framework)
- [Laravel Homestead](https://github.com/laravel/homestead)
- [Laravel Homestead Build Scripts](https://github.com/laravel/settler)
- [Laravel Horizon](https://github.com/laravel/horizon)
- [Laravel Jetstream](https://github.com/laravel/jetstream)
- [Laravel Passport](https://github.com/laravel/passport)
- [Laravel Pennant](https://github.com/laravel/pennant)
- [Laravel Pint](https://github.com/laravel/pint)
- [Laravel Prompts](https://github.com/laravel/prompts)
- [Laravel Reverb](https://github.com/laravel/reverb)
- [Laravel Sail](https://github.com/laravel/sail)
- [Laravel Sanctum](https://github.com/laravel/sanctum)
- [Laravel Scout](https://github.com/laravel/scout)
- [Laravel Socialite](https://github.com/laravel/socialite)
- [Laravel Telescope](https://github.com/laravel/telescope)
- [Laravel Website](https://github.com/laravel/laravel.com-next)

</div>

<a name="support-questions"></a>
## Питання до служби підтримки

Трекери проблем Laravel на GitHub не призначені для надання допомоги або підтримки Laravel. Замість цього використовуйте один з наступних каналів:

<div class="content-list" markdown="1">

- [GitHub Discussions](https://github.com/laravel/framework/discussions)
- [Laracasts Forums](https://laracasts.com/discuss)
- [Laravel.io Forums](https://laravel.io/forum)
- [StackOverflow](https://stackoverflow.com/questions/tagged/laravel)
- [Discord](https://discord.gg/laravel)
- [Larachat](https://larachat.co)
- [IRC](https://web.libera.chat/?nick=artisan&channels=#laravel)

</div>

<a name="core-development-discussion"></a>
## Обговорення основних питань розвитку

Ви можете запропонувати нові можливості або покращення існуючої поведінки фреймворку Laravel у репозиторії фреймворку Laravel на [дискусійній дошці GitHub](https://github.com/laravel/framework/discussions). Якщо ви пропонуєте нову функцію, будь ласка, будьте готові реалізувати хоча б частину коду, необхідного для її завершення.

Неформальне обговорення помилок, нових можливостей та реалізації існуючих можливостей відбувається на каналі `#internals` на [Laravel Discord server](https://discord.gg/laravel). Тейлор Отвелл (Taylor Otwell), супровідник Laravel, зазвичай присутній на каналі в робочі дні з 8 ранку до 5 вечора (UTC-06:00 або Америка/Чикаго), а також спорадично присутній на каналі в інший час.

<a name="which-branch"></a>
## Яке відділення?

**Всі** виправлення слід надсилати до останньої версії, яка підтримує виправлення (наразі `10.x`). Виправлення вад **ніколи** не слід надсилати до гілки `master`, якщо вони не виправляють можливості, які існують лише у наступному випуску.

До останньої стабільної гілки (наразі `11.x`) можуть бути перенесені **незначні** функції, які **повністю сумісні з поточним випуском**.

**Мажорні** нові можливості або можливості з суттєвими змінами завжди слід надсилати до гілки `master`, яка містить майбутній випуск.

<a name="compiled-assets"></a>
## Складені активи

Якщо ви надсилаєте зміни, які вплинуть на скомпільовані файли, такі як більшість файлів у `resources/css` або `resources/js` сховища `laravel/laravel`, не фіксуйте скомпільовані файли. Через їхній великий розмір вони не можуть бути реально переглянуті супровідником. Це може бути використано для впровадження шкідливого коду у Laravel. Щоб запобігти цьому, всі скомпільовані файли генеруються і фіксуються супровідниками Laravel.

<a name="security-vulnerabilities"></a>
## Вразливості безпеки

Якщо ви виявили уразливість в Laravel, будь ласка, надішліть листа Тейлору Отвеллу (Taylor Otwell) на <a href="mailto:taylor@laravel.com">taylor@laravel.com</a>. Всі вразливості безпеки будуть негайно усунені.

<a name="coding-style"></a>
## Стиль кодування

Laravel дотримується стандарту кодування [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) та стандарту автозавантаження [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md).

<a name="phpdoc"></a>
### PHPDoc

Нижче наведено приклад правильного блоку документації Laravel. Зверніть увагу, що після атрибута `@param` йдуть два пробіли, тип аргументу, ще два пробіли і, нарешті, ім'я змінної:

    /**
     * Зареєструйте прив'язку до контейнера.
     *
     * @param  string|array  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     *
     * @throws \Exception
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        // ...
    }

Якщо атрибути `@param` або `@return` є надлишковими через використання власних типів, їх можна вилучити:

    /**
     * Виконуй роботу.
     */
    public function handle(AudioProcessor $processor): void
    {
        //
    }

Однак, якщо власний тип є узагальненим, будь ласка, вкажіть узагальнений тип за допомогою атрибутів `@param` або `@return`:

    /**
     * Отримайте вкладення до повідомлення.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromStorage('/path/to/file'),
        ];
    }

<a name="styleci"></a>
### StyleCI

Не хвилюйтеся, якщо ваш код не ідеальний! [StyleCI](https://styleci.io/) автоматично об'єднає всі виправлення стилю в репозиторій Laravel після об'єднання запитів на витягування. Це дозволяє нам зосередитися на змісті внеску, а не на стилі коду.

<a name="code-of-conduct"></a>
## Кодекс поведінки

Кодекс поведінки Laravel походить від кодексу поведінки Ruby. Про будь-які порушення кодексу поведінки можна повідомляти Тейлору Отвеллу (taylor@laravel.com):

<div class="content-list" markdown="1">

- Учасники толерантно ставитимуться до протилежних поглядів.
- Учасники повинні переконатися, що їхня мова і дії вільні від особистих нападок і зневажливих особистих зауважень.
- Інтерпретуючи слова і дії інших, учасники завжди повинні виходити з добрих намірів.
- Поведінка, яку можна обґрунтовано вважати переслідуванням, буде неприпустимою.

</div>
