<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Support\PluginManager;
use App\Support\ThemeManager;
use Illuminate\Contracts\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly ThemeManager $themeManager,
        private readonly PluginManager $pluginManager
    )
    {
    }

    public function index(): View
    {
        $perPage = max(1, min(24, (int) Setting::valueFor('posts_per_page', 9)));

        return $this->themeManager->themedView('posts.index', [
            'posts' => Post::query()
                ->with(['author', 'category'])
                ->published()
                ->latest('published_at')
                ->paginate($perPage),
        ], 'posts.index');
    }

    public function show(string $slug): View
    {
        $post = Post::query()
            ->with(['author', 'category'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $post->content = $this->pluginManager->renderContent($post->content);

        return $this->themeManager->themedView('posts.show', compact('post'), 'posts.show');
    }

    public function category(string $slug): View
    {
        $category = Category::query()->where('slug', $slug)->firstOrFail();
        $perPage = max(1, min(24, (int) Setting::valueFor('posts_per_page', 9)));

        return $this->themeManager->themedView('posts.category', [
            'category' => $category,
            'posts' => $category->posts()
                ->with('author')
                ->published()
                ->latest('published_at')
                ->paginate($perPage),
        ], 'posts.category');
    }
}
