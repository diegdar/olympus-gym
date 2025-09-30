<?php
declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use Illuminate\Testing\TestResponse;

class ServicesViewTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);            
    }

    private function assertServicesViewContent(TestResponse $response): void
    {
        $response->assertStatus(200)
            ->assertSee('Nuestros Servicios')
            ->assertSee('Entrenamiento Personalizado')
            ->assertSee('Clases Grupales')
            ->assertSee('Nutrición y Asesoría')
            ->assertSee('Recuperación y Bienestar')
            ->assertSee('Valoración Física')
            ->assertSee('Acceso Libre al Gimnasio');

    }

    public function test_services_view_is_accessible_for_guests(): void
    {
        $response = $this->get(route('services'));
        $this->assertServicesViewContent($response);
    }

    public function test_services_view_is_accessible_for_authenticated_users(): void
    {
        $user = User::factory()->create()->assignRole('member');
        $response = $this->actingAs($user)->get(route('services'));
        $this->assertServicesViewContent($response);
    }
}
