<?php
declare(strict_types=1);

namespace App\Services\Export;

use App\Contracts\Export\Exporter;
use Illuminate\Support\Collection;

final class CsvExporter implements Exporter
{
    public function contentType(): string
    {
        return 'text/csv; charset=UTF-8';
    }

    public function filename(string $base): string
    {
        return $base . '.csv';
    }

    public function export(Collection $rows): string
    {
        $handle = fopen('php://temp', 'w+');
        // BOM UTF-8 for Excel es-ES
        fwrite($handle, "\xEF\xBB\xBF");
        $delimiter = ';';
        fputcsv($handle, ['ID','Nombre','Email','attended'], $delimiter);
        foreach ($rows as $row) {
            fputcsv($handle, [
                $row['id'],
                $row['name'],
                $row['email'],
                $row['attended'] ? 'true' : 'false',
            ], $delimiter);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        return (string) $csv;
    }
}
