<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Export;

use App\Services\Export\JsonExporter;
use Illuminate\Support\Collection;
use Tests\TestCase;

class JsonExporterTest extends TestCase
{
    public function test_content_type_and_filename(): void
    {
        $exporter = new JsonExporter();
        $this->assertSame('application/json', $exporter->contentType());
        $this->assertSame('sample.json', $exporter->filename('sample'));
    }

    public function test_export_structure_and_unicode(): void
    {
        $rows = new Collection([
            ['id' => 1, 'name' => 'José', 'email' => 'jose@example.com', 'attended' => true],
            ['id' => 2, 'name' => 'María', 'email' => 'maria@example.com', 'attended' => false],
        ]);

        $exporter = new JsonExporter();
        $json = $exporter->export($rows);

        $this->assertJson($json);
        $decoded = json_decode($json, true);
        $this->assertArrayHasKey('data', $decoded);
        $this->assertCount(2, $decoded['data']);
        $this->assertSame('José', $decoded['data'][0]['name']); // unicode preserved
        $this->assertTrue($decoded['data'][0]['attended']);
        $this->assertFalse($decoded['data'][1]['attended']);
    }
}
