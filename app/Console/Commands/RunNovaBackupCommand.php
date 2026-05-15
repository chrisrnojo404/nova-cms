<?php

namespace App\Console\Commands;

use App\Support\OperationsManager;
use Illuminate\Console\Command;

class RunNovaBackupCommand extends Command
{
    protected $signature = 'nova:backup {--sync : Run the backup in-process instead of queueing it}';

    protected $description = 'Queue or run a Nova CMS operational backup snapshot.';

    public function handle(OperationsManager $operationsManager): int
    {
        $run = $operationsManager->dispatchBackup(null, (bool) $this->option('sync'));

        $this->info(
            $this->option('sync')
                ? "Backup run #{$run->id} completed."
                : "Backup run #{$run->id} queued on [".config('queue.default').'].'
        );

        return self::SUCCESS;
    }
}
