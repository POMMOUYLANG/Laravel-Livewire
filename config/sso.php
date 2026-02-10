<?php

return [
    'base_url' => rtrim(env('SMIS_AUTH_BASE_URL', ''), '/'),
    'app_key' => env('SMIS_APP_KEY', ''),
    'callback_url' => env('SMIS_AUTH_CALLBACK_URL', ''),
    'logout_redirect' => env('SMIS_AUTH_LOGOUT_REDIRECT', '/'),
    'login_path' => env('SMIS_AUTH_LOGIN_PATH', '/sso/login'),

];
