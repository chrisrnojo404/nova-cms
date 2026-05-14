<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        abort_unless($request->user()?->can('access admin'), 403);

        $query = User::query()->with('roles')->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->string('search'));

            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->string('role')->toString());
        }

        return UserResource::collection(
            $query->paginate($this->perPage($request))->withQueryString()
        );
    }

    public function show(Request $request, User $user): UserResource
    {
        abort_unless($request->user()?->can('access admin'), 403);

        return new UserResource($user->load('roles'));
    }

    private function perPage(Request $request): int
    {
        return min(max($request->integer('per_page', 20), 1), 50);
    }
}
