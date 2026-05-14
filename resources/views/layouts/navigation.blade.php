@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard'],
        ['label' => 'Pages', 'route' => 'admin.pages.index', 'pattern' => 'admin.pages.*'],
        ['label' => 'Posts', 'route' => 'admin.posts.index', 'pattern' => 'admin.posts.*'],
        ['label' => 'Categories', 'route' => 'admin.categories.index', 'pattern' => 'admin.categories.*'],
        ['label' => 'Media', 'route' => 'admin.media.index', 'pattern' => 'admin.media.*'],
        ['label' => 'Menus', 'route' => 'admin.menus.index', 'pattern' => 'admin.menus.*'],
        ['label' => 'Themes', 'route' => 'admin.themes.index', 'pattern' => 'admin.themes.*'],
        ['label' => 'Plugins', 'route' => 'admin.plugins.index', 'pattern' => 'admin.plugins.*'],
        ['label' => 'Users', 'route' => 'admin.users.index', 'pattern' => 'admin.users.*'],
        ['label' => 'Settings', 'route' => 'admin.settings.index', 'pattern' => 'admin.settings.*'],
        ['label' => 'SEO', 'route' => 'admin.seo.index', 'pattern' => 'admin.seo.*'],
        ['label' => 'Logs', 'route' => 'admin.logs.index', 'pattern' => 'admin.logs.*'],
    ];
@endphp

<nav x-data="{ open: false }">
    <div class="fixed inset-x-0 top-0 z-30 border-b border-slate-200 bg-white/95 px-4 py-4 backdrop-blur dark:border-slate-800 dark:bg-slate-950/95 lg:hidden">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <x-application-logo class="h-10 w-10" />
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-600 dark:text-cyan-400">Nova CMS</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Phase 1 foundation</p>
                </div>
            </a>

            <button @click="open = ! open" class="rounded-full border border-slate-300 p-2 text-slate-700 dark:border-slate-700 dark:text-slate-200">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                    <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>
    </div>

    <aside :class="{ 'translate-x-0': open, '-translate-x-full': ! open }" class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white/95 px-5 pb-5 pt-6 shadow-2xl backdrop-blur transition-transform duration-300 dark:border-slate-800 dark:bg-slate-950/95 lg:translate-x-0">
        <div class="mt-14 lg:mt-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <x-application-logo class="h-11 w-11" />
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-600 dark:text-cyan-400">Nova CMS</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Commercial CMS foundation</p>
                </div>
            </a>
        </div>

        <div class="mt-8 rounded-3xl border border-cyan-100 bg-gradient-to-br from-cyan-50 to-white p-4 dark:border-cyan-900/40 dark:from-cyan-950/40 dark:to-slate-950">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700 dark:text-cyan-300">Signed in as</p>
            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ Auth::user()->name }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</p>
            <p class="mt-3 inline-flex rounded-full bg-slate-900 px-3 py-1 text-xs font-medium text-white dark:bg-white dark:text-slate-900">
                {{ Auth::user()->getRoleNames()->first() ?? 'member' }}
            </p>
        </div>

        <div class="mt-8">
            <p class="px-3 text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">Workspace</p>
            <div class="mt-3 space-y-1">
                @foreach ($items as $item)
                    @php $active = request()->routeIs($item['pattern']); @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="{{ $active ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/10 dark:bg-cyan-400 dark:text-slate-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white' }} flex items-center rounded-2xl px-3 py-2.5 text-sm font-medium transition"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mt-auto space-y-3 border-t border-slate-200 pt-6 dark:border-slate-800">
            <a href="{{ route('profile.edit') }}" class="flex items-center rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white">
                Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center rounded-2xl px-3 py-2.5 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-950/40">
                    Log out
                </button>
            </form>
        </div>
    </aside>
</nav>
