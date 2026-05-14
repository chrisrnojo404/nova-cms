<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_can_log_in_over_api_and_receive_token(): void
    {
        $user = User::factory()->create([
            'email' => 'api-user@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('editor');

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'api-user@example.com',
            'password' => 'password',
            'device_name' => 'phpunit',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'token_type',
                'user' => ['id', 'name', 'email'],
            ]);
    }

    public function test_authenticated_user_can_fetch_profile_from_api(): void
    {
        $user = User::factory()->create();
        $user->assignRole('editor');

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me');

        $response
            ->assertOk()
            ->assertJsonPath('user.email', $user->email);
    }

    public function test_admin_can_filter_users_by_role_over_api(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $editor = User::factory()->create([
            'email' => 'editor@example.com',
        ]);
        $editor->assignRole('editor');

        $author = User::factory()->create([
            'email' => 'author@example.com',
        ]);
        $author->assignRole('author');

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/users?role=editor&search=editor');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.email', 'editor@example.com');
    }
}
