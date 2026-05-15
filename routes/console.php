<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

app()->booted(function (): void {
    $schedule = app(Schedule::class);

    $schedule->command('nova:backup')->dailyAt('02:00')->withoutOverlapping();
    $schedule->command('nova:backups:prune')->dailyAt('02:30')->withoutOverlapping();
    $schedule->command('queue:prune-failed --hours=168')->dailyAt('03:00')->withoutOverlapping();
});
