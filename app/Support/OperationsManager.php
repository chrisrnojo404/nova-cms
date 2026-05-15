<?php

namespace App\Support;

use App\Jobs\GenerateBackupSnapshot;
use App\Models\BackupRun;

class OperationsManager
{
    public function __construct(private readonly CmsCache $cache)
    {
    }

    public function dispatchBackup(?int $userId = null, bool $sync = false): BackupRun
    {
        $run = BackupRun::create([
            'initiated_by' => $userId,
            'status' => $sync ? 'running' : 'queued',
            'queue_connection' => config('queue.default'),
            'artifact_disk' => 'local',
            'summary' => [
                'mode' => $sync ? 'sync' : 'queued',
            ],
        ]);

        if ($sync) {
            GenerateBackupSnapshot::dispatchSync($run->id);
        } else {
            GenerateBackupSnapshot::dispatch($run->id);
        }

        return $run->fresh();
    }

    public function refreshCmsCaches(): void
    {
        $this->cache->flushAll();
    }
}
