<div class="space-y-6">
    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <section class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="grid gap-6">
                    <div>
                        <label for="name" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Template name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $blockTemplate->name) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                        @error('name') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="slug" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Slug</label>
                        <input id="slug" name="slug" type="text" value="{{ old('slug', $blockTemplate->slug) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        @error('slug') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Description</label>
                        <textarea id="description" name="description" rows="4" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('description', $blockTemplate->description) }}</textarea>
                        @error('description') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            @include('admin.partials.builder-foundation')
        </section>

        <aside class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Template settings</p>
                <div class="mt-5 grid gap-5">
                    <div>
                        <label for="scope" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Scope</label>
                        <select id="scope" name="scope" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            <option value="both" @selected(old('scope', $blockTemplate->scope) === 'both')>Pages and posts</option>
                            <option value="page" @selected(old('scope', $blockTemplate->scope) === 'page')>Pages only</option>
                            <option value="post" @selected(old('scope', $blockTemplate->scope) === 'post')>Posts only</option>
                        </select>
                        @error('scope') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <label class="inline-flex items-center gap-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $blockTemplate->is_active ?? true)) class="rounded border-slate-300 text-cyan-500 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950">
                        Active and available in the builder
                    </label>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                    {{ $submitLabel }}
                </button>
                <a href="{{ route('admin.block-templates.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                    Cancel
                </a>
            </div>
        </aside>
    </div>
</div>
