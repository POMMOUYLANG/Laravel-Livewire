<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('sso_state', $state);

        $base = config('smis-sso.auth_base_url');
        $appKey = config('smis-sso.app_key');
        $callback = env('SMIS_AUTH_CALLBACK_URL');

        // In SsoController.php
        $url = $base . '/sso/login?' . http_build_query([
            'appKey' => $appKey,
            'redirectUri' => $callback,
            'state' => $state,
            'connect' => 1,
            'scope' => 'openid profile email', // Ensure 'email' is included here
        ]);

        Log::info('SSO redirect initiated', ['url' => $url]);
        return redirect()->away($url);
    }

    public function callback(Request $request)
    {
        Log::info('SSO CALLBACK HIT', [
            'fullUrl' => $request->fullUrl(),
            'query' => $request->query(),
        ]);

        // 1️⃣ Validate state
        $expected = (string) $request->session()->pull('sso_state');
        $state = (string) $request->query('state', '');

        if ($expected === '' || $state === '' || !hash_equals($expected, $state)) {
            Log::warning('SSO state mismatch');
            abort(403, 'Invalid SSO state');
        }

        // 2️⃣ Get token
        $token = (string) (
            $request->query('token')
            ?? $request->query('access_token')
            ?? $request->query('code')
            ?? ''
        );

        if ($token === '') {
            abort(400, 'Missing token');
        }

        // 3️⃣ Decode JWT
        $username = 'User';
        $email = null;

        $parts = explode('.', $token);

        if (count($parts) === 3) {
            $payload = json_decode(base64_decode($parts[1]), true);
            $username = $payload['username'] ?? $payload['name'] ?? 'User';
            $email = $payload['email'] ?? null;
        }

        // 4️⃣ Fallback probe if needed
        if (!$email) {
            $probe = $this->probeToken($token);

            if ($probe['ok'] ?? false) {
                $profile = $probe['user'] ?? $probe['data'] ?? $probe;
                $email = $profile['email'] ?? null;
                $username = $profile['username'] ?? $profile['name'] ?? $username;
            }
        }

        if (!$email) {
            Log::error('SSO profile missing email');
            abort(422, 'SSO profile missing email');
        }

        // 5️⃣ Create / update user
        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => $username]
        );

        // 6️⃣ Login
        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->put('sso_token', $token);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        // Clear SSO specific stuff
        $request->session()->forget(['sso_token', 'sso_username', 'sso_email']);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Use the redirect defined in your package config
        $redirectUrl = config('smis-sso.auth_logout_redirect', '/login');

        return redirect($redirectUrl);
    }

    private function probeToken(string $token): array
    {
        $base = rtrim(config('smis-sso.auth_base_url'), '/');
        $appKey = config('smis-sso.app_key');

        /** @var Response $res */
        $res = Http::timeout(10)
            ->acceptJson()
            ->withToken($token)
            ->get($base . config('smis-sso.probe_path'), [
                'appKeyParam' => $appKey,
            ]);

        if (!$res->successful()) {
            return [
                'ok' => false,
                'status' => $res->status(),
                'body' => $res->body(),
            ];
        }

        return $res->json() ?? [
            'ok' => false,
            'body' => $res->body(),
        ];
    }

    // app/Http/Controllers/SsoController.php
    public function sync(Request $request)
    {
        try {
            $validated = $request->validate([
                'email'    => 'required|email',
                'userId'   => 'required',
                'username' => 'required|string',
                'roles'    => 'nullable|array',
                'context'  => 'nullable|array' // New field
            ]);

            $user = User::updateOrCreate(
                ['email' => $validated['email']],
                [
                    'sso_id'   => $validated['userId'],
                    'name'     => $validated['username'],
                    'password' => null,
                ]
            );

            Auth::login($user, true);

            // Store everything in session for easy access in Blade/Controllers
            session([
                'sso_username' => $user->name,
                'sso_email'    => $user->email,
                'smis.roles'   => $validated['roles'] ?? [],
                'smis.context' => $validated['context'] ?? [], // This contains branches/depts
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}
