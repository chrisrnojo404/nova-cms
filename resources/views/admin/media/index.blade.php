<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Media module</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Media library</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Upload, search, preview, and remove assets for pages and posts.</p>
            </div>

            <form method="GET" action="{{ route('admin.media.index') }}" class="flex w-full max-w-md gap-3">
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search files, folders, mime types..."
                    class="w-full rounded-full border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >
                <button type="submit" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                    Search
                </button>
            </form>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">Total assets</p>
                <p class="mt-4 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">Images</p>
                <p class="mt-4 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($stats['images']) }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">Documents</p>
                <p class="mt-4 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($stats['documents']) }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">Videos</p>
                <p class="mt-4 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($stats['videos']) }}</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[0.8fr_1.2fr]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Upload assets</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Supports JPG, PNG, WebP, PDF, and MP4 files.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="directory" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Folder</label>
                        <input
                            id="directory"
                            name="directory"
                            type="text"
                            value="{{ old('directory', 'media/uploads') }}"
                            class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        >
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Use simple nested paths like `media/uploads`, `media/blog`, or `media/videos`.</p>
                    </div>

                    <div>
                        <label for="files" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Files</label>
                        <label for="files" class="mt-2 flex cursor-pointer flex-col items-center justify-center rounded-3xl border border-dashed border-cyan-200 bg-cyan-50/60 px-6 py-12 text-center transition hover:border-cyan-300 hover:bg-cyan-50 dark:border-cyan-900/50 dark:bg-cyan-950/20 dark:hover:border-cyan-700">
                            <span class="text-sm font-semibold text-slate-900 dark:text-white">Drop files here or choose from your device</span>
                            <span class="mt-2 text-sm text-slate-500 dark:text-slate-400">Multiple upload is enabled for up to 12 files at once.</span>
                        </label>
                        <input id="files" name="files[]" type="file" multiple class="sr-only" accept=".jpg,.jpeg,.png,.webp,.pdf,.mp4">
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                        Upload files
                    </button>
                </form>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Asset browser</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Published pages and posts can reference the generated storage URL.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
                        @forelse ($mediaItems as $item)
                            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
                                <div class="flex aspect-[4/3] items-center justify-center border-b border-slate-200 bg-slate-100 p-4 dark:border-slate-800 dark:bg-slate-900">
                                    @if ($item->isImage())
                                        <img src="{{ $item->url }}" alt="{{ $item->alt_text ?: $item->original_name }}" class="h-full w-full rounded-2xl object-cover">
                                    @elseif (str_starts_with($item->mime_type, 'video/'))
                                        <div class="flex flex-col items-center gap-2 text-center text-slate-500 dark:text-slate-400">
                                            <span class="text-xs font-semibold uppercase tracking-[0.24em]">Video</span>
                                            <span class="text-sm">{{ strtoupper($item->extension ?? 'mp4') }}</span>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center gap-2 text-center text-slate-500 dark:text-slate-400">
                                            <span class="text-xs font-semibold uppercase tracking-[0.24em]">Document</span>
                                            <span class="text-sm">{{ strtoupper($item->extension ?? 'file') }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="space-y-4 p-5">
                                    <div>
                                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $item->original_name }}</p>
                                        <p class="mt-1 truncate text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">{{ $item->directory }}</p>
                                    </div>

                                    <dl class="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                                        <div class="flex items-center justify-between gap-3">
                                            <dt>Type</dt>
                                            <dd class="truncate text-right">{{ $item->mime_type }}</dd>
                                        </div>
                                        <div class="flex items-center justify-between gap-3">
                                            <dt>Size</dt>
                                            <dd>{{ number_format($item->size / 1024, 1) }} KB</dd>
                                        </div>
                                        <div class="flex items-center justify-between gap-3">
                                            <dt>Uploaded</dt>
                                            <dd>{{ $item->created_at?->diffForHumans() }}</dd>
                                        </div>
                                    </dl>

                                    <div class="rounded-2xl bg-white px-3 py-2 text-xs text-slate-500 shadow-sm dark:bg-slate-900 dark:text-slate-400">
                                        <p class="font-semibold text-slate-700 dark:text-slate-200">URL</p>
                                        <p class="mt-1 break-all">{{ $item->url }}</p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3 text-sm">
                                        <a href="{{ $item->url }}" target="_blank" rel="noreferrer" class="font-medium text-cyan-700 transition hover:text-cyan-500 dark:text-cyan-300 dark:hover:text-cyan-200">
                                            Open
                                        </a>
                                        <form method="POST" action="{{ route('admin.media.destroy', $item) }}" onsubmit="return confirm('Delete this media item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-rose-600 transition hover:text-rose-500 dark:text-rose-300 dark:hover:text-rose-200">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-400 md:col-span-2 2xl:col-span-3">
                                No media found yet. Upload the first asset to start populating the library.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    {{ $mediaItems->links() }}
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
