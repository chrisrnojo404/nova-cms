<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\MetaController;
use App\Http\Controllers\Api\V1\OpenApiController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\MenuItemController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthenticatedUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/meta', MetaController::class);
    Route::get('/openapi.json', OpenApiController::class);
    Route::post('/auth/login', [AuthenticatedUserController::class, 'store'])
        ->middleware('throttle:api');

    Route::get('/pages', [PageController::class, 'index']);
    Route::get('/pages/{slug}', [PageController::class, 'show']);

    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{slug}', [PostController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);

    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/location/{location}', [MenuController::class, 'byLocation']);
    Route::get('/menus/{slug}', [MenuController::class, 'show']);
    Route::get('/settings/public', [SettingController::class, 'public']);

    Route::middleware(['auth:sanctum', 'permission:use api'])->group(function () {
        Route::get('/auth/me', [AuthenticatedUserController::class, 'show']);
        Route::delete('/auth/logout', [AuthenticatedUserController::class, 'destroy']);

        Route::get('/health', function (Request $request) {
            return response()->json([
                'status' => 'ok',
                'authenticated_as' => $request->user()->email,
            ]);
        });

        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);

        Route::post('/pages', [PageController::class, 'store']);
        Route::put('/pages/{page}', [PageController::class, 'update']);
        Route::delete('/pages/{page}', [PageController::class, 'destroy']);

        Route::post('/posts', [PostController::class, 'store']);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);

        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        Route::get('/media', [MediaController::class, 'index']);
        Route::post('/media', [MediaController::class, 'store']);
        Route::delete('/media/{media}', [MediaController::class, 'destroy']);

        Route::post('/menus', [MenuController::class, 'store']);
        Route::put('/menus/{menu}', [MenuController::class, 'update']);
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy']);

        Route::post('/menus/{menu}/items', [MenuItemController::class, 'store']);
        Route::put('/menus/{menu}/items/{item}', [MenuItemController::class, 'update']);
        Route::delete('/menus/{menu}/items/{item}', [MenuItemController::class, 'destroy']);
    });
});
