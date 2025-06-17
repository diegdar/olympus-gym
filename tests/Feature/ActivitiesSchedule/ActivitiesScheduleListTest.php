<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitiesSchedule;

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class ActivitiesScheduleListTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected const PERMISSION = 'activities.schedule.index';
    protected const ROUTE = 'activities.schedule.index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();            
    }

    private function getActivitiesScheduleListAs(string $roleName)
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE));
    }

    public function test_authorized_user_can_see_activities_schedule()
    {
        $activities = Activity::all();
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION)
            as $authorizedRole
        ) {
            $response = $this->getActivitiesScheduleListAs($authorizedRole);

            $response->assertStatus(200)
                        ->assertSee('Horario Actividades')
                        ->assertSee('Hora');
            foreach ($activities as $activity) {
                $response->assertSee($activity->name);
            }
        }
    }

    public function test_unauthorized_user_gets_403()
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION)
            as $unauthorizedRole
        ) {
            $response = $this->getActivitiesScheduleListAs($unauthorizedRole);

            $response->assertStatus(403)
                        ->assertDontSee('Horario Actividades')
                        ->assertDontSee('Hora');
        }
    }
}
