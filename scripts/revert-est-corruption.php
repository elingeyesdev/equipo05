<?php

$fixes = [
    '¿Estáado de cuenta del donante' => 'Estado de cuenta del donante',
    '¿Estáado de cuenta por usuario' => 'Estado de cuenta por usuario',
    '¿Estáado de cuenta' => 'Estado de cuenta',
    '¿Estáado del Sistema' => 'Estados del Sistema',
    '¿Estáado de animal' => 'Estado de animal',
    '¿Estáado PKG' => 'Estado PKG',
    '¿Estáado Actual' => 'Estado Actual',
    '¿Estáado actual' => 'Estado actual',
    '¿Estáado General' => 'Estado General',
    'gw¿Estáado' => 'gwEstado',
    'badge¿Estáado' => 'badgeEstado',
    '¿Estáados del Sistema' => 'Estados del Sistema',
    '¿Estáados de animal' => 'Estados de animal',
    '¿Estáado' => 'Estado',
    '¿Estáados' => 'Estados',
    '¿Estáadisticas' => 'Estadísticas',
    '¿Estáantes' => 'Estantes',
    '¿Estáante' => 'Estante',
    '¿Estáilos' => 'Estilos',
    '¿Estáructura' => 'Estructura',
    '¿Estáe usuario' => 'Este usuario',
    '¿Estáe mensaje' => 'Este mensaje',
    '¿Estáe paquete' => 'Este paquete',
    '¿Está:' => 'Est:',
    '¿Estáe ' => 'Este ',
    '¿¿Estáá seguro' => '¿Está seguro',
    '¿Estáa acción' => 'Esta acción',
    '¿Estáa lógica' => 'Esta lógica',
    '¿Estáadísticas' => 'Estadísticas',
    '¿Estáimado' => 'Estimado',
];

$roots = [__DIR__.'/../resources/views', __DIR__.'/../modulos'];

foreach ($roots as $root) {
    if (! is_dir($root)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
    foreach ($it as $file) {
        if (! str_ends_with($file->getFilename(), '.blade.php')) {
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
            echo "Reverted: {$path}\n";
        }
    }
}
