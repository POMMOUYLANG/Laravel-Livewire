<div class="min-h-screen grid place-items-center bg-base-200 p-4">
    <div class="card w-full max-w-md bg-base-100 shadow-lg border border-base-200">
        <div class="card-body space-y-4">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold">Sign in</h1>
                <p class="text-sm opacity-70">Login with your email and password</p>
            </div>

            {{-- Global error (optional) --}}
            @if ($errors->any())
                <div class="alert alert-error">
                    <span class="icon-[tabler--alert-triangle] text-lg"></span>
                    <div>
                        <div class="font-semibold">Login failed</div>
                        <div class="text-sm opacity-80">Please check your details and try again.</div>
                    </div>
                </div>
            @endif

            {{-- SSO  --}}
            <button type="button" class="btn w-full" onclick="window.location='{{ route('sso.redirect') }}'">
                Sign in with SSO
            </button>


            <div class="divider">or</div>


            <form wire:submit.prevent="login" class="space-y-4">

                {{-- Email --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>

                    <label class="input input-bordered flex items-center gap-2">
                        <span class="icon-[tabler--mail] text-lg opacity-70"></span>
                        <input type="email" class="grow" placeholder="you@example.com" wire:model.defer="email"
                            autocomplete="email" />
                    </label>

                    @error('email')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Password</span>
                    </label>

                    <label class="input input-bordered flex items-center gap-2">
                        <span class="icon-[tabler--lock] text-lg opacity-70"></span>

                        <input id="login-password" type="password" class="grow" placeholder="••••••••"
                            wire:model.defer="password" autocomplete="current-password" />

                        <button type="button" class="btn btn-ghost btn-xs"
                            onclick="const i=document.getElementById('login-password'); i.type = (i.type === 'password' ? 'text' : 'password');"
                            aria-label="Toggle password">
                            <span class="icon-[tabler--eye] text-lg"></span>
                        </button>
                    </label>

                    @error('password')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center justify-between">
                    <label class="label cursor-pointer gap-3 p-0">
                        <input type="checkbox" class="checkbox" wire:model="remember" />
                        <span class="label-text">Remember me</span>
                    </label>

                    {{-- If you have forgot-password route, enable this --}}
                    {{-- <a class="link link-hover text-sm" href="{{ route('password.request') }}">Forgot password?</a> --}}
                </div>

                <button class="btn btn-primary w-full" type="submit" wire:loading.attr="disabled">
                    <span class="icon-[tabler--login-2] text-lg"></span>
                    <span wire:loading.remove>Sign in</span>
                    <span wire:loading>Signing in...</span>
                </button>
            </form>

            {{-- Optional: register link --}}
            {{-- <p class="text-center text-sm opacity-70">
                Don’t have an account?
                <a class="link link-primary" href="{{ route('register') }}">Create one</a>
            </p> --}}
        </div>
    </div>
</div>
