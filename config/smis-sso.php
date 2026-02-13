    <?php

    use Illuminate\Support\Str;

    return [
        'app_key' => env('SMIS_APP_KEY'),
        'auth_base_url' => env('SMIS_AUTH_BASE_URL', 'http://127.0.0.1:3000'),

        // This fixed your IDE warning in the logout method
        'auth_logout_redirect' => env('SMIS_AUTH_LOGOUT_REDIRECT', '/'),

        'clock_skew' => 120,
        'user_resolver' => function (array $tokenInfo) {
            // The SSO Gateway profile typically contains 'email'
            $email = $tokenInfo['email'] ?? null;
            $name = $tokenInfo['displayName'] ?? ($tokenInfo['name'] ?? $tokenInfo['username']);

            return \App\Models\User::updateOrCreate(
                ['email' => $email], // Always search by the actual email
                [
                    'name' => $name,
                    'password' => bcrypt(Str::random(16)),
                ]
            );
        },
        'probe_path' => env('SMIS_PROBE_PATH', '/sso/probe'),
        'storage' => 'localStorage',
    ];
