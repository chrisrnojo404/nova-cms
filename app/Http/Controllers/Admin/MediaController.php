<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaStoreRequest;
use App\Models\ActivityLog;
use App\Models\Media;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $query = Media::query()
            ->with('user')
            ->latest();

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

        return view('admin.media.index', [
            'mediaItems' => $query->paginate(18)->withQueryString(),
            'search' => $search,
            'stats' => [
                'total' => Media::count(),
                'images' => Media::query()->where('mime_type', 'like', 'image/%')->count(),
                'documents' => Media::query()->where('mime_type', 'like', 'application/%')->count(),
                'videos' => Media::query()->where('mime_type', 'like', 'video/%')->count(),
            ],
        ]);
    }

    public function store(MediaStoreRequest $request): RedirectResponse
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

        return redirect()
            ->route('admin.media.index')
            ->with('status', count($createdMedia) > 1 ? 'Media files uploaded successfully.' : 'Media file uploaded successfully.');
    }

    public function destroy(Request $request, Media $medium): RedirectResponse
    {
        Storage::disk($medium->disk)->delete($medium->path);

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'event' => 'media.deleted',
            'subject_type' => Media::class,
            'subject_id' => $medium->id,
            'description' => 'Media file deleted.',
            'properties' => [
                'path' => $medium->path,
                'original_name' => $medium->original_name,
                'mime_type' => $medium->mime_type,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $medium->delete();

        return redirect()
            ->route('admin.media.index')
            ->with('status', 'Media item deleted successfully.');
    }
}
