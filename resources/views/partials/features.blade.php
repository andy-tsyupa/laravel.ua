<x-tabs>
    <x-tabs.tab name="authentication" title="Authentication" icon="lock-closed">
        <p>Автентифікація користувачів здійснюється так само просто, як і додавання проміжного програмного забезпечення для автентифікації до визначення маршруту Laravel:</p>

        <pre><x-torchlight-code language="php">
            Route::get('/profile', ProfileController::class)
                ->middleware('auth');
        </x-torchlight-code></pre>

        <p>Після автентифікації користувача ви можете отримати доступ до автентифікованого користувача через фасад <code>Author</code>:</p>

        <pre><x-torchlight-code language="php">
            use Illuminate\Support\Facades\Auth;

            // Отримати поточного автентифікованого користувача...
            $user = Auth::user();
        </x-torchlight-code></pre>

        <p>Звичайно, ви можете визначити власне проміжне програмне забезпечення для автентифікації, що дозволить вам налаштувати процес автентифікації.</p>

        <p>Для отримання додаткової інформації про функції автентифікації в Laravel зверніться до статті <a href="http://laravel2.loc/docs/authentication">документація з автентифікації</a>.</p>
    </x-tabs.tab>

    <x-tabs.tab name="authorization" title="Authorization" icon="identification">
        <p>Вам часто потрібно перевірити, чи авторизований користувач має право виконувати певну дію. Типові політики Laravel спрощують цю задачу:</p>

        <pre><x-torchlight-code language="bash">
            php artisan make:policy UserPolicy
        </x-torchlight-code></pre>

        <p>Після того, як ви визначили свої правила авторизації у створеному класі політики, ви можете авторизувати запит користувача у методах вашого контролера:</p>

        <pre><x-torchlight-code language="php">
            public function update(Request $request, Invoice $invoice)
            {
                Gate::authorize('update', $invoice);// [tl! focus]

                $invoice->update(/* ... */);
            }
        </x-torchlight-code></pre>

        <p><a href="http://laravel2.loc/docs/authorization">Дізнайтеся більше</a></p>
    </x-tabs.tab>

    <x-tabs.tab name="eloquent" title="Eloquent ORM" icon="table-cells">
        <p>Боїтеся баз даних? Не варто. Eloquent ORM від Laravel дозволяє безболісно взаємодіяти з даними вашого додатку, а моделі, міграції та зв'язки можуть бути швидко створені:</p>

        <pre><x-torchlight-code language="text">
            php artisan make:model Invoice --migration
        </x-torchlight-code></pre>

        <p>Після того, як ви визначили структуру моделі та взаємозв'язки, ви можете взаємодіяти з базою даних, використовуючи потужний, виразний синтаксис Eloquent:</p>

        <pre><x-torchlight-code language="php">
            // Створити пов'язану модель...
            $user->invoices()->create(['amount' => 100]);

            // Оновити модель...
            $invoice->update(['amount' => 200]);

            // Отримати моделі...
            $invoices = Invoice::unpaid()->where('amount', '>=', 100)->get();

            // Розширений API для взаємодії з моделями...
            $invoices->each->pay();
        </x-torchlight-code></pre>

        <p><a href="http://laravel2.loc/docs/eloquent">Дізнайтеся більше</a></p>
    </x-tabs.tab>

    <x-tabs.tab name="migrations" title="Database Migrations" icon="circle-stack">
        <p>Міграції схожі на контроль версій для вашої бази даних, що дозволяє вашій команді визначати та ділитися визначенням схеми бази даних вашого додатку:</p>

        <pre><x-torchlight-code language="php">
            public function up(): void
            {
                Schema::create('flights', function (Blueprint $table) {
                    $table->uuid()->primary();
                    $table->foreignUuid('airline_id')->constrained();
                    $table->string('name');
                    $table->timestamps();
                });
            }
        </x-torchlight-code></pre>

        <p><a href="http://laravel2.loc/docs/migrations">Дізнайтеся більше</a></p>
    </x-tabs.tab>

    <x-tabs.tab name="validation" title="Validation" icon="check-badge">
        <p>Laravel має понад 90 потужних вбудованих правил валідації і, використовуючи Laravel Precognition, може забезпечити валідацію в реальному часі на вашому фронтенді:</p>

        <pre><x-torchlight-code language="php">
            public function update(Request $request)
            {
                $validated = $request->validate([// [tl! focus:start]
                    'email' => 'required|email|unique:users',
                    'password' => Password::required()->min(8)->uncompromised(),
                ]);// [tl! focus:end]

                $request->user()->update($validated);
            }
        </x-torchlight-code></pre>

        <p><a href="http://laravel2.loc/docs/validation">Дізнайтеся більше</a></p>
    </x-tabs.tab>

    <x-tabs.tab name="notifications" title="Notifications & Mail" icon="bell-alert">
        <p>Використовуйте Laravel, щоб швидко надсилати красиво оформлені сповіщення своїм користувачам електронною поштою, Slack, SMS, через додаток тощо:</p>

        <pre><x-torchlight-code language="bash">
            php artisan make:notification InvoicePaid
        </x-torchlight-code></pre>

        <p>Після того, як ви створили сповіщення, ви можете легко надіслати його одному з користувачів вашого додатку:</p>

        <pre><x-torchlight-code language="php">
            $user->notify(new InvoicePaid($invoice));
        </x-torchlight-code></pre>

        <p><a href="http://laravel2.loc/docs/notifications">Дізнайтеся більше</a></p>
    </x-tabs.tab>

    <x-tabs.tab name="storage" title="File Storage" icon="archive-box">
        <p>Laravel забезпечує надійний рівень абстракції файлової системи, надаючи єдиний уніфікований API для взаємодії з локальними файловими системами та хмарними файловими системами, такими як Amazon S3:</p>

        <pre><x-torchlight-code language="php">
            $path = $request->file('avatar')->store('s3');
        </x-torchlight-code></pre>

        <p>Незалежно від того, де зберігаються ваші файли, взаємодійте з ними, використовуючи простий і елегантний синтаксис Laravel:</p>

        <pre><x-torchlight-code language="php">
            $content = Storage::get('photo.jpg');

            Storage::put('photo.jpg', $content);
        </x-torchlight-code></pre>

        <p><a href="http://laravel2.loc/docs/filesystem">Дізнайтеся більше</a></p>
    </x-tabs.tab>

    <x-tabs.tab name="queues" title="Job Queues" icon="queue-list">
        <p>Laravel дозволяє вивантажувати повільні завдання у фонову чергу, зберігаючи швидкість виконання веб-запитів:</p>

        <pre><x-torchlight-code language="php">
            $podcast = Podcast::create(/* ... */);

            ProcessPodcast::dispatch($podcast)->onQueue('podcasts');// [tl! focus]
        </x-torchlight-code></pre>

        <p>Ви можете запустити стільки працівників у черзі, скільки вам потрібно, щоб впоратися з навантаженням:</p>

        <pre><x-torchlight-code language="bash">
            php artisan queue:work redis --queue=podcasts
        </x-torchlight-code></pre>

        <p>Для більшої видимості та контролю над вашими чергами, <a href="http://laravel2.loc/docs/horizon">Laravel Horizon</a> надає красиву інформаційну панель і конфігурацію, керовану кодом, для ваших черг Redis на основі Laravel.</p>

        <p><strong>Дізнайтеся більше</strong></p>
        <ul>
            <li><a href="http://laravel2.loc/docs/queues">Job Queues</a></li>
            <li><a href="http://laravel2.loc/docs/horizon">Laravel Horizon</a></li>
        </ul>
    </x-tabs.tab>

    <x-tabs.tab name="scheduling" title="Task Scheduling" icon="clock">
        <p>Заплануйте повторювані завдання і команди з виразним синтаксисом і попрощайтеся зі складними конфігураційними файлами:</p>

        <pre><x-torchlight-code language="php">
            $schedule->job(NotifySubscribers::class)->hourly();
        </x-torchlight-code></pre>

        <p>Планувальник Laravel може працювати навіть з декількома серверами і пропонує вбудовану функцію запобігання накладок:</p>

        <pre><x-torchlight-code language="php">
            $schedule->job(NotifySubscribers::class)
                ->dailyAt('9:00')
                ->onOneServer()
                ->withoutOverlapping();
        </x-torchlight-code></pre>

        <p><a href="http://laravel2.loc/docs/scheduling">Дізнайтеся більше</a></p>
    </x-tabs.tab>

    <x-tabs.tab name="testing" title="Testing" icon="command-line">
        <p>Laravel створений для тестування. Від модульних тестів до браузерних тестів, ви будете почувати себе більш впевнено при розгортанні вашого додатку:</p>

        <pre><x-torchlight-code language="php">
            $user = User::factory()->create();

            $this->browse(fn (Browser $browser) => $browser
                ->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Login')
                ->assertPathIs('/home')
                ->assertSee("Welcome {$user->name}")
            );
        </x-torchlight-code></pre>

        <p><a href="http://laravel2.loc/docs/testing">Дізнайтеся більше</a></p>
    </x-tabs.tab>

    <x-tabs.tab name="events" title="Events & WebSockets" icon="arrows-right-left">
        <p>Події Laravel дозволяють вам надсилати та прослуховувати події у вашому додатку, а слухачі можуть бути легко відправлені до фонової черги:</p>

        <pre><x-torchlight-code language="php">
            OrderShipped::dispatch($order);
        </x-torchlight-code></pre>

        <pre><x-torchlight-code language="php">
            class SendShipmentNotification implements ShouldQueue
            {
                public function handle(OrderShipped $event): void
                {
                    // ...
                }
            }
        </x-torchlight-code></pre>

        <p>Ваш інтерфейсний додаток може навіть підписатися на події Laravel, використовуючи <a href="http://laravel2.loc/docs/broadcasting">Laravel Echo</a> та WebSockets, що дозволяє створювати динамічні додатки в режимі реального часу:</p>

        <pre><x-torchlight-code language="javascript">
            Echo.private(`orders.${orderId}`)
                .listen('OrderShipped', (e) => {
                    console.log(e.order);
                });
        </x-torchlight-code></pre>

        <p><a href="http://laravel2.loc/docs/events">Дізнайтеся більше</a></p>
    </x-tabs.tab>
</x-tabs>
