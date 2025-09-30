<?php
declare(strict_types=1);

namespace Tests\Feature\Facilities;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use Illuminate\Testing\TestResponse;

class FacilitiesViewTest extends TestCase
{
    use RefreshDatabase;

    private const FACILITIES_ROUTE = 'facilities';

    public function setUp(): void
    {
        parent::setUp();
    }

    private function assertFacilitiesViewContent(TestResponse $response): void
    {
        $response->assertStatus(200)
            ->assertSee('Instalaciones Olympus Gym')
            ->assertSee('Entrada principal')
            ->assertSee('Zona Cardio')
            ->assertSee('Área de Pesas')
            ->assertSee('Clases Grupales')
            ->assertSee('Vestuarios')
            ->assertSee('Zona de Relax');
    }

    public function test_facilities_view_is_accessible_for_guests(): void
    {
        $response = $this->get(route(self::FACILITIES_ROUTE));
        $this->assertFacilitiesViewContent($response);
    }

    public function test_facilities_view_is_accessible_for_authenticated_users(): void
    {
        $this->seed(RoleSeeder::class);            

        $user = User::factory()->create()->assignRole('member');
        $response = $this->actingAs($user)->get(route(self::FACILITIES_ROUTE));
        $this->assertFacilitiesViewContent($response);
    }
}
