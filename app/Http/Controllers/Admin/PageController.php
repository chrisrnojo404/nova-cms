<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageStoreRequest;
use App\Http\Requests\Admin\PageUpdateRequest;
use App\Models\ActivityLog;
use App\Models\BlockTemplate;
use App\Models\ContentRevision;
use App\Models\Media;
use App\Models\Page;
use App\Support\BlockBuilder;
use App\Support\RevisionManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class PageController extends Controller
{
    public function __construct(
        private readonly BlockBuilder $blockBuilder,
        private readonly RevisionManager $revisionManager
    )
    {
    }

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
        $fallbackBlocks = $this->blockBuilder->fallbackPageBlocks('', null);

        return view('admin.pages.create', [
            'page' => new Page([
                'status' => 'draft',
                'template' => 'default',
            ]),
            'builderCatalog' => $this->blockBuilder->availableBlocks(),
            'builderLayouts' => $this->blockBuilder->starterLayouts(),
            'builderBlocksJson' => $this->blockBuilder->editorJson([], $fallbackBlocks),
            'builderMediaLibrary' => $this->builderMediaLibrary(),
            'builderReusableTemplates' => BlockTemplate::query()->forContext('page')->latest()->get(),
            'builderAutosaveKey' => 'nova-builder-page-create',
            'featuredImageLibrary' => $this->featuredImageLibrary(),
            'draftAutosaveKey' => 'nova-draft-page-create',
        ]);
    }

    public function store(PageStoreRequest $request): RedirectResponse
    {
        $fallbackBlocks = $this->blockBuilder->fallbackPageBlocks(
            $request->string('title')->toString(),
            $request->input('content')
        );

        $page = Page::create([
            ...Arr::except($request->validated(), ['builder_blocks']),
            'author_id' => $request->user()->id,
            'blocks' => $this->resolveBlocks($request->input('builder_blocks'), $fallbackBlocks),
            'published_at' => $request->input('status') === 'published' ? now() : null,
        ]);

        $this->logPageActivity($request->user()->id, $page, 'page.created', 'Page created.');
        $this->revisionManager->capture($page, $request->user()->id, 'Initial page version');

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', 'Page created successfully.');
    }

    public function edit(Page $page): View
    {
        $fallbackBlocks = $this->blockBuilder->fallbackPageBlocks($page->title, $page->content);

        return view('admin.pages.edit', [
            'page' => $page,
            'builderCatalog' => $this->blockBuilder->availableBlocks(),
            'builderLayouts' => $this->blockBuilder->starterLayouts(),
            'builderBlocksJson' => $this->blockBuilder->editorJson($page->blocks, $fallbackBlocks),
            'builderMediaLibrary' => $this->builderMediaLibrary(),
            'builderReusableTemplates' => BlockTemplate::query()->forContext('page')->latest()->get(),
            'builderAutosaveKey' => 'nova-builder-page-'.$page->id,
            'featuredImageLibrary' => $this->featuredImageLibrary(),
            'draftAutosaveKey' => 'nova-draft-page-'.$page->id,
            'revisions' => $page->revisions()->with('user')->limit(8)->get(),
        ]);
    }

    public function update(PageUpdateRequest $request, Page $page): RedirectResponse
    {
        $status = $request->input('status');
        $fallbackBlocks = $this->blockBuilder->fallbackPageBlocks(
            $request->string('title')->toString(),
            $request->input('content')
        );

        $page->update([
            ...Arr::except($request->validated(), ['builder_blocks']),
            'blocks' => $this->resolveBlocks($request->input('builder_blocks'), $fallbackBlocks),
            'published_at' => $status === 'published'
                ? ($page->published_at ?? now())
                : null,
        ]);

        $this->logPageActivity($request->user()->id, $page, 'page.updated', 'Page updated.');
        $this->revisionManager->capture($page, $request->user()->id, 'Saved page revision');

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', 'Page updated successfully.');
    }

    public function restoreRevision(Page $page, ContentRevision $revision): RedirectResponse
    {
        abort_unless($revision->revisionable_type === Page::class && $revision->revisionable_id === $page->id, 404);

        $userId = request()->user()?->id;

        $this->revisionManager->capture($page, $userId, 'Pre-restore backup');
        $this->revisionManager->restore($page, $revision);

        $this->logPageActivity($userId ?? 0, $page->fresh(), 'page.restored', 'Page restored from revision.');

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', 'Page restored from revision successfully.');
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
