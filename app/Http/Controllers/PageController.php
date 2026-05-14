<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\PluginManager;
use App\Support\SeoManager;
use App\Support\ThemeManager;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly ThemeManager $themeManager,
        private readonly PluginManager $pluginManager,
        private readonly SeoManager $seoManager
    )
    {
    }

    public function show(string $slug): View
    {
        $page = Page::query()
            ->with('author')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $page->content = $this->pluginManager->renderContent($page->content);

        return $this->themeManager->themedView('pages.show', [
            'page' => $page,
            'title' => $this->seoManager->buildTitle($page->meta_title ?: $page->title),
            'description' => $page->meta_description ?: $this->seoManager->settings()['default_meta_description'],
            'ogImage' => $page->featured_image,
            'canonical' => route('pages.show', $page->slug),
        ], 'pages.show');
    }
}
