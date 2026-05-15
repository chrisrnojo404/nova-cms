<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlockTemplateImportRequest;
use App\Http\Requests\Admin\BlockTemplateStoreRequest;
use App\Http\Requests\Admin\BlockTemplateUpdateRequest;
use App\Models\ActivityLog;
use App\Models\BlockTemplate;
use App\Models\Media;
use App\Support\BlockBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use JsonException;

class BlockTemplateController extends Controller
{
    public function __construct(private readonly BlockBuilder $blockBuilder)
    {
    }

    public function index(): View
    {
        return view('admin.block-templates.index', [
            'templates' => BlockTemplate::query()
                ->with('user')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.block-templates.create', [
            'blockTemplate' => new BlockTemplate([
                'scope' => 'both',
                'is_active' => true,
            ]),
            'builderCatalog' => $this->blockBuilder->availableBlocks(),
            'builderLayouts' => $this->blockBuilder->starterLayouts(),
            'builderBlocksJson' => $this->blockBuilder->editorJson([], []),
            'builderMediaLibrary' => $this->builderMediaLibrary(),
            'builderReusableTemplates' => collect(),
            'builderAutosaveKey' => 'nova-builder-template-create',
        ]);
    }

    public function store(BlockTemplateStoreRequest $request): RedirectResponse
    {
        $template = BlockTemplate::create([
            'user_id' => $request->user()->id,
            'name' => $request->string('name')->toString(),
            'slug' => $request->string('slug')->toString(),
            'scope' => $request->string('scope')->toString(),
            'description' => $request->input('description'),
            'blocks' => $this->blockBuilder->normalize($this->blockBuilder->decode($request->input('builder_blocks'))),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->logActivity($request->user()->id, $template, 'block_template.created', 'Block template created.');

        return redirect()
            ->route('admin.block-templates.edit', $template)
            ->with('status', 'Block template created successfully.');
    }

    public function edit(BlockTemplate $blockTemplate): View
    {
        return view('admin.block-templates.edit', [
            'blockTemplate' => $blockTemplate,
            'builderCatalog' => $this->blockBuilder->availableBlocks(),
            'builderLayouts' => $this->blockBuilder->starterLayouts(),
            'builderBlocksJson' => $this->blockBuilder->editorJson($blockTemplate->blocks, []),
            'builderMediaLibrary' => $this->builderMediaLibrary(),
            'builderReusableTemplates' => collect(),
            'builderAutosaveKey' => 'nova-builder-template-'.$blockTemplate->id,
        ]);
    }

    public function update(BlockTemplateUpdateRequest $request, BlockTemplate $blockTemplate): RedirectResponse
    {
        $blockTemplate->update([
            'name' => $request->string('name')->toString(),
            'slug' => $request->string('slug')->toString(),
            'scope' => $request->string('scope')->toString(),
            'description' => $request->input('description'),
            'blocks' => $this->blockBuilder->normalize($this->blockBuilder->decode($request->input('builder_blocks'))),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->logActivity($request->user()->id, $blockTemplate, 'block_template.updated', 'Block template updated.');

        return redirect()
            ->route('admin.block-templates.edit', $blockTemplate)
            ->with('status', 'Block template updated successfully.');
    }

    public function destroy(BlockTemplate $blockTemplate): RedirectResponse
    {
        $userId = request()->user()?->id;
        $name = $blockTemplate->name;
        $blockTemplate->delete();

        ActivityLog::create([
            'user_id' => $userId,
            'event' => 'block_template.deleted',
            'subject_type' => BlockTemplate::class,
            'subject_id' => $blockTemplate->id,
            'description' => 'Block template deleted.',
            'properties' => [
                'name' => $name,
                'slug' => $blockTemplate->slug,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.block-templates.index')
            ->with('status', 'Block template deleted successfully.');
    }

    public function export(BlockTemplate $blockTemplate): JsonResponse
    {
        return response()->json([
            'name' => $blockTemplate->name,
            'slug' => $blockTemplate->slug,
            'scope' => $blockTemplate->scope,
            'description' => $blockTemplate->description,
            'is_active' => $blockTemplate->is_active,
            'blocks' => $blockTemplate->blocks,
        ], 200, [
            'Content-Disposition' => 'attachment; filename="'.$blockTemplate->slug.'-template.json"',
        ]);
    }

    public function import(BlockTemplateImportRequest $request): RedirectResponse
    {
        $raw = trim((string) $request->input('template_json'));

        if ($raw === '' && $request->hasFile('template_file')) {
            $raw = trim((string) $request->file('template_file')?->get());
        }

        if ($raw === '') {
            return back()->withErrors([
                'template_json' => 'Provide template JSON or upload a template file to import.',
            ])->withInput();
        }

        try {
            $payload = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return back()->withErrors([
                'template_json' => 'Imported template JSON could not be parsed.',
            ])->withInput();
        }

        if (! is_array($payload)) {
            return back()->withErrors([
                'template_json' => 'Imported template must decode to an object.',
            ])->withInput();
        }

        $blocks = $this->blockBuilder->normalize($payload['blocks'] ?? []);
        $errors = $this->blockBuilder->validationErrors($payload['blocks'] ?? []);

        if ($blocks === [] || $errors !== []) {
            return back()->withErrors([
                'template_json' => $errors[0] ?? 'Imported template did not contain valid blocks.',
            ])->withInput();
        }

        $name = trim((string) ($payload['name'] ?? 'Imported Template'));
        $scope = in_array(($payload['scope'] ?? 'both'), ['page', 'post', 'both'], true) ? $payload['scope'] : 'both';
        $slug = Str::slug((string) ($payload['slug'] ?? $name));
        $originalSlug = $slug !== '' ? $slug : Str::slug($name);
        $slug = $originalSlug;
        $counter = 1;

        while (BlockTemplate::query()->where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        $template = BlockTemplate::create([
            'user_id' => $request->user()->id,
            'name' => $name,
            'slug' => $slug,
            'scope' => $scope,
            'description' => $payload['description'] ?? null,
            'blocks' => $blocks,
            'is_active' => (bool) ($payload['is_active'] ?? true),
        ]);

        $this->logActivity($request->user()->id, $template, 'block_template.imported', 'Block template imported.');

        return redirect()
            ->route('admin.block-templates.edit', $template)
            ->with('status', 'Block template imported successfully.');
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

    private function logActivity(int $userId, BlockTemplate $template, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'event' => $event,
            'subject_type' => BlockTemplate::class,
            'subject_id' => $template->id,
            'description' => $description,
            'properties' => [
                'name' => $template->name,
                'slug' => $template->slug,
                'scope' => $template->scope,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
