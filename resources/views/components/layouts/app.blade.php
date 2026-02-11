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

    {{-- 1️⃣ Token Persistence --}}
    <script>
        document.addEventListener('livewire:init', () => {
            const token = "{{ session('sso_token') }}";
            if (token) {
                localStorage.setItem('accessToken', token);
            }
        });
    </script>

    {{-- 2️⃣ Proper ES Module Import --}}
    <script type="module">
        import {
            AuthClient
        } from "{{ asset('vendor/smis-sso/sso-client/sso-client.js') }}";

        const baseUrl = "{{ config('smis-sso.auth_base_url') }}";

        console.log("SMIS base_url =", baseUrl);

        const smisClient = new AuthClient({
            appKey: "{{ config('smis-sso.app_key') }}",
            authBaseUrl: baseUrl
        });

        window.smis = smisClient;
    </script>



</body>

</html>
