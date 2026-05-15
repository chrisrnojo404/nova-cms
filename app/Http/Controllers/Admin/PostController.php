<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostStoreRequest;
use App\Http\Requests\Admin\PostUpdateRequest;
use App\Models\ActivityLog;
use App\Models\BlockTemplate;
use App\Models\Category;
use App\Models\ContentRevision;
use App\Models\Media;
use App\Models\Post;
use App\Support\BlockBuilder;
use App\Support\RevisionManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class PostController extends Controller
{
    public function __construct(
        private readonly BlockBuilder $blockBuilder,
        private readonly RevisionManager $revisionManager
    )
    {
    }

    public function index(): View
    {
        return view('admin.posts.index', [
            'posts' => Post::query()
                ->with(['author', 'category'])
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        $fallbackBlocks = $this->blockBuilder->fallbackPostBlocks('', null, null);

        return view('admin.posts.create', [
            'post' => new Post(['status' => 'draft']),
            'categories' => Category::query()->orderBy('name')->get(),
            'builderCatalog' => $this->blockBuilder->availableBlocks(),
            'builderLayouts' => $this->blockBuilder->starterLayouts(),
            'builderBlocksJson' => $this->blockBuilder->editorJson([], $fallbackBlocks),
            'builderMediaLibrary' => $this->builderMediaLibrary(),
            'builderReusableTemplates' => $this->reusableTemplatesFor('post'),
            'builderAutosaveKey' => 'nova-builder-post-create',
            'featuredImageLibrary' => $this->featuredImageLibrary(),
            'draftAutosaveKey' => 'nova-draft-post-create',
        ]);
    }

    public function store(PostStoreRequest $request): RedirectResponse
    {
        $fallbackBlocks = $this->blockBuilder->fallbackPostBlocks(
            $request->string('title')->toString(),
            $request->input('excerpt'),
            $request->input('content')
        );

        $post = Post::create([
            ...Arr::except($request->validated(), ['builder_blocks']),
            'author_id' => $request->user()->id,
            'blocks' => $this->resolveBlocks($request->input('builder_blocks'), $fallbackBlocks),
            'published_at' => $request->input('status') === 'published' ? now() : null,
        ]);

        $this->logPostActivity($request->user()->id, $post, 'post.created', 'Post created.');
        $this->revisionManager->capture($post, $request->user()->id, 'Initial post version');

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('status', 'Post created successfully.');
    }

    public function edit(Post $post): View
    {
        $fallbackBlocks = $this->blockBuilder->fallbackPostBlocks($post->title, $post->excerpt, $post->content);

        return view('admin.posts.edit', [
            'post' => $post,
            'categories' => Category::query()->orderBy('name')->get(),
            'builderCatalog' => $this->blockBuilder->availableBlocks(),
            'builderLayouts' => $this->blockBuilder->starterLayouts(),
            'builderBlocksJson' => $this->blockBuilder->editorJson($post->blocks, $fallbackBlocks),
            'builderMediaLibrary' => $this->builderMediaLibrary(),
            'builderReusableTemplates' => $this->reusableTemplatesFor('post'),
            'builderAutosaveKey' => 'nova-builder-post-'.$post->id,
            'featuredImageLibrary' => $this->featuredImageLibrary(),
            'draftAutosaveKey' => 'nova-draft-post-'.$post->id,
            'revisions' => $post->revisions()->with('user')->limit(8)->get(),
        ]);
    }

    public function update(PostUpdateRequest $request, Post $post): RedirectResponse
    {
        $status = $request->input('status');
        $fallbackBlocks = $this->blockBuilder->fallbackPostBlocks(
            $request->string('title')->toString(),
            $request->input('excerpt'),
            $request->input('content')
        );

        $post->update([
            ...Arr::except($request->validated(), ['builder_blocks']),
            'blocks' => $this->resolveBlocks($request->input('builder_blocks'), $fallbackBlocks),
            'published_at' => $status === 'published'
                ? ($post->published_at ?? now())
                : null,
        ]);

        $this->logPostActivity($request->user()->id, $post, 'post.updated', 'Post updated.');
        $this->revisionManager->capture($post, $request->user()->id, 'Saved post revision');

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('status', 'Post updated successfully.');
    }

    public function restoreRevision(Post $post, ContentRevision $revision): RedirectResponse
    {
        abort_unless($revision->revisionable_type === Post::class && $revision->revisionable_id === $post->id, 404);

        $userId = request()->user()?->id;

        $this->revisionManager->capture($post, $userId, 'Pre-restore backup');
        $this->revisionManager->restore($post, $revision);

        $this->logPostActivity($userId ?? 0, $post->fresh(), 'post.restored', 'Post restored from revision.');

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('status', 'Post restored from revision successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $userId = request()->user()?->id;
        $title = $post->title;
        $post->delete();

        ActivityLog::create([
            'user_id' => $userId,
            'event' => 'post.deleted',
            'subject_type' => Post::class,
            'subject_id' => $post->id,
            'description' => 'Post deleted.',
            'properties' => [
                'title' => $title,
                'slug' => $post->slug,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('status', 'Post deleted successfully.');
    }

    private function resolveBlocks(?string $rawBlocks, array $fallback): array
    {
        $blocks = $this->blockBuilder->normalize($this->blockBuilder->decode($rawBlocks));

        return $blocks === [] ? $fallback : $blocks;
    }

    private function builderMediaLibrary(): array
    {
        return Media::query()
            ->where(function ($query): void {
                $query
                    ->where('mime_type', 'like', 'image/%')
                    ->orWhere('mime_type', 'like', 'video/%');
            })
            ->latest()
            ->limit(24)
            ->get()
            ->map(fn (Media $media): array => [
                'id' => $media->id,
                'url' => $media->url,
                'alt' => $media->alt_text,
                'name' => $media->original_name,
                'mime_type' => $media->mime_type,
            ])
            ->all();
    }

    private function featuredImageLibrary(): array
    {
        return Media::query()
            ->where('mime_type', 'like', 'image/%')
            ->latest()
            ->limit(12)
            ->get()
            ->map(fn (Media $media): array => [
                'id' => $media->id,
                'url' => $media->url,
                'alt' => $media->alt_text,
                'name' => $media->original_name,
            ])
            ->all();
    }

    private function reusableTemplatesFor(string $context)
    {
        if (! Schema::hasTable('block_templates')) {
            return collect();
        }

        return BlockTemplate::query()
            ->forContext($context)
            ->latest()
            ->get();
    }

    private function logPostActivity(int $userId, Post $post, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'event' => $event,
            'subject_type' => Post::class,
            'subject_id' => $post->id,
            'description' => $description,
            'properties' => [
                'title' => $post->title,
                'status' => $post->status,
                'slug' => $post->slug,
                'category_id' => $post->category_id,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
