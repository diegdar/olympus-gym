<?php
declare(strict_types=1);

namespace Tests\Feature\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;
use Database\Seeders\RoleSeeder;

class IntroductionTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_introduction_component_renders_on_home_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee('Bienvenido a Olympus Gym')
            ->assertSee('tu santuario del fitness');
    }

    public function test_introduction_images_are_present(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee('img/home/crossfit_class.webp')
            ->assertSee('img/home/exercise_two_girls.webp')
            ->assertSee('img/home/girl_using_small_weights.webp')
            ->assertSee('img/home/spinning.webp')
            ->assertSee('img/home/zumba_class.webp');
    }

    public function test_introduction_unique_features_section_is_present(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee('¿Qué nos hace únicos?')
            ->assertSee('Entrenadores expertos')
            ->assertSee('Variedad de clases')
            ->assertSee('Comunidad inspiradora')
            ->assertSee('Ambiente motivador');
    }

    public function test_fees_section_is_visible_for_guests(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee('Elige tu cuota')
            ->assertSee('Cuota Mensual')
            ->assertSee('Cuota Trimestral')
            ->assertSee('Cuota Anual')
            ->assertSee('/register?fee=monthly')
            ->assertSee('/register?fee=quarterly')
            ->assertSee('/register?fee=yearly');
    }

    public function test_fees_section_is_hidden_for_authenticated_users(): void
    {
        $this->seed(RoleSeeder::class);

        $user = $this->createUserAndSignIn('member');

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200)
            ->assertDontSee('Elige tu cuota')
            ->assertDontSee('Cuota Mensual')
            ->assertDontSee('Cuota Trimestral')
            ->assertDontSee('Cuota Anual');
    }

    public function test_gym_schedule_section_is_present(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee('Horario del gym')
            ->assertSee('Lunes a viernes:')
            ->assertSee('de 07:00 a 23:00 h')
            ->assertSee('Sábados:')
            ->assertSee('de 09:00 a 21:00 h')
            ->assertSee('Domingos/festivos:')
            ->assertSee('de 09:00 a 14:00 h')
            ->assertSee('*Club abierto todo el año, salvo 25 de diciembre y 1 de enero.');
    }

    public function test_introduction_call_to_action_is_present(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee('Te invitamos a explorar nuestra página web')
            ->assertSee('¡En Olympus Gym, tu éxito es nuestra meta!');
    }
}
