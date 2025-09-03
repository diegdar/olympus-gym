<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\Subscriptions\CsvExportService;
use Tests\TestCase;

class CsvExportServiceTest extends TestCase
{
    public function test_generates_csv_file(): void
    {
        $service = new CsvExportService();
        $rows = [
            ['col1' => 'a', 'col2' => 'b,c', 'number' => 1],
            ['col1' => 'd', 'col2' => 'e', 'number' => 2],
        ];
        $response = $service($rows, 'test_export');
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'test_export'));
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), '.csv'));
    }
}
