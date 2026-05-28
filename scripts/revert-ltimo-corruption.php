<?php

/**
 * Revierte el reemplazo global 'ltimo' => 'Último' que rompió variables PHP y textos.
 * Orden: variables PHP primero, luego textos de UI.
 */
$fixes = [
    '$uÚltimosMensajes' => '$ultimosMensajes',
    '$uÚltimosUsuarios' => '$ultimosUsuarios',
    '$uÚltimoEnvioFecha' => '$ultimoEnvioFecha',
    '$uÚltimo' => '$ultimo',
    'úÚltimos' => 'últimos',
    'úÚltimo' => 'último',
    'ÚÚltimos' => 'Últimos',
    'ÚÚltimo' => 'Último',
];

$roots = [__DIR__.'/../resources/views', __DIR__.'/../modulos', __DIR__.'/../app'];

foreach ($roots as $root) {
    if (! is_dir($root)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
    foreach ($it as $file) {
        if (! preg_match('/\.(blade\.php|php)$/', $file->getFilename())) {
            continue;
        }
        $path = $file->getPathname();
        $content = file_get_contents($path);
        $original = $content;
        foreach ($fixes as $from => $to) {
            $content = str_replace($from, $to, $content);
        }
        if ($content !== $original) {
            file_put_contents($path, $content);
            echo "Fixed: {$path}\n";
        }
    }
}
