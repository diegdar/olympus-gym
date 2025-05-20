<?php
declare(strict_types=1);

namespace Tests\Feature\Activities;

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\RoleTestHelper;

class ActivityListTest extends TestCase
{
    use RefreshDatabase, RoleTestHelper;

    protected array $authorizedRoles;

    protected array $unauthorizedRoles;

    protected const PERMISSION_NAME = 'activities.index';
    protected const ROUTE_ACTIVITIES_INDEX = 'activities.index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->authorizedRoles = $this->getAuthorizedRoles(self::PERMISSION_NAME);

        $this->unauthorizedRoles = $this->getUnauthorizedRoles(self::PERMISSION_NAME);              
    }

    private function getActivitiesIndexAs(?string $roleName = null)
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_ACTIVITIES_INDEX));
    }

    public function test_authorized_user_can_see_activity_list()
    {
        $activities = Activity::all();
        foreach ($this->authorizedRoles as $authorizedRole) {
            $response = $this->getActivitiesIndexAs($authorizedRole);

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
        foreach ($this->unauthorizedRoles as $unauthorizedRole) {
            $response = $this->getActivitiesIndexAs($unauthorizedRole);

            $response->assertStatus(403)
                        ->assertDontSee('Horario Actividades')
                        ->assertDontSee('Hora');
        }
    }
}
