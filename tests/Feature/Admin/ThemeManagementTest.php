<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_themes_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Theme::factory()->create([
            'slug' => 'default',
            'path' => 'themes/default',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.themes.index'));

        $response->assertOk()->assertSee('Manage themes');
    }

    public function test_admin_can_activate_theme(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $theme = Theme::factory()->create([
            'slug' => 'default',
            'path' => 'themes/default',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.themes.activate', $theme));

        $response->assertRedirect(route('admin.themes.index'));
        $this->assertSame('default', Setting::valueFor('active_theme'));
    }

    public function test_homepage_uses_themed_template_marker(): void
    {
        $response = $this->get('/');

        $response->assertOk()->assertSee('Default Theme Experience');
    }
}
