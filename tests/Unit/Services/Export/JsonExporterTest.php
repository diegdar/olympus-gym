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
		$this->assertSame('enrolled.json', $exporter->filename('enrolled'));
	}

	public function test_export_wraps_data_and_preserves_unicode(): void
	{
		$rows = Collection::make([
			['id' => 1, 'name' => 'José', 'email' => 'jose@example.com', 'attended' => true],
			['id' => 2, 'name' => 'Ana',  'email' => 'ana@example.com',  'attended' => false],
		]);

		$exporter = new JsonExporter();
		$json = $exporter->export($rows);

		$this->assertJson($json);
		$data = json_decode($json, true);
		$this->assertArrayHasKey('data', $data);
		$this->assertCount(2, $data['data']);
		$this->assertSame('José', $data['data'][0]['name']);
		$this->assertTrue($data['data'][0]['attended']);
		$this->assertFalse($data['data'][1]['attended']);
	}
}

