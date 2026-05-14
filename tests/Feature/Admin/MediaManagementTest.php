<?php

namespace Tests\Feature\Admin;

use App\Models\Media;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_media_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.media.index'));

        $response->assertOk()->assertSee('Media library');
    }

    public function test_admin_can_upload_multiple_media_files(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.media.store'), [
            'directory' => 'media/blog',
            'files' => [
                UploadedFile::fake()->image('hero.jpg'),
                UploadedFile::fake()->create('launch-guide.pdf', 240, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect(route('admin.media.index'));
        $this->assertDatabaseCount('media', 2);

        $storedPaths = Media::query()->pluck('path')->all();

        foreach ($storedPaths as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_media_index_can_be_filtered_by_search(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Media::factory()->create([
            'original_name' => 'Brand Kit.pdf',
            'directory' => 'media/brand',
            'path' => 'media/brand/brand-kit.pdf',
        ]);

        Media::factory()->create([
            'original_name' => 'Team Photo.jpg',
            'directory' => 'media/photos',
            'path' => 'media/photos/team-photo.jpg',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.media.index', ['search' => 'Brand']));

        $response->assertOk()
            ->assertSee('Brand Kit.pdf')
            ->assertDontSee('Team Photo.jpg');
    }

    public function test_admin_can_delete_media_item(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Storage::disk('public')->put('media/uploads/sample.jpg', 'file-content');

        $media = Media::factory()->create([
            'user_id' => $admin->id,
            'path' => 'media/uploads/sample.jpg',
            'url' => '/storage/media/uploads/sample.jpg',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.media.destroy', $media));

        $response->assertRedirect(route('admin.media.index'));
        $this->assertSoftDeleted('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing('media/uploads/sample.jpg');
    }
}
