<?php

$filename = __DIR__ . '/resources/excel/Ops_Sem1_Inventory April 2026_RevFINAL.xlsx';

$zip = new ZipArchive();
if ($zip->open($filename) !== true) {
    echo "Unable to open file\n";
    exit(1);
}

for ($i = 0; $i < $zip->numFiles; $i++) {
    echo "ZIP: " . $zip->getNameIndex($i) . "\n";
}

echo "---\n";

$shared = [];
if (($index = $zip->locateName('xl/sharedStrings.xml')) !== false) {
    $xml = simplexml_load_string($zip->getFromIndex($index));
    foreach ($xml->si as $si) {
        if (isset($si->t)) {
            $shared[] = trim((string) $si->t);
        } elseif (isset($si->r->t)) {
            $shared[] = trim((string) $si->r->t);
        } else {
            $shared[] = '';
        }
    }
}

$index = $zip->locateName('xl/worksheets/sheet1.xml');
if ($index === false) {
    echo "Worksheet not found\n";
    exit(1);
}

$sheetXml = simplexml_load_string($zip->getFromIndex($index));
if ($sheetXml === false) {
    echo "Invalid sheet XML\n";
    exit(1);
}

$rowNumber = 0;
foreach ($sheetXml->sheetData->row as $row) {
    $rowNumber++;
    $cells = [];
    foreach ($row->c as $cell) {
        $column = preg_replace('/\d+/', '', (string) $cell['r']);
        $type = (string) $cell['t'];
        if ($type === 's') {
            $value = $shared[(int) $cell->v] ?? '';
        } elseif ($type === 'inlineStr') {
            $value = trim((string) $cell->is->t);
        } else {
            $value = trim((string) $cell->v);
        }
        $cells[$column] = $value;
    }
    ksort($cells);
    echo sprintf("ROW %s: %s\n", $rowNumber, implode('|', $cells));
}

$zip->close();
