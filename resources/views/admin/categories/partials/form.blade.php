<div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
    <section class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="grid gap-6">
                <div>
                    <label for="name" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    @error('name') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="slug" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Slug</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $category->slug) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Leave blank to generate from the category name.</p>
                    @error('slug') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Description</label>
                    <textarea id="description" name="description" rows="6" class="mt-2 w-full rounded-3xl border-slate-300 bg-white px-4 py-4 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('description', $category->description) }}</textarea>
                    @error('description') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </section>

    <aside class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">SEO</p>
            <div class="mt-5 grid gap-5">
                <div>
                    <label for="meta_title" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Meta title</label>
                    <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title', $category->meta_title) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    @error('meta_title') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="meta_description" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Meta description</label>
                    <textarea id="meta_description" name="meta_description" rows="4" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('meta_description', $category->meta_description) }}</textarea>
                    @error('meta_description') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Why this matters</p>
            <p class="mt-3 text-sm leading-7 text-slate-500 dark:text-slate-400">
                Categories are the taxonomy layer that will organize blog posts, listings, and future filters once the posts module is added.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                {{ $submitLabel }}
            </button>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                Cancel
            </a>
        </div>
    </aside>
</div>
