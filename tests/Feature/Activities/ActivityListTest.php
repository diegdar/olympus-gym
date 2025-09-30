<?php
declare(strict_types=1);

namespace Tests\Feature\Activities;

use App\Models\Activity;
use Database\Seeders\ActivitySeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use Tests\TestCase;

class ActivityListTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, ActivitySeeder::class]);
    }

    private function getActivitiesListAs(string $roleName)
    {
        return $this->actingAsRole($roleName)->get(route('activities.index'));
    }

    public function test_authorized_user_can_see_activities_list(): void
    {
        $activities = Activity::all();
        foreach (
            $this->getAuthorizedRoles('activities.index')
            as $authorizedRole
        ) {
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
        foreach (
            $this->getUnauthorizedRoles('activities.index')
            as $unauthorizedRole
        ) {
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
            'activities.create',
            'Crear actividad'
        );

        $this->assertButtonNotVisibleOr403(
            'activities.create',
            'Crear actividad'
        );
    }

    public function test_edit_button_is_visible_depends_on_permission(): void
    {
        $activity = Activity::factory()->create();

        $this->assertButtonVisible(
            'activities.edit',
            'Editar'
        );

        $this->assertButtonNotVisibleOr403(
            'activities.edit',
            'Editar'
        );
    }

    public function test_destroy_button_is_visible_depends_on_permission(): void
    {
        Activity::factory()->create();

        $this->assertButtonVisible(
            'activities.destroy',
            'Borrar'
        );

        $this->assertButtonNotVisibleOr403(
            'activities.destroy',
            'Borrar'
        );
    }

    private function assertButtonVisible(string $permission, string $text): void
    {
        foreach (
            $this->getAuthorizedRoles($permission) 
            as $authorizedRole
        ) {
            $response = $this->getActivitiesListAs($authorizedRole);
            $response->assertStatus(200)
                     ->assertSeeText($text);
        }
    }

    private function assertButtonNotVisibleOr403(string $permission, string $text): void
    {
        foreach (
            $this->getUnauthorizedRoles($permission) as $unauthorizedRole
        ) {
            $response = $this->getActivitiesListAs($unauthorizedRole);

            if ($response->status() === 200) {
                $response->assertDontSeeText($text);
            } else {
                $response->assertStatus(403);
            }
        }
    }
}
