<?php
declare(strict_types=1);

namespace Tests\Feature\Activities;

use Tests\Traits\TestHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;
    protected const PERMISSION = 'activities.schedule.show';
    protected const ROUTE = 'activities.schedule.index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();                       
    }

    private function getActivitiesScheduleAs(?string $roleName = null)
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE));
    }

    public function test_authorized_user_can_see_activity_show_button()
    {
        foreach (
                $this->getAuthorizedRoles                (self::PERMISSION)       
                as $authorizedRole
            ) 
        {             $response = $this->getActivitiesScheduleAs($authorizedRole);

            $response->assertStatus(200)
                        ->assertSee('Horario Actividades')
                        ->assertSee('Hora')
                        ->assertSee('Ver Actividad');
        }
    }

    public function test_unauthorized_user_can_not_see_activity_show_button()
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION) 
            as $unauthorizedRole
        ) {            $response = $this->getActivitiesScheduleAs($unauthorizedRole);

            $response->assertStatus(403)
                        ->assertDontSee('Horario Actividades')
                        ->assertDontSee('Hora')
                        ->assertDontSee('Ver Actividad');
        }
    }
}
