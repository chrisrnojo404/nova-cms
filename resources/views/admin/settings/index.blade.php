<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Settings module</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Site settings</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Control the site identity, homepage behavior, reading defaults, and media preferences from one place.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            @csrf
            @method('PUT')

            <section class="space-y-6">
                @foreach ($definitions as $group => $fields)
                    <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-600 dark:text-cyan-400">{{ $group }}</p>
                            <h2 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ ucfirst($group) }} settings</h2>
                        </div>

                        <div class="mt-6 grid gap-5">
                            @foreach ($fields as $field)
                                <div>
                                    <label for="{{ $field['key'] }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $field['label'] }}</label>

                                    @if ($field['type'] === 'textarea')
                                        <textarea id="{{ $field['key'] }}" name="{{ $field['key'] }}" rows="3" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old($field['key'], $values[$field['key']] ?? $field['default']) }}</textarea>
                                    @elseif ($field['type'] === 'select')
                                        <select id="{{ $field['key'] }}" name="{{ $field['key'] }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                            @foreach ($field['options'] as $value => $label)
                                                <option value="{{ $value }}" @selected(old($field['key'], $values[$field['key']] ?? $field['default']) === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($field['type'] === 'page_select')
                                        <select id="{{ $field['key'] }}" name="{{ $field['key'] }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                            <option value="">Use preview landing page</option>
                                            @foreach ($pages as $page)
                                                <option value="{{ $page->id }}" @selected((string) old($field['key'], $values[$field['key']] ?? '') === (string) $page->id)>{{ $page->title }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input id="{{ $field['key'] }}" name="{{ $field['key'] }}" type="{{ $field['type'] }}" value="{{ old($field['key'], $values[$field['key']] ?? $field['default']) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                    @endif

                                    @error($field['key']) <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </section>

            <aside class="space-y-6">
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Live effects</p>
                    <ul class="mt-4 space-y-3 text-sm leading-7 text-slate-500 dark:text-slate-400">
                        <li>`Site name` and `tagline` update the public-facing preview language.</li>
                        <li>`Homepage mode` can switch `/` from the preview landing page to a published CMS page.</li>
                        <li>`Posts per page` controls blog and category pagination.</li>
                        <li>`Media upload directory` becomes the default folder for new uploads.</li>
                    </ul>
                </article>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                        Save settings
                    </button>
                </div>
            </aside>
        </form>
    </div>
</x-app-layout>
