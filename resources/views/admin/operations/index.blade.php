<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Operations</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Production operations center</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Run backups, refresh CMS caches, and keep an eye on queue-backed operational health.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Queue connection</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $queueConnection }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Pending jobs</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $pendingJobs }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Failed jobs</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $failedJobs }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Backup retention</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $settings['backup_retention_days'] }} days</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-lg font-semibold text-slate-900 dark:text-white">Operational actions</p>
                        <p class="mt-1 text-sm leading-7 text-slate-500 dark:text-slate-400">Queue a portable CMS backup snapshot or refresh the public CMS caches after theme, SEO, or menu work.</p>
                    </div>
                    <div class="rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700 dark:border-cyan-900/60 dark:bg-cyan-950/40 dark:text-cyan-300">
                        Scheduler-ready
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('admin.operations.backups.queue') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                            Queue backup snapshot
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.operations.caches.refresh') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                            Refresh CMS caches
                        </button>
                    </form>
                </div>

                <div class="mt-8 rounded-3xl border border-amber-200 bg-amber-50/70 p-5 dark:border-amber-900/60 dark:bg-amber-950/20">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Restore a backup snapshot</p>
                    <p class="mt-2 text-sm leading-7 text-slate-500 dark:text-slate-400">Upload a Nova backup `.json` or `.zip` archive to replace the captured CMS tables. A safety backup is created first by default.</p>

                    <form method="POST" action="{{ route('admin.operations.backups.restore') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="backup_snapshot" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Backup snapshot file</label>
                            <input id="backup_snapshot" name="backup_snapshot" type="file" accept=".json,.zip" class="mt-2 block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            @error('backup_snapshot') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                            <input type="checkbox" name="create_safety_backup" value="1" checked class="mt-1 rounded border-slate-300 text-cyan-500 focus:ring-cyan-400">
                            <span>Create a safety backup of the current CMS state before importing this snapshot.</span>
                        </label>

                        <label class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-white px-4 py-3 text-sm text-slate-600 dark:border-rose-900/60 dark:bg-slate-900 dark:text-slate-300">
                            <input type="checkbox" name="confirmation" value="1" class="mt-1 rounded border-slate-300 text-rose-500 focus:ring-rose-400">
                            <span>I understand this will replace CMS tables included in the snapshot and may overwrite content, settings, menus, users, and access roles.</span>
                        </label>
                        @error('confirmation') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror

                        <button type="submit" class="inline-flex items-center justify-center rounded-full border border-amber-400 bg-amber-300 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-200">
                            Restore snapshot
                        </button>
                    </form>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/60">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Scheduled cadence</p>
                        <ul class="mt-3 space-y-2 text-sm leading-7 text-slate-500 dark:text-slate-400">
                            <li>Daily backup dispatch at <span class="font-semibold text-slate-700 dark:text-slate-200">02:00</span></li>
                            <li>Backup pruning at <span class="font-semibold text-slate-700 dark:text-slate-200">02:30</span></li>
                            <li>Failed queue pruning at <span class="font-semibold text-slate-700 dark:text-slate-200">03:00</span></li>
                        </ul>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/60">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Deployment reminders</p>
                        <ul class="mt-3 space-y-2 text-sm leading-7 text-slate-500 dark:text-slate-400">
                            <li>Run a queue worker in production for queued backups.</li>
                            <li>Run `php artisan schedule:work` or a cron entry for the scheduler.</li>
                            <li>Keep `storage/app/backups` on persistent storage.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-lg font-semibold text-slate-900 dark:text-white">Strategy</p>
                <div class="mt-4 space-y-4 text-sm leading-7 text-slate-500 dark:text-slate-400">
                    <p>Backups are created as portable CMS snapshots containing the core database tables and, when running on SQLite, the raw database file inside the generated archive.</p>
                    <p>Caches use explicit invalidation after settings, SEO, menu, and theme changes so the public site stays fresh without full cache flushes.</p>
                    <p>Recent artifacts can be downloaded directly after completion from the run history below, and uploaded snapshots can be restored from this screen when recovery is needed.</p>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-lg font-semibold text-slate-900 dark:text-white">Recent backup runs</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Latest queued and completed backup artifacts created by the operations layer.</p>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-950/60">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Run</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Initiated by</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Completed</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Artifact</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                        @forelse ($backupRuns as $run)
                            <tr>
                                <td class="px-5 py-4 align-top text-sm text-slate-700 dark:text-slate-200">
                                    <p class="font-semibold">#{{ $run->id }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $run->queue_connection ?: 'n/a' }}</p>
                                </td>
                                <td class="px-5 py-4 align-top text-sm">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] {{ $run->status === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : ($run->status === 'failed' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300') }}">
                                        {{ $run->status }}
                                    </span>
                                    @if ($run->error_message)
                                        <p class="mt-2 max-w-sm text-xs text-rose-500">{{ $run->error_message }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 align-top text-sm text-slate-500 dark:text-slate-400">
                                    {{ $run->user?->name ?? 'System' }}
                                </td>
                                <td class="px-5 py-4 align-top text-sm text-slate-500 dark:text-slate-400">
                                    {{ $run->completed_at?->diffForHumans() ?? 'In progress' }}
                                </td>
                                <td class="px-5 py-4 align-top text-sm text-slate-500 dark:text-slate-400">
                                    @if ($run->artifact_path)
                                        <a href="{{ route('admin.operations.backups.download', $run) }}" class="font-semibold text-cyan-600 hover:text-cyan-500 dark:text-cyan-300 dark:hover:text-cyan-200">
                                            Download
                                        </a>
                                        <p class="mt-1 text-xs">{{ number_format(((int) $run->artifact_size) / 1024, 1) }} KB</p>
                                    @else
                                        Pending artifact
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                    No backup runs yet. Queue the first snapshot to bootstrap the operational history.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
