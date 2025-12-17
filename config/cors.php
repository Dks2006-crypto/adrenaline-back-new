<?php

return [
    // Указываем, что CORS работает для всех путей API
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // ВАЖНО: Разрешаем только ваш фронтенд на Vercel
    'allowed_origins' => [
        'https://adrenaline-frontend.vercel.app',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // ВАЖНО: Устанавливаем в true для работы авторизации (JWT/Cookies)
    'supports_credentials' => true,
];
