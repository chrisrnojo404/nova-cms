<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Support\BlockBuilder;
use App\Support\PluginManager;
use App\Support\SeoManager;
use App\Support\ThemeManager;
use Illuminate\Contracts\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly ThemeManager $themeManager,
        private readonly PluginManager $pluginManager,
        private readonly SeoManager $seoManager,
        private readonly BlockBuilder $blockBuilder
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
            'title' => $this->seoManager->buildTitle('Blog'),
            'description' => $this->seoManager->settings()['default_meta_description'],
            'canonical' => route('posts.index'),
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
        $post->blocks = $this->blockBuilder->renderReady(
            $this->blockBuilder->stripLeadingMetadataBlocks(
                $post->blocks ?: $this->blockBuilder->fallbackPostBlocks($post->title, $post->excerpt, $post->content),
                $post->title,
                $post->excerpt
            ),
            fn (string $value): string => $this->pluginManager->renderContent($value)
        );

        return $this->themeManager->themedView('posts.show', [
            'post' => $post,
            'title' => $this->seoManager->buildTitle($post->meta_title ?: $post->title),
            'description' => $post->meta_description ?: $post->excerpt ?: $this->seoManager->settings()['default_meta_description'],
            'ogImage' => $post->featured_image,
            'canonical' => route('posts.show', $post->slug),
            'ogType' => 'article',
            'renderRichContent' => $this->shouldRenderClassicContent($post->content),
        ], 'posts.show');
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
            'title' => $this->seoManager->buildTitle($category->meta_title ?: $category->name),
            'description' => $category->meta_description ?: $category->description ?: $this->seoManager->settings()['default_meta_description'],
            'canonical' => route('posts.category', $category->slug),
        ], 'posts.category');
    }

    private function shouldRenderClassicContent(?string $content): bool
    {
        $content = (string) $content;

        return $content !== '' && preg_match('/\[[^\]]+\]/', $content) === 1;
    }
}
