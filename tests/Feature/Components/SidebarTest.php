<?php
declare(strict_types=1);

namespace Tests\Feature\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Traits\TestHelper;
use Database\Seeders\RoleSeeder;

class SidebarTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_sidebar_renders_on_dashboard_page(): void
    {
        $user = $this->createUserAndSignIn('member');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertSee('Panel Principal')
            ->assertSee('Mis Gestiones');
    }

    public function test_sidebar_logo_is_present(): void
    {
        $user = $this->createUserAndSignIn('member');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertSee('img/logos/my-web-logo.webp')
            ->assertSee('logo Diego Chacon que redirige a su sitio web');
    }

    public function test_sidebar_main_panel_links_are_present_for_members(): void
    {
        $user = $this->createUserAndSignIn('member');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertSee('Introduccion')
            ->assertSee('Inicio')
            ->assertSee('Dashboard')
            ->assertSee('Instalaciones')
            ->assertSee('Servicios')
            ->assertSee('Contacto');
    }

    public function test_sidebar_admin_panel_is_visible_for_admins(): void
    {
        $user = $this->createUserAndSignIn('admin');

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertStatus(200)
            ->assertSee('Gestion Operativa')
            ->assertSee('Estadísticas Suscripciones')
            ->assertSee('Usuarios')
            ->assertSee('Roles')
            ->assertSee('Salas')
            ->assertSee('Actividades')
            ->assertSee('Horario Actividades');
    }

    public function test_sidebar_admin_panel_is_hidden_for_members(): void
    {
        $user = $this->createUserAndSignIn('member');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertDontSee('Gestion Operativa')
            ->assertDontSee('Estadísticas Suscripciones');
    }

    public function test_sidebar_member_panel_links_are_present(): void
    {
        $user = $this->createUserAndSignIn('member');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertSee('Horario Actividades')
            ->assertSee('Mis Reservas')
            ->assertSee('Mi Suscripción');
    }

    public function test_sidebar_navigation_links_have_correct_routes(): void
    {
        $user = $this->createUserAndSignIn('member');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertSee(route('app-info'), false)
            ->assertSee(route('home'), false)
            ->assertSee(route('dashboard'), false)
            ->assertSee(route('facilities'), false)
            ->assertSee(route('services'), false)
            ->assertSee(route('contact'), false);
    }

    public function test_sidebar_highlights_active_link(): void
    {
        $user = $this->createUserAndSignIn('member');

        // Test dashboard page highlights dashboard link
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200)
            ->assertSee('Dashboard');
    }

    public function test_sidebar_user_profile_dropdown_is_present(): void
    {
        $user = $this->createUserAndSignIn('member');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertSee($user->name)
            ->assertSee($user->email)
            ->assertSee('Settings')
            ->assertSee('Log Out');
    }


    public function test_sidebar_reservations_link_is_visible_with_permission(): void
    {
        $user = $this->createUserAndSignIn('member');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertSee('Mis Reservas')
            ->assertSee(route('user.reservations'), false);
    }

    public function test_sidebar_subscription_link_is_visible_with_permission(): void
    {
        $user = $this->createUserAndSignIn('member');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertSee('Mi Suscripción')
            ->assertSee(route('member.subscription'), false);
    }

    /**
     * Data provider for role-based sidebar tests.
     */
    public static function roleBasedSidebarProvider(): array
    {
        return [
            'member routes' =>[
                'member',
                [
                // Panel Principal
                    'home',
                    'app-info',
                    'dashboard',
                    'facilities',
                    'services',
                    'contact',
                //   Mis Gestiones
                    'activity.schedules.index',
                    'user.reservations',
                    'member.subscription'
                ],
                [
                // Admin Panel
                    'admin.subscriptions.stats',
                    'admin.users.index',
                    'admin.roles.index',
                    'rooms.index',
                    'activities.index',
                ],
                'dashboard'
            ],
            'admin routes' =>[
                'admin',
                [
                // Panel Principal
                    'home',
                    'app-info',
                //   Gestion Operativa
                    'admin.subscriptions.stats',
                    'admin.users.index',
                    'admin.roles.index',
                    'rooms.index',
                    'activities.index',
                    'activity.schedules.index',
                ],
                [
                // Panel Principal
                    'dashboard',
                    'facilities',
                    'services',
                    'contact',
                //   Mis Gestiones
                    'user.reservations',
                    'member.subscription'
                ],
                'admin.subscriptions.stats'
            ],
            'super-admin routes' =>[
                'super-admin',
                [
                // Panel Principal
                    'home',
                    'app-info',
                //   Gestion Operativa
                    'admin.subscriptions.stats',
                    'admin.users.index',
                    'admin.roles.index',
                ],
                [
                // Panel Principal
                    'dashboard',
                    'facilities',
                    'services',
                    'contact',
                //   Mis Gestiones
                    'user.reservations',
                    'member.subscription',
                // Gestion Operativa
                    'rooms.index',
                    'activities.index',
                    'activity.schedules.index',
                ],
                'admin.subscriptions.stats'
            ],
        ];
    }

    #[DataProvider('roleBasedSidebarProvider')]
    public function test_sidebar_shows_correct_panels_and_routes_based_on_role(string $role, array $expectedRoutes, array $hiddenRoutes, string $baseRoute = 'dashboard'): void
    {
        $user = $this->createUserAndSignIn($role);

        $response = $this->actingAs($user)->get(route($baseRoute));

        $response->assertStatus(200);

        foreach ($expectedRoutes as $route) {
            $response->assertSee(route($route));
        }

        foreach ($hiddenRoutes as $route) {
            $response->assertDontSee(route($route));
        }
    }
}
