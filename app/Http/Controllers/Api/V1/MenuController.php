<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuStoreRequest;
use App\Http\Requests\Admin\MenuUpdateRequest;
use App\Http\Resources\Api\V1\MenuResource;
use App\Models\ActivityLog;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MenuController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Menu::query()
            ->withCount('items')
            ->latest();

        if (! $request->user()?->can('manage menus')) {
            $query->active();
        }

        if ($request->filled('location')) {
            $query->where('location', $request->string('location')->toString());
        }

        return MenuResource::collection($query->paginate($this->perPage($request))->withQueryString());
    }

    public function show(Request $request, string $slug): MenuResource
    {
        $menu = $this->menuQuery($request)
            ->where('slug', $slug)
            ->firstOrFail();

        return new MenuResource($menu);
    }

    public function byLocation(Request $request, string $location): MenuResource
    {
        $menu = $this->menuQuery($request)
            ->where('location', $location)
            ->firstOrFail();

        return new MenuResource($menu);
    }

    public function store(MenuStoreRequest $request): JsonResponse
    {
        $menu = Menu::create($request->validated());

        $this->logActivity($request, $menu, 'menu.created', 'Menu created.');

        return (new MenuResource($menu->load($this->menuRelations())))
            ->response()
            ->setStatusCode(201);
    }

    public function update(MenuUpdateRequest $request, Menu $menu): MenuResource
    {
        $menu->update($request->validated());

        $this->logActivity($request, $menu, 'menu.updated', 'Menu updated.');

        return new MenuResource($menu->fresh()->load($this->menuRelations()));
    }

    public function destroy(Request $request, Menu $menu): JsonResponse
    {
        abort_unless($request->user()?->can('manage menus'), 403);

        $this->logActivity($request, $menu, 'menu.deleted', 'Menu deleted.');
        $menu->delete();

        return response()->json([
            'message' => 'Menu deleted successfully.',
        ]);
    }

    private function menuQuery(Request $request)
    {
        return Menu::query()
            ->when(! $request->user()?->can('manage menus'), fn ($query) => $query->active())
            ->with($this->menuRelations())
            ->withCount('items');
    }

    private function menuRelations(): array
    {
        return [
            'rootItems.children.children',
            'rootItems.page',
            'rootItems.post',
            'rootItems.category',
            'rootItems.children.page',
            'rootItems.children.post',
            'rootItems.children.category',
            'rootItems.children.children.page',
            'rootItems.children.children.post',
            'rootItems.children.children.category',
        ];
    }

    private function perPage(Request $request): int
    {
        return min(max($request->integer('per_page', 12), 1), 50);
    }

    private function logActivity(Request $request, Menu $menu, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'event' => $event,
            'subject_type' => Menu::class,
            'subject_id' => $menu->id,
            'description' => $description,
            'properties' => [
                'name' => $menu->name,
                'slug' => $menu->slug,
                'location' => $menu->location,
                'is_active' => $menu->is_active,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
