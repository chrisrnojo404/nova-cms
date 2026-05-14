<footer class="mx-auto max-w-6xl px-6 pb-12">
    <div class="space-y-6 rounded-[2rem] bg-white/70 px-6 py-6 backdrop-blur dark:bg-slate-900/70">
        @if ($footerMenu?->rootItems->count())
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">Footer menu</p>
                <x-public-menu :items="$footerMenu->rootItems" class="mt-4" />
            </div>
        @endif

        <x-developer-credit />
    </div>
</footer>
