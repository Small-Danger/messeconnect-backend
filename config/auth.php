<?php

use App\Models\Fidele;
use App\Models\User;
use App\Models\UserParoisse;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | web     → Super Admin (session, backoffice Blade éventuel)
    | admin   → Super Admin API (Sanctum bearer token)
    | paroisse → Staff paroissial API (Sanctum bearer token)
    | fidele  → Fidèle API (Sanctum bearer token)
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'admin' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],

        'paroisse' => [
            'driver' => 'sanctum',
            'provider' => 'user_paroisses',
        ],

        'fidele' => [
            'driver' => 'sanctum',
            'provider' => 'fideles',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        'user_paroisses' => [
            'driver' => 'eloquent',
            'model' => UserParoisse::class,
        ],

        'fideles' => [
            'driver' => 'eloquent',
            'model' => Fidele::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'user_paroisses' => [
            'provider' => 'user_paroisses',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'fideles' => [
            'provider' => 'fideles',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
