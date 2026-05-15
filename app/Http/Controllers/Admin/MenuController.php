<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuStoreRequest;
use App\Http\Requests\Admin\MenuUpdateRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Support\CmsCache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MenuController extends Controller
{
    public function __construct(private readonly CmsCache $cache)
    {
    }

    public function index(): View
    {
        return view('admin.menus.index', [
            'menus' => Menu::query()
                ->withCount('items')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.menus.create', [
            'menu' => new Menu(['location' => 'header', 'is_active' => true]),
        ]);
    }

    public function store(MenuStoreRequest $request): RedirectResponse
    {
        $menu = Menu::create($request->validated());
        $this->cache->flushMenus();

        $this->logMenuActivity($request->user()->id, $menu, 'menu.created', 'Menu created.');

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('status', 'Menu created successfully.');
    }

    public function edit(Menu $menu): View
    {
        $menu->load([
            'items.parent',
            'items.page',
            'items.post',
            'items.category',
        ]);

        return view('admin.menus.edit', [
            'menu' => $menu,
            'pages' => Page::query()->orderBy('title')->get(),
            'posts' => Post::query()->orderBy('title')->get(),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(MenuUpdateRequest $request, Menu $menu): RedirectResponse
    {
        $menu->update($request->validated());
        $this->cache->flushMenus();

        $this->logMenuActivity($request->user()->id, $menu, 'menu.updated', 'Menu updated.');

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('status', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $userId = request()->user()?->id;
        $name = $menu->name;
        $menu->delete();
        $this->cache->flushMenus();

        ActivityLog::create([
            'user_id' => $userId,
            'event' => 'menu.deleted',
            'subject_type' => Menu::class,
            'subject_id' => $menu->id,
            'description' => 'Menu deleted.',
            'properties' => [
                'name' => $name,
                'slug' => $menu->slug,
                'location' => $menu->location,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.menus.index')
            ->with('status', 'Menu deleted successfully.');
    }

    private function logMenuActivity(int $userId, Menu $menu, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $userId,
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
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
