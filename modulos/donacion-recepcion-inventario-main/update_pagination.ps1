$files = @(
    "resources\views\donante\index.blade.php",
    "resources\views\espacio\index.blade.php",
    "resources\views\estante\index.blade.php",
    "resources\views\producto\index.blade.php",
    "resources\views\puntos-recoleccion\index.blade.php",
    "resources\views\solicitudes-recoleccion\index.blade.php"
)

$oldPattern = @'
"paging": false
'@

$newPattern = @'
"paging": true,
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
'@

$oldInfo = '"info": false'
$newInfo = '"info": true'

$oldPaginate = @'
"emptyTable": "
'@

$newPaginate = @'
"emptyTable": "
'@

foreach ($file in $files) {
    Write-Host "Processing $file"
    $content = Get-Content $file -Raw -Encoding UTF8
    $content = $content -replace [regex]::Escape('"paging": false'), '"paging": true,
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],'
    $content = $content -replace [regex]::Escape('"info": false'), '"info": true'
    Set-Content $file $content -Encoding UTF8 -NoNewline
    Write-Host "Updated $file"
}

Write-Host "Done!"
