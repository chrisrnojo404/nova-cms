<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageStoreRequest;
use App\Http\Requests\Admin\PageUpdateRequest;
use App\Http\Resources\Api\V1\PageResource;
use App\Models\ActivityLog;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PageController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Page::query()
            ->with('author')
            ->latest();

        if (! $request->user()?->can('manage pages')) {
            $query->published();
        } elseif ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->string('search'));

            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        return PageResource::collection($query->paginate($this->perPage($request))->withQueryString());
    }

    public function show(Request $request, string $slug): PageResource
    {
        $page = Page::query()
            ->with('author')
            ->when(
                ! $request->user()?->can('manage pages'),
                fn ($query) => $query->published()
            )
            ->where('slug', $slug)
            ->firstOrFail();

        return new PageResource($page);
    }

    public function store(PageStoreRequest $request): JsonResponse
    {
        $page = Page::create([
            ...$request->validated(),
            'author_id' => $request->user()->id,
            'blocks' => $this->buildBlocks($request->string('title')->toString(), $request->input('content')),
            'published_at' => $request->input('status') === 'published' ? now() : null,
        ]);

        $this->logActivity($request, $page, 'page.created', 'Page created.');

        return (new PageResource($page->load('author')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(PageUpdateRequest $request, Page $page): PageResource
    {
        $status = $request->input('status');

        $page->update([
            ...$request->validated(),
            'blocks' => $this->buildBlocks($request->string('title')->toString(), $request->input('content')),
            'published_at' => $status === 'published'
                ? ($page->published_at ?? now())
                : null,
        ]);

        $this->logActivity($request, $page, 'page.updated', 'Page updated.');

        return new PageResource($page->fresh()->load('author'));
    }

    public function destroy(Request $request, Page $page): JsonResponse
    {
        abort_unless($request->user()?->can('manage pages'), 403);

        $this->logActivity($request, $page, 'page.deleted', 'Page deleted.');
        $page->delete();

        return response()->json([
            'message' => 'Page deleted successfully.',
        ]);
    }

    private function buildBlocks(string $title, ?string $content): array
    {
        return array_values(array_filter([
            ['type' => 'heading', 'content' => $title],
            $content ? ['type' => 'paragraph', 'content' => strip_tags($content)] : null,
        ]));
    }

    private function perPage(Request $request): int
    {
        return min(max($request->integer('per_page', 12), 1), 50);
    }

    private function logActivity(Request $request, Page $page, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'event' => $event,
            'subject_type' => Page::class,
            'subject_id' => $page->id,
            'description' => $description,
            'properties' => [
                'title' => $page->title,
                'status' => $page->status,
                'slug' => $page->slug,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
