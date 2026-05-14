<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class OpenApiController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'openapi' => '3.1.0',
            'info' => [
                'title' => config('app.name', 'Nova CMS').' API',
                'version' => 'v1',
                'description' => 'Versioned REST API for Nova CMS public content delivery and authenticated CMS management.',
            ],
            'servers' => [
                [
                    'url' => url('/api/v1'),
                    'description' => 'Local application server',
                ],
            ],
            'tags' => [
                ['name' => 'Meta'],
                ['name' => 'Authentication'],
                ['name' => 'Pages'],
                ['name' => 'Posts'],
                ['name' => 'Categories'],
                ['name' => 'Menus'],
                ['name' => 'Settings'],
                ['name' => 'Media'],
                ['name' => 'Users'],
            ],
            'components' => [
                'securitySchemes' => [
                    'sanctumBearer' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'Token',
                        'description' => 'Laravel Sanctum personal access token.',
                    ],
                ],
                'schemas' => [
                    'Page' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'content' => ['type' => ['string', 'null']],
                            'status' => ['type' => 'string'],
                            'template' => ['type' => ['string', 'null']],
                            'featured_image' => ['type' => ['string', 'null']],
                            'meta_title' => ['type' => ['string', 'null']],
                            'meta_description' => ['type' => ['string', 'null']],
                            'published_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
                        ],
                    ],
                    'Post' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'excerpt' => ['type' => ['string', 'null']],
                            'content' => ['type' => ['string', 'null']],
                            'status' => ['type' => 'string'],
                            'featured_image' => ['type' => ['string', 'null']],
                            'meta_title' => ['type' => ['string', 'null']],
                            'meta_description' => ['type' => ['string', 'null']],
                            'published_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
                        ],
                    ],
                    'Category' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'description' => ['type' => ['string', 'null']],
                            'meta_title' => ['type' => ['string', 'null']],
                            'meta_description' => ['type' => ['string', 'null']],
                        ],
                    ],
                    'Menu' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'location' => ['type' => ['string', 'null']],
                            'description' => ['type' => ['string', 'null']],
                            'is_active' => ['type' => 'boolean'],
                        ],
                    ],
                    'User' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'email' => ['type' => 'string', 'format' => 'email'],
                        ],
                    ],
                ],
            ],
            'paths' => [
                '/meta' => [
                    'get' => [
                        'tags' => ['Meta'],
                        'summary' => 'Discover API capabilities',
                        'responses' => [
                            '200' => [
                                'description' => 'API metadata returned.',
                            ],
                        ],
                    ],
                ],
                '/openapi.json' => [
                    'get' => [
                        'tags' => ['Meta'],
                        'summary' => 'Retrieve lightweight OpenAPI contract',
                        'responses' => [
                            '200' => [
                                'description' => 'OpenAPI document returned.',
                            ],
                        ],
                    ],
                ],
                '/auth/login' => [
                    'post' => [
                        'tags' => ['Authentication'],
                        'summary' => 'Create a Sanctum token',
                        'responses' => [
                            '200' => ['description' => 'Authenticated successfully.'],
                            '422' => ['description' => 'Validation or credential failure.'],
                        ],
                    ],
                ],
                '/settings/public' => [
                    'get' => [
                        'tags' => ['Settings'],
                        'summary' => 'Return public CMS settings',
                        'responses' => [
                            '200' => ['description' => 'Public settings returned.'],
                        ],
                    ],
                ],
                '/pages' => [
                    'get' => [
                        'tags' => ['Pages'],
                        'summary' => 'List pages',
                        'responses' => [
                            '200' => ['description' => 'Page collection returned.'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['Pages'],
                        'summary' => 'Create a page',
                        'security' => [['sanctumBearer' => []]],
                        'responses' => [
                            '201' => ['description' => 'Page created.'],
                            '403' => ['description' => 'Missing permission.'],
                        ],
                    ],
                ],
                '/posts' => [
                    'get' => [
                        'tags' => ['Posts'],
                        'summary' => 'List posts',
                        'responses' => [
                            '200' => ['description' => 'Post collection returned.'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['Posts'],
                        'summary' => 'Create a post',
                        'security' => [['sanctumBearer' => []]],
                        'responses' => [
                            '201' => ['description' => 'Post created.'],
                            '403' => ['description' => 'Missing permission.'],
                        ],
                    ],
                ],
                '/categories' => [
                    'get' => [
                        'tags' => ['Categories'],
                        'summary' => 'List categories',
                        'responses' => [
                            '200' => ['description' => 'Category collection returned.'],
                        ],
                    ],
                ],
                '/menus/location/{location}' => [
                    'get' => [
                        'tags' => ['Menus'],
                        'summary' => 'Fetch active menu by location',
                        'parameters' => [
                            [
                                'name' => 'location',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'string'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Menu returned.'],
                            '404' => ['description' => 'Menu not found.'],
                        ],
                    ],
                ],
                '/media' => [
                    'get' => [
                        'tags' => ['Media'],
                        'summary' => 'List media items',
                        'security' => [['sanctumBearer' => []]],
                        'responses' => [
                            '200' => ['description' => 'Media collection returned.'],
                            '403' => ['description' => 'Missing permission.'],
                        ],
                    ],
                ],
                '/users' => [
                    'get' => [
                        'tags' => ['Users'],
                        'summary' => 'List users',
                        'security' => [['sanctumBearer' => []]],
                        'responses' => [
                            '200' => ['description' => 'User collection returned.'],
                            '403' => ['description' => 'Missing permission.'],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
