<?php

return [
    'users' => [
        'url' => 'http://users_nginx',
        'public_routes' => [
            'auth/register-via-telegram',
            'auth/register-via-email',
            'auth/login-via-telegram',
            'auth/login-via-email',
        ],
    ],
    'rates' => [
        'url' => 'http://rates_app',
    ],
];
