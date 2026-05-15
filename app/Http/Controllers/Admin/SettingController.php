<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsUpdateRequest;
use App\Models\ActivityLog;
use App\Models\Page;
use App\Models\Setting;
use App\Support\CmsCache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    public function __construct(private readonly CmsCache $cache)
    {
    }

    public function index(): View
    {
        $values = [];

        foreach ($this->definitions() as $group => $fields) {
            foreach ($fields as $field) {
                $values[$field['key']] = Setting::valueFor($field['key'], $field['default']);
            }
        }

        return view('admin.settings.index', [
            'definitions' => $this->definitions(),
            'values' => $values,
            'pages' => Page::query()->published()->orderBy('title')->get(),
        ]);
    }

    public function update(SettingsUpdateRequest $request): RedirectResponse
    {
        $payload = [];

        foreach ($this->definitions() as $group => $fields) {
            foreach ($fields as $field) {
                $payload[] = [
                    'group' => $group,
                    'key' => $field['key'],
                    'value' => ['value' => $request->input($field['key'], $field['default'])],
                    'is_public' => $field['public'] ?? false,
                    'autoload' => true,
                ];
            }
        }

        Setting::storeMany($payload);
        $this->cache->flushSettings();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'event' => 'settings.updated',
            'subject_type' => Setting::class,
            'subject_id' => null,
            'description' => 'Site settings updated.',
            'properties' => [
                'keys' => array_column($payload, 'key'),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Settings updated successfully.');
    }

    private function definitions(): array
    {
        return [
            'general' => [
                ['key' => 'site_name', 'label' => 'Site name', 'type' => 'text', 'default' => 'Nova CMS', 'public' => true],
                ['key' => 'site_tagline', 'label' => 'Tagline', 'type' => 'textarea', 'default' => 'Commercial-ready Laravel CMS foundation', 'public' => true],
                ['key' => 'site_email', 'label' => 'Site email', 'type' => 'email', 'default' => 'hello@nova-cms.test', 'public' => false],
            ],
            'branding' => [
                ['key' => 'active_theme', 'label' => 'Active theme', 'type' => 'text', 'default' => 'default', 'public' => true],
                ['key' => 'brand_accent', 'label' => 'Brand accent hex', 'type' => 'text', 'default' => '#22d3ee', 'public' => true],
            ],
            'homepage' => [
                ['key' => 'homepage_mode', 'label' => 'Homepage mode', 'type' => 'select', 'default' => 'preview', 'options' => ['preview' => 'Preview landing', 'page' => 'Published page'], 'public' => true],
                ['key' => 'homepage_page_id', 'label' => 'Homepage page', 'type' => 'page_select', 'default' => null, 'public' => true],
            ],
            'reading' => [
                ['key' => 'posts_per_page', 'label' => 'Posts per page', 'type' => 'number', 'default' => 9, 'public' => true],
            ],
            'media' => [
                ['key' => 'media_upload_directory', 'label' => 'Default upload directory', 'type' => 'text', 'default' => 'media/uploads', 'public' => false],
                ['key' => 'image_quality', 'label' => 'Default image quality', 'type' => 'number', 'default' => 82, 'public' => false],
            ],
            'operations' => [
                ['key' => 'backup_retention_days', 'label' => 'Backup retention (days)', 'type' => 'number', 'default' => 14, 'public' => false],
            ],
        ];
    }
}
