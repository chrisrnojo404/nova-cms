<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MetaController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => [
                'name' => config('app.name', 'Nova CMS').' API',
                'version' => 'v1',
                'authentication' => [
                    'scheme' => 'Bearer',
                    'driver' => 'Laravel Sanctum',
                    'login_endpoint' => url('/api/v1/auth/login'),
                    'profile_endpoint' => url('/api/v1/auth/me'),
                ],
                'documentation' => [
                    'repository_path' => 'docs/API.md',
                    'openapi_endpoint' => url('/api/v1/openapi.json'),
                ],
                'capabilities' => [
                    'public' => [
                        'pages.read',
                        'posts.read',
                        'categories.read',
                        'menus.read',
                        'settings.public',
                        'meta.discover',
                    ],
                    'authenticated' => [
                        'auth.profile',
                        'users.read',
                        'pages.write',
                        'posts.write',
                        'categories.write',
                        'media.manage',
                        'menus.write',
                    ],
                ],
                'filters' => [
                    'pages' => ['search', 'status', 'per_page'],
                    'posts' => ['search', 'status', 'category', 'author', 'per_page'],
                    'categories' => ['search', 'per_page'],
                    'menus' => ['location', 'per_page'],
                    'media' => ['search', 'directory', 'mime_type', 'per_page'],
                    'users' => ['search', 'role', 'per_page'],
                ],
                'endpoints' => [
                    'public' => [
                        'GET /api/v1/meta',
                        'GET /api/v1/openapi.json',
                        'GET /api/v1/settings/public',
                        'GET /api/v1/pages',
                        'GET /api/v1/pages/{slug}',
                        'GET /api/v1/posts',
                        'GET /api/v1/posts/{slug}',
                        'GET /api/v1/categories',
                        'GET /api/v1/categories/{slug}',
                        'GET /api/v1/menus',
                        'GET /api/v1/menus/{slug}',
                        'GET /api/v1/menus/location/{location}',
                    ],
                    'authenticated' => [
                        'POST /api/v1/auth/login',
                        'GET /api/v1/auth/me',
                        'DELETE /api/v1/auth/logout',
                        'GET /api/v1/users',
                        'GET /api/v1/users/{user}',
                        'POST|PUT|DELETE /api/v1/pages',
                        'POST|PUT|DELETE /api/v1/posts',
                        'POST|PUT|DELETE /api/v1/categories',
                        'GET|POST|DELETE /api/v1/media',
                        'POST|PUT|DELETE /api/v1/menus',
                        'POST|PUT|DELETE /api/v1/menus/{menu}/items',
                    ],
                ],
            ],
        ]);
    }
}
