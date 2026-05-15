<?php

namespace App\Console\Commands;

use App\Models\BackupRun;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PruneNovaBackupsCommand extends Command
{
    protected $signature = 'nova:backups:prune';

    protected $description = 'Prune old Nova CMS backup artifacts and backup run records.';

    public function handle(): int
    {
        if (! Schema::hasTable('backup_runs')) {
            $this->info('No backup_runs table present yet. Nothing to prune.');

            return self::SUCCESS;
        }

        $retentionDays = (int) Setting::valueFor('backup_retention_days', 14);
        $cutoff = now()->subDays(max($retentionDays, 1));

        $staleRuns = BackupRun::query()
            ->where('completed_at', '<', $cutoff)
            ->whereNotNull('artifact_path')
            ->get();

        foreach ($staleRuns as $run) {
            Storage::disk($run->artifact_disk ?: 'local')->delete($run->artifact_path);
            $run->delete();
        }

        $this->info("Pruned {$staleRuns->count()} backup artifact(s) older than {$retentionDays} day(s).");

        return self::SUCCESS;
    }
}
