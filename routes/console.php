<?php

declare(strict_types=1);

use App\Jobs\SendDailyReportJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes / Scheduled Tasks
|--------------------------------------------------------------------------
|
| This file defines the scheduled task timeline for the application.
| Commands are run by the OS scheduler: php artisan schedule:run (every minute)
| or schedule:work (for local development without cron).
|
*/

// ── Daily summary report — dispatched at 08:00 every morning ─────────────────
Schedule::job(new SendDailyReportJob())
    ->dailyAt('08:00')
    ->timezone('America/Sao_Paulo')
    ->name('daily-report')
    ->withoutOverlapping()
    ->onFailure(function (): void {
        \Illuminate\Support\Facades\Log::error('Scheduled daily report job failed to dispatch.');
    });

// ── Manual dispatch command (for testing without cron) ────────────────────────
Artisan::command('report:daily', function (): void {
    $this->info('Dispatching daily report job...');
    SendDailyReportJob::dispatch();
    $this->info('Done. Check the queue worker output.');
})->purpose('Manually dispatch the daily Telegram report');

// ── Kept for fun ──────────────────────────────────────────────────────────────
Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
