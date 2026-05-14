<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Theme engine</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Manage themes</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Discover installed themes, inspect their manifests, and activate the frontend presentation layer.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <section class="grid gap-6 lg:grid-cols-2">
            @foreach ($themes as $theme)
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-600 dark:text-cyan-400">Theme</p>
                            <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $theme->name }}</h2>
                            <p class="mt-2 text-sm leading-7 text-slate-500 dark:text-slate-400">{{ $theme->description }}</p>
                        </div>
                        <span class="{{ $theme->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]">
                            {{ $theme->is_active ? 'active' : 'inactive' }}
                        </span>
                    </div>

                    <dl class="mt-6 grid gap-3 text-sm text-slate-500 dark:text-slate-400">
                        <div class="flex items-center justify-between gap-4">
                            <dt>Slug</dt>
                            <dd>{{ $theme->slug }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt>Version</dt>
                            <dd>{{ $theme->version }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt>Author</dt>
                            <dd>{{ $theme->author ?: 'Unknown' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt>Path</dt>
                            <dd>{{ $theme->path }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex flex-wrap gap-3">
                        @if ($theme->is_active)
                            <span class="inline-flex items-center justify-center rounded-full border border-emerald-200 px-5 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-900/60 dark:text-emerald-300">
                                Currently active
                            </span>
                        @else
                            <form method="POST" action="{{ route('admin.themes.activate', $theme) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                                    Activate theme
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </section>
    </div>
</x-app-layout>
