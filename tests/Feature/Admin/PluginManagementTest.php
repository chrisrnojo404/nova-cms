<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\Plugin;
use App\Models\User;
use App\Support\PluginManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PluginManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_plugins_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Plugin::factory()->create([
            'slug' => 'contact-form',
            'path' => 'plugins/contact-form',
            'meta' => ['shortcodes' => ['contact_form']],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.plugins.index'));

        $response->assertOk()->assertSee('Manage plugins');
    }

    public function test_admin_can_activate_plugin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $plugin = Plugin::factory()->create([
            'slug' => 'contact-form',
            'path' => 'plugins/contact-form',
            'is_active' => false,
            'meta' => ['shortcodes' => ['contact_form']],
        ]);

        $response = $this->actingAs($admin)->post(route('admin.plugins.activate', $plugin));

        $response->assertRedirect(route('admin.plugins.index'));
        $this->assertTrue($plugin->fresh()->is_active);
    }

    public function test_active_plugin_shortcode_renders_inside_page_content(): void
    {
        Plugin::factory()->create([
            'slug' => 'contact-form',
            'path' => 'plugins/contact-form',
            'is_active' => true,
            'meta' => ['shortcodes' => ['contact_form']],
        ]);

        $pluginManager = app(PluginManager::class);
        $pluginManager->registerDiscoveredPluginNamespaces();
        $pluginManager->registerDefaultShortcodes();
        $pluginManager->registerActivePluginRoutes();

        $page = Page::factory()->published()->create([
            'slug' => 'contact',
            'title' => 'Contact',
            'content' => '<p>Get in touch.</p>[contact_form]',
        ]);

        $response = $this->get('/contact');

        $response->assertOk()
            ->assertSee('Reach Out')
            ->assertSee('/plugins/contact-form/submit', false);
    }

    public function test_active_plugin_admin_page_is_accessible(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Plugin::factory()->create([
            'slug' => 'contact-form',
            'path' => 'plugins/contact-form',
            'is_active' => true,
            'meta' => ['shortcodes' => ['contact_form']],
        ]);

        $pluginManager = app(PluginManager::class);
        $pluginManager->registerActivePluginRoutes();
        $pluginManager->loadActivePluginBootstraps();

        $response = $this->actingAs($admin)->get('/admin/plugins/contact-form');

        $response->assertOk()->assertSee('Contact Form');
    }

    public function test_active_plugin_can_extend_dashboard_quick_actions(): void
    {
        Plugin::factory()->create([
            'slug' => 'contact-form',
            'path' => 'plugins/contact-form',
            'is_active' => true,
            'meta' => ['shortcodes' => ['contact_form']],
        ]);

        $pluginManager = app(PluginManager::class);
        $pluginManager->registerActivePluginRoutes();
        $pluginManager->loadActivePluginBootstraps();

        $actions = $pluginManager->runHook('dashboard.quick-actions', []);

        $this->assertTrue(collect($actions)->contains(
            fn (array $action): bool => $action['label'] === 'Open contact form plugin'
        ));
    }
}
