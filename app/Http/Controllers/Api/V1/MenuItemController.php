<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemStoreRequest;
use App\Http\Requests\Admin\MenuItemUpdateRequest;
use App\Http\Resources\Api\V1\MenuItemResource;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function store(MenuItemStoreRequest $request, Menu $menu): JsonResponse
    {
        $item = $menu->items()->create($this->payload($request->validated()));

        $this->logItemActivity($request, $menu, $item, 'menu_item.created', 'Menu item created.');

        return (new MenuItemResource($item->load('children')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(MenuItemUpdateRequest $request, Menu $menu, MenuItem $item): MenuItemResource
    {
        abort_unless($item->menu_id === $menu->id, 404);

        $item->update($this->payload($request->validated()));

        $this->logItemActivity($request, $menu, $item, 'menu_item.updated', 'Menu item updated.');

        return new MenuItemResource($item->fresh()->load('children'));
    }

    public function destroy(Request $request, Menu $menu, MenuItem $item): JsonResponse
    {
        abort_unless($request->user()?->can('manage menus'), 403);
        abort_unless($item->menu_id === $menu->id, 404);

        $this->logItemActivity($request, $menu, $item, 'menu_item.deleted', 'Menu item deleted.');
        $item->delete();

        return response()->json([
            'message' => 'Menu item deleted successfully.',
        ]);
    }

    private function payload(array $validated): array
    {
        $linkedType = $validated['linked_type'];
        $linkedId = $validated['linked_id'] ?? null;

        if ($linkedType !== 'custom' && $linkedId) {
            $model = match ($linkedType) {
                'page' => Page::query()->find($linkedId),
                'post' => Post::query()->find($linkedId),
                'category' => Category::query()->find($linkedId),
                default => null,
            };

            $validated['title'] = $validated['title'] ?: match ($linkedType) {
                'page', 'post' => $model?->title,
                'category' => $model?->name,
                default => $validated['title'] ?? null,
            };
        }

        if ($linkedType !== 'custom') {
            $validated['url'] = null;
        }

        return $validated;
    }

    private function logItemActivity(Request $request, Menu $menu, MenuItem $item, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'event' => $event,
            'subject_type' => MenuItem::class,
            'subject_id' => $item->id,
            'description' => $description,
            'properties' => [
                'menu_id' => $menu->id,
                'title' => $item->resolved_title,
                'linked_type' => $item->linked_type,
                'linked_id' => $item->linked_id,
                'position' => $item->position,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
