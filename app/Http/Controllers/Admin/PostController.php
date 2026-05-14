<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostStoreRequest;
use App\Http\Requests\Admin\PostUpdateRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    public function index(): View
    {
        return view('admin.posts.index', [
            'posts' => Post::query()
                ->with(['author', 'category'])
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.create', [
            'post' => new Post(['status' => 'draft']),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(PostStoreRequest $request): RedirectResponse
    {
        $post = Post::create([
            ...$request->validated(),
            'author_id' => $request->user()->id,
            'blocks' => $this->buildBlocks(
                $request->string('title')->toString(),
                $request->input('excerpt'),
                $request->input('content')
            ),
            'published_at' => $request->input('status') === 'published' ? now() : null,
        ]);

        $this->logPostActivity($request->user()->id, $post, 'post.created', 'Post created.');

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('status', 'Post created successfully.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.edit', [
            'post' => $post,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(PostUpdateRequest $request, Post $post): RedirectResponse
    {
        $status = $request->input('status');

        $post->update([
            ...$request->validated(),
            'blocks' => $this->buildBlocks(
                $request->string('title')->toString(),
                $request->input('excerpt'),
                $request->input('content')
            ),
            'published_at' => $status === 'published'
                ? ($post->published_at ?? now())
                : null,
        ]);

        $this->logPostActivity($request->user()->id, $post, 'post.updated', 'Post updated.');

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('status', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $userId = request()->user()?->id;
        $title = $post->title;
        $post->delete();

        ActivityLog::create([
            'user_id' => $userId,
            'event' => 'post.deleted',
            'subject_type' => Post::class,
            'subject_id' => $post->id,
            'description' => 'Post deleted.',
            'properties' => [
                'title' => $title,
                'slug' => $post->slug,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('status', 'Post deleted successfully.');
    }

    private function buildBlocks(string $title, ?string $excerpt, ?string $content): array
    {
        return array_values(array_filter([
            ['type' => 'heading', 'content' => $title],
            $excerpt ? ['type' => 'paragraph', 'content' => $excerpt] : null,
            $content ? ['type' => 'paragraph', 'content' => strip_tags($content)] : null,
        ]));
    }

    private function logPostActivity(int $userId, Post $post, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'event' => $event,
            'subject_type' => Post::class,
            'subject_id' => $post->id,
            'description' => $description,
            'properties' => [
                'title' => $post->title,
                'status' => $post->status,
                'slug' => $post->slug,
                'category_id' => $post->category_id,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
