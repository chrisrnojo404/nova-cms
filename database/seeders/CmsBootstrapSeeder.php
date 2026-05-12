<?php

namespace Database\Seeders;

use App\Models\Plugin;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CmsBootstrapSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@nova-cms.test'],
            [
                'name' => 'Nova Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['super-admin']);

        Setting::upsert([
            ['group' => 'general', 'key' => 'site_name', 'value' => json_encode(['value' => 'Nova CMS']), 'is_public' => true, 'autoload' => true],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => json_encode(['value' => 'Commercial-ready Laravel CMS foundation']), 'is_public' => true, 'autoload' => true],
            ['group' => 'branding', 'key' => 'active_theme', 'value' => json_encode(['value' => 'default']), 'is_public' => true, 'autoload' => true],
        ], ['key'], ['group', 'value', 'is_public', 'autoload']);

        Theme::updateOrCreate(
            ['slug' => 'default'],
            [
                'name' => 'Default Theme',
                'version' => '1.0.0',
                'author' => 'CRN CMS',
                'description' => 'Default Nova CMS frontend theme scaffold.',
                'path' => 'themes/default',
                'is_active' => true,
                'meta' => ['type' => 'frontend'],
            ]
        );

        Plugin::updateOrCreate(
            ['slug' => 'contact-form'],
            [
                'name' => 'Contact Form',
                'version' => '1.0.0',
                'author' => 'CRN CMS',
                'description' => 'Example plugin scaffold for future shortcode and widget support.',
                'path' => 'plugins/contact-form',
                'is_active' => false,
                'meta' => ['shortcodes' => ['contact_form']],
            ]
        );
    }
}
