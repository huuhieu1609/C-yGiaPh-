<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'register'],

    'allowed_methods' => ['*'],

<<<<<<< HEAD
    /*
     * Đã sửa thành ['*'] để cho phép tất cả các origin.
     * Rất tiện lợi khi làm việc ở môi trường local với Vite.
     */
    'allowed_origins' => ['*'],
=======
    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:5173'),
        'http://localhost:5174',
        'http://localhost:3000'
    ],
>>>>>>> 03738f7 (Bo_sung_chuc_nang_con_thieu_ADMIM)

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
