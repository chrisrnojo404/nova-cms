<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_meta_endpoint_exposes_api_discovery_information(): void
    {
        $response = $this->getJson('/api/v1/meta');

        $response
            ->assertOk()
            ->assertJsonPath('data.version', 'v1')
            ->assertJsonPath('data.authentication.driver', 'Laravel Sanctum')
            ->assertJsonPath('data.documentation.repository_path', 'docs/API.md')
            ->assertJsonPath('data.documentation.openapi_endpoint', url('/api/v1/openapi.json'))
            ->assertJsonPath('data.filters.posts.0', 'search');
    }

    public function test_openapi_endpoint_exposes_machine_readable_contract(): void
    {
        $response = $this->getJson('/api/v1/openapi.json');

        $response
            ->assertOk()
            ->assertJsonPath('openapi', '3.1.0')
            ->assertJsonPath('info.version', 'v1')
            ->assertJsonPath('components.securitySchemes.sanctumBearer.scheme', 'bearer')
            ->assertJsonPath('paths./settings/public.get.tags.0', 'Settings');
    }
}
