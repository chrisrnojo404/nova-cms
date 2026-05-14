<div class="grid gap-5">
    <div>
        <label for="title-{{ $item->id ?? 'new' }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Label override</label>
        <input id="title-{{ $item->id ?? 'new' }}" name="title" type="text" value="{{ old('title', $item->title) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Optional for linked content. Required for custom links.</p>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="linked_type-{{ $item->id ?? 'new' }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Link type</label>
            <select id="linked_type-{{ $item->id ?? 'new' }}" name="linked_type" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <option value="page" @selected(old('linked_type', $item->linked_type) === 'page')>Page</option>
                <option value="post" @selected(old('linked_type', $item->linked_type) === 'post')>Post</option>
                <option value="category" @selected(old('linked_type', $item->linked_type) === 'category')>Category</option>
                <option value="custom" @selected(old('linked_type', $item->linked_type ?? 'custom') === 'custom')>Custom URL</option>
            </select>
        </div>

        <div>
            <label for="linked_id-{{ $item->id ?? 'new' }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Source record</label>
            <select id="linked_id-{{ $item->id ?? 'new' }}" name="linked_id" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <option value="">Choose linked content</option>
                @foreach ($pages as $page)
                    <option value="{{ $page->id }}" data-type="page" @selected(old('linked_id', $item->linked_id) == $page->id && old('linked_type', $item->linked_type) === 'page')>
                        Page: {{ $page->title }}
                    </option>
                @endforeach
                @foreach ($posts as $post)
                    <option value="{{ $post->id }}" data-type="post" @selected(old('linked_id', $item->linked_id) == $post->id && old('linked_type', $item->linked_type) === 'post')>
                        Post: {{ $post->title }}
                    </option>
                @endforeach
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" data-type="category" @selected(old('linked_id', $item->linked_id) == $category->id && old('linked_type', $item->linked_type) === 'category')>
                        Category: {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('linked_id') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="url-{{ $item->id ?? 'new' }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Custom URL</label>
        <input id="url-{{ $item->id ?? 'new' }}" name="url" type="text" value="{{ old('url', $item->url) }}" placeholder="/contact or https://example.com" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        @error('url') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="parent_id-{{ $item->id ?? 'new' }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Parent item</label>
            <select id="parent_id-{{ $item->id ?? 'new' }}" name="parent_id" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <option value="">Top level</option>
                @foreach ($menu->items->where('id', '!=', $item->id) as $candidate)
                    <option value="{{ $candidate->id }}" @selected((string) old('parent_id', $item->parent_id) === (string) $candidate->id)>
                        {{ $candidate->resolved_title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="position-{{ $item->id ?? 'new' }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Position</label>
            <input id="position-{{ $item->id ?? 'new' }}" name="position" type="number" min="0" value="{{ old('position', $item->position ?? 0) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="target-{{ $item->id ?? 'new' }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Target</label>
            <select id="target-{{ $item->id ?? 'new' }}" name="target" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <option value="same_tab" @selected(old('target', $item->target) === 'same_tab')>Same tab</option>
                <option value="new_tab" @selected(old('target', $item->target) === 'new_tab')>New tab</option>
            </select>
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:text-slate-200">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true)) class="rounded border-slate-300 text-cyan-500 focus:ring-cyan-400">
            <span>Active</span>
        </label>
    </div>

    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
        {{ $submitLabel }}
    </button>
</div>
