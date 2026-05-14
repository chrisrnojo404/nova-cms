<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_settings_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));

        $response->assertOk()->assertSee('Site settings');
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'site_name' => 'CRN CMS',
            'site_tagline' => 'A modern publishing stack',
            'site_email' => 'team@example.com',
            'active_theme' => 'default',
            'brand_accent' => '#0ea5e9',
            'homepage_mode' => 'preview',
            'homepage_page_id' => '',
            'posts_per_page' => 6,
            'media_upload_directory' => 'media/library',
            'image_quality' => 90,
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertSame('CRN CMS', Setting::valueFor('site_name'));
        $this->assertSame(6, (int) Setting::valueFor('posts_per_page'));
    }

    public function test_homepage_can_render_configured_page(): void
    {
        $page = Page::factory()->published()->create([
            'title' => 'Home Page',
            'slug' => 'home-page',
        ]);

        Setting::storeMany([
            ['group' => 'homepage', 'key' => 'homepage_mode', 'value' => ['value' => 'page'], 'is_public' => true, 'autoload' => true],
            ['group' => 'homepage', 'key' => 'homepage_page_id', 'value' => ['value' => $page->id], 'is_public' => true, 'autoload' => true],
        ]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('Home Page');
    }
}
