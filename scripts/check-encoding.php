<?php
$files = glob(__DIR__.'/../modulos/donacion-recepcion-inventario-main/resources/views/**/index.blade.php');
foreach ($files as $f) {
    $c = file_get_contents($f);
    $rep = str_contains($c, "\xEF\xBF\xBD");
    $utf = mb_check_encoding($c, 'UTF-8');
    if ($rep) {
        foreach (explode("\n", $c) as $n => $line) {
            if (str_contains($line, "\xEF\xBF\xBD")) {
                echo basename(dirname($f)).':'.($n+1).': '.trim($line)."\n";
            }
        }
    }
}
