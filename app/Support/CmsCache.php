<?php

namespace App\Support;

use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class CmsCache
{
    public const SITE_SETTINGS_KEY = 'cms.settings.site';

    public const HEADER_MENU_KEY = 'cms.menu.header';

    public const FOOTER_MENU_KEY = 'cms.menu.footer';

    public const SEO_SETTINGS_KEY = 'cms.settings.seo';

    public function siteSettings(array $defaults): array
    {
        return Cache::rememberForever(self::SITE_SETTINGS_KEY, function () use ($defaults): array {
            if (! Schema::hasTable('settings')) {
                return $defaults;
            }

            return array_merge($defaults, [
                'site_name' => Setting::valueFor('site_name', $defaults['site_name']),
                'site_tagline' => Setting::valueFor('site_tagline', $defaults['site_tagline']),
                'brand_accent' => Setting::valueFor('brand_accent', $defaults['brand_accent']),
                'media_upload_directory' => Setting::valueFor('media_upload_directory', $defaults['media_upload_directory']),
            ]);
        });
    }

    public function menu(?string $location): ?Menu
    {
        if (! $location || ! Schema::hasTable('menus')) {
            return null;
        }

        $cacheKey = $this->menuKey($location);
        $cachedValue = Cache::get($cacheKey);

        if ($cachedValue instanceof Menu) {
            Cache::forget($cacheKey);
            $cachedValue = null;
        }

        if ($cachedValue instanceof \__PHP_Incomplete_Class || (is_object($cachedValue) && ! is_scalar($cachedValue))) {
            Cache::forget($cacheKey);
            $cachedValue = null;
        }

        $menuId = is_numeric($cachedValue)
            ? (int) $cachedValue
            : Cache::rememberForever($cacheKey, function () use ($location): ?int {
                return Menu::query()
                    ->active()
                    ->where('location', $location)
                    ->value('id');
            });

        if (! $menuId) {
            return null;
        }

        return $this->loadMenu($menuId);
    }

    public function seoSettings(callable $resolver): array
    {
        return Cache::rememberForever(self::SEO_SETTINGS_KEY, fn (): array => $resolver());
    }

    public function flushPublicConfiguration(): void
    {
        Cache::forget(self::SITE_SETTINGS_KEY);
        Cache::forget(self::HEADER_MENU_KEY);
        Cache::forget(self::FOOTER_MENU_KEY);
        Cache::forget(self::SEO_SETTINGS_KEY);
    }

    public function flushMenus(): void
    {
        Cache::forget(self::HEADER_MENU_KEY);
        Cache::forget(self::FOOTER_MENU_KEY);
    }

    public function flushSettings(): void
    {
        Cache::forget(self::SITE_SETTINGS_KEY);
    }

    public function flushSeo(): void
    {
        Cache::forget(self::SEO_SETTINGS_KEY);
    }

    public function flushAll(): void
    {
        $this->flushPublicConfiguration();
    }

    private function menuKey(string $location): string
    {
        return match ($location) {
            'header' => self::HEADER_MENU_KEY,
            'footer' => self::FOOTER_MENU_KEY,
            default => 'cms.menu.'.$location,
        };
    }

    private function loadMenu(int $menuId): ?Menu
    {
        return Menu::query()
            ->with([
                'rootItems' => fn ($query) => $query->with('children.children'),
                'rootItems.page',
                'rootItems.post',
                'rootItems.category',
                'rootItems.children.page',
                'rootItems.children.post',
                'rootItems.children.category',
                'rootItems.children.children.page',
                'rootItems.children.children.post',
                'rootItems.children.children.category',
            ])
            ->find($menuId);
    }
}
