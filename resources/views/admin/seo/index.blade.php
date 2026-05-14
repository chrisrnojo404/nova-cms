<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">SEO module</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Search engine settings</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Control metadata defaults, canonical base URL, Open Graph behavior, `robots.txt`, and the public sitemap.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.seo.update') }}" class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            @csrf
            @method('PUT')

            <section class="space-y-6">
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Metadata defaults</h2>
                    <div class="mt-6 grid gap-5">
                        <div>
                            <label for="meta_title_template" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Title template</label>
                            <input id="meta_title_template" name="meta_title_template" type="text" value="{{ old('meta_title_template', $values['meta_title_template']) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Use `{title}` and `{site_name}` placeholders.</p>
                            @error('meta_title_template') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="default_meta_description" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Default meta description</label>
                            <textarea id="default_meta_description" name="default_meta_description" rows="4" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('default_meta_description', $values['default_meta_description']) }}</textarea>
                            @error('default_meta_description') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="meta_robots" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Default robots meta</label>
                            <input id="meta_robots" name="meta_robots" type="text" value="{{ old('meta_robots', $values['meta_robots']) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @error('meta_robots') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </article>

                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Canonical and social</h2>
                    <div class="mt-6 grid gap-5">
                        <div>
                            <label for="canonical_base_url" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Canonical base URL</label>
                            <input id="canonical_base_url" name="canonical_base_url" type="url" value="{{ old('canonical_base_url', $values['canonical_base_url']) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @error('canonical_base_url') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="og_site_name" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Open Graph site name</label>
                            <input id="og_site_name" name="og_site_name" type="text" value="{{ old('og_site_name', $values['og_site_name']) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @error('og_site_name') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="twitter_card" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Twitter card</label>
                            <select id="twitter_card" name="twitter_card" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="summary" @selected(old('twitter_card', $values['twitter_card']) === 'summary')>summary</option>
                                <option value="summary_large_image" @selected(old('twitter_card', $values['twitter_card']) === 'summary_large_image')>summary_large_image</option>
                            </select>
                            @error('twitter_card') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </article>
            </section>

            <aside class="space-y-6">
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Discovery files</h2>
                    <div class="mt-6 space-y-5">
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:text-slate-200">
                            <input type="hidden" name="sitemap_enabled" value="0">
                            <input type="checkbox" name="sitemap_enabled" value="1" @checked(old('sitemap_enabled', $values['sitemap_enabled'])) class="rounded border-slate-300 text-cyan-500 focus:ring-cyan-400">
                            <span>Enable public `sitemap.xml`</span>
                        </label>
                        <div>
                            <label for="robots_txt_content" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Custom robots.txt</label>
                            <textarea id="robots_txt_content" name="robots_txt_content" rows="10" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 font-mono text-sm text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('robots_txt_content', $values['robots_txt_content']) }}</textarea>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Leave blank to generate a default robots file that includes the sitemap URL.</p>
                            @error('robots_txt_content') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </article>

                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Public endpoints</p>
                    <ul class="mt-4 space-y-3 text-sm text-slate-500 dark:text-slate-400">
                        <li><a href="{{ url('/sitemap.xml') }}" target="_blank" rel="noreferrer" class="text-cyan-700 hover:text-cyan-500 dark:text-cyan-300 dark:hover:text-cyan-200">/sitemap.xml</a></li>
                        <li><a href="{{ url('/robots.txt') }}" target="_blank" rel="noreferrer" class="text-cyan-700 hover:text-cyan-500 dark:text-cyan-300 dark:hover:text-cyan-200">/robots.txt</a></li>
                    </ul>
                </article>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                        Save SEO settings
                    </button>
                </div>
            </aside>
        </form>
    </div>
</x-app-layout>
