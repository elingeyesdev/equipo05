<?php

namespace App\Console\Commands;

use App\Support\RescateMedia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class EnsureRescateMedia extends Command
{
    protected $signature = 'rescate:ensure-media
                            {--sync-db : Sincroniza también rutas imagen_url en la BD rescate}
                            {--force : Re-descargar imágenes aunque ya existan}';

    protected $description = 'Prepara storage:link, catálogo público de fauna y (opcional) sincroniza imágenes en BD';

    public function handle(): int
    {
        $this->callSilent('rescate:ensure-schema');

        $this->components->info('Enlace storage/app/public → public/storage');
        $this->callSilent('storage:link');

        $force = (bool) $this->option('force');
        $catalog = RescateMedia::ensureCatalogImages($force);
        $this->components->info("Catálogo público: {$catalog} especies verificadas en public/images/rescate/");

        if ($this->option('sync-db') && Schema::connection('rescate')->hasTable('animal_files')) {
            $this->components->info('Sincronizando imagen_url en hallazgos, hojas de vida y registros…');
            $code = $this->call('rescate:sync-animal-images', $force ? ['--force' => true] : []);

            return $code === self::SUCCESS ? self::SUCCESS : self::FAILURE;
        }

        if (! $this->option('sync-db')) {
            $this->components->warn('Tip: tras migrar/seed ejecuta --sync-db para alinear rutas en PostgreSQL.');
        }

        return self::SUCCESS;
    }
}
