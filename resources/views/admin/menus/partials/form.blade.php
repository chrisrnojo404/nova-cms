<div class="grid gap-6 xl:grid-cols-[1fr_0.8fr]">
    <section class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="grid gap-6">
                <div>
                    <label for="name" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $menu->name) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    @error('name') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="slug" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Slug</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $menu->slug) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Leave blank to generate from the menu name.</p>
                    @error('slug') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Description</label>
                    <textarea id="description" name="description" rows="4" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('description', $menu->description) }}</textarea>
                    @error('description') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </section>

    <aside class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Placement</p>
            <div class="mt-5 grid gap-5">
                <div>
                    <label for="location" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Location</label>
                    <select id="location" name="location" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="header" @selected(old('location', $menu->location) === 'header')>Header</option>
                        <option value="footer" @selected(old('location', $menu->location) === 'footer')>Footer</option>
                        <option value="social" @selected(old('location', $menu->location) === 'social')>Social</option>
                        <option value="custom" @selected(old('location', $menu->location) === 'custom')>Custom</option>
                    </select>
                    @error('location') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:text-slate-200">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $menu->is_active ?? true)) class="rounded border-slate-300 text-cyan-500 focus:ring-cyan-400">
                    <span>Active and available for frontend rendering</span>
                </label>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                {{ $submitLabel }}
            </button>
            <a href="{{ route('admin.menus.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                Cancel
            </a>
        </div>
    </aside>
</div>
