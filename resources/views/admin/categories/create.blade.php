<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Taxonomy module</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Create category</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Define a reusable content category for the blog and future modules.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-6">
        @csrf
        @include('admin.categories.partials.form', ['submitLabel' => 'Create category'])
    </form>
</x-app-layout>
