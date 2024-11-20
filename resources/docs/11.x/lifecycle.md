# Життєвий цикл запиту

- [Вступ](#introduction)
- [Огляд життєвого циклу](#lifecycle-overview)
    - [Перші кроки](#first-steps)
    - [HTTP / Консольні ядра](#http-console-kernels)
    - [Постачальники послуг](#service-providers)
    - [Маршрутизація](#routing)
    - [Закінчуємо](#finishing-up)
- [Фокус на постачальниках послуг](#focus-on-service-providers)

<a name="introduction"></a>
## Вступ

Використовуючи будь-який інструмент у «реальному світі», ви почуваєтеся впевненіше, якщо розумієте, як він працює. Розробка додатків не є винятком. Коли ви розумієте, як функціонують ваші інструменти розробки, ви відчуваєте себе більш комфортно і впевнено, використовуючи їх.

Мета цього документу - дати вам хороший огляд того, як працює фреймворк Laravel на високому рівні. Познайомившись з фреймворком ближче, все стане менш «магічним», і ви будете більш впевнено створювати свої додатки. Якщо ви не розумієте всіх термінів відразу, не падайте духом! Просто спробуйте отримати базове уявлення про те, що відбувається, і ваші знання будуть зростати в міру того, як ви будете вивчати інші розділи документації.

<a name="lifecycle-overview"></a>
## Огляд життєвого циклу

<a name="first-steps"></a>
### Перші кроки

Точкою входу для всіх запитів до програми Laravel є файл `public/index.php`. Всі запити спрямовуються на цей файл конфігурацією вашого веб-сервера (Apache / Nginx). Файл `index.php` не містить багато коду. Це скоріше відправна точка для завантаження решти фреймворку.

Файл `index.php` завантажує визначення автозавантаження, створене Composer, а потім отримує екземпляр програми Laravel з файлу `bootstrap/app.php`. Першою дією, яку виконує сам Laravel, є створення екземпляру програми / [service container](/docs/{{version}}/container).

<a name="http-console-kernels"></a>
### HTTP / консольні ядра

Далі вхідний запит надсилається або до ядра HTTP, або до ядра консолі за допомогою методів `handleRequest` або `handleCommand` екземпляра програми, залежно від типу запиту, що надходить до програми. Ці два ядра слугують центральним місцем, через яке проходять всі запити. Наразі зосередимося на ядрі HTTP, яке є екземпляром `Illuminate\Foundation\Http\Kernel`.

Ядро HTTP визначає масив «завантажувачів», які будуть запущені перед виконанням запиту. Ці завантажувачі налаштовують обробку помилок, налаштовують ведення журналу, [detect the application environment](/docs/{{version}}/configuration#environment-configuration)і виконують інші завдання, які необхідно виконати до того, як запит буде оброблено. Зазвичай ці класи обробляють внутрішню конфігурацію Laravel, про яку вам не потрібно турбуватися.

Ядро HTTP також відповідає за передачу запиту через стек проміжного програмного забезпечення програми. Це проміжне програмне забезпечення обробляє читання і запис [HTTP session](/docs/{{version}}/session) визначаючи, чи перебуває програма в режимі обслуговування, [verifying the CSRF token](/docs/{{version}}/csrf) і багато іншого. Незабаром ми поговоримо про них докладніше.

Сигнатура методу `handle` ядра HTTP досить проста: він отримує `запит` і повертає `відповідь`. Уявіть собі ядро як велику чорну скриньку, яка представляє весь ваш додаток. Подавайте йому HTTP-запити, а він повертатиме HTTP-відповіді.

<a name="service-providers"></a>
### Постачальники послуг

Однією з найважливіших дій завантажувача ядра є завантаження [постачальники послуг](/docs/{{version}}/providers) для вашого додатку. Постачальники послуг відповідають за завантаження всіх компонентів фреймворку, таких як база даних, черга, валідація та компоненти маршрутизації.

Laravel буде ітераційно переглядати цей список провайдерів і створювати екземпляри кожного з них. Після створення екземплярів провайдерів буде викликано метод `register` для всіх провайдерів. Після того, як всі провайдери будуть зареєстровані, для кожного з них буде викликано метод `boot`. Це робиться для того, щоб провайдери могли розраховувати на те, що кожне прив'язування контейнера буде зареєстроване і доступне на момент виконання їхнього методу `boot`.

По суті, кожна основна функція, яку пропонує Laravel, встановлюється і налаштовується провайдером послуг. Оскільки вони завантажують і налаштовують так багато функцій, пропонованих фреймворком, постачальники послуг є найважливішим аспектом всього процесу завантаження Laravel.

Хоча фреймворк використовує десятки постачальників послуг, у вас також є можливість створити власного. Ви можете знайти список визначених користувачем або сторонніх постачальників послуг, які використовує ваш додаток, у файлі `bootstrap/providers.php`.

<a name="routing"></a>
### Маршрутизація

Після того, як додаток буде завантажено і всі постачальники послуг будуть зареєстровані, «Запит» буде передано маршрутизатору для відправки. Маршрутизатор відправить запит на маршрут або контролер, а також запустить будь-яке проміжне програмне забезпечення для конкретного маршруту.

Проміжне програмне забезпечення надає зручний механізм для фільтрації або перевірки HTTP-запитів, що надходять до вашого додатку. Наприклад, Laravel містить проміжне програмне забезпечення, яке перевіряє, чи користувач вашого додатку автентифікований. Якщо користувач не автентифікований, проміжне програмне забезпечення перенаправляє його на екран входу в систему. Однак, якщо користувач автентифікований, проміжне програмне забезпечення дозволить запиту продовжити роботу з додатком. Деякі проміжні програми призначені для всіх маршрутів у програмі, наприклад, `PreventRequestsDuringMaintenance`, тоді як деякі призначені лише для певних маршрутів або груп маршрутів. Ви можете дізнатися більше про проміжне програмне забезпечення, прочитавши повну [документацію про проміжне програмне забезпечення](/docs/{{version}}/middleware).

Якщо запит проходить через усі призначені проміжні програми маршруту, буде виконано метод маршруту або контролера, а відповідь, повернута методом маршруту або контролера, буде надіслано назад через ланцюжок проміжних програм маршруту.

<a name="finishing-up"></a>
### Закінчуємо.

Після того, як метод маршруту або контролера поверне відповідь, відповідь буде відправлена назад через проміжне програмне забезпечення маршруту, даючи програмі можливість модифікувати або перевірити вихідну відповідь.

Нарешті, після того, як відповідь пройде через проміжне програмне забезпечення, метод `handle` ядра HTTP повертає об'єкт відповіді в `handleRequest` екземпляра програми, а цей метод викликає метод `end` на поверненій відповіді. Метод send надсилає вміст відповіді до веб-браузера користувача. На цьому ми завершили нашу подорож по всьому життєвому циклу запиту в Laravel!

<a name="focus-on-service-providers"></a>
## Фокус на постачальниках послуг

Постачальники послуг - це ключ до бутстрапування Laravel-додатків. Створюється екземпляр програми, реєструються постачальники послуг, і запит передається завантаженому додатку. Це дійсно так просто!

Тверде розуміння того, як створюється та завантажується додаток Laravel за допомогою постачальників послуг, є дуже цінним. Визначені користувачем постачальники послуг вашого додатку зберігаються в каталозі `app/Providers`.

За замовчуванням, `AppServiceProvider` є досить порожнім. Цей провайдер є чудовим місцем для додавання власного завантажувача та прив'язок контейнерів служб до вашої програми. Для великих програм ви можете створити декілька провайдерів, кожен з яких матиме більш детальний бутстрапінг для конкретних сервісів, що використовуються вашою програмою.