<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;

class SystemAudit extends Command
{
    protected $signature = 'qa:audit {--json : Salida JSON}';

    protected $description = 'Auditoría estática: rutas, controladores, vistas y codificación UTF-8';

    /** @var array<int, array<string, string>> */
    protected array $issues = [];

    /** @var array<int, array<string, string>> */
    protected array $passed = [];

    public function handle(): int
    {
        $this->auditRoutes();
        $this->auditBladeEncoding();
        $this->auditEncodingCorruptionPatterns();
        $this->auditStorageLink();
        $this->auditDatabaseConnectivity();

        if ($this->option('json')) {
            $this->line(json_encode([
                'issues' => $this->issues,
                'passed' => $this->passed,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return count($this->issues) > 0 ? self::FAILURE : self::SUCCESS;
        }

        $this->newLine();
        $this->info('=== Resumen auditoría ===');
        $this->info('Comprobaciones OK: '.count($this->passed));
        $this->warn('Problemas encontrados: '.count($this->issues));

        foreach ($this->issues as $issue) {
            $this->line("[{$issue['severity']}] {$issue['area']}: {$issue['message']}");
        }

        return count($this->issues) > 0 ? self::FAILURE : self::SUCCESS;
    }

    protected function auditRoutes(): void
    {
        $broken = 0;
        $checked = 0;

        foreach (Route::getRoutes() as $route) {
            $action = $route->getAction('controller');
            if (! is_string($action) || ! str_contains($action, '@')) {
                continue;
            }

            $checked++;
            [$class, $method] = explode('@', $action);

            if (! class_exists($class)) {
                $broken++;
                $this->addIssue('critical', 'routes', "Controlador inexistente: {$class} ({$route->uri()})");
                continue;
            }

            if (! method_exists($class, $method)) {
                $broken++;
                $this->addIssue('critical', 'routes', "Método inexistente: {$class}@{$method} ({$route->uri()})");
            }
        }

        if ($broken === 0) {
            $this->addPass('routes', "{$checked} rutas con controlador válido");
        }
    }

    protected function auditBladeEncoding(): void
    {
        $paths = [
            resource_path('views'),
            base_path('modulos'),
        ];

        $bad = 0;

        foreach ($paths as $root) {
            if (! is_dir($root)) {
                continue;
            }

            foreach (File::allFiles($root) as $file) {
                if (! str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }

                $content = file_get_contents($file->getPathname());

                if (str_contains($content, "\xEF\xBF\xBD") || preg_match('/Gesti\x{FFFD}/u', $content)) {
                    $bad++;
                    $rel = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $this->addIssue('high', 'encoding', "Caracteres corruptos en: {$rel}");
                }
            }
        }

        if ($bad === 0) {
            $this->addPass('encoding', 'Vistas Blade sin caracteres de reemplazo UTF-8');
        }
    }

    protected function auditEncodingCorruptionPatterns(): void
    {
        $patterns = [
            'u'."\xC3\x9A".'ltimo' => 'Variables PHP corruptas por reemplazo ltimo',
            "\xC2\xBFEst\xC3\xA1ado" => 'Texto Estado corrupto por reemplazo Est',
            "\xC2\xBF\xC2\xBFEst\xC3\xA1\xC3\xA1" => 'Confirmaciones corruptas',
            "\xC3\x9A\xC3\x9Altimo" => 'DataTables ultimo corrupto',
        ];

        $paths = [resource_path('views'), base_path('modulos'), app_path()];
        $bad = 0;

        foreach ($paths as $root) {
            if (! is_dir($root)) {
                continue;
            }

            foreach (File::allFiles($root) as $file) {
                if (! preg_match('/\.(blade\.php|php)$/', $file->getFilename())) {
                    continue;
                }

                $content = file_get_contents($file->getPathname());
                foreach ($patterns as $needle => $hint) {
                    if (str_contains($content, $needle)) {
                        $bad++;
                        $rel = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file->getPathname());
                        $this->addIssue('high', 'encoding', "{$hint} en: {$rel}");
                        break;
                    }
                }
            }
        }

        if ($bad === 0) {
            $this->addPass('encoding', 'Sin patrones de corrupción por reemplazos globales');
        }
    }

    protected function auditStorageLink(): void
    {
        if (is_link(public_path('storage')) || is_dir(public_path('storage'))) {
            $this->addPass('storage', 'Enlace public/storage configurado');
        } else {
            $this->addIssue('medium', 'storage', 'Falta php artisan storage:link (imágenes/archivos públicos)');
        }
    }

    protected function auditDatabaseConnectivity(): void
    {
        $connections = ['core', 'transparencia', 'inventario'];

        foreach ($connections as $name) {
            try {
                Schema::connection($name)->getConnection()->getPdo();
                $this->addPass('database', "Conexión {$name} disponible");
            } catch (\Throwable $e) {
                $this->addIssue('medium', 'database', "Conexión {$name}: ".$e->getMessage());
            }
        }
    }

    protected function addIssue(string $severity, string $area, string $message): void
    {
        $this->issues[] = compact('severity', 'area', 'message');
    }

    protected function addPass(string $area, string $message): void
    {
        $this->passed[] = compact('area', 'message');
    }
}
