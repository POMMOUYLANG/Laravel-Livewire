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

        $base = config('sso.base_url');
        $appKey = config('sso.app_key');
        $callback = config('sso.callback_url');

        $loginPath = config('sso.login_path');
        $url = $base . $loginPath . '?' . http_build_query([
            'appKey' => $appKey,
            'redirectUri' => $callback, // http_build_query will encode it correctly
            'state' => $state,
            'connect' => 1,
        ]);

        Log::info('SSO redirect URL', ['url' => $url]);


        return redirect()->away($url);
    }

    public function callback(Request $request)
    {
        Log::info('SSO CALLBACK HIT', [
            'fullUrl' => $request->fullUrl(),
            'query' => $request->query(),
            'session_id' => $request->session()->getId(),
            'has_state_in_session' => $request->session()->has('sso_state'),
        ]);

        // 1) Validate state
        $expected = (string) $request->session()->pull('sso_state');
        $state    = (string) $request->query('state', '');

        if ($expected === '' || $state === '' || !hash_equals($expected, $state)) {
            Log::warning('SSO state mismatch', [
                'expected' => $expected ? 'exists' : 'missing',
                'state' => $state,
            ]);
            abort(403, 'Invalid SSO state');
        }

        // 2) Get token (or code)
        $token = (string) ($request->query('token') ?? $request->query('access_token') ?? '');
        $code  = (string) $request->query('code', '');

        if ($token === '' && $code !== '') $token = $code;

        if ($token === '') {
            Log::warning('SSO callback missing token/code', ['query' => $request->query()]);
            abort(400, 'Missing token');
        }

        // 3) Probe token
        $probe = $this->probeToken($token);
        if (!($probe['ok'] ?? false)) {
            Log::error('SSO token probe failed', ['probe' => $probe]);
            abort(401, 'SSO token is invalid');
        }

        $profile = $probe['user'] ?? $probe['data'] ?? $probe;

        $email = $profile['email'] ?? null;
        if (!$email) {
            Log::error('SSO profile missing email', ['profile' => $profile]);
            abort(422, 'SSO profile missing email');
        }

        $name = $profile['name'] ?? $profile['fullName'] ?? $profile['username'] ?? $email;

        // 4) Create/update local user
        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => $name]
        );

        // 5) Login + regenerate session + store token
        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->put('sso_token', $token);

        return redirect()->route('dashboard');
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
