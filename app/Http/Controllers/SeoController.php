<?php

namespace App\Http\Controllers;

use App\Support\SeoManager;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SeoController extends Controller
{
    public function __construct(private readonly SeoManager $seoManager)
    {
    }

    public function sitemap(): Response|View
    {
        if (! $this->seoManager->settings()['sitemap_enabled']) {
            abort(404);
        }

        return response()
            ->view('seo.sitemap', [
                'items' => $this->seoManager->sitemapItems(),
            ])
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $settings = $this->seoManager->settings();
        $content = trim((string) ($settings['robots_txt_content'] ?: ''));

        return response(
            $content !== '' ? $content : $this->seoManager->defaultRobotsTxt(),
            200,
            ['Content-Type' => 'text/plain; charset=UTF-8']
        );
    }
}
