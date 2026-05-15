<?php

namespace Tests\Feature\Admin;

use App\Jobs\GenerateBackupSnapshot;
use App\Models\BackupRun;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OperationsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_operations_center(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.operations.index'));

        $response->assertOk()->assertSee('Production operations center');
    }

    public function test_admin_can_queue_backup_from_operations_center(): void
    {
        Queue::fake();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.operations.backups.queue'));

        $response->assertRedirect(route('admin.operations.index'));
        $this->assertDatabaseCount('backup_runs', 1);
        Queue::assertPushed(GenerateBackupSnapshot::class);
    }

    public function test_admin_can_refresh_cms_caches(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Cache::forever('cms.settings.site', ['site_name' => 'Cached']);
        Cache::forever('cms.menu.header', ['cached' => true]);
        Cache::forever('cms.settings.seo', ['cached' => true]);

        $response = $this->actingAs($admin)->post(route('admin.operations.caches.refresh'));

        $response->assertRedirect(route('admin.operations.index'));
        $this->assertNull(Cache::get('cms.settings.site'));
        $this->assertNull(Cache::get('cms.menu.header'));
        $this->assertNull(Cache::get('cms.settings.seo'));
    }

    public function test_sync_backup_command_creates_completed_backup_artifact(): void
    {
        Storage::fake('local');

        Artisan::call('nova:backup --sync');

        $run = BackupRun::query()->latest()->first();

        $this->assertNotNull($run);
        $this->assertSame('completed', $run->status);
        $this->assertNotNull($run->artifact_path);
        Storage::disk('local')->assertExists($run->artifact_path);
    }

    public function test_admin_can_restore_backup_snapshot_from_json_upload(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $page = Page::factory()->create([
            'author_id' => $admin->id,
            'title' => 'Original Page',
            'slug' => 'original-page',
            'status' => 'draft',
        ]);

        Setting::storeMany([
            ['group' => 'general', 'key' => 'site_name', 'value' => ['value' => 'Original Site'], 'is_public' => true, 'autoload' => true],
        ]);

        $timestamp = now()->toDateTimeString();

        $manifest = [
            'meta' => [
                'app_name' => 'Nova CMS',
                'generated_at' => now()->toIso8601String(),
                'database_connection' => 'sqlite',
            ],
            'tables' => [
                'settings' => [[
                    'id' => 1,
                    'group' => 'general',
                    'key' => 'site_name',
                    'value' => json_encode(['value' => 'Restored Site']),
                    'is_public' => 1,
                    'autoload' => 1,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                    'deleted_at' => null,
                ]],
                'pages' => [[
                    'id' => $page->id,
                    'author_id' => $admin->id,
                    'title' => 'Restored Page',
                    'slug' => 'original-page',
                    'content' => '<p>Restored content</p>',
                    'status' => 'published',
                    'template' => 'default',
                    'featured_image' => null,
                    'meta_title' => 'Restored Page',
                    'meta_description' => 'Restored description',
                    'blocks' => '[]',
                    'published_at' => $timestamp,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                    'deleted_at' => null,
                ]],
            ],
        ];

        $upload = UploadedFile::fake()->createWithContent(
            'restore-snapshot.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $response = $this->actingAs($admin)->post(route('admin.operations.backups.restore'), [
            'backup_snapshot' => $upload,
            'confirmation' => '1',
            'create_safety_backup' => '1',
        ]);

        $response->assertRedirect(route('admin.operations.index'));
        $this->assertSame('Restored Site', Setting::valueFor('site_name'));
        $this->assertSame('Restored Page', Page::query()->findOrFail($page->id)->title);
        $this->assertDatabaseCount('backup_runs', 1);
        Storage::disk('local')->assertExists(BackupRun::query()->firstOrFail()->artifact_path);
    }
}
