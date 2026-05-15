<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Pages module</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Create page</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Draft or publish a new website page with CMS-managed metadata and routing.</p>
        </div>
    </x-slot>

    <form
        method="POST"
        action="{{ route('admin.pages.store') }}"
        class="space-y-6"
        x-data='draftSnapshot({ key: @js($draftAutosaveKey), fields: ["title","slug","content","status","template","featured_image","meta_title","meta_description","builder_blocks"] })'
    >
        @csrf
        @include('admin.pages.partials.form', ['submitLabel' => 'Create page'])
    </form>
</x-app-layout>
