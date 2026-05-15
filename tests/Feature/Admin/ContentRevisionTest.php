<?php

namespace Tests\Feature\Admin;

use App\Models\ContentRevision;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentRevisionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_page_update_creates_revision_snapshot(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $page = Page::factory()->create([
            'title' => 'Original Page',
            'content' => '<p>Original body</p>',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => 'Updated Page',
            'slug' => $page->slug,
            'content' => '<p>Updated body</p>',
            'status' => 'draft',
            'template' => 'default',
            'featured_image' => $page->featured_image,
            'meta_title' => 'Updated Page',
            'meta_description' => 'Updated description',
        ]);

        $response->assertRedirect(route('admin.pages.edit', $page));

        $this->assertDatabaseHas('content_revisions', [
            'revisionable_type' => Page::class,
            'revisionable_id' => $page->id,
            'label' => 'Saved page revision',
        ]);
    }

    public function test_admin_can_restore_page_revision(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $page = Page::factory()->create([
            'title' => 'Current Page',
            'content' => '<p>Current</p>',
            'blocks' => [
                ['type' => 'paragraph', 'data' => ['content' => 'Current block']],
            ],
        ]);

        $revision = ContentRevision::create([
            'user_id' => $admin->id,
            'revisionable_type' => Page::class,
            'revisionable_id' => $page->id,
            'label' => 'Older version',
            'snapshot' => [
                'title' => 'Older Page',
                'slug' => $page->slug,
                'content' => '<p>Older</p>',
                'status' => 'draft',
                'template' => 'default',
                'featured_image' => null,
                'meta_title' => 'Older Page',
                'meta_description' => 'Older description',
                'blocks' => [
                    ['type' => 'paragraph', 'data' => ['content' => 'Older block']],
                ],
                'published_at' => null,
            ],
        ]);

        $response = $this->actingAs($admin)->post(route('admin.pages.revisions.restore', [$page, $revision]));

        $response->assertRedirect(route('admin.pages.edit', $page));

        $page->refresh();

        $this->assertSame('Older Page', $page->title);
        $this->assertSame('Older block', $page->blocks[0]['data']['content']);
    }

    public function test_post_edit_screen_shows_revision_history_panel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $post = Post::factory()->create();

        ContentRevision::create([
            'user_id' => $admin->id,
            'revisionable_type' => Post::class,
            'revisionable_id' => $post->id,
            'label' => 'Saved post revision',
            'snapshot' => [
                'category_id' => $post->category_id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'status' => $post->status,
                'featured_image' => $post->featured_image,
                'meta_title' => $post->meta_title,
                'meta_description' => $post->meta_description,
                'blocks' => $post->blocks,
                'published_at' => $post->published_at,
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.posts.edit', $post));

        $response
            ->assertOk()
            ->assertSee('Revision history')
            ->assertSee('Saved post revision')
            ->assertSee('Restore revision');
    }
}
