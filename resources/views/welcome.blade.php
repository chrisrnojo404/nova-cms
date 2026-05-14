<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $siteSettings['site_name'] ?? 'Nova CMS' }} Preview</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 text-white antialiased">
        <div class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top,_rgba(34,211,238,0.28),_transparent_38%),radial-gradient(circle_at_bottom_right,_rgba(14,116,144,0.35),_transparent_34%),linear-gradient(160deg,_#020617,_#0f172a,_#111827)]">
            <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[size:52px_52px] opacity-20"></div>

            <main class="relative mx-auto flex min-h-screen max-w-6xl flex-col px-6 py-12">
                <div class="flex items-center justify-between gap-6">
                    <p class="inline-flex rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-white/80">
                        Public navigation
                    </p>
                    @if ($headerMenu?->rootItems->count())
                        <x-public-menu :items="$headerMenu->rootItems" theme="dark" class="justify-end" />
                    @endif
                </div>

                <div class="flex min-h-[calc(100vh-9rem)] flex-col justify-center">
                <div class="max-w-4xl">
                    <p class="inline-flex rounded-full border border-cyan-400/20 bg-cyan-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-cyan-100">
                        {{ $siteSettings['site_name'] ?? 'Nova CMS' }} preview
                    </p>
                    <h1 class="mt-8 text-5xl font-semibold tracking-tight sm:text-6xl">
                        Production-minded CMS architecture for pages, posts, themes, plugins, and APIs.
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">
                        {{ $siteSettings['site_tagline'] ?? 'Commercial-ready Laravel CMS foundation' }}
                    </p>

                    <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                            Open admin login
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 px-6 py-3 text-sm font-semibold text-white transition hover:border-white/40 hover:bg-white/5">
                            Go to dashboard
                        </a>
                    </div>
                </div>

                <div class="mt-24 w-full max-w-4xl self-start space-y-6">
                    @if ($footerMenu?->rootItems->count())
                        <div class="rounded-3xl border border-white/10 bg-white/5 px-6 py-5 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/50">Footer menu</p>
                            <x-public-menu :items="$footerMenu->rootItems" theme="dark" class="mt-4" />
                        </div>
                    @endif

                    <x-developer-credit light />
                </div>
                </div>
            </main>
        </div>
    </body>
</html>
