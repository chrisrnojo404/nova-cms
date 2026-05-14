<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SeoSettingsUpdateRequest;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Support\SeoManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SeoController extends Controller
{
    public function __construct(private readonly SeoManager $seoManager)
    {
    }

    public function index(): View
    {
        return view('admin.seo.index', [
            'values' => $this->seoManager->settings(),
        ]);
    }

    public function update(SeoSettingsUpdateRequest $request): RedirectResponse
    {
        $payload = [
            ['group' => 'seo', 'key' => 'meta_title_template', 'value' => ['value' => $request->string('meta_title_template')->toString()], 'is_public' => true, 'autoload' => true],
            ['group' => 'seo', 'key' => 'default_meta_description', 'value' => ['value' => $request->input('default_meta_description')], 'is_public' => true, 'autoload' => true],
            ['group' => 'seo', 'key' => 'meta_robots', 'value' => ['value' => $request->input('meta_robots')], 'is_public' => true, 'autoload' => true],
            ['group' => 'seo', 'key' => 'canonical_base_url', 'value' => ['value' => rtrim((string) $request->input('canonical_base_url'), '/')], 'is_public' => true, 'autoload' => true],
            ['group' => 'seo', 'key' => 'og_site_name', 'value' => ['value' => $request->input('og_site_name')], 'is_public' => true, 'autoload' => true],
            ['group' => 'seo', 'key' => 'twitter_card', 'value' => ['value' => $request->input('twitter_card')], 'is_public' => true, 'autoload' => true],
            ['group' => 'seo', 'key' => 'robots_txt_content', 'value' => ['value' => $request->input('robots_txt_content')], 'is_public' => false, 'autoload' => true],
            ['group' => 'seo', 'key' => 'sitemap_enabled', 'value' => ['value' => $request->boolean('sitemap_enabled')], 'is_public' => true, 'autoload' => true],
        ];

        Setting::storeMany($payload);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'event' => 'seo.updated',
            'subject_type' => Setting::class,
            'subject_id' => null,
            'description' => 'SEO settings updated.',
            'properties' => [
                'keys' => array_column($payload, 'key'),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('admin.seo.index')
            ->with('status', 'SEO settings updated successfully.');
    }
}
