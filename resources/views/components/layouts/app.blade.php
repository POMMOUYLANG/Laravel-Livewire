<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Manager</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="">

    <!-- Navbar -->
    <x-navbar />

    <!-- Sidebar -->
    <x-sidebar />

    <!-- Main Content -->
    <main class="bg-grey-100 text-gray-800 min-h-screen pt-20 md:ml-64 p-6">
        <div class="container mx-auto px-x py-6">
            {{ $slot }}
        </div>
    </main>

    @livewireScripts()
</body>

</html>
