@php
    $title = $title ?? (($siteSettings['site_name'] ?? 'Nova CMS').' Preview');
    $description = $description ?? ($siteSettings['site_tagline'] ?? 'Commercial-ready Laravel CMS foundation');
@endphp

@extends('theme::layouts.app')

@section('content')
    <section class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Default Theme Experience</p>
            <h1 class="mt-6 text-5xl font-semibold tracking-tight text-slate-900 dark:text-white sm:text-6xl">
                Production-minded CMS architecture for pages, posts, themes, plugins, and APIs.
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600 dark:text-slate-300">
                {{ $siteSettings['site_tagline'] ?? 'Commercial-ready Laravel CMS foundation' }}
            </p>

            <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                    Open admin login
                </a>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                    Go to dashboard
                </a>
            </div>
        </div>

        <aside class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/50 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">Theme rendering</p>
            <div class="mt-5 space-y-4 text-sm leading-7 text-slate-500 dark:text-slate-400">
                <p>The active theme now owns the frontend layout, page templates, post views, and shared partials.</p>
                <p>Menus, settings, content, and media all flow through the theme layer instead of hardcoded resource views.</p>
            </div>
        </aside>
    </section>
@endsection
