<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixBladeEncoding extends Command
{
    protected $signature = 'fix:blade-encoding {--dry-run : Solo mostrar cambios sin guardar}';

    protected $description = 'Corrige textos con caracteres corruptos () en vistas Blade';

    public function handle(): int
    {
        $paths = [
            base_path('modulos/donacion-recepcion-inventario-main/resources/views'),
            resource_path('views'),
        ];

        $regex = [
            '/Gesti\x{FFFD}n/u'                    => 'Gestión',
            '/Tel\x{FFFD}fono/u'                   => 'Teléfono',
            '/p\x{FFFD}gina/u'                     => 'página',
            '/p\x{FFFD}ginas/u'                    => 'páginas',
            '/\x{FFFD}Est\x{FFFD}\s+seguro/u'      => '¿Está seguro',
            '/\x{FFFD}ltimo/u'                     => 'Último',
            '/\x{FFFD}ltima/u'                     => 'Última',
            '/Recolecci\x{FFFD}n/u'                => 'Recolección',
            '/recolecci\x{FFFD}n/u'                => 'recolección',
            '/Configuraci\x{FFFD}n/u'              => 'Configuración',
            '/Informaci\x{FFFD}n/u'                => 'Información',
            '/ubicaci\x{FFFD}n/u'                  => 'ubicación',
            '/descripci\x{FFFD}n/u'                => 'descripción',
            '/Campaa/u'                            => 'Campaña',
            '/campaa/u'                            => 'campaña',
            '/Gesti\xc3\xb3n/u'                    => 'Gestión',
            '/Tel\xc3\xa9fono/u'                   => 'Teléfono',
        ];

        $literal = [
            'Recoleccin' => 'Recolección',
            'recoleccin' => 'recolección',
            'Telefono:' => 'Teléfono:',
            'Telefono' => 'Teléfono',
            'Gestion' => 'Gestión',
        ];

        $fixed = 0;

        foreach ($paths as $root) {
            if (! is_dir($root)) {
                continue;
            }

            foreach (File::allFiles($root) as $file) {
                if (! str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }

                $path = $file->getPathname();
                $content = file_get_contents($path);
                $original = $content;

                foreach ($literal as $from => $to) {
                    $content = str_replace($from, $to, $content);
                }

                foreach ($regex as $pattern => $replacement) {
                    $content = preg_replace($pattern, $replacement, $content);
                }

                if ($content !== $original) {
                    $fixed++;
                    $relative = str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
                    $this->line("✔ {$relative}");

                    if (! $this->option('dry-run')) {
                        file_put_contents($path, $content);
                    }
                }
            }
        }

        $this->info($this->option('dry-run')
            ? "Archivos a corregir: {$fixed}"
            : "Archivos corregidos: {$fixed}");

        return self::SUCCESS;
    }
}
