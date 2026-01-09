<?php
$inputFile = 'realestate.csv';
$outputFile = 'realestate_corrigido.csv';

$in = fopen($inputFile, 'r');
$out = fopen($outputFile, 'w');

$header = fgetcsv($in);
fputcsv($out, $header);

while ($row = fgetcsv($in)) {
    // Corrige preço (remove .00)
    $row[5] = str_replace('.00', '', $row[5]);

    // Corrige título e descrição
    $row[1] = str_replace('q', 'quartos', $row[1]);
    $row[2] = str_replace('q', 'quartos', $row[2]);
    $row[2] = str_replace('m²', ' m²', $row[2]);

    fputcsv($out, $row);
}

fclose($in);
fclose($out);

echo "Arquivo corrigido criado: $outputFile\n";
