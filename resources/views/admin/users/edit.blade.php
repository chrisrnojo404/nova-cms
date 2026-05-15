<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Users module</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Edit user</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update account details, change the role, or reset the password for this workspace user.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.users.partials.form', ['submitLabel' => 'Save changes', 'isEditing' => true])
    </form>
</x-app-layout>
