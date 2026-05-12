<?php

use App\Http\Controllers\Api\V1\Auth\AuthenticatedUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthenticatedUserController::class, 'store'])
        ->middleware('throttle:api');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthenticatedUserController::class, 'show']);
        Route::delete('/auth/logout', [AuthenticatedUserController::class, 'destroy']);

        Route::get('/health', function (Request $request) {
            return response()->json([
                'status' => 'ok',
                'authenticated_as' => $request->user()->email,
            ]);
        });
    });
});
