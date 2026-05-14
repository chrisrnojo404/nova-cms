@php
    $title = $title ?? (($siteSettings['site_name'] ?? 'Nova CMS').' Blog');
@endphp

@extends('theme::layouts.app')

@section('content')
    <div class="max-w-3xl">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Nova CMS blog</p>
        <h1 class="mt-4 text-5xl font-semibold tracking-tight text-slate-900 dark:text-white">Editorial publishing, now live.</h1>
        <p class="mt-5 text-lg leading-8 text-slate-600 dark:text-slate-300">This theme renders published posts, categories, and single article pages directly from the CMS.</p>
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
@endsection
