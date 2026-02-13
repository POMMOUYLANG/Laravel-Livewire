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
                console.log("SSO User Data captured:", user);

                const response = await fetch("{{ route('sso.sync') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(user)
                });

                if (response.ok) {
                    // Once the PHP session is set, redirect to dashboard
                    // This will make session('sso_username') available to Blade
                    window.location.href = "{{ route('dashboard') }}";
                } else {
                    const errorData = await response.json();
                    console.error("Sync failed:", errorData);
                }
            } catch (err) {
                console.error("SSO login failed:", err);
            }
        });
    </script>


</body>

</html>
