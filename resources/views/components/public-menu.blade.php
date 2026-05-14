@props([
    'items' => [],
    'class' => '',
    'theme' => 'light',
])

@php
    $listClass = $theme === 'dark'
        ? 'text-sm font-medium text-slate-200'
        : 'text-sm font-medium text-slate-700';
    $linkClass = $theme === 'dark'
        ? 'text-slate-200 transition hover:text-cyan-300'
        : 'text-slate-700 transition hover:text-cyan-700';
    $childClass = $theme === 'dark'
        ? 'ml-4 mt-3 space-y-3 border-l border-white/10 pl-4'
        : 'ml-4 mt-3 space-y-3 border-l border-slate-200 pl-4';
@endphp

@if (count($items))
    <ul {{ $attributes->merge(['class' => trim("flex flex-wrap gap-x-6 gap-y-3 {$listClass} {$class}")]) }}>
        @foreach ($items as $item)
            <li>
                <a href="{{ $item->resolved_url }}" @if ($item->target === 'new_tab') target="_blank" rel="noreferrer" @endif class="{{ $linkClass }}">
                    {{ $item->resolved_title }}
                </a>

                @if ($item->children->count())
                    <x-public-menu :items="$item->children" :theme="$theme" class="{{ $childClass }}" />
                @endif
            </li>
        @endforeach
    </ul>
@endif
