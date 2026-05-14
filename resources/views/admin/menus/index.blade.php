<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Navigation module</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Manage menus</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create assignable navigation groups for the header, footer, and custom placements.</p>
            </div>

            <a href="{{ route('admin.menus.create') }}" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                New menu
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-950/50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">
                            <th class="px-6 py-4">Menu</th>
                            <th class="px-6 py-4">Location</th>
                            <th class="px-6 py-4">Items</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Updated</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($menus as $menu)
                            <tr>
                                <td class="px-6 py-4 align-top">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $menu->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $menu->slug }}</p>
                                </td>
                                <td class="px-6 py-4 align-top text-sm text-slate-600 dark:text-slate-300">
                                    {{ $menu->location ?: 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 align-top text-sm text-slate-600 dark:text-slate-300">
                                    {{ $menu->items_count }}
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <span class="{{ $menu->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]">
                                        {{ $menu->is_active ? 'active' : 'inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 align-top text-sm text-slate-500 dark:text-slate-400">
                                    {{ $menu->updated_at?->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-wrap gap-3 text-sm">
                                        <a href="{{ route('admin.menus.edit', $menu) }}" class="font-medium text-cyan-700 transition hover:text-cyan-500 dark:text-cyan-300 dark:hover:text-cyan-200">Edit</a>
                                        <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" onsubmit="return confirm('Delete this menu and its items?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-rose-600 transition hover:text-rose-500 dark:text-rose-300 dark:hover:text-rose-200">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                    No menus yet. Create the first navigation set to power the public frontend.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>
            {{ $menus->links() }}
        </div>
    </div>
</x-app-layout>
