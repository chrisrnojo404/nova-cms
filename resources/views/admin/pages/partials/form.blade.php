<div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <section class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="grid gap-6">
                <div>
                    <label for="title" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $page->title) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    @error('title') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="slug" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Slug</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $page->slug) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Leave blank to generate from the page title.</p>
                    @error('slug') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="content" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Content</label>
                    <textarea id="content" name="content" rows="14" class="mt-2 w-full rounded-3xl border-slate-300 bg-white px-4 py-4 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('content', $page->content) }}</textarea>
                    @error('content') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">SEO</p>
            <div class="mt-5 grid gap-5">
                <div>
                    <label for="meta_title" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Meta title</label>
                    <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title', $page->meta_title) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    @error('meta_title') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="meta_description" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Meta description</label>
                    <textarea id="meta_description" name="meta_description" rows="4" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('meta_description', $page->meta_description) }}</textarea>
                    @error('meta_description') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        @include('admin.partials.builder-foundation')
    </section>

    <aside class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Publishing</p>
            <div class="mt-5 grid gap-5">
                <div>
                    <label for="status" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Status</label>
                    <select id="status" name="status" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="draft" @selected(old('status', $page->status) === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $page->status) === 'published')>Published</option>
                    </select>
                    @error('status') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="template" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Template</label>
                    <input id="template" name="template" type="text" value="{{ old('template', $page->template ?: 'default') }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    @error('template') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="featured_image" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Featured image</label>
                    <input id="featured_image" name="featured_image" type="text" value="{{ old('featured_image', $page->featured_image) }}" placeholder="/storage/pages/hero.jpg" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    @error('featured_image') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        @include('admin.partials.featured-image-picker')

        @include('admin.partials.draft-snapshot-panel')

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                {{ $submitLabel }}
            </button>
            <a href="{{ route('admin.pages.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                Cancel
            </a>
        </div>
    </aside>
</div>
