<aside
    id="sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 bg-white border-r border-gray-200
           -translate-x-full md:translate-x-0 transition-transform"
>
    <div class="h-full px-3 py-4 overflow-y-auto">
        <ul class="space-y-2 text-sm font-medium">

            <li>
                <a href="/"
                class="flex items-center p-2 rounded-lg hover:bg-gray-100">
                <span>📊</span>
                <span class="ml-3">Dashboard</span>
            </a>
        </li>

                    <li>
                        <a href="{{ route('students.index') }}"
                           class="flex items-center p-2 rounded-lg
                           {{ request()->routeIs('students.index')
                                ? 'bg-gray-100 font-semibold'
                                : 'hover:bg-gray-100' }}">
                            <span>🎓</span>
                            <span class="ml-3">Students</span>
                        </a>
                    </li>

            <li>
                <a href="#"
                   class="flex items-center p-2 rounded-lg hover:bg-gray-100">
                    <span>⚙️</span>
                    <span class="ml-3">Settings</span>
                </a>
            </li>

        </ul>
    </div>
</aside>
