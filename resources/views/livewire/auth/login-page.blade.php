<div class="min-h-screen flex items-center justify-center bg-base-200 p-4">
    <livewire:smis-sso::smis-session />

    <div class="card w-full max-w-md bg-base-100 shadow-xl border border-base-200">
        <div class="card-body space-y-4 text-center">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold">Sign in</h1>
                <p class="text-sm opacity-70">Please authenticate to continue</p>
            </div>

            <button id="sso-login-btn" class="btn w-full btn-neutral">
                <span class="icon-[tabler--shield-lock] text-lg"></span>
                Sign in with SSO
            </button>

            <div id="sso-error-area" class="hidden mt-4 alert alert-error text-xs text-left">
                <div class="flex flex-col">
                    <span id="error-msg" class="font-bold"></span>
                    <pre id="error-debug" class="mt-2 opacity-70 overflow-auto max-h-32"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import {
        AuthClient
    } from "{{ asset('vendor/smis-sso/sso-client/sso-client.js') }}";

    const smisClient = new AuthClient({
        appKey: @js(config('smis-sso.app_key')),
        authBaseUrl: @js(config('smis-sso.auth_base_url'))
    });

    const ssoBtn = document.getElementById('sso-login-btn');

    if (ssoBtn) {
        ssoBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            ssoBtn.classList.add('loading');

            try {
                // Triggers popup only on click
                const session = await smisClient.signIn({
                    force: true
                });

                if (session?.accessToken) {
                    const response = await fetch("{{ route('smis.auth.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json', // Prevents SyntaxError crash
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            accessToken: session.accessToken,
                            refreshToken: session.refreshToken // Pass for Gateway logout
                        })
                    });

                    if (response.ok) {
                        window.location.href = "{{ route('dashboard') }}";
                    } else {
                        const err = await response.json();
                        throw new Error(err.message);
                    }
                }
            } catch (err) {
                alert(err.message + ". Please enable popups.");
                ssoBtn.classList.remove('loading');
            }
        });
    }
</script>
