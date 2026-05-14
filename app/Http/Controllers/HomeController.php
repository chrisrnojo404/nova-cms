<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Setting;
use App\Support\SeoManager;
use App\Support\ThemeManager;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly ThemeManager $themeManager,
        private readonly SeoManager $seoManager
    )
    {
    }

    public function __invoke(): View
    {
        $homepageMode = Setting::valueFor('homepage_mode', 'preview');

        if ($homepageMode === 'page') {
            $pageId = Setting::valueFor('homepage_page_id');

            if ($pageId) {
                $page = Page::query()
                    ->with('author')
                    ->published()
                    ->find($pageId);

                if ($page) {
                    return $this->themeManager->themedView('pages.show', [
                        'page' => $page,
                        'title' => $this->seoManager->buildTitle($page->meta_title ?: $page->title),
                        'description' => $page->meta_description,
                        'ogImage' => $page->featured_image,
                        'canonical' => route('home'),
                    ], 'pages.show');
                }
            }
        }

        return $this->themeManager->themedView('welcome', [
            'title' => $this->seoManager->buildTitle($this->seoManager->settings()['site_name']),
            'description' => $this->seoManager->settings()['default_meta_description'],
            'canonical' => route('home'),
        ], 'welcome');
    }
}
