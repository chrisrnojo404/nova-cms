<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\BackupRun;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;
use ZipArchive;

class BackupRestoreManager
{
    public function __construct(
        private readonly OperationsManager $operationsManager,
        private readonly CmsCache $cache
    )
    {
    }

    public function restoreFromUpload(UploadedFile $file, ?int $userId = null, bool $createSafetyBackup = true): array
    {
        if ($createSafetyBackup) {
            $this->operationsManager->dispatchBackup($userId, true);
        }

        $manifest = $this->extractManifest($file);
        $tables = Arr::get($manifest, 'tables', []);

        if (! is_array($tables) || $tables === []) {
            throw new RuntimeException('Backup snapshot does not contain any restorable tables.');
        }

        $restoredTables = [];

        DB::transaction(function () use ($tables, &$restoredTables): void {
            Schema::disableForeignKeyConstraints();

            try {
                foreach ($this->restoreOrder($tables) as $table) {
                    if (! Schema::hasTable($table)) {
                        continue;
                    }

                    $rows = $tables[$table] ?? [];

                    if (! is_array($rows)) {
                        continue;
                    }

                    DB::table($table)->delete();

                    foreach (array_chunk($rows, 100) as $chunk) {
                        if ($chunk !== []) {
                            DB::table($table)->insert($chunk);
                        }
                    }

                    $restoredTables[] = [
                        'table' => $table,
                        'rows' => count($rows),
                    ];
                }
            } finally {
                Schema::enableForeignKeyConstraints();
            }
        });

        $this->cache->flushAll();

        ActivityLog::create([
            'user_id' => $userId,
            'event' => 'operations.backup.restored',
            'subject_type' => BackupRun::class,
            'subject_id' => null,
            'description' => 'Backup snapshot restored from operations center.',
            'properties' => [
                'tables' => $restoredTables,
                'generated_at' => Arr::get($manifest, 'meta.generated_at'),
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return [
            'meta' => Arr::get($manifest, 'meta', []),
            'tables' => $restoredTables,
            'safety_backup_created' => $createSafetyBackup,
        ];
    }

    private function extractManifest(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'json') {
            $payload = File::get($file->getRealPath());

            return $this->decodeManifest($payload);
        }

        if ($extension === 'zip') {
            return $this->extractManifestFromZip($file->getRealPath());
        }

        throw new RuntimeException('Unsupported backup snapshot format.');
    }

    private function extractManifestFromZip(string $path): array
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException('Unable to open backup archive.');
        }

        try {
            $payload = $zip->getFromName('snapshot.json');

            if ($payload === false) {
                throw new RuntimeException('Backup archive does not contain snapshot.json.');
            }

            return $this->decodeManifest($payload);
        } finally {
            $zip->close();
        }
    }

    private function decodeManifest(string $payload): array
    {
        try {
            $manifest = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            throw new RuntimeException('Backup snapshot JSON is invalid.');
        }

        if (! is_array($manifest) || ! isset($manifest['tables'])) {
            throw new RuntimeException('Backup snapshot is missing the expected manifest structure.');
        }

        return $manifest;
    }

    private function restoreOrder(array $tables): array
    {
        $preferredOrder = [
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
            'content_revisions',
            'block_templates',
            'activity_logs',
        ];

        $available = array_keys($tables);
        $ordered = array_values(array_intersect($preferredOrder, $available));

        foreach ($available as $table) {
            if (! in_array($table, $ordered, true)) {
                $ordered[] = $table;
            }
        }

        return $ordered;
    }
}
