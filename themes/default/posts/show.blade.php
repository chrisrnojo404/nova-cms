@php
    $title = $post->meta_title ?: $post->title;
    $description = $post->meta_description ?: $post->excerpt;
@endphp

@extends('theme::layouts.app')

@section('content')
    <article class="mx-auto max-w-4xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-200/60 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
        <div class="px-8 py-10 sm:px-12 sm:py-12">
            @if ($post->category)
                <a href="{{ route('posts.category', $post->category->slug) }}" class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-600 dark:text-cyan-400">
                    {{ $post->category->name }}
                </a>
            @endif
            <h1 class="mt-4 text-4xl font-semibold tracking-tight text-slate-900 dark:text-white sm:text-5xl">{{ $post->title }}</h1>
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
@endsection
