<?php

$root = dirname(__DIR__).'/modulos/rescate-animales-silvestres-main/resources/views';

$homeShell = <<<'BLADE'
<div class="container-fluid res-page-shell pb-4">
    @include('fusion.modulos.partials.rescate-module-nav')
    @include('fusion.modulos.partials.rescate-flash')

BLADE;

$flashBlock = <<<'BLADE'
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

BLADE;

$flashBlock2 = preg_quote($flashBlock, '/');
$flashPattern = '/\s*@if\s*\(\$message\s*=\s*Session::get\(\'success\'\)\).*?@endif\s*\n/s';

$pagePad = '<div class="container-fluid page-pad">';
$pagePadReplacement = '';

$titleLinePattern = '/\s*<h3 class="res-card-title mb-0">.*?<\/h3>\s*\n/s';

$updated = 0;

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $name = $file->getFilename();
    $content = file_get_contents($path);
    $original = $content;

    if (str_contains($content, $homeShell)) {
        $content = str_replace($homeShell, '', $content);
    }

    $content = preg_replace($flashPattern, "\n", $content) ?? $content;

    if (str_contains($content, $pagePad)) {
        $content = str_replace($pagePad, $pagePadReplacement, $content);
    }

    if (str_ends_with($name, 'index.blade.php') && str_contains($content, 'res-card-title')) {
        $content = preg_replace($titleLinePattern, "\n", $content) ?? $content;
        $content = str_replace(
            '<div class="card-header">',
            '<div class="card-header res-card-header--actions-only">',
            $content
        );
    }

    if ($content !== $original) {
        file_put_contents($path, $content);
        $updated++;
        echo basename(dirname($path))."/$name\n";
    }
}

echo "Updated $updated files\n";
