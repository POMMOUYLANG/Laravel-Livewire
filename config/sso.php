<?php

return [
    'base_url' => rtrim(env('SMIS_AUTH_BASE_URL', 'https://accounts.itc.edu.kh'), '/'),
    'app_key' => env('SMIS_APP_KEY'),
    'callback_url' => env('SMIS_AUTH_CALLBACK_URL'),
    'logout_redirect' => env('SMIS_AUTH_LOGOUT_REDIRECT', env('APP_URL', '/')),
    'login_path' => env('SMIS_AUTH_LOGIN_PATH', '/login'),
];
