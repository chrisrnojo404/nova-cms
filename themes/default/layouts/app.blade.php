<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @php
            $seoTitle = $title ?? ($siteSettings['site_name'] ?? 'Nova CMS');
            $seoDescription = $description ?: ($seoSettings['default_meta_description'] ?? ($siteSettings['site_tagline'] ?? 'Commercial-ready Laravel CMS foundation'));
            $seoCanonical = $canonical ?? request()->url();
            $seoRobots = $robots ?? ($seoSettings['meta_robots'] ?? 'index,follow');
            $seoOgImage = $ogImage ?? null;
            $seoOgType = $ogType ?? (request()->routeIs('posts.show', 'pages.show') ? 'article' : 'website');
        @endphp
        <title>{{ $seoTitle }}</title>
        @if (! empty($seoDescription))
            <meta name="description" content="{{ $seoDescription }}">
        @endif
        <meta name="robots" content="{{ $seoRobots }}">
        <link rel="canonical" href="{{ $seoCanonical }}">
        <meta property="og:title" content="{{ $seoTitle }}">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:type" content="{{ $seoOgType }}">
        <meta property="og:url" content="{{ $seoCanonical }}">
        <meta property="og:site_name" content="{{ $seoSettings['og_site_name'] ?? ($siteSettings['site_name'] ?? 'Nova CMS') }}">
        <meta name="twitter:card" content="{{ $seoSettings['twitter_card'] ?? 'summary_large_image' }}">
        <meta name="twitter:title" content="{{ $seoTitle }}">
        <meta name="twitter:description" content="{{ $seoDescription }}">
        @if ($seoOgImage)
            <meta property="og:image" content="{{ $seoOgImage }}">
            <meta name="twitter:image" content="{{ $seoOgImage }}">
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
