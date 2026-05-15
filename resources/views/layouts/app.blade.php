<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Nova CMS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            let storedTheme = null;

            try {
                storedTheme = window.localStorage.getItem('nova-theme');
            } catch (_error) {
                storedTheme = null;
            }

            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const shouldUseDark = storedTheme ? storedTheme === 'dark' : prefersDark;

            document.documentElement.classList.toggle('dark', shouldUseDark);
        </script>
    </head>
    <body class="bg-slate-950 font-sans antialiased text-slate-900 dark:text-slate-100">
        <div class="min-h-screen bg-slate-100 dark:bg-slate-950">
            @include('layouts.navigation')

            <div class="lg:pl-72">
                @isset($header)
                    <header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80">
                        <div class="flex items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                            <div>
                                {{ $header }}
                            </div>

                            <button
                                type="button"
                                x-data
                                @click="
                                    const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
                                    document.documentElement.classList.toggle('dark', next === 'dark');
                                    try {
                                        localStorage.setItem('nova-theme', next);
                                    } catch (_error) {
                                        // Theme preference still applies for this request even if storage is unavailable.
                                    }
                                "
                                class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300"
                            >
                                Toggle theme
                            </button>
                        </div>
                    </header>
                @endisset

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>

                <x-developer-credit class="mt-10" />
            </div>
        </div>
    </body>
</html>
