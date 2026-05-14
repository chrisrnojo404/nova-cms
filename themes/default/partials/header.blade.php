<header class="mx-auto flex max-w-6xl flex-col gap-5 px-6 py-8 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="{{ route('home') }}" class="inline-flex rounded-full border border-cyan-400/20 bg-cyan-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-cyan-700 dark:text-cyan-200">
            {{ $siteSettings['site_name'] ?? 'Nova CMS' }}
        </a>
        <p class="mt-3 max-w-xl text-sm text-slate-500 dark:text-slate-400">{{ $siteSettings['site_tagline'] ?? 'Commercial-ready Laravel CMS foundation' }}</p>
    </div>

    @if ($headerMenu?->rootItems->count())
        <x-public-menu :items="$headerMenu->rootItems" class="justify-end" />
    @endif
</header>
