<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Smis\SsoClient\Services\TokenVerifier;

class SmisAuthController extends Controller
{
    public function store(Request $request, TokenVerifier $verifier)
    {
        if (!$request->expectsJson()) {
            $request->headers->set('Accept', 'application/json');
        }

        $request->validate([
            'accessToken' => ['required', 'string'],
            'refreshToken' => ['nullable', 'string']
        ]);

        $appKey = config('smis-sso.app_key');
        $authBaseUrl = rtrim(config('smis-sso.auth_base_url'), '/');

        try {
            // 1. Verify Token
            $tokenInfo = $verifier->verify($request->input('accessToken'), $appKey);

            // 2. Fetch Profile
            $profileResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $request->input('accessToken'),
                'X-SMIS-APP-KEY' => $appKey
            ])->get($authBaseUrl . '/api/users/me');

            if (!$profileResponse->ok()) throw new \Exception("Failed to fetch SSO profile.");
            $profile = $profileResponse->json();

            // 3. Fetch Context (Branches/Depts)
            $contextResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $request->input('accessToken'),
                'X-SMIS-APP-KEY' => $appKey
            ])->get($authBaseUrl . '/api/sso/authorizations/context');

            $context = $contextResponse->ok() ? $contextResponse->json() : [];

            // --- DEBUG DATA TO LOG FILE ---
            Log::info('--- SSO LOGIN DATA DEBUG ---');
            Log::info('Token Info:', (array)$tokenInfo);
            Log::info('User Profile:', (array)$profile);
            Log::info('Org Context:', (array)$context);
            // ------------------------------

            $user = User::updateOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['displayName'] ?? $profile['username'] ?? 'SSO User',
                    'sso_id' => $profile['id'] ?? $tokenInfo['sub'],
                    'password' => null,
                ]
            );

            Auth::login($user, true);

            session([
                'smis.token' => $request->input('accessToken'),
                'smis.refresh_token' => $request->input('refreshToken'),
                'smis.info' => array_merge($tokenInfo, $profile),
                'smis.context' => $context,
                'smis.roles' => $context['roles'] ?? $tokenInfo['roles'] ?? [],
            ]);

            return response()->json(['message' => 'ok']);
        } catch (\Throwable $e) {
            Log::error('SSO Login Error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function destroy(Request $request)
    {
        $refreshToken = session('smis.refresh_token');
        $authBaseUrl = rtrim(config('smis-sso.auth_base_url'), '/');

        // Required per Integration Notes: POST /auth/logout with refresh token
        if ($refreshToken && $authBaseUrl) {
            try {
                Http::post($authBaseUrl . '/auth/logout', [
                    'refreshToken' => $refreshToken
                ]);
            } catch (\Throwable $e) {
                Log::warning('Gateway logout failed: ' . $e->getMessage());
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
