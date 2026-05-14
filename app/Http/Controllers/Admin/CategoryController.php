<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::query()->latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category(),
        ]);
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        $this->logActivity($request->user()->id, $category, 'category.created', 'Category created.');

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('status', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryUpdateRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        $this->logActivity($request->user()->id, $category, 'category.updated', 'Category updated.');

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('status', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $userId = request()->user()?->id;
        $name = $category->name;
        $category->delete();

        ActivityLog::create([
            'user_id' => $userId,
            'event' => 'category.deleted',
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'description' => 'Category deleted.',
            'properties' => [
                'name' => $name,
                'slug' => $category->slug,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Category deleted successfully.');
    }

    private function logActivity(int $userId, Category $category, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'event' => $event,
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'description' => $description,
            'properties' => [
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
