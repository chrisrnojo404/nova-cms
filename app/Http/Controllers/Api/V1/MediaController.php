<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaStoreRequest;
use App\Http\Resources\Api\V1\MediaResource;
use App\Models\ActivityLog;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        abort_unless($request->user()?->can('manage media'), 403);

        $search = trim((string) $request->string('search'));
        $query = Media::query()->with('user')->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('original_name', 'like', "%{$search}%")
                    ->orWhere('filename', 'like', "%{$search}%")
                    ->orWhere('directory', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%")
                    ->orWhere('mime_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('directory')) {
            $query->where('directory', 'like', '%'.trim((string) $request->string('directory')).'%');
        }

        if ($request->filled('mime_type')) {
            $query->where('mime_type', 'like', trim((string) $request->string('mime_type')).'%');
        }

        return MediaResource::collection($query->paginate($this->perPage($request, 18))->withQueryString());
    }

    public function store(MediaStoreRequest $request): JsonResponse
    {
        $directory = trim((string) $request->input('directory', 'media/uploads'), '/');
        $createdMedia = [];

        foreach ($request->file('files', []) as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = Str::uuid()->toString().($extension ? ".{$extension}" : '');
            $path = $file->storeAs($directory, $filename, 'public');

            $createdMedia[] = Media::create([
                'user_id' => $request->user()->id,
                'disk' => 'public',
                'directory' => $directory,
                'filename' => basename($path),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream',
                'extension' => $extension ?: null,
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'size' => $file->getSize() ?: 0,
                'alt_text' => Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                    ->replace(['-', '_'], ' ')
                    ->title()
                    ->toString(),
            ]);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'event' => 'media.uploaded',
            'subject_type' => Media::class,
            'subject_id' => $createdMedia[0]?->id,
            'description' => count($createdMedia) > 1 ? 'Multiple media files uploaded.' : 'Media file uploaded.',
            'properties' => [
                'count' => count($createdMedia),
                'directory' => $directory,
                'paths' => array_map(static fn (Media $media): string => $media->path, $createdMedia),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => count($createdMedia) > 1 ? 'Media files uploaded successfully.' : 'Media file uploaded successfully.',
            'data' => MediaResource::collection(EloquentCollection::make($createdMedia)->load('user'))->resolve(),
        ], 201);
    }

    public function destroy(Request $request, Media $media): JsonResponse
    {
        abort_unless($request->user()?->can('manage media'), 403);

        Storage::disk($media->disk)->delete($media->path);

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'event' => 'media.deleted',
            'subject_type' => Media::class,
            'subject_id' => $media->id,
            'description' => 'Media file deleted.',
            'properties' => [
                'path' => $media->path,
                'original_name' => $media->original_name,
                'mime_type' => $media->mime_type,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $media->delete();

        return response()->json([
            'message' => 'Media item deleted successfully.',
        ]);
    }

    private function perPage(Request $request, int $default): int
    {
        return min(max($request->integer('per_page', $default), 1), 50);
    }
}
