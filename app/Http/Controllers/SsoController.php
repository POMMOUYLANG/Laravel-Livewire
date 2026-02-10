<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('sso_state', $state);

        $base = config('sso.base_url');
        $appKey = config('sso.app_key');
        $callback = config('sso.callback_url');

        $loginPath = config('sso.login_path');
        $url = $base . $loginPath . '?' . http_build_query([
            'appKey' => $appKey,
            'redirectUri' => $callback,
            'state' => $state,
            'connect' => 1,
        ]);


        Log::info('SSO redirect URL', ['url' => $url]);


        return redirect()->away($url);
    }

    public function callback(Request $request)
    {
        $expectedState = $request->session()->pull('sso_state');
        $state = $request->query('state');

        if (!$expectedState || !$state || !hash_equals($expectedState, $state)) {
            abort(403, 'Invalid SSO state.');
        }

        // token could come as ?token=... (recommended)
        $token = $request->query('token')
            ?? $request->query('access_token')
            ?? $request->query('jwt');


        // If your SSO returns it as "#token=..." (hash fragment), Laravel can't see it.
        // In that case you must change SSO to return query param OR add a small JS page to POST it.
        if (!$token) {
            abort(400, 'Missing token from SSO callback.');
        }

        $probe = $this->probeToken($token);

        // IMPORTANT: adapt to your real probe response shape
        // Expect something like: { ok: true, user: { id, email, name, roles... } }
        if (!($probe['ok'] ?? false)) {
            abort(401, 'SSO token invalid.');
        }

        $userData = $probe['user'] ?? null;
        if (!$userData || empty($userData['email'])) {
            abort(401, 'SSO user payload missing email.');
        }

        // Create/update local user
        $user = User::updateOrCreate(
            ['email' => $userData['email']],
            [
                'name' => $userData['name'] ?? ($userData['email']),
                // store extra SSO info if you want:
                // 'sso_id' => $userData['id'] ?? null,
            ]
        );

        Auth::login($user, remember: true);

        // Store token in session if you need it for API calls
        $request->session()->put('sso_token', $token);
        $request->session()->put('sso_user', $userData);

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        $token = $request->session()->pull('sso_token');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Optional: also log out from SSO side if they have an endpoint
        // return redirect()->away(config('sso.base_url').'/sso/logout?redirectUri='.urlencode(config('sso.logout_redirect')));

        return redirect(config('sso.logout_redirect'));
    }

    private function probeToken(string $token): array
    {
        $base = rtrim(config('sso.base_url'), '/');
        $appKey = config('sso.app_key');

        /** @var Response $res */
        $res = Http::timeout(10)
            ->acceptJson()
            ->withToken($token)
            ->get($base . '/sso/probe', [
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
}