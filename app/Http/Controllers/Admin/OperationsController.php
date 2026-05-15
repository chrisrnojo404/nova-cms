<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BackupRestoreRequest;
use App\Models\ActivityLog;
use App\Models\BackupRun;
use App\Models\Setting;
use App\Support\BackupRestoreManager;
use App\Support\OperationsManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OperationsController extends Controller
{
    public function __construct(
        private readonly OperationsManager $operationsManager,
        private readonly BackupRestoreManager $backupRestoreManager
    )
    {
    }

    public function index(): View
    {
        return view('admin.operations.index', [
            'backupRuns' => BackupRun::query()->with('user')->latest()->limit(10)->get(),
            'queueConnection' => config('queue.default'),
            'pendingJobs' => DB::table('jobs')->count(),
            'failedJobs' => DB::table('failed_jobs')->count(),
            'settings' => [
                'backup_retention_days' => (int) Setting::valueFor('backup_retention_days', 14),
                'cache_strategy' => 'explicit-invalidation',
                'backup_disk' => 'local',
            ],
        ]);
    }

    public function queueBackup(): RedirectResponse
    {
        $run = $this->operationsManager->dispatchBackup(request()->user()?->id);

        ActivityLog::create([
            'user_id' => request()->user()?->id,
            'event' => 'operations.backup.queued',
            'subject_type' => BackupRun::class,
            'subject_id' => $run->id,
            'description' => 'Operations backup queued from admin.',
            'properties' => [
                'queue_connection' => $run->queue_connection,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.operations.index')
            ->with('status', "Backup run #{$run->id} queued successfully.");
    }

    public function refreshCaches(): RedirectResponse
    {
        $this->operationsManager->refreshCmsCaches();

        ActivityLog::create([
            'user_id' => request()->user()?->id,
            'event' => 'operations.cache.refreshed',
            'subject_type' => Setting::class,
            'subject_id' => null,
            'description' => 'CMS caches refreshed from operations center.',
            'properties' => [
                'strategy' => 'explicit-invalidation',
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.operations.index')
            ->with('status', 'CMS caches refreshed successfully.');
    }

    public function restoreBackup(BackupRestoreRequest $request): RedirectResponse
    {
        $result = $this->backupRestoreManager->restoreFromUpload(
            $request->file('backup_snapshot'),
            $request->user()?->id,
            $request->boolean('create_safety_backup', true),
        );

        $tableSummary = collect($result['tables'])
            ->map(fn (array $table): string => "{$table['table']} ({$table['rows']})")
            ->implode(', ');

        return redirect()
            ->route('admin.operations.index')
            ->with('status', 'Backup restored successfully: '.$tableSummary);
    }

    public function downloadBackup(BackupRun $backupRun)
    {
        abort_unless($backupRun->artifact_path, 404);

        return Storage::disk($backupRun->artifact_disk ?: 'local')->download($backupRun->artifact_path);
    }
}
