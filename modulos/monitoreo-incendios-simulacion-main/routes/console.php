<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule FIRMS data update every 5 minutes
Schedule::command('firms:update')->everyFiveMinutes()->withoutOverlapping();

// Alternative schedules (uncomment as needed):
// Schedule::command('firms:update')->everyMinute(); // For testing
// Schedule::command('firms:update')->everyTenMinutes();
// Schedule::command('firms:update')->hourly();
