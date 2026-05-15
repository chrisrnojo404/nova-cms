<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\BackupRun;
use App\Support\BackupManifestBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Throwable;
use ZipArchive;

class GenerateBackupSnapshot implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $backupRunId)
    {
    }

    public function handle(BackupManifestBuilder $builder): void
    {
        $run = BackupRun::query()->findOrFail($this->backupRunId);

        $run->update([
            'status' => 'running',
            'started_at' => now(),
            'error_message' => null,
        ]);

        try {
            $manifest = $builder->build();
            $directory = 'backups/'.now()->format('Y/m');
            $basename = 'nova-backup-'.$run->id.'-'.now()->format('Ymd-His');
            $jsonPath = $directory.'/'.$basename.'.json';

            Storage::disk('local')->put($jsonPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $artifactPath = $jsonPath;
            $artifactSize = Storage::disk('local')->size($jsonPath);

            if (class_exists(ZipArchive::class)) {
                $artifactPath = $directory.'/'.$basename.'.zip';
                $zipFullPath = Storage::disk('local')->path($artifactPath);

                File::ensureDirectoryExists(dirname($zipFullPath));

                $zip = new ZipArchive();
                $zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                $zip->addFromString('snapshot.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

                $sqlitePath = database_path('database.sqlite');

                if (config('database.default') === 'sqlite' && file_exists($sqlitePath)) {
                    $zip->addFile($sqlitePath, 'database.sqlite');
                }

                $zip->close();

                Storage::disk('local')->delete($jsonPath);
                $artifactSize = Storage::disk('local')->size($artifactPath);
            }

            $run->update([
                'status' => 'completed',
                'artifact_path' => $artifactPath,
                'artifact_size' => $artifactSize,
                'summary' => [
                    'counts' => $manifest['counts'],
                    'generated_at' => $manifest['meta']['generated_at'],
                    'database_connection' => $manifest['meta']['database_connection'],
                ],
                'completed_at' => now(),
            ]);

            ActivityLog::create([
                'user_id' => $run->initiated_by,
                'event' => 'operations.backup.completed',
                'subject_type' => BackupRun::class,
                'subject_id' => $run->id,
                'description' => 'Operations backup completed.',
                'properties' => [
                    'artifact_path' => $artifactPath,
                    'artifact_size' => $artifactSize,
                ],
                'ip_address' => null,
                'user_agent' => 'queue-job',
            ]);
        } catch (Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);

            ActivityLog::create([
                'user_id' => $run->initiated_by,
                'event' => 'operations.backup.failed',
                'subject_type' => BackupRun::class,
                'subject_id' => $run->id,
                'description' => 'Operations backup failed.',
                'properties' => [
                    'error_message' => $exception->getMessage(),
                ],
                'ip_address' => null,
                'user_agent' => 'queue-job',
            ]);

            throw $exception;
        }
    }
}
