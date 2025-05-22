<?php
declare(strict_types=1);

namespace Tests\Feature\Activities;

use Tests\Traits\RoleTestHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowActivityTest extends TestCase
{
    use RefreshDatabase, RoleTestHelper;

    protected array $authorizedRoles;
    protected array $unauthorizedRoles;
    protected const SHOW_ACTIVITY_PERMISSION = 'activities.show';
    protected const ROUTE_ACTIVITIES_INDEX = 'activities.index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
             
        $this->authorizedRoles = $this->getAuthorizedRoles(self::SHOW_ACTIVITY_PERMISSION);

        $this->unauthorizedRoles = $this->getUnauthorizedRoles(self::SHOW_ACTIVITY_PERMISSION);              
    }

    private function getActivitiesShowAs(?string $roleName = null)
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_ACTIVITIES_INDEX));
    }

    public function test_authorized_user_can_see_activity_show_button()
    {
        foreach ($this->authorizedRoles as $authorizedRole) {
            $response = $this->getActivitiesShowAs($authorizedRole);

            $response->assertStatus(200)
                        ->assertSee('Horario Actividades')
                        ->assertSee('Hora')
                        ->assertSee('Ver Actividad');
        }
    }

    public function test_unauthorized_user_can_not_see_activity_show_button()
    {
        foreach ($this->unauthorizedRoles as $unauthorizedRole) {
            $response = $this->getActivitiesShowAs($unauthorizedRole);

            $response->assertStatus(403)
                        ->assertDontSee('Horario Actividades')
                        ->assertDontSee('Hora')
                        ->assertDontSee('Ver Actividad');
        }
    }
}
