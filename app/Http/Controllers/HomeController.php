<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Setting;
use App\Support\ThemeManager;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly ThemeManager $themeManager)
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
                    return $this->themeManager->themedView('pages.show', compact('page'), 'pages.show');
                }
            }
        }

        return $this->themeManager->themedView('welcome', [], 'welcome');
    }
}
