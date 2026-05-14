<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use Database\Seeders\CmsBootstrapSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(CmsBootstrapSeeder::class);
    }

    public function test_sitemap_includes_core_public_urls(): void
    {
        $page = Page::factory()->published()->create(['slug' => 'services']);
        $category = Category::factory()->create(['slug' => 'product-news']);
        Post::factory()->published()->create(['slug' => 'launch-update', 'category_id' => $category->id]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('home'), false)
            ->assertSee(route('pages.show', $page->slug), false)
            ->assertSee(route('posts.show', 'launch-update'), false)
            ->assertSee(route('posts.category', $category->slug), false);
    }

    public function test_robots_txt_uses_custom_setting_when_present(): void
    {
        Setting::storeMany([
            ['group' => 'seo', 'key' => 'robots_txt_content', 'value' => ['value' => "User-agent: *\nDisallow: /private"], 'is_public' => false, 'autoload' => true],
        ]);

        $response = $this->get('/robots.txt');

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /private');
    }
}
