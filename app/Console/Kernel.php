<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // orden correcto por dependencias
        $schedule->command('sync:campanias')->hourly();
        $schedule->command('sync:donaciones-dinero')->hourly();

        $schedule->command('sync:categorias-productos')->dailyAt('02:00');
        $schedule->command('sync:almacenes')->dailyAt('02:15');

        $schedule->command('sync:donaciones-especie')->everyThirtyMinutes();

        // gateway (si lo quieres “casi en tiempo real”)
        $schedule->command('sync:gateway-paquetes')->everyTenMinutes();
        $schedule->command('sync:gateway-vehiculos')->everyTenMinutes();
        $schedule->command('sync:gateway-especies')->hourly();
        $schedule->command('sync:gateway-animales-liberados')->everyThirtyMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
