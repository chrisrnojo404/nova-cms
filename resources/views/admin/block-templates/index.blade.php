<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Builder system</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Builder templates</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Save, edit, and reuse structured block compositions across the CMS.</p>
            </div>

            <a href="{{ route('admin.block-templates.create') }}" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                Create template
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Import template</p>
                    <p class="mt-2 text-sm leading-7 text-slate-500 dark:text-slate-400">Paste exported template JSON or upload a `.json` file to reuse a composition in this workspace.</p>
                </div>

                <form method="POST" action="{{ route('admin.block-templates.import') }}" enctype="multipart/form-data" class="grid gap-4">
                    @csrf
                    <textarea name="template_json" rows="8" placeholder='{"name":"Template","scope":"both","blocks":[...]}' class="w-full rounded-3xl border-slate-300 bg-slate-950 px-4 py-4 font-mono text-sm leading-7 text-cyan-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700">{{ old('template_json') }}</textarea>
                    <input type="file" name="template_file" accept=".json,.txt" class="w-full rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                    <div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                            Import template
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
            @forelse ($templates as $template)
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $template->name }}</p>
                        <span class="rounded-full border border-slate-200 px-2 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500 dark:border-slate-700 dark:text-slate-400">{{ $template->scope }}</span>
                    </div>
                    <p class="mt-3 text-sm leading-7 text-slate-500 dark:text-slate-400">{{ $template->description ?: 'Reusable builder template.' }}</p>
                    <div class="mt-4 text-xs uppercase tracking-[0.22em] text-slate-400 dark:text-slate-500">
                        {{ count($template->blocks ?? []) }} blocks
                    </div>
                    <div class="mt-6 flex items-center gap-4 text-sm">
                        <a href="{{ route('admin.block-templates.edit', $template) }}" class="font-medium text-cyan-700 transition hover:text-cyan-500 dark:text-cyan-300 dark:hover:text-cyan-200">Edit</a>
                        <a href="{{ route('admin.block-templates.export', $template) }}" class="font-medium text-slate-700 transition hover:text-cyan-500 dark:text-slate-200 dark:hover:text-cyan-200">Export</a>
                        <form method="POST" action="{{ route('admin.block-templates.destroy', $template) }}" onsubmit="return confirm('Delete this builder template?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-rose-600 transition hover:text-rose-500 dark:text-rose-300 dark:hover:text-rose-200">Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-400 lg:col-span-2 xl:col-span-3">
                    No builder templates yet. Create one to make your page-builder patterns reusable across content types.
                </div>
            @endforelse
        </div>

        {{ $templates->links() }}
    </div>
</x-app-layout>
