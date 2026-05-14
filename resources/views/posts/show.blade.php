<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $post->meta_title ?: $post->title }}</title>
        <meta name="description" content="{{ $post->meta_description ?: $post->excerpt }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        <main class="mx-auto max-w-4xl px-6 py-12">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('posts.index') }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-300 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                    Back to blog
                </a>
                @if ($post->category)
                    <a href="{{ route('posts.category', $post->category->slug) }}" class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-600 dark:text-cyan-400">
                        {{ $post->category->name }}
                    </a>
                @endif
                </div>
                @if ($headerMenu?->rootItems->count())
                    <x-public-menu :items="$headerMenu->rootItems" class="justify-end" />
                @endif
            </div>

            <article class="mt-8 overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-200/60 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
                <div class="px-8 py-10 sm:px-12 sm:py-12">
                    <h1 class="text-4xl font-semibold tracking-tight text-slate-900 dark:text-white sm:text-5xl">{{ $post->title }}</h1>
                    <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-500 dark:text-slate-400">
                        <span>By {{ $post->author?->name ?? 'Nova CMS' }}</span>
                        <span>{{ optional($post->published_at)->format('F j, Y') }}</span>
                    </div>
                    @if ($post->excerpt)
                        <p class="mt-6 text-lg leading-8 text-slate-600 dark:text-slate-300">{{ $post->excerpt }}</p>
                    @endif

                    @if ($post->featured_image)
                        <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800">
                            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="h-auto w-full object-cover">
                        </div>
                    @endif

                    <div class="prose prose-slate mt-10 max-w-none prose-headings:text-slate-900 prose-p:text-slate-700 dark:prose-invert dark:prose-p:text-slate-300">
                        {!! $post->content !!}
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
    </body>
</html>
