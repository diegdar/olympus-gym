<?php
declare(strict_types=1);

namespace App\Services\Subscriptions;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Service: CSV export with UTF-8 BOM and configurable delimiter.
 * Keeps filesystem writes isolated and easily replaceable (e.g. stream response in future).
 */

class CsvExportService
{
    public function __invoke(array $rows, string $baseName, string $delimiter = ';', ?string $suffix = null): BinaryFileResponse
    {
        $headers = $this->extractHeaders($rows);
        $csv = $this->buildCsv($rows, $headers, $delimiter);
        $filename = $this->generateFilename($baseName, $suffix);
        $tempPath = storage_path('app/'.$filename);
        file_put_contents($tempPath, $csv);
        return response()->download($tempPath, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ])->deleteFileAfterSend(true);
    }

    private function extractHeaders(array $rows): array
    {
        if (empty($rows)) {
            return [];
        }
        // Use first row's keys as canonical header order
        return array_keys($rows[0]);
    }

    private function buildCsv(array $rows, array $headers, string $delimiter): string
    {
        $lines = [];
        if ($headers) {
            $lines[] = $this->csvLine($headers, $delimiter);
            foreach ($rows as $row) {
                $lines[] = $this->csvLine(array_map(fn($h) => $row[$h] ?? '', $headers), $delimiter);
            }
        }
        return "\xEF\xBB\xBF".implode("\r\n", $lines);
    }

    private function generateFilename(string $baseName, ?string $suffix): string
    {
        $stamp = date('Ymd_His');
        return $baseName.($suffix ? '_'.$suffix : '').'_'.$stamp.'.csv';
    }

    private function csvLine(array $fields, string $delimiter): string
    {
        return collect($fields)->map(function($v) use ($delimiter){
            $v = (string)$v;
            $needsQuote = str_contains($v, '"') || str_contains($v, $delimiter) || str_contains($v, "\n") || str_contains($v, "\r") || str_contains($v, ',');
            $v = str_replace('"', '""', $v);
            return $needsQuote ? '"'.$v.'"' : $v;
        })->implode($delimiter);
    }
}
