<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('roles')
            ->when($request->string('search')->toString(), function ($query, string $search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->string('role')->toString(), function ($query, string $role): void {
                $query->role($role);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
            'roles' => $this->availableRoles(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'managedUser' => new User(),
            'roles' => $this->availableRoles(),
        ]);
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
            'email_verified_at' => $request->boolean('email_verified') ? now() : null,
        ]);

        $user->syncRoles([$request->string('role')->toString()]);

        $this->logUserAdminAction($request, $user, 'user.managed.created', 'Admin user account created.');

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $user->load('roles');

        return view('admin.users.edit', [
            'managedUser' => $user,
            'roles' => $this->availableRoles(),
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $payload = [
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
        ];

        if ($request->filled('password')) {
            $payload['password'] = $request->string('password')->toString();
        }

        if ($user->email !== $payload['email']) {
            $payload['email_verified_at'] = $request->boolean('email_verified') ? now() : null;
        } elseif ($request->boolean('email_verified') && ! $user->email_verified_at) {
            $payload['email_verified_at'] = now();
        } elseif (! $request->boolean('email_verified')) {
            $payload['email_verified_at'] = null;
        }

        $user->update($payload);
        $user->syncRoles([$request->string('role')->toString()]);

        $this->logUserAdminAction($request, $user, 'user.managed.updated', 'Admin user account updated.');

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()?->is($user), 422, 'You cannot delete your own account from user management.');

        $roleNames = $user->getRoleNames()->all();
        $userName = $user->name;
        $userEmail = $user->email;
        $user->delete();

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'event' => 'user.managed.deleted',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Admin user account deleted.',
            'properties' => [
                'name' => $userName,
                'email' => $userEmail,
                'roles' => $roleNames,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User deleted successfully.');
    }

    private function availableRoles(): array
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->orderByRaw("case name when 'super-admin' then 1 when 'admin' then 2 when 'editor' then 3 when 'author' then 4 else 5 end")
            ->pluck('name')
            ->all();
    }

    private function logUserAdminAction(Request $request, User $user, string $event, string $description): void
    {
        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'event' => $event,
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => $description,
            'properties' => [
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->all(),
                'verified' => (bool) $user->email_verified_at,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
