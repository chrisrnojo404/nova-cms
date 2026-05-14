<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Plugin system</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Manage plugins</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Discover installed plugins, activate extensions, and expose shortcodes and routes to the frontend.</p>
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
            @foreach ($plugins as $plugin)
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-600 dark:text-cyan-400">Plugin</p>
                            <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $plugin->name }}</h2>
                            <p class="mt-2 text-sm leading-7 text-slate-500 dark:text-slate-400">{{ $plugin->description }}</p>
                        </div>
                        <span class="{{ $plugin->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]">
                            {{ $plugin->is_active ? 'active' : 'inactive' }}
                        </span>
                    </div>

                    <dl class="mt-6 grid gap-3 text-sm text-slate-500 dark:text-slate-400">
                        <div class="flex items-center justify-between gap-4">
                            <dt>Slug</dt>
                            <dd>{{ $plugin->slug }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt>Version</dt>
                            <dd>{{ $plugin->version }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt>Path</dt>
                            <dd>{{ $plugin->path }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt>Shortcodes</dt>
                            <dd>{{ implode(', ', $plugin->meta['shortcodes'] ?? []) ?: 'None' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex flex-wrap gap-3">
                        @if ($plugin->is_active)
                            <form method="POST" action="{{ route('admin.plugins.deactivate', $plugin) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-rose-300 hover:text-rose-600 dark:border-slate-700 dark:text-slate-200 dark:hover:border-rose-700 dark:hover:text-rose-300">
                                    Deactivate
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.plugins.activate', $plugin) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                                    Activate
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </section>
    </div>
</x-app-layout>
