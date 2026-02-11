<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Student Manager' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans bg-gray-100 text-gray-800">

    {{-- SMIS Session Component --}}
    <livewire:smis-sso::smis-session :app-key="config('smis-sso.app_key')" />

    <x-navbar />
    <x-sidebar />

    <main class="min-h-screen pt-20 md:ml-64 p-6">
        <div class="px-3 py-3">
            {{ $slot }}
        </div>
    </main>

    @livewireScripts

    {{-- SSO Login Button --}}
    <div class="fixed bottom-4 right-4">
        <button id="sso-login" class="px-4 py-2 bg-blue-600 text-white rounded shadow">
            Login with SMIS
        </button>
    </div>

    {{-- 1️⃣ Token Persistence --}}
    <script>
        document.addEventListener('livewire:init', () => {
            const token = "{{ session('sso_token') }}";
            if (token) {
                localStorage.setItem('accessToken', token);
            }
        });
    </script>

    <div class="sync-session mb-4">
        <button id="sso-login" class="px-4 py-2 bg-blue-600 text-white rounded shadow">
            Sync Session
        </button>
    </div>

    <script type="module">
        import {
            AuthClient
        } from "{{ asset('vendor/smis-sso/sso-client/sso-client.js') }}";

        const smisClient = new AuthClient({
            appKey: "{{ config('smis-sso.app_key') }}",
            authBaseUrl: "{{ config('smis-sso.auth_base_url') }}"
        });

        window.smis = smisClient;

        document.getElementById('sso-login').addEventListener('click', async () => {
            try {
                const user = await smisClient.user({
                    force: true
                });
                console.log("SSO User:", user);

                // Update UI
                document.querySelector('.sync-session').innerHTML = `
            <p>Username: ${user.username}</p>
            <p>Email: ${user.email || user.username + '@itc.edu.kh'}</p>
        `;

                // Store locally
                localStorage.setItem('sso_username', user.username);
                localStorage.setItem('sso_email', user.email || `${user.username}@itc.edu.kh`);
            } catch (err) {
                console.error("SSO login failed:", err);
                alert("SSO login failed. Check console for details.");
            }
        });
    </script>


</body>

</html>
