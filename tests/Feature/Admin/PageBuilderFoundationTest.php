<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageBuilderFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_store_custom_page_builder_blocks(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Builder Page',
            'content' => '<p>Fallback content</p>',
            'status' => 'published',
            'template' => 'default',
            'builder_blocks' => json_encode([
                ['type' => 'heading', 'data' => ['content' => 'Hero headline', 'level' => 1]],
                ['type' => 'button', 'data' => ['text' => 'Start now', 'url' => '/signup', 'style' => 'primary']],
            ], JSON_THROW_ON_ERROR),
        ]);

        $page = Page::query()->where('title', 'Builder Page')->firstOrFail();

        $response->assertRedirect(route('admin.pages.edit', $page));
        $this->assertSame('heading', $page->blocks[0]['type']);
        $this->assertSame('Hero headline', $page->blocks[0]['data']['content']);
        $this->assertSame('button', $page->blocks[1]['type']);
    }

    public function test_invalid_builder_blocks_are_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->from(route('admin.pages.create'))->post(route('admin.pages.store'), [
            'title' => 'Broken Builder Page',
            'status' => 'draft',
            'builder_blocks' => json_encode([
                ['type' => 'gallery', 'data' => ['images' => []]],
            ], JSON_THROW_ON_ERROR),
        ]);

        $response
            ->assertRedirect(route('admin.pages.create'))
            ->assertSessionHasErrors('builder_blocks');
    }

    public function test_public_post_renders_saved_builder_blocks(): void
    {
        $post = Post::factory()->published()->create([
            'title' => 'Builder Post',
            'slug' => 'builder-post',
            'excerpt' => 'Builder excerpt',
            'content' => '<p>Legacy content</p>',
            'blocks' => [
                ['type' => 'heading', 'data' => ['content' => 'Builder Headline', 'level' => 1]],
                ['type' => 'paragraph', 'data' => ['content' => 'Builder body copy']],
            ],
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response
            ->assertOk()
            ->assertSee('Builder Headline')
            ->assertSee('Builder body copy')
            ->assertDontSee('Legacy content', false);
    }
}
