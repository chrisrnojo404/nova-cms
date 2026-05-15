<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Builder system</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Create builder template</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Turn a useful block composition into a reusable CMS asset.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.block-templates.store') }}" class="space-y-6">
        @csrf
        @include('admin.block-templates.partials.form', ['submitLabel' => 'Create template'])
    </form>
</x-app-layout>
