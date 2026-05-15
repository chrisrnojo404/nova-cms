<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Builder system</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Edit builder template</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Maintain the reusable composition your editors can apply across pages and posts.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.block-templates.update', $blockTemplate) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.block-templates.partials.form', ['submitLabel' => 'Save template'])
        </form>
    </div>
</x-app-layout>
