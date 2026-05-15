<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <p class="text-sm font-semibold text-slate-900 dark:text-white">Draft autosave</p>
    <p class="mt-2 text-sm leading-7 text-slate-500 dark:text-slate-400">Keep a browser-side snapshot of the current form while you edit, then restore it if you leave and come back.</p>
    <p class="mt-4 text-sm text-slate-500 dark:text-slate-400" x-text="status"></p>
    <div class="mt-4 flex flex-wrap gap-3">
        <button type="button" @click="restore()" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200">Restore draft snapshot</button>
        <button type="button" @click="clear()" class="rounded-full border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/40">Clear draft snapshot</button>
    </div>
</div>
