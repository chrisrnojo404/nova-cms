<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_pages_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.pages.index'));

        $response->assertOk()->assertSee('Manage pages');
    }

    public function test_admin_can_create_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Services',
            'slug' => '',
            'content' => '<p>Services page content</p>',
            'status' => 'published',
            'template' => 'default',
            'meta_title' => 'Services',
            'meta_description' => 'Our services page',
        ]);

        $page = Page::query()->where('title', 'Services')->first();

        $this->assertNotNull($page);
        $this->assertSame('services', $page->slug);
        $this->assertSame('published', $page->status);
        $response->assertRedirect(route('admin.pages.edit', $page));
    }

    public function test_published_page_can_be_rendered_publicly(): void
    {
        $page = Page::factory()->published()->create([
            'slug' => 'about-us',
            'title' => 'About Us',
        ]);

        $response = $this->get('/about-us');

        $response->assertOk()->assertSee('About Us');
    }

    public function test_draft_page_is_not_publicly_visible(): void
    {
        Page::factory()->create([
            'slug' => 'private-draft',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get('/private-draft');

        $response->assertNotFound();
    }
}
