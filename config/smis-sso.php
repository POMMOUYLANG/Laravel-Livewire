    <?php

    use Illuminate\Support\Str;

    return [
        'app_key' => env('SMIS_APP_KEY'),
        'auth_base_url' => env('SMIS_AUTH_BASE_URL', 'http://127.0.0.1:3000'),

        // This fixed your IDE warning in the logout method
        'auth_logout_redirect' => env('SMIS_AUTH_LOGOUT_REDIRECT', '/'),

        'clock_skew' => 120,
        'user_resolver' => function (array $tokenInfo) {
            $email = $tokenInfo['email'] ?? null;

            // Store extra SSO details in Laravel Session
            session([
                'sso_token' => request()->bearerToken(), // or from $tokenInfo if provided
                'sso_username' => $tokenInfo['username'] ?? 'user',
                'sso_email' => $email,
            ]);

            return \App\Models\User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $tokenInfo['name'] ?? $tokenInfo['username'],
                    'password' => bcrypt(Str::random(16)),
                ]
            );
        },
        'probe_path' => env('SMIS_PROBE_PATH', '/sso/probe'),
        'storage' => 'localStorage',
    ];
