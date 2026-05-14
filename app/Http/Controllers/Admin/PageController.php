<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageStoreRequest;
use App\Http\Requests\Admin\PageUpdateRequest;
use App\Models\ActivityLog;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => Page::query()
                ->with('author')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create', [
            'page' => new Page([
                'status' => 'draft',
                'template' => 'default',
            ]),
        ]);
    }

    public function store(PageStoreRequest $request): RedirectResponse
    {
        $page = Page::create([
            ...$request->validated(),
            'author_id' => $request->user()->id,
            'blocks' => $this->buildBlocks($request->string('title')->toString(), $request->input('content')),
            'published_at' => $request->input('status') === 'published' ? now() : null,
        ]);

        $this->logPageActivity($request->user()->id, $page, 'page.created', 'Page created.');

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', 'Page created successfully.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(PageUpdateRequest $request, Page $page): RedirectResponse
    {
        $status = $request->input('status');

        $page->update([
            ...$request->validated(),
            'blocks' => $this->buildBlocks($request->string('title')->toString(), $request->input('content')),
            'published_at' => $status === 'published'
                ? ($page->published_at ?? now())
                : null,
        ]);

        $this->logPageActivity($request->user()->id, $page, 'page.updated', 'Page updated.');

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', 'Page updated successfully.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $userId = request()->user()?->id;
        $pageTitle = $page->title;
        $page->delete();

        ActivityLog::create([
            'user_id' => $userId,
            'event' => 'page.deleted',
            'subject_type' => Page::class,
            'subject_id' => $page->id,
            'description' => 'Page deleted.',
            'properties' => [
                'title' => $pageTitle,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.pages.index')
            ->with('status', 'Page deleted successfully.');
    }

    private function buildBlocks(string $title, ?string $content): array
    {
        return array_values(array_filter([
            ['type' => 'heading', 'content' => $title],
            $content ? ['type' => 'paragraph', 'content' => strip_tags($content)] : null,
        ]));
    }

    private function logPageActivity(int $userId, Page $page, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'event' => $event,
            'subject_type' => Page::class,
            'subject_id' => $page->id,
            'description' => $description,
            'properties' => [
                'title' => $page->title,
                'status' => $page->status,
                'slug' => $page->slug,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
