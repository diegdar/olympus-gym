<?php
declare(strict_types=1);

namespace Tests\Feature\Activities;

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RoleTestHelper;
use Tests\TestCase;

class ActivityListTest extends TestCase
{
    use RefreshDatabase, RoleTestHelper;

    // Permissions
    protected const PERMISSION_LIST_ACTIVITIES = 'activities.index';
    protected const PERMISSION_CREATE_ACTIVITY = 'activities.create';
    protected const PERMISSION_EDIT_ACTIVITY = 'activities.edit';
    protected const PERMISSION_DESTROY_ACTIVITY = 'activities.destroy';

    // Routes
    protected const ROUTE_ACTIVITIES_INDEX = 'activities.index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function getActivitiesListAs(?string $roleName = null)
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_ACTIVITIES_INDEX));
    }

    public function test_authorized_user_can_see_activities_list(): void
    {
        $activities = Activity::all();
        foreach ($this->getAuthorizedRoles(self::PERMISSION_LIST_ACTIVITIES) as $authorizedRole) {
            $response = $this->getActivitiesListAs($authorizedRole);
            $response->assertStatus(200)
                     ->assertSee('Lista de actividades')
                     ->assertSeeInOrder(['Id', 'Actividad']);

            foreach ($activities as $activity) {
                $response->assertSeeText($activity->name);
            }
        }
    }

    public function test_unauthorized_user_cannot_see_activities_list(): void
    {
        foreach ($this->getUnauthorizedRoles(self::PERMISSION_LIST_ACTIVITIES) as $unauthorizedRole) {
            $response = $this->getActivitiesListAs($unauthorizedRole);
            $response->assertStatus(403)
                     ->assertDontSee('Lista de actividades')
                     ->assertDontSee('ID')
                     ->assertDontSee('Actividad');
        }
    }

    public function test_create_button_is_visible_depends_on_permission(): void
    {
        $this->assertButtonVisible(
            self::PERMISSION_CREATE_ACTIVITY,
            'Crear actividad'
        );

        $this->assertButtonNotVisibleOr403(
            self::PERMISSION_CREATE_ACTIVITY,
            'Crear actividad'
        );
    }

    public function test_edit_button_is_visible_depends_on_permission(): void
    {
        $activity = Activity::factory()->create();

        $this->assertButtonVisible(
            self::PERMISSION_EDIT_ACTIVITY,
            'Editar'
        );

        $this->assertButtonNotVisibleOr403(
            self::PERMISSION_EDIT_ACTIVITY,
            'Editar'
        );
    }

    public function test_destroy_button_is_visible_depends_on_permission(): void
    {
        $activity = Activity::factory()->create();

        $this->assertButtonVisible(
            self::PERMISSION_DESTROY_ACTIVITY,
            'Borrar'
        );

        $this->assertButtonNotVisibleOr403(
            self::PERMISSION_DESTROY_ACTIVITY,
            'Borrar'            
        );
    }

    private function assertButtonVisible(string $permission, string $text): void
    {
        foreach ($this->getAuthorizedRoles($permission) as $authorizedRole) {
            $response = $this->getActivitiesListAs($authorizedRole);
            $response->assertStatus(200)
                     ->assertSeeText($text);
        }
    }

    private function assertButtonNotVisibleOr403(string $permission, string $text): void
    {
        foreach ($this->getUnauthorizedRoles($permission) as $unauthorizedRole) {
            $response = $this->getActivitiesListAs($unauthorizedRole);

            if ($response->status() === 200) {
                $response->assertDontSeeText($text);
            } else {
                $response->assertStatus(403);
            }
        }
    }
}
