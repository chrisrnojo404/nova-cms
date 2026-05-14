<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Admin dashboard</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Nova CMS control center</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Phase 1 establishes authentication, access control, API security, project structure, and the admin shell.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            @foreach ($stats as $stat)
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/60 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">{{ $stat['label'] }}</p>
                    <p class="mt-4 text-3xl font-semibold text-slate-900 dark:text-white">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $stat['hint'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/60 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Recent activity</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">Audit trail</h2>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($recentActivity as $activity)
                        <div class="flex items-start justify-between gap-4 rounded-2xl border border-slate-200/70 px-4 py-3 dark:border-slate-800">
                            <div>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $activity->description }}</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $activity->user?->name ?? 'System' }} • {{ $activity->event }}
                                </p>
                            </div>
                            <p class="shrink-0 text-xs font-medium text-slate-400">{{ $activity->created_at?->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            Activity logs will populate here as the team uses the CMS.
                        </div>
                    @endforelse
                </div>
            </article>

            <div class="space-y-6">
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/60 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Quick actions</p>
                    <div class="mt-5 grid gap-3">
                        @foreach ($quickActions as $action)
                            @if (Route::has($action['route']))
                                <a href="{{ route($action['route']) }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-800 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                                    {{ $action['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </article>

                <article class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-900 to-cyan-900 p-6 text-white shadow-sm shadow-slate-900/20 dark:border-slate-800">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-200">Platform readiness</p>
                    <h2 class="mt-3 text-xl font-semibold">Nova CMS now has a protected admin spine.</h2>
                    <ul class="mt-4 space-y-3 text-sm text-slate-200">
                        <li>Authentication, email verification, sessions, and rate-limited login are active.</li>
                        <li>Roles and permissions are seeded for `super-admin`, `admin`, `editor`, and `author`.</li>
                        <li>Theme, plugin, settings, audit logging, Docker, and API foundations are in place.</li>
                    </ul>
                </article>
            </div>
        </section>
    </div>
</x-app-layout>
