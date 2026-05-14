<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_public_api_only_returns_published_pages(): void
    {
        Page::factory()->published()->create([
            'title' => 'Published Page',
            'slug' => 'published-page',
        ]);

        Page::factory()->create([
            'title' => 'Draft Page',
            'slug' => 'draft-page',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->getJson('/api/v1/pages');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'published-page');
    }

    public function test_api_user_with_permissions_can_create_page(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        Sanctum::actingAs($editor);

        $response = $this->postJson('/api/v1/pages', [
            'title' => 'API Services',
            'slug' => '',
            'content' => '<p>API managed content</p>',
            'status' => 'published',
            'template' => 'default',
            'meta_title' => 'API Services',
            'meta_description' => 'Page created over the API.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.slug', 'api-services')
            ->assertJsonPath('data.status', 'published');

        $this->assertDatabaseHas('pages', [
            'slug' => 'api-services',
            'title' => 'API Services',
        ]);
    }

    public function test_author_without_page_permission_cannot_create_page_over_api(): void
    {
        $author = User::factory()->create();
        $author->assignRole('author');

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/pages', [
            'title' => 'Unauthorized Page',
            'status' => 'draft',
        ]);

        $response->assertForbidden();
    }

    public function test_public_menu_endpoint_returns_nested_items(): void
    {
        $menu = Menu::factory()->create([
            'slug' => 'primary',
            'location' => 'header',
            'is_active' => true,
        ]);

        $parent = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'title' => 'Parent',
            'position' => 1,
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $parent->id,
            'title' => 'Child',
            'position' => 2,
        ]);

        $response = $this->getJson('/api/v1/menus/location/header');

        $response
            ->assertOk()
            ->assertJsonPath('data.slug', 'primary')
            ->assertJsonPath('data.items.0.resolved_title', 'Parent')
            ->assertJsonPath('data.items.0.children.0.resolved_title', 'Child');
    }

    public function test_public_category_endpoint_returns_published_posts(): void
    {
        $category = Category::factory()->create([
            'name' => 'Announcements',
            'slug' => 'announcements',
        ]);

        Post::factory()->published()->create([
            'category_id' => $category->id,
            'slug' => 'launch-update',
            'title' => 'Launch Update',
        ]);

        Post::factory()->create([
            'category_id' => $category->id,
            'slug' => 'draft-update',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->getJson('/api/v1/categories/announcements');

        $response
            ->assertOk()
            ->assertJsonPath('category.slug', 'announcements')
            ->assertJsonCount(1, 'posts.data')
            ->assertJsonPath('posts.data.0.slug', 'launch-update');
    }

    public function test_public_posts_endpoint_supports_search_and_category_filters(): void
    {
        $category = Category::factory()->create([
            'name' => 'Releases',
            'slug' => 'releases',
        ]);

        Post::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'Nova Release Notes',
            'slug' => 'nova-release-notes',
        ]);

        Post::factory()->published()->create([
            'title' => 'Editorial Calendar',
            'slug' => 'editorial-calendar',
        ]);

        $response = $this->getJson('/api/v1/posts?category=releases&search=release');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'nova-release-notes');
    }

    public function test_public_settings_endpoint_only_returns_public_settings(): void
    {
        Setting::storeMany([
            ['group' => 'general', 'key' => 'site_name', 'value' => ['value' => 'Nova CMS'], 'is_public' => true, 'autoload' => true],
            ['group' => 'general', 'key' => 'site_email', 'value' => ['value' => 'private@example.com'], 'is_public' => false, 'autoload' => true],
            ['group' => 'seo', 'key' => 'meta_robots', 'value' => ['value' => 'index,follow'], 'is_public' => true, 'autoload' => true],
        ]);

        $response = $this->getJson('/api/v1/settings/public');

        $response
            ->assertOk()
            ->assertJsonPath('data.general.site_name', 'Nova CMS')
            ->assertJsonPath('data.seo.meta_robots', 'index,follow')
            ->assertJsonMissingPath('data.general.site_email');
    }
}
