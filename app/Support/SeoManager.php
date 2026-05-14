<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Collection;

class SeoManager
{
    public function settings(): array
    {
        $siteName = Setting::valueFor('site_name', 'Nova CMS');
        $siteTagline = Setting::valueFor('site_tagline', 'Commercial-ready Laravel CMS foundation');
        $baseUrl = rtrim((string) Setting::valueFor('canonical_base_url', config('app.url')), '/');

        return [
            'meta_title_template' => Setting::valueFor('meta_title_template', '{title} | {site_name}'),
            'default_meta_description' => Setting::valueFor('default_meta_description', $siteTagline),
            'meta_robots' => Setting::valueFor('meta_robots', 'index,follow'),
            'canonical_base_url' => $baseUrl,
            'og_site_name' => Setting::valueFor('og_site_name', $siteName),
            'twitter_card' => Setting::valueFor('twitter_card', 'summary_large_image'),
            'robots_txt_content' => Setting::valueFor('robots_txt_content'),
            'sitemap_enabled' => (bool) Setting::valueFor('sitemap_enabled', true),
            'site_name' => $siteName,
        ];
    }

    public function buildTitle(?string $title = null): string
    {
        $settings = $this->settings();
        $title = $title ?: $settings['site_name'];

        return str_replace(
            ['{title}', '{site_name}'],
            [$title, $settings['site_name']],
            $settings['meta_title_template']
        );
    }

    public function defaultRobotsTxt(): string
    {
        $settings = $this->settings();

        return implode("\n", [
            'User-agent: *',
            'Allow: /',
            '',
            'Sitemap: '.$settings['canonical_base_url'].'/sitemap.xml',
        ]);
    }

    public function sitemapItems(): Collection
    {
        $items = collect([
            [
                'loc' => route('home'),
                'lastmod' => now(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('posts.index'),
                'lastmod' => now(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
        ]);

        $pages = Page::query()->published()->get()->map(fn (Page $page): array => [
            'loc' => route('pages.show', $page->slug),
            'lastmod' => $page->updated_at ?? $page->published_at ?? now(),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ]);

        $posts = Post::query()->published()->get()->map(fn (Post $post): array => [
            'loc' => route('posts.show', $post->slug),
            'lastmod' => $post->updated_at ?? $post->published_at ?? now(),
            'changefreq' => 'weekly',
            'priority' => '0.7',
        ]);

        $categories = Category::query()
            ->whereHas('posts', fn ($query) => $query->published())
            ->get()
            ->map(fn (Category $category): array => [
                'loc' => route('posts.category', $category->slug),
                'lastmod' => $category->updated_at ?? now(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ]);

        return $items
            ->merge($pages)
            ->merge($posts)
            ->merge($categories);
    }
}
