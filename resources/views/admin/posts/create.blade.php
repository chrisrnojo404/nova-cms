<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Blog module</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Create post</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Write a new article and prepare it for category-based publishing.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.posts.store') }}" class="space-y-6">
        @csrf
        @include('admin.posts.partials.form', ['submitLabel' => 'Create post'])
    </form>
</x-app-layout>
