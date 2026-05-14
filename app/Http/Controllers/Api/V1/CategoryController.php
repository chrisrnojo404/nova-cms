<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Category::query()->withCount('posts')->orderBy('name');

        if ($request->filled('search')) {
            $search = trim((string) $request->string('search'));

            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return CategoryResource::collection(
            $query->paginate($this->perPage($request))->withQueryString()
        );
    }

    public function show(string $slug): JsonResponse
    {
        $category = Category::query()
            ->withCount('posts')
            ->where('slug', $slug)
            ->firstOrFail();

        $posts = $category->posts()
            ->published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(12);

        return response()->json([
            'category' => (new CategoryResource($category))->resolve(),
            'posts' => PostResource::collection($posts)->response()->getData(true),
        ]);
    }

    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        $this->logActivity($request, $category, 'category.created', 'Category created.');

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    public function update(CategoryUpdateRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());

        $this->logActivity($request, $category, 'category.updated', 'Category updated.');

        return new CategoryResource($category->fresh());
    }

    public function destroy(Request $request, Category $category): JsonResponse
    {
        abort_unless($request->user()?->can('manage categories'), 403);

        $this->logActivity($request, $category, 'category.deleted', 'Category deleted.');
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }

    private function logActivity(Request $request, Category $category, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'event' => $event,
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'description' => $description,
            'properties' => [
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    private function perPage(Request $request): int
    {
        return min(max($request->integer('per_page', 20), 1), 50);
    }
}
