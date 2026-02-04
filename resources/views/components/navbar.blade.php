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
            <a href="{{ url('/') }}" class="flex items-center gap-3 group transition-all">
                <div class="btn btn-sm btn-primary btn-square shadow-md shadow-primary/20">
                    <span class="icon-[tabler--menu-2] text-lg text-white"></span>
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

            {{-- User Profile Dropdown --}}
            {{-- Right Section: User Profile Dropdown --}}
            <div class="dropdown dropdown-bottom dropdown-end flex items-center">
                <button type="button" id="dropdown-account"
                    class="dropdown-toggle btn btn-ghost h-12 px-2 rounded-xl flex items-center gap-3"
                    aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                    <div class="avatar online">
                        <div class="w-9 rounded-lg ring ring-primary ring-offset-base-100 ring-offset-1">
                            <img src="https://ui-avatars.com/api/?name=Admin&background=random" alt="Admin" />
                        </div>
                    </div>
                    <div class="hidden lg:block text-left">
                        <div class="text-sm font-bold leading-none text-white :hover:text-black">Admin User</div>
                        <div class="text-[10px] opacity-50 font-medium uppercase tracking-wider">Super Admin</div>
                    </div>
                    <span class="icon-[tabler--chevron-down] text-xs opacity-50 hidden lg:block"></span>
                </button>

                <ul class="dropdown-menu dropdown-open:opacity-100 hidden min-w-60 bg-base-100 shadow-xl rounded-2xl border border-base-200 p-2 mt-2"
                    role="menu" aria-orientation="vertical" aria-labelledby="dropdown-account">
                    <li class="px-4 py-2 mb-1">
                        <p class="text-xs font-semibold uppercase tracking-widest opacity-40">Account</p>
                    </li>
                    <li>
                        <a class="dropdown-item flex items-center gap-3">
                            <span class="icon-[tabler--user-circle] text-lg"></span>
                            My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item flex items-center gap-3 rounded-lg py-2.5 px-4 hover:bg-base-200 transition-colors"
                            href="#">
                            <span class="icon-[tabler--credit-card] text-lg"></span>
                            Billing
                        </a>
                    </li>

                    <div class="divider my-1 opacity-50"></div>

                    <li>
                        <a class="dropdown-item flex items-center gap-3 rounded-lg py-2.5 px-4 text-error hover:bg-error/10 transition-colors"
                            href="#">
                            <span class="icon-[tabler--logout] text-lg"></span>
                            Sign Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
