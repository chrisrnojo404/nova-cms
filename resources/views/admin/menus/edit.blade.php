<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Navigation module</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Edit {{ $menu->name }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage menu settings, item hierarchy, and menu placement.</p>
            </div>
            <a href="{{ route('admin.menus.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                Back to menus
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.menus.update', $menu) }}">
            @csrf
            @method('PUT')
            @include('admin.menus.partials.form', ['submitLabel' => 'Save menu'])
        </form>

        <section class="grid gap-6 xl:grid-cols-[0.82fr_1.18fr]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Add menu item</p>
                <p class="mt-2 text-sm leading-7 text-slate-500 dark:text-slate-400">Link to pages, posts, category archives, or custom URLs. Parent items create simple nested navigation.</p>

                <form method="POST" action="{{ route('admin.menu-items.store', $menu) }}" class="mt-6 space-y-5">
                    @csrf
                    @include('admin.menus.partials.item-form', [
                        'item' => new \App\Models\MenuItem(['target' => 'same_tab', 'position' => $menu->items->count()]),
                        'submitLabel' => 'Add item',
                    ])
                </form>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Menu items</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Use `position` for order and `parent` to create dropdown-style hierarchy.</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($menu->items->sortBy('position') as $item)
                            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/40">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $item->resolved_title }}</p>
                                        <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                            {{ $item->linked_type }} @if ($item->parent) · child of {{ $item->parent->resolved_title }} @endif
                                        </p>
                                    </div>
                                    <a href="{{ $item->resolved_url }}" target="{{ $item->target === 'new_tab' ? '_blank' : '_self' }}" rel="noreferrer" class="text-sm font-medium text-cyan-700 transition hover:text-cyan-500 dark:text-cyan-300 dark:hover:text-cyan-200">
                                        Preview link
                                    </a>
                                </div>

                                <form method="POST" action="{{ route('admin.menu-items.update', [$menu, $item]) }}" class="mt-5 space-y-5">
                                    @csrf
                                    @method('PUT')
                                    @include('admin.menus.partials.item-form', ['submitLabel' => 'Update item'])
                                </form>

                                <form method="POST" action="{{ route('admin.menu-items.destroy', [$menu, $item]) }}" class="mt-4" onsubmit="return confirm('Delete this menu item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-rose-600 transition hover:text-rose-500 dark:text-rose-300 dark:hover:text-rose-200">
                                        Delete item
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-400">
                                This menu does not have items yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
