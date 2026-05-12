<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Module scaffold</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $title }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
        </div>
    </x-slot>

    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            This area is intentionally reserved for the next implementation phase so the admin information architecture is already visible.
        </p>
    </div>
</x-app-layout>
