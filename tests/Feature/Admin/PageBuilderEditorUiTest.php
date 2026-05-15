<?php

namespace Tests\Feature\Admin;

use App\Models\Media;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageBuilderEditorUiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_page_create_screen_shows_visual_builder_editor(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.pages.create'));

        $response
            ->assertOk()
            ->assertSee('Visual block editor enabled')
            ->assertSee('Load JSON Into Editor')
            ->assertSee('Add blocks')
            ->assertSee('Starter layouts')
            ->assertSee('Hero + CTA')
            ->assertSee('Media library picker')
            ->assertSee('Featured image')
            ->assertSee('Draft autosave')
            ->assertSee('Advanced JSON editor');
    }

    public function test_page_create_screen_exposes_uploaded_media_to_builder_picker(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Media::factory()->create([
            'user_id' => $admin->id,
            'original_name' => 'builder-image.jpg',
            'alt_text' => 'Builder image asset',
            'url' => '/storage/media/images/builder-image.jpg',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.pages.create'));

        $response
            ->assertOk()
            ->assertSee('builder-image.jpg')
            ->assertSee('Builder image asset');
    }
}
