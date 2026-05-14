<?php

namespace App\Support;

use App\Models\Setting;
use App\Models\Theme;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ThemeManager
{
    public function __construct(private readonly ViewFactory $view)
    {
    }

    public function activeThemeSlug(): string
    {
        return (string) Setting::valueFor('active_theme', 'default');
    }

    public function activeThemePath(): string
    {
        return base_path('themes/'.$this->activeThemeSlug());
    }

    public function registerActiveThemeNamespace(): void
    {
        $path = $this->activeThemePath();

        if (is_dir($path)) {
            $this->view->replaceNamespace('theme', [$path]);
            return;
        }

        $this->view->replaceNamespace('theme', [base_path('themes/default')]);
    }

    public function discoverThemes(): array
    {
        $themes = [];

        foreach (File::directories(base_path('themes')) as $directory) {
            $slug = basename($directory);
            $manifestPath = $directory.'/theme.json';

            if (! File::exists($manifestPath)) {
                continue;
            }

            $manifest = json_decode((string) File::get($manifestPath), true);

            if (! is_array($manifest)) {
                continue;
            }

            $themes[] = [
                'name' => $manifest['name'] ?? ucfirst(str_replace('-', ' ', $slug)),
                'slug' => $slug,
                'version' => $manifest['version'] ?? '1.0.0',
                'author' => $manifest['author'] ?? null,
                'description' => $manifest['description'] ?? null,
                'path' => 'themes/'.$slug,
                'is_active' => $slug === $this->activeThemeSlug(),
                'meta' => $manifest,
            ];
        }

        return $themes;
    }

    public function syncDiscoveredThemes(): void
    {
        if (! Schema::hasTable('themes')) {
            return;
        }

        foreach ($this->discoverThemes() as $theme) {
            Theme::updateOrCreate(
                ['slug' => $theme['slug']],
                $theme
            );
        }

        Theme::query()->update([
            'is_active' => false,
        ]);

        Theme::query()
            ->where('slug', $this->activeThemeSlug())
            ->update(['is_active' => true]);
    }

    public function themedView(string $view, array $data = [], ?string $fallback = null)
    {
        $themed = "theme::{$view}";

        if ($this->view->exists($themed)) {
            return view($themed, $data);
        }

        if ($fallback) {
            return view($fallback, $data);
        }

        abort(404, "Theme view [{$view}] not found.");
    }
}
