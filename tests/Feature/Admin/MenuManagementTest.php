<?php

namespace Tests\Feature\Admin;

use App\Models\Menu;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_menus_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.menus.index'));

        $response->assertOk()->assertSee('Manage menus');
    }

    public function test_admin_can_create_menu(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.menus.store'), [
            'name' => 'Header Navigation',
            'slug' => '',
            'location' => 'header',
            'description' => 'Primary site navigation.',
            'is_active' => '1',
        ]);

        $menu = Menu::query()->where('name', 'Header Navigation')->first();

        $this->assertNotNull($menu);
        $this->assertSame('header-navigation', $menu->slug);
        $response->assertRedirect(route('admin.menus.edit', $menu));
    }

    public function test_admin_can_attach_page_item_to_menu(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $page = Page::factory()->published()->create([
            'title' => 'Services',
            'slug' => 'services',
        ]);
        $menu = Menu::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.menu-items.store', $menu), [
            'linked_type' => 'page',
            'linked_id' => $page->id,
            'title' => '',
            'url' => '',
            'target' => 'same_tab',
            'position' => 0,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.menus.edit', $menu));
        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'linked_type' => 'page',
            'linked_id' => $page->id,
        ]);
    }

    public function test_public_home_renders_header_menu_items(): void
    {
        $page = Page::factory()->published()->create([
            'title' => 'Services',
            'slug' => 'services',
        ]);

        $menu = Menu::factory()->create([
            'name' => 'Header Navigation',
            'slug' => 'header-navigation',
            'location' => 'header',
            'is_active' => true,
        ]);

        $menu->items()->create([
            'linked_type' => 'page',
            'linked_id' => $page->id,
            'title' => 'Services',
            'target' => 'same_tab',
            'position' => 0,
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('Services');
    }
}
