@php
    $title = $page->meta_title ?: $page->title;
    $description = $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content ?? ''), 155);
@endphp

@extends('theme::layouts.app')

@section('content')
    <article class="mx-auto max-w-4xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-200/60 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
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
@endsection
