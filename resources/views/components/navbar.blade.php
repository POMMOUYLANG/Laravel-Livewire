<nav class="fixed top-0 z-50 w-full border-b border-base-200 bg-base-100/70 backdrop-blur-md">
    <div class="px-4 lg:px-6 h-16 flex items-center justify-between gap-4">

        {{-- LEFT: Brand & Mobile Toggle --}}
        <div class="flex items-center gap-4">
            {{-- Mobile drawer toggle --}}
            <button type="button" data-overlay="#sidebar" aria-controls="sidebar"
                class="btn btn-sm btn-ghost btn-square lg:hidden" aria-label="Open sidebar">
                <span class="icon-[tabler--menu-2] text-xl"></span>
            </button>

            {{-- Brand --}}
            <a href="{{ url('/') }}" class="hidden lg:flex items-center gap-3 group transition-all">
                <div class="btn btn-sm btn-primary btn-square shadow-md shadow-primary/20">
                    <span class="icon-[tabler--carambola-filled] text-lg text-white"></span>
                </div>

                <div class="leading-tight">
                    <div class="text-base font-bold tracking-tight group-hover:text-primary transition-colors">
                        Student Manager
                    </div>
                    <div class="text-[10px] uppercase font-semibold opacity-50 tracking-wider hidden sm:block">
                        Admin Portal
                    </div>
                </div>
            </a>
        </div>

        {{-- CENTER: Search (Hidden on Mobile) --}}
        <div class="hidden md:flex flex-1 justify-center max-w-2xl px-4">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-3 flex items-center opacity-50">
                    <span class="icon-[tabler--search] text-lg"></span>
                </span>
                <input type="text" placeholder="Search anything... (Press /)"
                    class="input input-bordered h-10 w-full pl-10 rounded-xl bg-base-200/50 border-transparent focus:border-primary/30 focus:bg-base-100 transition-all" />
                <div class="absolute inset-y-0 right-3 flex items-center gap-1 pointer-events-none">
                    <kbd class="kbd kbd-sm bg-base-100">⌘</kbd>
                    <kbd class="kbd kbd-sm bg-base-100">K</kbd>
                </div>
            </div>
        </div>

        {{-- RIGHT: Actions & User --}}
        <div class="flex items-center gap-1 sm:gap-3">
            {{-- Action Buttons --}}
            <div class="flex items-center gap-1">
                <button class="btn btn-sm btn-ghost btn-square rounded-lg" aria-label="Notifications">
                    <div class="indicator">
                        <span class="indicator-item badge badge-primary badge-xs scale-75"></span>
                        <span class="icon-[tabler--bell] text-xl"></span>
                    </div>
                </button>

                <button class="btn btn-sm btn-ghost btn-square rounded-lg" aria-label="Settings">
                    <span class="icon-[tabler--settings] text-xl"></span>
                </button>
            </div>

            <div class="divider divider-horizontal mx-0 hidden sm:flex h-8 self-center"></div>

            @php
                $user = auth()->user();

                // 1. Get Name: Try SSO session first, then DB name, then fallback to Guest
                $name = session('sso_username', $user?->name ?? 'Guest');

                // 2. Get Email: Try SSO session first, then DB email
                $email = session('sso_email', $user?->email ?? 'no-email@itc.edu.kh');

                // 3. Get Role: Standardize the display
                $role = strtoupper($user?->role ?? ($user?->is_admin ? 'ADMIN' : 'USER'));

                // 4. Correct Initials Logic (e.g., "Admin Istrator" -> "AI")
                $initials =
                    collect(preg_split('/\s+/', trim($name)))
                        ->take(2)
                        ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                        ->join('') ?:
                    'G';
            @endphp

            <div class="dropdown dropdown-bottom dropdown-end">
                <button type="button"
                    class="dropdown-toggle btn btn-ghost h-12 px-3 rounded-xl flex items-center gap-3"
                    aria-haspopup="menu" aria-expanded="false">

                    <div class="avatar">
                        <div
                            class="w-9 rounded-xl ring ring-primary ring-offset-base-100 ring-offset-1 grid place-items-center bg-base-200">
                            <span class="text-sm font-bold">{{ $initials }}</span>
                        </div>
                    </div>

                    <div class="hidden sm:block text-left leading-tight">
                        <div class="text-sm font-bold">{{ $name }}</div>
                        <div class="text-[10px] opacity-60 font-semibold uppercase tracking-wider">{{ $role }}
                        </div>
                    </div>

                    <span class="icon-[tabler--chevron-down] text-sm opacity-60"></span>
                </button>

                <ul class="dropdown-menu dropdown-open:opacity-100 hidden min-w-56 bg-base-100 shadow-xl rounded-2xl border border-base-200 p-2 mt-2"
                    role="menu">
                    <li class="px-4 py-2">
                        <div class="text-sm font-semibold">{{ $name }}</div>
                        <div class="text-xs opacity-60">{{ $user?->email }}</div>
                    </li>

                    <div class="divider my-1 opacity-50"></div>

                    <li>
                        <a class="dropdown-item flex items-center gap-3">
                            <span class="icon-[tabler--user-circle] text-lg"></span>
                            My Profile
                        </a>
                    </li>

                    <div class="divider my-1 opacity-50"></div>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="dropdown-item flex items-center gap-3 w-full text-left text-error hover:bg-error/10">
                                <span class="icon-[tabler--logout] text-lg"></span>
                                Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>


        </div>
    </div>
</nav>
