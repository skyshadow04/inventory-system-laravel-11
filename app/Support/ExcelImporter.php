<?php

namespace App\Support;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class ExcelImporter
{
    public static function readSpreadsheet(string $path): array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return $extension === 'csv'
            ? self::readCsv($path)
            : self::readXlsx($path);
    }

    protected static function readCsv(string $path): array
    {
        $rows = [];

        if (($handle = fopen($path, 'r')) === false) {
            throw new RuntimeException('Unable to open uploaded CSV file.');
        }

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null] || $row === false) {
                continue;
            }

            $rows[] = array_map(function ($value) {
                return trim((string) $value);
            }, $row);
        }

        fclose($handle);

        return self::normalizeRows($rows);
    }

    protected static function readXlsx(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('The uploaded XLSX file is invalid or corrupted. Please upload a valid XLSX file.');
        }

        $sharedStrings = [];
        if (($index = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $sharedStrings = self::parseSharedStrings($zip->getFromIndex($index));
        }

        $sheetName = 'xl/worksheets/sheet1.xml';
        if (($index = $zip->locateName($sheetName)) === false) {
            $zip->close();
            throw new RuntimeException('The uploaded XLSX file is invalid or corrupted. Please upload a valid XLSX file.');
        }

        $sheetXml = simplexml_load_string($zip->getFromIndex($index));
        if ($sheetXml === false) {
            $zip->close();
            throw new RuntimeException('The uploaded XLSX file is invalid or corrupted. Please upload a valid XLSX file.');
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $rowData = [];
            foreach ($row->c as $cell) {
                $column = preg_replace('/\d+/', '', (string) $cell['r']);
                $rowData[self::columnLetterToIndex($column)] = self::getCellValue($cell, $sharedStrings);
            }
            if (!empty($rowData)) {
                ksort($rowData);
                $rows[] = array_values($rowData);
            }
        }

        $zip->close();

        return self::normalizeRows($rows);
    }

    protected static function parseSharedStrings(string $xml): array
    {
        $sharedStrings = [];
        $xml = simplexml_load_string($xml);
        if ($xml === false) {
            return $sharedStrings;
        }

        foreach ($xml->si as $si) {
            $sharedStrings[] = self::extractText($si);
        }

        return $sharedStrings;
    }

    protected static function getCellValue(SimpleXMLElement $cell, array $sharedStrings): string
    {
        $type = (string) $cell['t'];

        if ($type === 's') {
            $index = (int) $cell->v;
            return $sharedStrings[$index] ?? '';
        }

        if ($type === 'inlineStr') {
            return self::extractText($cell->is);
        }

        if (isset($cell->v)) {
            return trim((string) $cell->v);
        }

        return '';
    }

    protected static function extractText(SimpleXMLElement $element): string
    {
        if (isset($element->t)) {
            return trim((string) $element->t);
        }

        $text = '';
        foreach ($element->r as $run) {
            $text .= trim((string) $run->t);
        }

        return trim($text);
    }

    protected static function columnLetterToIndex(string $letter): int
    {
        $letter = strtoupper($letter);
        $index = 0;

        foreach (str_split($letter) as $character) {
            $index = $index * 26 + (ord($character) - ord('A') + 1);
        }

        return $index - 1;
    }

    protected static function normalizeRows(array $rows): array
    {
        $normalized = [];

        foreach ($rows as $row) {
            $row = array_values($row);
            $trimmed = array_map(function ($value) {
                return trim((string) $value);
            }, $row);

            if ($trimmed === [] || array_reduce($trimmed, fn($carry, $value) => $carry && $value === '', true)) {
                continue;
            }

            $normalized[] = $trimmed;
        }

        return $normalized;
    }
}
