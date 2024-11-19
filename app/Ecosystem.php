<?php

namespace App;

final class Ecosystem
{
    public static function featured(): array
    {
        return [
            'forge' => [
                'name' => 'Forge',
                'image-alt' => 'Forge Logo',
                'description' => 'Управління сервером не повинно бути кошмаром. Надавайте та розгортайте необмежену кількість PHP-додатків на DigitalOcean, Linode, Vultr, Amazon, Hetzner тощо.',
                'href' => 'https://forge.laravel.com',
            ],
            'vapor' => [
                'name' => 'Vapor',
                'image-alt' => 'Vapor Logo',
                'description' => 'Laravel Vapor - це безсерверна платформа для розгортання Laravel на базі AWS. Запустіть свою інфраструктуру Laravel на Vapor і полюбите масштабовану простоту безсерверної системи.',
                'href' => 'https://vapor.laravel.com',
            ],
        ];
    }

    public static function items(): array
    {
        return [
            'breeze' => [
                'name' => 'Breeze',
                'image-alt' => 'Laravel Breeze Logo Logo',
                'description' => 'Полегшені риштування стартового набору для нових застосувань з лезами або інерційними риштуваннями.',
                'href' => '/docs/' . DEFAULT_VERSION . '/starter-kits#laravel-breeze',
            ],
            'cashier' => [
                'name' => 'Cashier',
                'image-alt' => 'Laravel Cashier Logo',
                'description' => 'Позбудьтеся болю в управлінні підписками на Stripe або Paddle.',
                'href' => '/docs/' . DEFAULT_VERSION . '/billing',
            ],
            'dusk' => [
                'name' => 'Dusk',
                'image-alt' => 'Laravel Dusk Logo',
                'description' => 'Автоматизоване тестування браузерів для впевненої відправки вашого додатку.',
                'href' => '/docs/' . DEFAULT_VERSION . '/dusk',
            ],
            'echo' => [
                'name' => 'Echo',
                'image-alt' => 'Laravel Echo Logo',
                'description' => 'Слухайте події WebSocket, що транслюються вашим додатком Laravel.',
                'href' => '/docs/' . DEFAULT_VERSION . '/broadcasting',
            ],
            'envoyer' => [
                'name' => 'Envoyer',
                'image-alt' => 'Envoyer Logo',
                'description' => 'Розгортайте свої Laravel-додатки для клієнтів з нульовим часом простою.',
                'href' => 'https://envoyer.io',
            ],
            'forge' => [
                'name' => 'Forge',
                'image-alt' => 'Forge Logo',
                'description' => 'Управління сервером не повинно бути кошмаром.',
                'href' => 'https://forge.laravel.com',
            ],
            'herd' => [
                'name' => 'Herd',
                'image-alt' => 'Herd Logo',
                'description' => 'Найшвидше локальне середовище розробки Laravel - тепер для macOS і Windows.',
                'href' => 'https://herd.laravel.com',
            ],
            'horizon' => [
                'name' => 'Horizon',
                'image-alt' => 'Laravel Horizon Logo',
                'description' => 'Гарний інтерфейс для моніторингу ваших черг Laravel, керованих Redis.',
                'href' => '/docs/' . DEFAULT_VERSION . '/horizon',
            ],
            'inertia' => [
                'name' => 'Inertia',
                'image-alt' => 'Inertia Logo',
                'description' => 'Створюйте сучасні односторінкові додатки React та Vue, використовуючи класичну серверну маршрутизацію.',
                'href' => 'https://inertiajs.com',
            ],
            'jetstream' => [
                'name' => 'Jetstream',
                'image-alt' => 'Laravel Jetstream Logo',
                'description' => 'Надійний стартовий набір, що включає автентифікацію та управління командою.',
                'href' => 'https://jetstream.laravel.com',
            ],
            'livewire' => [
                'name' => 'Livewire',
                'image-alt' => 'Laravel Livewire Logo',
                'description' => 'Створюйте реактивні, динамічні додатки за допомогою Laravel та Blade.',
                'href' => 'https://livewire.laravel.com',
            ],
            'nova' => [
                'name' => 'Nova',
                'image-alt' => 'Laravel Nova Logo',
                'description' => 'Продумана панель адміністрування для ваших Laravel-додатків.',
                'href' => 'https://nova.laravel.com',
            ],
            'octane' => [
                'name' => 'Octane',
                'image-alt' => 'Laravel Octane Logo',
                'description' => 'Підвищуйте продуктивність вашої програми, зберігаючи її в пам\'яті.',
                'href' => '/docs/' . DEFAULT_VERSION . '/octane',
            ],
            'pennant' => [
                'name' => 'Pennant',
                'image-alt' => 'Laravel Pennant Logo',
                'description' => 'Проста, легка бібліотека для керування прапорами функцій.',
                'href' => '/docs/' . DEFAULT_VERSION . '/pennant',
            ],
            'pint' => [
                'name' => 'Pint',
                'image-alt' => 'Laravel Pint Logo',
                'description' => 'Виправник стилю PHP-коду для мінімалістів.',
                'href' => '/docs/' . DEFAULT_VERSION . '/pint',
            ],
            'prompts' => [
                'name' => 'Prompts',
                'image-alt' => 'Laravel Prompts Logo',
                'description' => 'Красиві та зручні форми для додатків командного рядка.',
                'href' => '/docs/' . DEFAULT_VERSION . '/prompts',
            ],
            'pulse' => [
                'name' => 'Pulse',
                'image-alt' => 'Laravel Pulse Logo',
                'description' => 'Миттєва інформація про продуктивність та використання вашого додатку.',
                'href' => 'https://pulse.laravel.com',
            ],
            'reverb' => [
                'name' => 'Reverb',
                'image-alt' => 'Laravel Reverb Logo',
                'description' => 'Швидкі та масштабовані WebSockets для ваших додатків.',
                'href' => 'https://reverb.laravel.com',
            ],
            'sail' => [
                'name' => 'Sail',
                'image-alt' => 'Laravel Sail Logo',
                'description' => 'Власноруч розроблений досвід локальної розробки Laravel з використанням Docker.',
                'href' => '/docs/' . DEFAULT_VERSION . '/sail',
            ],
            'sanctum' => [
                'name' => 'Sanctum',
                'image-alt' => 'Laravel Sanctum Logo',
                'description' => 'API та автентифікація мобільних додатків без необхідності рвати на собі волосся.',
                'href' => '/docs/' . DEFAULT_VERSION . '/sanctum',
            ],
            'scout' => [
                'name' => 'Scout',
                'image-alt' => 'Laravel Scout Logo',
                'description' => 'Блискавичний повнотекстовий пошук моделей Eloquent для вашої програми.',
                'href' => '/docs/' . DEFAULT_VERSION . '/scout',
            ],
            'socialite' => [
                'name' => 'Socialite',
                'image-alt' => 'Laravel Socialite Logo',
                'description' => 'Соціальна автентифікація через Facebook, Twitter, GitHub, LinkedIn тощо.',
                'href' => '/docs/' . DEFAULT_VERSION . '/socialite',
            ],
            'spark' => [
                'name' => 'Spark',
                'image-alt' => 'Laravel Spark Logo',
                'description' => 'Розпочніть свій наступний бізнес за допомогою нашого повнофункціонального порталу для виставлення рахунків.',
                'href' => 'https://spark.laravel.com',
            ],
            'telescope' => [
                'name' => 'Telescope',
                'image-alt' => 'Laravel Telescope Logo',
                'description' => 'Налагоджуйте свій додаток за допомогою нашого інтерфейсу налагодження та інсайту.',
                'href' => '/docs/' . DEFAULT_VERSION . '/telescope',
            ],
            'vapor' => [
                'name' => 'Vapor',
                'image-alt' => 'Laravel Vapor Logo',
                'description' => 'Laravel Vapor - це безсерверна платформа для розгортання Laravel на базі AWS.',
                'href' => 'https://vapor.laravel.com',
            ]
        ];
    }
}
