<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use App\Models\ActivitySchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class ActivitySchedulesListTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    // permission
    protected const PERMISSION_LIST = 'activity.schedules.index';
    protected const PERMISSION_CREATE = 'activity.schedules.create';
    protected const PERMISSION_SHOW = 'activity.schedules.show';
    protected const PERMISSION_EDIT = 'activity.schedules.edit';
    protected const PERMISSION_DESTROY = 'activity.schedules.destroy';
    protected const PERMISSION_ENROLL_USER = 'activity.schedules.enroll';


    // routes
    protected const ROUTE = 'activity.schedules.index';

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
        $activities = ActivitySchedule::all();
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION_LIST)
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
            $this->getUnauthorizedRoles(self::PERMISSION_LIST)
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
            self::PERMISSION_CREATE,
            'Crear horario actividad'
        );

        $this->assertButtonNotVisibleOr403(
            self::PERMISSION_CREATE,
            'Crear horario actividad'
        );
    }

    public function test_show_button_is_visible_depends_on_permission(): void
    {
        ActivitySchedule::factory()->create();

        $this->assertButtonVisible(
            self::PERMISSION_SHOW,
            'Ver'
        );

        $this->assertButtonNotVisibleOr403(
            self::PERMISSION_SHOW,
            'Ver'
        );
    }
    public function test_edit_button_is_visible_depends_on_permission(): void
    {
        ActivitySchedule::factory()->create();

        $this->assertButtonVisible(
            self::PERMISSION_EDIT,
            'Editar'
        );

        $this->assertButtonNotVisibleOr403(
            self::PERMISSION_EDIT,
            'Editar'
        );
    }

    public function test_destroy_button_is_visible_depends_on_permission(): void
    {
        ActivitySchedule::factory()->create();

        $this->assertButtonVisible(
            self::PERMISSION_DESTROY,
            'Borrar'
        );

        $this->assertButtonNotVisibleOr403(
            self::PERMISSION_DESTROY,
            'Borrar'
        );
    }
    
    public function test_enroll_button_is_visible_depends_on_permission(): void
    {
        ActivitySchedule::factory()->create();

        $this->assertButtonVisible(
            self::PERMISSION_ENROLL_USER,
            'Inscribirse'
        );

        $this->assertButtonNotVisibleOr403(
            self::PERMISSION_ENROLL_USER,
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

        $u1 = $this->createUserAndAssignRole('member');
        $u2 = $this->createUserAndAssignRole('member');
        $schedule->users()->attach([$u1->id, $u2->id]);

        $admin = $this->createUserAndAssignRole('admin');
        $resp = $this->actingAs($admin)->get(route(self::ROUTE));

        $resp->assertStatus(200)
             ->assertSeeText('Plazas libres:')
             ->assertSeeText('1');
    }
}
