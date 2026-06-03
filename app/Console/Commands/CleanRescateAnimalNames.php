<?php

namespace App\Console\Commands;

use App\Support\RescateAnimalNameCleaner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CleanRescateAnimalNames extends Command
{
    protected $signature = 'rescate:clean-animal-names';

    protected $description = 'Elimina el sufijo "demo" y números de los nombres de animales del módulo rescate';

    public function handle(): int
    {
        if (! Schema::connection('rescate')->hasTable('animals')) {
            $this->warn('No existe la tabla rescate.animals.');

            return self::FAILURE;
        }

        $count = RescateAnimalNameCleaner::cleanAll();
        $this->info("Nombres de animales actualizados: {$count}");

        return self::SUCCESS;
    }
}
