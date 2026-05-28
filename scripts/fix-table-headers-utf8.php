<?php

$map = [
    'Cdigo' => 'Código',
    'Almacn' => 'Almacén',
    'almacn' => 'almacén',
    'Descripcin' => 'Descripción',
    'Categora' => 'Categoría',
    'categora' => 'categoría',
    'Direccin' => 'Dirección',
    'Gestin' => 'Gestión',
    'Telfono' => 'Teléfono',
    'pgina' => 'página',
    'pginas' => 'páginas',
    'Recoleccin' => 'Recolección',
];

$roots = [
    __DIR__.'/../modulos/donacion-recepcion-inventario-main/resources/views',
    __DIR__.'/../resources/views',
];

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
        foreach ($map as $from => $to) {
            $content = str_replace($from, $to, $content);
        }
        $content = preg_replace('/\x{FFFD}/u', '', $content) ?? $content;
        if ($content !== $original) {
            file_put_contents($path, $content);
            echo "Fixed: {$path}\n";
        }
    }
}
