<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Nova CMS Blog</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        <main class="mx-auto max-w-6xl px-6 py-12">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('home') }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-300 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                    Back to preview
                </a>
                @if ($headerMenu?->rootItems->count())
                    <x-public-menu :items="$headerMenu->rootItems" class="justify-end" />
                @endif
            </div>

            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Nova CMS blog</p>
                <h1 class="mt-4 text-5xl font-semibold tracking-tight text-slate-900 dark:text-white">Editorial publishing, now live.</h1>
                <p class="mt-5 text-lg leading-8 text-slate-600 dark:text-slate-300">This Phase 2 blog layer renders published posts, categories, and single-article pages directly from the CMS.</p>
            </div>

            <section class="mt-12 grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
                @forelse ($posts as $post)
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        @if ($post->category)
                            <a href="{{ route('posts.category', $post->category->slug) }}" class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-600 dark:text-cyan-400">
                                {{ $post->category->name }}
                            </a>
                        @endif
                        <h2 class="mt-4 text-2xl font-semibold text-slate-900 dark:text-white">
                            <a href="{{ route('posts.show', $post->slug) }}" class="transition hover:text-cyan-600 dark:hover:text-cyan-300">
                                {{ $post->title }}
                            </a>
                        </h2>
                        <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $post->excerpt }}</p>
                        <div class="mt-6 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400">
                            <span>{{ $post->author?->name ?? 'Nova CMS' }}</span>
                            <span>{{ optional($post->published_at)->format('M j, Y') }}</span>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[2rem] border border-dashed border-slate-300 px-6 py-16 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400 lg:col-span-2 xl:col-span-3">
                        No published posts yet.
                    </div>
                @endforelse
            </section>

            <div class="mt-10">
                {{ $posts->links() }}
            </div>

            <div class="mt-12 space-y-6">
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
