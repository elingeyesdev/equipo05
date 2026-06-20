<?php

$root = dirname(__DIR__).'/modulos/rescate-animales-silvestres-main/resources/views';

$homeShell = <<<'BLADE'
<div class="container-fluid res-page-shell pb-4">
    @include('fusion.modulos.partials.rescate-module-nav')
    @include('fusion.modulos.partials.rescate-flash')

BLADE;

$patterns = [
    "/\r?\n[ \t]*<\/div>\r?\n[ \t]*@include\('partials\.page-pad'\)/" => '',
    "/\r?\n@include\('partials\.page-pad'\)/" => '',
];

$updated = 0;

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $content = file_get_contents($path);
    $original = $content;

    if (str_contains($content, $homeShell)) {
        $content = str_replace($homeShell, '', $content);
    }

    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content) ?? $content;
    }

    // Legacy index titles duplicados con la pestaña activa del módulo.
    if (str_ends_with($file->getFilename(), 'index.blade.php')) {
        $content = preg_replace(
            '/\s*<span id="card_title"[^>]*>.*?<\/span>\s*\n/s',
            "\n",
            $content
        ) ?? $content;
    }

    // Cierre sobrante del antiguo res-page-shell en listados.
    if (str_ends_with($file->getFilename(), 'index.blade.php')) {
        $content = preg_replace(
            '/(\n[ \t]*<\/div>\r?\n)<\/div>(\r?\n(?![ \t]*<\/div>))/',
            '$1',
            $content,
            1
        ) ?? $content;
    }

    if ($content !== $original) {
        file_put_contents($path, $content);
        $updated++;
    }
}

echo "Cleaned $updated files\n";
