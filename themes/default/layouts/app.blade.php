<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? ($siteSettings['site_name'] ?? 'Nova CMS') }}</title>
        @if (! empty($description))
            <meta name="description" content="{{ $description }}">
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(34,211,238,0.12),_transparent_28%),linear-gradient(180deg,_rgba(255,255,255,0.24),_rgba(255,255,255,0))] dark:bg-[radial-gradient(circle_at_top,_rgba(34,211,238,0.16),_transparent_28%),linear-gradient(180deg,_rgba(15,23,42,0.9),_rgba(2,6,23,1))]">
            @include('theme::partials.header')

            <main class="mx-auto max-w-6xl px-6 py-12">
                @yield('content')
            </main>

            @include('theme::partials.footer')
        </div>
    </body>
</html>
