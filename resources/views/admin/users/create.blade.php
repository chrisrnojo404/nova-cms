<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Users module</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Create user</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Provision a new Nova CMS account and assign the right editorial role from day one.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf
        @include('admin.users.partials.form', ['submitLabel' => 'Create user', 'isEditing' => false])
    </form>
</x-app-layout>
