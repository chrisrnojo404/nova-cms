<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Blog module</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Edit post</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update content, excerpt, category, and SEO details for this article.</p>
            </div>

            @if ($post->status === 'published')
                <a href="{{ route('posts.show', $post->slug) }}" target="_blank" rel="noreferrer" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                    View post
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.posts.partials.form', ['submitLabel' => 'Save changes'])
        </form>
    </div>
</x-app-layout>
