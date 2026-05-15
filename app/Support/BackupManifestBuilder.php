<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupManifestBuilder
{
    public function build(): array
    {
        $tables = [
            'permissions',
            'roles',
            'role_has_permissions',
            'users',
            'model_has_permissions',
            'model_has_roles',
            'settings',
            'themes',
            'plugins',
            'pages',
            'categories',
            'posts',
            'media',
            'menus',
            'menu_items',
            'activity_logs',
            'content_revisions',
            'block_templates',
        ];

        $snapshot = [];
        $counts = [];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $rows = DB::table($table)->orderBy('id')->get()->map(static fn ($row): array => (array) $row)->all();

            $snapshot[$table] = $rows;
            $counts[$table] = count($rows);
        }

        return [
            'meta' => [
                'app_name' => config('app.name', 'Nova CMS'),
                'generated_at' => now()->toIso8601String(),
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'database_connection' => config('database.default'),
                'active_theme' => Setting::valueFor('active_theme', 'default'),
            ],
            'counts' => $counts,
            'tables' => $snapshot,
        ];
    }
}
