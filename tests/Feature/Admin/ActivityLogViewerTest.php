<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogViewerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_activity_logs(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        ActivityLog::create([
            'user_id' => $admin->id,
            'event' => 'page.updated',
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'description' => 'A log entry for testing.',
            'properties' => ['context' => 'testing'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'phpunit',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.logs.index'));

        $response->assertOk()->assertSee('Activity logs')->assertSee('A log entry for testing.');
    }

    public function test_admin_can_filter_activity_logs(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        ActivityLog::create([
            'user_id' => $admin->id,
            'event' => 'page.updated',
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'description' => 'Updated page.',
            'properties' => ['context' => 'page'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'phpunit',
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'event' => 'plugin.activated',
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'description' => 'Activated plugin.',
            'properties' => ['context' => 'plugin'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'phpunit',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.logs.index', ['event' => 'plugin.activated']));

        $response->assertOk()->assertSee('Activated plugin.')->assertDontSee('Updated page.');
    }
}
