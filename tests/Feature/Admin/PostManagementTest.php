<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_posts_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.posts.index'));

        $response->assertOk()->assertSee('Manage posts');
    }

    public function test_admin_can_create_post(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.posts.store'), [
            'category_id' => $category->id,
            'title' => 'Editorial Launch',
            'slug' => '',
            'excerpt' => 'A short announcement.',
            'content' => '<p>Editorial Launch body.</p>',
            'status' => 'published',
            'meta_title' => 'Editorial Launch',
            'meta_description' => 'A short announcement.',
        ]);

        $post = Post::query()->where('title', 'Editorial Launch')->first();

        $this->assertNotNull($post);
        $this->assertSame('editorial-launch', $post->slug);
        $this->assertSame($category->id, $post->category_id);
        $response->assertRedirect(route('admin.posts.edit', $post));
    }

    public function test_published_post_can_be_rendered_publicly(): void
    {
        $post = Post::factory()->published()->create([
            'slug' => 'launch-note',
            'title' => 'Launch Note',
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertOk()->assertSee('Launch Note');
    }

    public function test_category_archive_lists_published_posts(): void
    {
        $category = Category::factory()->create([
            'slug' => 'product-news',
            'name' => 'Product News',
        ]);

        $post = Post::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'New Release',
            'slug' => 'new-release',
        ]);

        $response = $this->get(route('posts.category', $category->slug));

        $response->assertOk()->assertSee($post->title);
    }
}
