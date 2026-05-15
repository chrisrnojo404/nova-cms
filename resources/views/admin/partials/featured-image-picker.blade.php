<div x-data="{ openFeaturedPicker: false }" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Featured image</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Choose from uploaded images instead of pasting a URL manually.</p>
        </div>
        <button type="button" @click="openFeaturedPicker = !openFeaturedPicker" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200">
            <span x-text="openFeaturedPicker ? 'Hide picker' : 'Open picker'"></span>
        </button>
    </div>

    <div x-show="openFeaturedPicker" x-cloak class="mt-5 grid gap-4 sm:grid-cols-2">
        @forelse ($featuredImageLibrary as $image)
            <button
                type="button"
                @click="$root.querySelector('[name=featured_image]').value = '{{ $image['url'] }}'; $root.querySelector('[name=featured_image]').dispatchEvent(new Event('input', { bubbles: true })); openFeaturedPicker = false;"
                class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 text-left transition hover:border-cyan-400 hover:shadow-md dark:border-slate-800 dark:bg-slate-950/60"
            >
                <div class="aspect-[4/3] overflow-hidden bg-slate-100 dark:bg-slate-950">
                    <img src="{{ $image['url'] }}" alt="{{ $image['alt'] ?: $image['name'] }}" class="h-full w-full object-cover">
                </div>
                <div class="p-4">
                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $image['name'] }}</p>
                    <p class="mt-1 truncate text-sm text-slate-500 dark:text-slate-400">{{ $image['alt'] ?: 'No alt text yet' }}</p>
                </div>
            </button>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/60 dark:text-slate-400 sm:col-span-2">
                No image assets available yet. Upload some in the media library first.
            </div>
        @endforelse
    </div>
</div>
