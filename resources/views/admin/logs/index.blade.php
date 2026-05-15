<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Audit module</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Activity logs</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Review the operational audit trail across authentication, content, themes, plugins, backups, and admin actions.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form method="GET" action="{{ route('admin.logs.index') }}" class="grid gap-4 xl:grid-cols-[1fr_240px_260px_auto]">
                <div>
                    <label for="search" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Search</label>
                    <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}" placeholder="Event, description, or subject" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </div>

                <div>
                    <label for="user_id" class="text-sm font-semibold text-slate-700 dark:text-slate-200">User</label>
                    <select id="user_id" name="user_id" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="">All users</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="event" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Event</label>
                    <select id="event" name="event" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="">All events</option>
                        @foreach ($events as $event)
                            <option value="{{ $event }}" @selected(($filters['event'] ?? '') === $event)>{{ $event }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                        Filter
                    </button>
                    <a href="{{ route('admin.logs.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-950/50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">
                            <th class="px-6 py-4">Description</th>
                            <th class="px-6 py-4">Event</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Subject</th>
                            <th class="px-6 py-4">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-6 py-4 align-top">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $log->description }}</p>
                                    @if ($log->properties)
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                            {{ collect($log->properties->getArrayCopy())->take(3)->map(fn ($value, $key) => $key.': '.(is_array($value) ? implode(', ', $value) : (is_bool($value) ? ($value ? 'true' : 'false') : $value)))->implode(' • ') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-top text-sm text-slate-600 dark:text-slate-300">{{ $log->event }}</td>
                                <td class="px-6 py-4 align-top text-sm text-slate-600 dark:text-slate-300">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="px-6 py-4 align-top text-sm text-slate-500 dark:text-slate-400">{{ class_basename((string) $log->subject_type) }}{{ $log->subject_id ? ' #'.$log->subject_id : '' }}</td>
                                <td class="px-6 py-4 align-top text-sm text-slate-500 dark:text-slate-400">{{ $log->created_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                    No log entries matched the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
