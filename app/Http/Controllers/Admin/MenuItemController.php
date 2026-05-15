<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemStoreRequest;
use App\Http\Requests\Admin\MenuItemUpdateRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Support\CmsCache;
use Illuminate\Http\RedirectResponse;

class MenuItemController extends Controller
{
    public function __construct(private readonly CmsCache $cache)
    {
    }

    public function store(MenuItemStoreRequest $request, Menu $menu): RedirectResponse
    {
        $item = $menu->items()->create($this->payload($request->validated()));
        $this->cache->flushMenus();

        $this->logItemActivity($request->user()->id, $item, 'menu_item.created', 'Menu item created.');

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('status', 'Menu item added successfully.');
    }

    public function update(MenuItemUpdateRequest $request, Menu $menu, MenuItem $item): RedirectResponse
    {
        abort_unless($item->menu_id === $menu->id, 404);

        $item->update($this->payload($request->validated()));
        $this->cache->flushMenus();

        $this->logItemActivity($request->user()->id, $item, 'menu_item.updated', 'Menu item updated.');

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('status', 'Menu item updated successfully.');
    }

    public function destroy(Menu $menu, MenuItem $item): RedirectResponse
    {
        abort_unless($item->menu_id === $menu->id, 404);

        $userId = request()->user()?->id;
        $title = $item->resolved_title;
        $item->delete();
        $this->cache->flushMenus();

        ActivityLog::create([
            'user_id' => $userId,
            'event' => 'menu_item.deleted',
            'subject_type' => MenuItem::class,
            'subject_id' => $item->id,
            'description' => 'Menu item deleted.',
            'properties' => [
                'menu_id' => $menu->id,
                'title' => $title,
                'linked_type' => $item->linked_type,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('status', 'Menu item deleted successfully.');
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

    private function logItemActivity(int $userId, MenuItem $item, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'event' => $event,
            'subject_type' => MenuItem::class,
            'subject_id' => $item->id,
            'description' => $description,
            'properties' => [
                'menu_id' => $item->menu_id,
                'title' => $item->resolved_title,
                'linked_type' => $item->linked_type,
                'linked_id' => $item->linked_id,
                'position' => $item->position,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
