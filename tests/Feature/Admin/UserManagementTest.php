<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_users_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk()->assertSee('Manage users');
    }

    public function test_admin_can_create_user_with_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Editorial User',
            'email' => 'editorial@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'editor',
            'email_verified' => '1',
        ]);

        $user = User::query()->where('email', 'editorial@example.com')->firstOrFail();

        $response->assertRedirect(route('admin.users.edit', $user));
        $this->assertTrue($user->hasRole('editor'));
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $managedUser = User::factory()->create();
        $managedUser->assignRole('author');

        $response = $this->actingAs($admin)->put(route('admin.users.update', $managedUser), [
            'name' => 'Updated User',
            'email' => $managedUser->email,
            'password' => '',
            'password_confirmation' => '',
            'role' => 'editor',
            'email_verified' => '1',
        ]);

        $response->assertRedirect(route('admin.users.edit', $managedUser));
        $this->assertTrue($managedUser->fresh()->hasRole('editor'));
        $this->assertSame('Updated User', $managedUser->fresh()->name);
    }

    public function test_admin_cannot_delete_own_account_from_user_management(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertStatus(422);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
