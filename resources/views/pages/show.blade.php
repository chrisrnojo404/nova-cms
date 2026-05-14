<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $page->meta_title ?: $page->title }}</title>
        <meta name="description" content="{{ $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content ?? ''), 155) }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(34,211,238,0.12),_transparent_28%),linear-gradient(180deg,_rgba(255,255,255,0.2),_rgba(255,255,255,0))] dark:bg-[radial-gradient(circle_at_top,_rgba(34,211,238,0.14),_transparent_28%),linear-gradient(180deg,_rgba(15,23,42,0.9),_rgba(2,6,23,1))]">
            <main class="mx-auto max-w-4xl px-6 py-12">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('home') }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-300 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                        Back to preview
                    </a>

                    @if ($headerMenu?->rootItems->count())
                        <x-public-menu :items="$headerMenu->rootItems" class="justify-end" />
                    @endif
                </div>

                <article class="mt-8 overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-200/60 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
                    <div class="px-8 py-10 sm:px-12 sm:py-12">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">{{ $page->template ?: 'default' }} template</p>
                        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-slate-900 dark:text-white sm:text-5xl">{{ $page->title }}</h1>
                        <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">
                            Published {{ optional($page->published_at)->format('F j, Y') }} by {{ $page->author?->name ?? 'Nova CMS' }}
                        </p>

                        @if ($page->featured_image)
                            <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800">
                                <img src="{{ $page->featured_image }}" alt="{{ $page->title }}" class="h-auto w-full object-cover">
                            </div>
                        @endif

                        <div class="prose prose-slate mt-10 max-w-none prose-headings:text-slate-900 prose-p:text-slate-700 dark:prose-invert dark:prose-p:text-slate-300">
                            {!! $page->content !!}
                        </div>
                    </div>
                </article>

                <div class="mt-10 space-y-6">
                    @if ($footerMenu?->rootItems->count())
                        <div class="rounded-3xl bg-white/70 px-6 py-5 backdrop-blur dark:bg-slate-900/70">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">Footer menu</p>
                            <x-public-menu :items="$footerMenu->rootItems" class="mt-4" />
                        </div>
                    @endif

                    <x-developer-credit class="rounded-3xl bg-white/70 backdrop-blur dark:bg-slate-900/70" />
                </div>
            </main>
        </div>
    </body>
</html>
