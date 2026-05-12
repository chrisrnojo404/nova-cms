<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'access admin',
            'manage pages',
            'manage posts',
            'manage categories',
            'manage media',
            'manage menus',
            'manage themes',
            'manage plugins',
            'manage users',
            'manage settings',
            'manage seo',
            'view logs',
            'use api',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roles = [
            'super-admin' => $permissions,
            'admin' => $permissions,
            'editor' => ['access admin', 'manage pages', 'manage posts', 'manage categories', 'manage media', 'manage menus', 'manage seo', 'use api'],
            'author' => ['access admin', 'manage posts', 'manage media', 'use api'],
        ];

        foreach ($roles as $roleName => $grantedPermissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions(
                Permission::query()
                    ->where('guard_name', 'web')
                    ->whereIn('name', $grantedPermissions)
                    ->get()
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
