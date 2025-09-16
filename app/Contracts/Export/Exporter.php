<?php
declare(strict_types=1);

namespace App\Contracts\Export;

use Illuminate\Support\Collection;

interface Exporter
{
    public function contentType(): string;

    public function filename(string $base): string;

    public function export(Collection $rows): string;
}
