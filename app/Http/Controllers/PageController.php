<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\ThemeManager;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function __construct(private readonly ThemeManager $themeManager)
    {
    }

    public function show(string $slug): View
    {
        $page = Page::query()
            ->with('author')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->themeManager->themedView('pages.show', compact('page'), 'pages.show');
    }
}
