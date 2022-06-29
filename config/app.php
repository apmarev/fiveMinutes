<?php

use Illuminate\Support\Facades\Facade;

return [
    'name' => env('APP_NAME', 'Umschool'),
    'env' => env('APP_ENV', 'production'),
    'services' => [
        'amo' => [
            'subdomain' => env('AMO_SUBDOMAIN', ''),
            'domain' => env('AMO_INTEGRATION_DOMAIN', ''),
        ],
        'senler' => [
            'groups' => [
                137331585 => [ 'name' => 'Русский язык ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_1', '') ],
                143084342 => [ 'name' => 'Базовая математика ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_2', '') ],
                135803480 => [ 'name' => 'Математика ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_3', '') ],
                151441700 => [ 'name' => 'Литература ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_4', '') ],
                137331378 => [ 'name' => 'История ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_5', '') ],
                137331702 => [ 'name' => 'Обществознание ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_6', '') ],
                137331446 => [ 'name' => 'Биология ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_7', '') ],
                168452327 => [ 'name' => 'География ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_8', '') ],
                137331920 => [ 'name' => 'Информатика ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_9', '') ],
                99797563 => [ 'name' => 'Физика ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_10', '') ],
                137332003 => [ 'name' => 'Химия ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_11', '') ],
                137331795 => [ 'name' => 'Английский язык ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_12', '') ],
                168456080 => [ 'name' => 'Немецкий язык ЕГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_13', '') ],
                197343744 => [ 'name' => 'Химия 10 класс ВК vkontakte', 'key' => env('SENLER_GROUPS_14', '') ],
                197343798 => [ 'name' => 'Английский язык 10 класс ВК vkontakte', 'key' => env('SENLER_GROUPS_15', '') ],
                197366397 => [ 'name' => 'Математика 10 класс ВК vkontakte', 'key' => env('SENLER_GROUPS_16', '') ],
                197343788 => [ 'name' => 'Биология 10 класс ВК vkontakte', 'key' => env('SENLER_GROUPS_17', '') ],
                198276645 => [ 'name' => 'История 10 класс ВК vkontakte', 'key' => env('SENLER_GROUPS_18', '') ],
                198276627 => [ 'name' => 'Русский язык 10 класс ВК vkontakte', 'key' => env('SENLER_GROUPS_19', '') ],
                197343806 => [ 'name' => 'Обществознание 10 класс ВК vkontakte', 'key' => env('SENLER_GROUPS_20', '') ],
                197343818 => [ 'name' => 'Физика 10 класс ВК vkontakte', 'key' => env('SENLER_GROUPS_21', '') ],
                197343827 => [ 'name' => 'Литература 10 класс ВК vkontakte', 'key' => env('SENLER_GROUPS_22', '') ],
                168455375 => [ 'name' => 'Русский язык ОГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_23', '') ],
                168456727 => [ 'name' => 'Математика ОГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_24', '') ],
                168455533 => [ 'name' => 'Литература ОГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_25', '') ],
                168455361 => [ 'name' => 'Физика ОГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_26', '') ],
                168455430 => [ 'name' => 'Биология ОГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_27', '') ],
                168455444 => [ 'name' => 'Химия ОГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_28', '') ],
                168455409 => [ 'name' => 'Обществознание ОГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_29', '') ],
                168455550 => [ 'name' => 'География ОГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_30', '') ],
                168455415 => [ 'name' => 'История ОГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_31', '') ],
                168455540 => [ 'name' => 'Информатика ОГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_32', '') ],
                168455391 => [ 'name' => 'Английский язык ОГЭ ВК vkontakte', 'key' => env('SENLER_GROUPS_33', '') ],
                124303372 => [ 'name' => 'Умскул ВК vkontakte', 'key' => env('SENLER_GROUPS_34', '') ],
            ],
        ],
    ],
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'Europe/Moscow',
    'locale' => 'ru',
    'fallback_locale' => 'ru',
    'faker_locale' => 'ru_RU',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],
    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],
    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
    ])->toArray(),

];
