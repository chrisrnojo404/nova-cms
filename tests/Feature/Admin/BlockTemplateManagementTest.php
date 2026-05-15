<?php

namespace Tests\Feature\Admin;

use App\Models\BlockTemplate;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockTemplateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_create_builder_template(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.block-templates.store'), [
            'name' => 'Landing Hero',
            'slug' => '',
            'scope' => 'both',
            'description' => 'Reusable hero template',
            'is_active' => '1',
            'builder_blocks' => json_encode([
                ['type' => 'heading', 'data' => ['content' => 'Hero heading', 'level' => 1]],
                ['type' => 'button', 'data' => ['text' => 'Get started', 'url' => '/contact', 'style' => 'primary']],
            ], JSON_THROW_ON_ERROR),
        ]);

        $template = BlockTemplate::query()->where('name', 'Landing Hero')->firstOrFail();

        $response->assertRedirect(route('admin.block-templates.edit', $template));
        $this->assertSame('landing-hero', $template->slug);
        $this->assertSame('button', $template->blocks[1]['type']);
    }

    public function test_page_builder_screen_shows_reusable_templates(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        BlockTemplate::create([
            'user_id' => $admin->id,
            'name' => 'Reusable Hero',
            'slug' => 'reusable-hero',
            'scope' => 'page',
            'description' => 'Shared landing hero',
            'blocks' => [
                ['type' => 'heading', 'data' => ['content' => 'Reusable hero', 'level' => 1]],
            ],
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.pages.create'));

        $response
            ->assertOk()
            ->assertSee('Reusable templates')
            ->assertSee('Reusable Hero')
            ->assertSee('Manage templates');
    }

    public function test_post_builder_screen_hides_page_only_templates(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        BlockTemplate::create([
            'user_id' => $admin->id,
            'name' => 'Page Only Template',
            'slug' => 'page-only-template',
            'scope' => 'page',
            'description' => 'Only for pages',
            'blocks' => [
                ['type' => 'paragraph', 'data' => ['content' => 'Page template']],
            ],
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.posts.create'));

        $response
            ->assertOk()
            ->assertDontSee('Page Only Template');
    }

    public function test_admin_can_export_builder_template(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $template = BlockTemplate::create([
            'user_id' => $admin->id,
            'name' => 'Exportable Template',
            'slug' => 'exportable-template',
            'scope' => 'both',
            'description' => 'Export test',
            'blocks' => [
                ['type' => 'paragraph', 'data' => ['content' => 'Export me']],
            ],
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.block-templates.export', $template));

        $response
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename="exportable-template-template.json"')
            ->assertJsonPath('name', 'Exportable Template')
            ->assertJsonPath('blocks.0.type', 'paragraph');
    }

    public function test_admin_can_import_builder_template(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.block-templates.import'), [
            'template_json' => json_encode([
                'name' => 'Imported Template',
                'scope' => 'both',
                'description' => 'Imported description',
                'blocks' => [
                    ['type' => 'heading', 'data' => ['content' => 'Imported heading', 'level' => 2]],
                ],
            ], JSON_THROW_ON_ERROR),
        ]);

        $template = BlockTemplate::query()->where('name', 'Imported Template')->firstOrFail();

        $response->assertRedirect(route('admin.block-templates.edit', $template));
        $this->assertSame('heading', $template->blocks[0]['type']);
    }
}
