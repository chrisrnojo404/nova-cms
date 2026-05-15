<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostStoreRequest;
use App\Http\Requests\Admin\PostUpdateRequest;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Support\BlockBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;

class PostController extends Controller
{
    public function __construct(private readonly BlockBuilder $blockBuilder)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Post::query()
            ->with(['author', 'category'])
            ->latest();

        if (! $request->user()?->can('manage posts')) {
            $query->published();
        } elseif ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($builder) => $builder->where('slug', $request->string('category')->toString()));
        }

        if ($request->filled('author')) {
            $query->whereHas('author', fn ($builder) => $builder->where('email', $request->string('author')->toString()));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->string('search'));

            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        return PostResource::collection($query->paginate($this->perPage($request))->withQueryString());
    }

    public function show(Request $request, string $slug): PostResource
    {
        $post = Post::query()
            ->with(['author', 'category'])
            ->when(
                ! $request->user()?->can('manage posts'),
                fn ($query) => $query->published()
            )
            ->where('slug', $slug)
            ->firstOrFail();

        return new PostResource($post);
    }

    public function store(PostStoreRequest $request): JsonResponse
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

        $this->logActivity($request, $post, 'post.created', 'Post created.');

        return (new PostResource($post->load(['author', 'category'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(PostUpdateRequest $request, Post $post): PostResource
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

        $this->logActivity($request, $post, 'post.updated', 'Post updated.');

        return new PostResource($post->fresh()->load(['author', 'category']));
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        abort_unless($request->user()?->can('manage posts'), 403);

        $this->logActivity($request, $post, 'post.deleted', 'Post deleted.');
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully.',
        ]);
    }

    private function resolveBlocks(?string $rawBlocks, array $fallback): array
    {
        $blocks = $this->blockBuilder->normalize($this->blockBuilder->decode($rawBlocks));

        return $blocks === [] ? $fallback : $blocks;
    }

    private function perPage(Request $request): int
    {
        return min(max($request->integer('per_page', 12), 1), 50);
    }

    private function logActivity(Request $request, Post $post, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $request->user()?->id,
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
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
