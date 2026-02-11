<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Manager</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans bg-gray-100 text-gray-800">
    <x-navbar />
    <x-sidebar />

    <main class="min-h-screen pt-20 md:ml-64 p-6">
        <div class="px-3 py-3">
            {{ $slot }}
        </div>
    </main>

    @livewireScripts
    @stack('scripts')
</body>

</html>
