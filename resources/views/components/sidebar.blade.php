@php
    // Refined active state with better FlyonUI colors
    $isActive = fn($cond) => $cond
        ? 'bg-primary/10 text-primary font-semibold border-r-4 border-primary rounded-r-none'
        : 'hover:bg-base-200/60 text-base-content/80 hover:text-base-content';

    $linkBase = 'flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200';
@endphp

<aside id="sidebar"
    class="overlay overlay-open:translate-x-0 drawer drawer-start fixed top-0 left-0 z-40
           w-64 h-screen pt-16 bg-base-100 border-r border-base-200
           -translate-x-full md:translate-x-0 transition-transform duration-300"
    role="dialog" tabindex="-1">


    <div class="h-full px-3 py-4 overflow-y-auto">

        {{-- Brand Section (Visible only on Mobile if Navbar is hidden) --}}
        <div class="flex items-center justify-between px-3 pb-3 lg:hidden">
            <div>
                <div class="text-xl font-bold tracking-tight text-primary">Student Manager</div>
                <div class="text-[10px] uppercase opacity-50 font-bold tracking-widest">Admin Portal</div>
            </div>

            <button type="button" class="btn btn-sm btn-ghost btn-square" data-overlay="#sidebar"
                aria-label="Close sidebar">
                <span class="icon-[tabler--x] text-xl"></span>
            </button>
        </div>

        <ul class="menu menu-sm w-full gap-1 px-0">

            <li class="menu-title px-4 mt-2">
                <span class="text-[11px] font-bold tracking-widest opacity-50 uppercase">Main Menu</span>
            </li>

            {{-- Dashboard --}}
            <li>
                <a href="{{ url('/') }}"
                    class="{{ $linkBase }} {{ $isActive(request()->is('/') || request()->is('dashboard')) }}">
                    <span class="icon-[tabler--layout-dashboard] text-xl"></span>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Students --}}
            <li>
                <a href="{{ route('students.index') }}" data-overlay="#sidebar"
                    class="{{ $linkBase }} {{ $isActive(request()->routeIs('students.*')) }}">
                    <span class="icon-[tabler--school] text-xl"></span>
                    <span>Students</span>
                </a>

            </li>

            {{-- Teachers --}}
            <li>
                <a href="{{ route('teachers.index') }}"
                    class="{{ $linkBase }} {{ $isActive(request()->routeIs('teachers.*')) }}">
                    <span class="icon-[tabler--chalkboard-teacher] text-xl"></span>
                    <span>Teachers</span>
                </a>
            </li>

            {{-- Posts --}}
            <li>
                <a href="{{ route('posts.index') }}"
                    class="{{ $linkBase }} {{ $isActive(request()->routeIs('posts.*')) }}">
                    <span class="icon-[tabler--news] text-xl"></span>
                    <span>Posts</span>
                </a>
            </li>

            <li class="menu-title px-4 mt-6">
                <span class="text-[11px] font-bold tracking-widest opacity-50 uppercase">System</span>
            </li>

            {{-- Settings --}}
            <li>
                <a href="#" class="{{ $linkBase }} {{ $isActive(request()->is('settings*')) }}">
                    <span class="icon-[tabler--settings] text-xl"></span>
                    <span>Settings</span>
                </a>
            </li>

            {{-- Support/Help --}}
            <li>
                <a href="#" class="{{ $linkBase }} {{ $isActive(false) }}">
                    <span class="icon-[tabler--help-circle] text-xl"></span>
                    <span>Help Center</span>
                </a>
            </li>

        </ul>

        {{-- Bottom Profile Card --}}
        <div class="absolute bottom-4 left-0 w-full px-4">
            <div class="p-3 rounded-xl bg-base-200/50 flex items-center gap-3 border border-base-300/50">
                <div class="avatar">
                    <div
                        class="w-8 rounded-lg bg-primary/20 text-primary grid place-items-center font-bold text-[10px]">
                        @php
                            $sidebarName = session('sso_username', auth()->user()->name ?? 'G');
                            $sidebarInitials = collect(explode(' ', $sidebarName))
                                ->map(fn($n) => substr($n, 0, 1))
                                ->take(2)
                                ->join('');
                        @endphp
                        {{ $sidebarInitials }}
                    </div>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold truncate">
                        {{ session('sso_username', auth()->user()->name ?? 'Guest User') }}
                    </p>
                    <p class="text-[10px] opacity-50 truncate">
                        {{ session('sso_email', auth()->user()->email ?? 'guest@itc.edu.kh') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>
