<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily security summary to SECURITY_ALERT_EMAIL (requires the scheduler:
// a cron / Task Scheduler entry running `php artisan schedule:run` every minute).
Schedule::command('security:report --hours=24 --mail')->dailyAt('07:00');
