<?php
declare(strict_types=1);

namespace Tests\Feature\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Traits\TestHelper;

class NavbarTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_navbar_renders_on_home_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee('Introduccion')
            ->assertSee('Inicio')
            ->assertSee('Instalaciones')
            ->assertSee('Servicios')
            ->assertSee('Contacto')
            ->assertSee('Log in')
            ->assertSee('Register');
    }

    public function test_navbar_logo_is_present(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee('img/logos/my-web-logo.webp')
            ->assertSee('logo Diego Chacon que redirige a su sitio web');
    }

    public function test_navbar_navigation_links_are_present(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee(route('app-info'), false)
            ->assertSee(route('home'), false)
            ->assertSee(route('facilities'), false)
            ->assertSee(route('services'), false)
            ->assertSee(route('contact'), false);
    }

    public function test_navbar_shows_login_and_register_for_guests(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee('Log in')
            ->assertSee('Register')
            ->assertDontSee('Dashboard')
            ->assertDontSee('Stats');
    }

    #[DataProvider('authenticatedUserProvider')]
    public function test_navbar_shows_correct_links_for_authenticated_users(string $role, string $expectedLink): void
    {
        $user = $this->createUserAndAssignRole($role);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200)
            ->assertDontSee('Log in')
            ->assertDontSee('Register')
            ->assertSee($expectedLink);
    }

    public function test_navbar_highlights_active_link(): void
    {
        $response = $this->get(route('app-info'));
        $response->assertStatus(200)
            ->assertSee('border-[#c8a27a]', false);

        $response = $this->get(route('home'));
        $response->assertStatus(200)
            ->assertSee('border-[#c8a27a]', false);

        $response = $this->get(route('facilities'));
        $response->assertStatus(200)
            ->assertSee('border-[#c8a27a]', false);
    }

    public function test_navbar_menu_toggle_button_is_present(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee('menu-toggle')
            ->assertSee('w-6 h-6 fill-current');
    }

    /**
     * Data provider for authenticated user tests.
     */
    public static function authenticatedUserProvider(): array
    {
        return [
            'member sees Dashboard' => ['member', 'Dashboard'],
            'admin sees Stats' => ['admin', 'Panel'],
            'super-admin sees Stats' => ['super-admin', 'Panel'],
        ];
    }
}
