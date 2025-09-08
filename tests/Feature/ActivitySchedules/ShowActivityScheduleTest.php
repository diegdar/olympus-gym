<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use App\Models\ActivitySchedule;
use Carbon\Carbon;
use Tests\Traits\TestHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Testing\TestResponse;
use App\Models\User;

class ShowActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;
    protected const PERMISSION = 'activity.schedules.show';
    protected const ROUTE = 'activity.schedules.show';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();                       
    }

    private function showActivitySchedule(string $roleName, int $activityScheduleId): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE, $activityScheduleId));
    }

    public function test_authorized_user_can_see_an_activity_show_view()
    {
        foreach (
                $this->getAuthorizedRoles(self::PERMISSION)       
                as $authorizedRole
            ) 
        {
            $activitySchedule = ActivitySchedule::factory()->create();

            $dayDateFormatted = Carbon::parse($activitySchedule->start_datetime)->translatedFormat('l, d F'); 
            $startTime = Carbon::parse($activitySchedule->start_datetime)->format('G:i');
            $availableSlots = (string) (
                                    $activitySchedule->max_enrollment 
                                    - $activitySchedule->current_enrollment
                                );

            $response = $this->showActivitySchedule($authorizedRole, $activitySchedule->id);         

            $response->assertStatus(200)
                     ->assertSeeInOrder(
                        [
                            $activitySchedule->room->name,
                            $dayDateFormatted,
                            $startTime,
                            $activitySchedule->activity->duration,
                            $availableSlots,
                            $activitySchedule->max_enrollment,
                            $activitySchedule->current_enrollment
                        ]
                     );
        }
    }

    public function test_unauthorized_user_cannot_see_an_activity_show_view()
    {
        foreach ($this->getUnauthorizedRoles(self::PERMISSION) as $unauthorizedRole) {
            $activitySchedule = ActivitySchedule::factory()->create();

            $response = $this->showActivitySchedule($unauthorizedRole, $activitySchedule->id);

            $response->assertStatus(403);
        }
    }

    public function test_member_sees_enroll_button_when_not_enrolled_and_slots_available(): void
    {
        $schedule = ActivitySchedule::factory()->create([
            'max_enrollment' => 5,
            'current_enrollment' => 0,
        ]);

        $member = $this->createUserAndAssignRole('member');

        $resp = $this->actingAs($member)->get(route(self::ROUTE, $schedule));
        $resp->assertStatus(200)
             ->assertSee('Inscribirme')
             ->assertDontSee('Desinscribirme');
    }

    public function test_member_sees_unenroll_button_when_already_enrolled(): void
    {
        $schedule = ActivitySchedule::factory()->create([
            'max_enrollment' => 5,
            'current_enrollment' => 0,
        ]);

        $member = $this->createUserAndAssignRole('member');
        // attach enrollment
        $schedule->users()->attach($member->id);

        $resp = $this->actingAs($member)->get(route(self::ROUTE, $schedule));
        $resp->assertStatus(200)
             ->assertSee('Desinscribirme')
             ->assertDontSee('Inscribirme');
    }

    public function test_member_sees_disabled_inscribe_when_no_slots_available(): void
    {
        $schedule = ActivitySchedule::factory()->create([
            'max_enrollment' => 1,
            'current_enrollment' => 0,
        ]);
        // Fill the only slot with another user
        $another = User::factory()->create();
        $schedule->users()->attach($another->id);

        $member = $this->createUserAndAssignRole('member');
        $resp = $this->actingAs($member)->get(route(self::ROUTE, $schedule));
        $resp->assertStatus(200)
             ->assertSee('Sin plazas disponibles');
    }

}
