<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use App\Models\ActivitySchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;
use Database\Seeders\ActivitySeeder;
use Database\Seeders\RoleSeeder;

class ActivitySchedulesListTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, ActivitySeeder::class]);
    }

    private function getActivitiesScheduleListAs(string $roleName)
    {
        return $this->actingAsRole($roleName)->get(route('activity.schedules.index'));
    }

    public function test_authorized_user_can_see_activities_schedule()
    {
        $activities = ActivitySchedule::all();
        foreach (
            $this->getAuthorizedRoles('activity.schedules.index')
            as $authorizedRole
        ) {
            $response = $this->getActivitiesScheduleListAs($authorizedRole);

            $response->assertStatus(200)
                        ->assertSee('Horario Actividades')
                        ->assertSee('Hora');
            foreach ($activities as $activitySchedule) {
                $response->assertSee($activitySchedule->name);
            }
        }
    }

    public function test_unauthorized_user_gets_403()
    {
        foreach (
            $this->getUnauthorizedRoles('activity.schedules.index')
            as $unauthorizedRole
        ) {
            $response = $this->getActivitiesScheduleListAs($unauthorizedRole);

            $response->assertStatus(403)
                        ->assertDontSee('Horario Actividades')
                        ->assertDontSee('Hora');
        }
    }

    private function assertButtonVisible(string $permission, string $text): void
    {
        foreach (
            $this->getAuthorizedRoles($permission)
            as $authorizedRole
        ) {
            $response = $this->getActivitiesScheduleListAs($authorizedRole);
            $response->assertStatus(200)
                     ->assertSeeText($text);
        }
    }

    private function assertButtonNotVisibleOr403(string $permission, string $text): void
    {
        foreach (
            $this->getUnauthorizedRoles($permission) as $unauthorizedRole
        ) {
            $response = $this->getActivitiesScheduleListAs($unauthorizedRole);

            if ($response->status() === 200) {
                $response->assertDontSeeText($text);
            } else {
                $response->assertStatus(403);
            }
        }
    }

    public function test_create_button_is_visible_depends_on_permission(): void
    {
        $this->assertButtonVisible(
            'activity.schedules.create',
            'Crear horario actividad'
        );

        $this->assertButtonNotVisibleOr403(
            'activity.schedules.create',
            'Crear horario actividad'
        );
    }

    public function test_show_button_is_visible_depends_on_permission(): void
    {
        ActivitySchedule::factory()->create();

        $this->assertButtonVisible(
            'activity.schedules.show',
            'Ver'
        );

        $this->assertButtonNotVisibleOr403(
            'activity.schedules.show',
            'Ver'
        );
    }
    public function test_edit_button_is_visible_depends_on_permission(): void
    {
        ActivitySchedule::factory()->create();

        $this->assertButtonVisible(
            'activity.schedules.edit',
            'Editar'
        );

        $this->assertButtonNotVisibleOr403(
            'activity.schedules.edit',
            'Editar'
        );
    }

    public function test_destroy_button_is_visible_depends_on_permission(): void
    {
        ActivitySchedule::factory()->create();

        $this->assertButtonVisible(
            'activity.schedules.destroy',
            'Borrar'
        );

        $this->assertButtonNotVisibleOr403(
            'activity.schedules.destroy',
            'Borrar'
        );
    }

    public function test_enroll_button_is_visible_depends_on_permission(): void
    {
        ActivitySchedule::factory()->create();

        $this->assertButtonVisible(
            'activity.schedules.enroll',
            'Inscribirse'
        );

        $this->assertButtonNotVisibleOr403(
            'activity.schedules.enroll',
            'Inscribirse'
        );
    }

    public function test_index_shows_free_slots(): void
    {
        $schedule = ActivitySchedule::factory()->create([
            'max_enrollment' => 3,
            // Usamos una hora de tarde que sabemos que el servicio lista (horas presentes en seeders)
            'start_datetime' => now()->addDay()->setTime(18, 30),
            'end_datetime' => now()->addDay()->setTime(19, 30),
        ]);

        $u1 = $this->createUserAndSignIn('member');
        $u2 = $this->createUserAndSignIn('member');
        $schedule->users()->attach([$u1->id, $u2->id]);

        $admin = $this->createUserAndSignIn('admin');
        $resp = $this->actingAs($admin)->get(route('activity.schedules.index'));

        $resp->assertStatus(200)
             ->assertSeeText('Plazas libres:')
             ->assertSeeText('1');
    }
}
