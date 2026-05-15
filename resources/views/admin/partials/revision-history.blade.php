<section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Revision history</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Restore a previous saved state if the current draft goes sideways.</p>
        </div>
        <div class="rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:border-slate-700 dark:text-slate-400">
            {{ $revisions->count() }} recent revisions
        </div>
    </div>

    <div class="mt-5 space-y-3">
        @forelse ($revisions as $revision)
            <article class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-800 dark:bg-slate-950/60">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $revision->label }}</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            {{ $revision->created_at?->format('M j, Y g:i A') }} by {{ $revision->user?->name ?? 'System' }}
                        </p>
                    </div>

                    <form method="POST" action="{{ $restoreRoute($revision) }}" onsubmit="return confirm('Restore this revision? The current state will be backed up first.');">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                            Restore revision
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/60 dark:text-slate-400">
                No revisions yet. The first saved version will appear here automatically.
            </div>
        @endforelse
    </div>
</section>
