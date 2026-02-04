<aside id="sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 bg-white border-r border-gray-200
           -translate-x-full md:translate-x-0 transition-transform">
    <div class="h-full px-3 py-4 overflow-y-auto">
        <ul class="space-y-2 text-sm font-medium">

            {{-- Dashboard --}}
            <li>
                <a href="#"
                    class="flex items-center p-2 rounded-lg hover:bg-gray-100
                   {{ request()->is('/') ? 'bg-gray-100 font-semibold' : '' }}">
                    <span>📊</span>
                    <span class="ml-3">Dashboard</span>
                </a>
            </li>

            {{-- Students --}}
            <li>
                <a href="{{ route('students.index') }}"
                    class="flex items-center p-2 rounded-lg
                   {{ request()->routeIs('students.*') ? 'bg-gray-100 font-semibold' : 'hover:bg-gray-100' }}">
                    <span>🎓</span>
                    <span class="ml-3">Students</span>
                </a>
            </li>

            {{-- Posts --}}
            <li>
                <a href="{{ route('posts.index') }}"
                    class="flex items-center p-2 rounded-lg
                   {{ request()->routeIs('posts.*') ? 'bg-gray-100 font-semibold' : 'hover:bg-gray-100' }}">
                    <span>📝</span>
                    <span class="ml-3">Posts</span>
                </a>
            </li>

            {{-- Settings --}}
            <li>
                <a href="#" class="flex items-center p-2 rounded-lg hover:bg-gray-100">
                    <span>⚙️</span>
                    <span class="ml-3">Settings</span>
                </a>
            </li>

        </ul>
    </div>
</aside>
