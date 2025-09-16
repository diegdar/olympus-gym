<?php
declare(strict_types=1);

namespace App\Services\Export;

use App\Contracts\Export\Exporter;
use Illuminate\Support\Collection;

final class JsonExporter implements Exporter
{
    public function contentType(): string
    {
        return 'application/json';
    }

    public function filename(string $base): string
    {
        return $base . '.json';
    }

    public function export(Collection $rows): string
    {
        return json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE);
    }
}
