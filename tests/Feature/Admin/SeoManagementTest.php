<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_seo_settings(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.seo.index'));

        $response->assertOk()->assertSee('Search engine settings');
    }

    public function test_admin_can_update_seo_settings(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->put(route('admin.seo.update'), [
            'meta_title_template' => '{title} | CRN CMS',
            'default_meta_description' => 'A strong default description.',
            'meta_robots' => 'index,follow',
            'canonical_base_url' => 'http://localhost',
            'og_site_name' => 'CRN CMS',
            'twitter_card' => 'summary_large_image',
            'robots_txt_content' => "User-agent: *\nAllow: /",
            'sitemap_enabled' => '1',
        ]);

        $response->assertRedirect(route('admin.seo.index'));
        $this->assertSame('{title} | CRN CMS', Setting::valueFor('meta_title_template'));
    }
}
