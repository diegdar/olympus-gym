<?php
namespace Tests\Feature\Facilities;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilitiesViewTest extends TestCase
{
    /**
     * Test that the facilities view loads successfully and displays all sections.
     */
    public function test_facilities_view_displays_all_sections(): void
    {
        $response = $this->get(route('facilities'));

        $response->assertStatus(200);
        $response->assertSee('Instalaciones Olympus Gym');
        $response->assertSee('Entrada principal');
        $response->assertSee('Zona Cardio');
        $response->assertSee('Área de Pesas');
        $response->assertSee('Clases Grupales');
        $response->assertSee('Vestuarios');
        $response->assertSee('Zona de Relax');
        $response->assertSee('Olympus Gym: Instalaciones modernas y espaciosas');
    }
}