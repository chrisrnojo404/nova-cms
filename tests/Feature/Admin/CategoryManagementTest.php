<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_categories_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.categories.index'));

        $response->assertOk()->assertSee('Manage categories');
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Product Updates',
            'slug' => '',
            'description' => 'News about the product.',
            'meta_title' => 'Product Updates',
            'meta_description' => 'News about the product.',
        ]);

        $category = Category::query()->where('name', 'Product Updates')->first();

        $this->assertNotNull($category);
        $this->assertSame('product-updates', $category->slug);
        $response->assertRedirect(route('admin.categories.edit', $category));
    }
}
