<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TKDN') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-300">
    <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- Dark mode toggle -->
        <div class="absolute top-6 right-6">
            <button id="darkModeToggle" class="dark-mode-toggle" aria-label="Toggle dark mode">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="darkIcon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <svg class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="lightIcon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </button>
        </div>

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">PROCALIST</h1>
            <p class="text-gray-600 dark:text-gray-400">Welcome back</p>
        </div>

        <!-- Auth Card -->
        <div class="w-full max-w-md">
            <div class="card p-8 shadow-strong">
                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                © {{ date('Y') }} PROCALIST V0.01.25. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        // Dark mode toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkIcon = document.getElementById('darkIcon');
        const lightIcon = document.getElementById('lightIcon');
        const html = document.documentElement;

        function updateDarkMode() {
            const isDark = html.classList.contains('dark');
            darkIcon.classList.toggle('hidden', isDark);
            lightIcon.classList.toggle('hidden', !isDark);
        }

        // Initialize dark mode
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        }
        updateDarkMode();

        darkModeToggle?.addEventListener('click', function() {
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
            updateDarkMode();
        });
    </script>
</body>

</html>