<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Models\Theme;
use App\Support\CmsCache;
use App\Support\ThemeManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ThemeController extends Controller
{
    public function __construct(
        private readonly ThemeManager $themeManager,
        private readonly CmsCache $cache
    )
    {
    }

    public function index(): View
    {
        $this->themeManager->syncDiscoveredThemes();

        return view('admin.themes.index', [
            'themes' => Theme::query()->orderByDesc('is_active')->orderBy('name')->get(),
            'activeTheme' => $this->themeManager->activeThemeSlug(),
        ]);
    }

    public function activate(Theme $theme): RedirectResponse
    {
        Setting::storeMany([
            ['group' => 'branding', 'key' => 'active_theme', 'value' => ['value' => $theme->slug], 'is_public' => true, 'autoload' => true],
        ]);
        $this->cache->flushSettings();

        $this->themeManager->syncDiscoveredThemes();
        $this->themeManager->registerActiveThemeNamespace();

        ActivityLog::create([
            'user_id' => request()->user()?->id,
            'event' => 'theme.activated',
            'subject_type' => Theme::class,
            'subject_id' => $theme->id,
            'description' => 'Theme activated.',
            'properties' => [
                'theme' => $theme->slug,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.themes.index')
            ->with('status', "{$theme->name} is now the active theme.");
    }
}
