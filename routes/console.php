<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 👉 Nuestro scheduler:
Schedule::command('externo:sync-datos')->everyMinute();
// o si quieres menos frecuencia:
// Schedule::command('externo:sync-datos')->everyFiveMinutes();
// Schedule::command('externo:sync-datos')->everyTenMinutes();
