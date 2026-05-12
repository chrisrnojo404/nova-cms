@props([
    'light' => false,
])

@php
    $baseClasses = $light
        ? 'border-white/10 text-slate-300'
        : 'border-slate-200 text-slate-500 dark:border-slate-800 dark:text-slate-400';
@endphp

<div {{ $attributes->merge(['class' => "flex items-center justify-center gap-2 border-t px-4 py-4 text-sm {$baseClasses}"]) }}>
    <a
        href="https://github.com/chrisrnojo404"
        target="_blank"
        rel="noreferrer"
        class="inline-flex items-center gap-2 transition hover:text-cyan-500 dark:hover:text-cyan-300"
    >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 .5C5.65.5.5 5.65.5 12.03c0 5.1 3.3 9.42 7.88 10.95.58.1.78-.25.78-.56 0-.27-.01-1.18-.02-2.14-3.21.7-3.89-1.37-3.89-1.37-.53-1.35-1.28-1.7-1.28-1.7-1.05-.72.08-.71.08-.71 1.16.08 1.77 1.2 1.77 1.2 1.03 1.78 2.71 1.26 3.38.96.1-.75.4-1.26.73-1.55-2.56-.29-5.24-1.3-5.24-5.77 0-1.27.45-2.3 1.2-3.12-.12-.3-.52-1.5.12-3.13 0 0 .98-.32 3.2 1.2a10.96 10.96 0 0 1 5.82 0c2.22-1.52 3.2-1.2 3.2-1.2.64 1.63.24 2.83.12 3.13.75.82 1.2 1.85 1.2 3.12 0 4.48-2.69 5.48-5.26 5.77.41.36.78 1.05.78 2.12 0 1.54-.01 2.77-.01 3.15 0 .31.2.67.79.56A11.54 11.54 0 0 0 23.5 12.03C23.5 5.65 18.35.5 12 .5Z"/>
        </svg>
        <span>Developed by <strong class="font-semibold">chrisrnojo404</strong></span>
    </a>
</div>
