<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use App\Models\ActivitySchedule;
use App\Models\Activity;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;
use Carbon\Carbon;

class EnrollUserInActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    // Permissions and Routes
    protected const PERMISSION_LIST = 'activity.schedules.index';
    protected const PERMISSION_ENROLL_USER = 'activity.schedules.enroll';
    protected const ROUTE_INDEX = 'activity.schedules.index';

    protected Activity $activity;
    protected Room $room;
    protected ActivitySchedule $activitySchedule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->activity = Activity::factory()->create();
        $this->room = Room::factory()->create();
        $this->activitySchedule = ActivitySchedule::factory()->create([
            'activity_id' => $this->activity->id,
            'room_id' => $this->room->id,
            'max_enrollment' => 10,
            'current_enrollment' => 0,
        ]);
    }

    private function actingAsRole(string $roleName): User
    {
        $user = User::factory()->create()->assignRole($roleName);
        $this->actingAs($user);
        return $user;
    }

    private function performEnrollmentRequest(ActivitySchedule $activitySchedule)
    {
        return $this->post(route('activity.schedules.enroll', $activitySchedule));
    }

    private function getAlreadyEnrolledErrorMessage(ActivitySchedule $activitySchedule): string
    {
        $dateFormatted = Carbon::parse($activitySchedule->start_datetime)
                         ->translatedFormat('l/d, \a \l\a\s G:i');
        return "⚠️ Ya estabas inscrito en la actividad {$activitySchedule->activity->name} para el {$dateFormatted}.";
    }

    private function getNoSlotsAvailableErrorMessage(ActivitySchedule $activitySchedule): string
    {
        $dateFormatted = Carbon::parse($activitySchedule->start_datetime)
                         ->translatedFormat('l/d, \a \l\a\s G:i');
        return "⚠️ No hay cupos disponibles para la actividad {$activitySchedule->activity->name} el día {$dateFormatted}.";
    }

    public function test_authorized_user_can_enroll_in_an_activity_schedule(): void
    {
        foreach ($this->getAuthorizedRoles(self::PERMISSION_ENROLL_USER) as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole);

            $response = $this->performEnrollmentRequest($this->activitySchedule);

            $response->assertStatus(302);
            $response->assertRedirect(route(self::ROUTE_INDEX));
            $response->assertSessionHas('success');

            $this->assertDatabaseHas('activity_schedule_user', [
                'user_id' => $user->id,
                'activity_schedule_id' => $this->activitySchedule->id,
            ]);
            $this->activitySchedule->refresh();
            $this->assertEquals(1, $this->activitySchedule->current_enrollment);
        }
    }

    public function test_unauthorized_user_cannot_enroll_in_an_activity_schedule(): void
    {
        foreach ($this->getUnauthorizedRoles(self::PERMISSION_ENROLL_USER) as $unauthorizedRole) {
            $this->actingAsRole($unauthorizedRole);

            $response = $this->performEnrollmentRequest($this->activitySchedule);

            $response->assertStatus(403);
            $this->assertDatabaseMissing('activity_schedule_user', [
                'activity_schedule_id' => $this->activitySchedule->id,
            ]);
        }
    }

    public function test_authorized_user_cannot_enroll_in_an_activity_schedule_twice(): void
    {
        foreach ($this->getAuthorizedRoles(self::PERMISSION_ENROLL_USER) as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole); 

            $firstEnrollmentResponse = $this->performEnrollmentRequest($this->activitySchedule);
            $firstEnrollmentResponse->assertStatus(302);
            $firstEnrollmentResponse->assertSessionHas('success');

            $this->assertDatabaseHas('activity_schedule_user', [
                'user_id' => $user->id,
                'activity_schedule_id' => $this->activitySchedule->id,
            ]);
            $this->activitySchedule->refresh();
            $this->assertEquals(1, $this->activitySchedule->current_enrollment);

            $secondEnrollmentResponse = $this->performEnrollmentRequest($this->activitySchedule);
            $secondEnrollmentResponse->assertStatus(302);
            $secondEnrollmentResponse->assertSessionHas(
                'error', 
                $this->getAlreadyEnrolledErrorMessage($this->activitySchedule)
                );

            $this->assertDatabaseCount('activity_schedule_user', 1);
            $this->activitySchedule->refresh();
            $this->assertEquals(1, $this->activitySchedule->current_enrollment);
        }
    }

    public function test_authorized_user_cannot_enroll_if_no_slots_available(): void
    {
        $activitySchedule = ActivitySchedule::factory()->create([
            'activity_id' => $this->activity->id,
            'room_id' => $this->room->id,
            'max_enrollment' => 1,    
            'current_enrollment' => 1,
        ]);

        foreach ($this->getAuthorizedRoles(self::PERMISSION_ENROLL_USER) as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole); 

            $response = $this->performEnrollmentRequest($activitySchedule);

            $response->assertStatus(302);
            $response->assertSessionHas(
                'error', 
                $this->getNoSlotsAvailableErrorMessage($activitySchedule)
                );

            $this->assertDatabaseMissing('activity_schedule_user', [
                'user_id' => $user->id,
                'activity_schedule_id' => $activitySchedule->id,
            ]);
            $activitySchedule->refresh();
            $this->assertEquals(1, $activitySchedule->current_enrollment);
        }
    }

    public function test_authorized_user_cannot_enroll_in_another_activity_schedule_at_the_same_time(): void
    {
        $otherRoom = Room::factory()->create();
        $otherActivity = Activity::factory()->create();
        $conflictingActivitySchedule = ActivitySchedule::factory()->create([
            'activity_id' => $otherActivity->id,
            'room_id' => $otherRoom->id,
            'start_datetime' => $this->activitySchedule->start_datetime,
            'end_datetime' => $this->activitySchedule->end_datetime,
            'max_enrollment' => 10,
            'current_enrollment' => 0,
        ]);

        foreach (
            $this->getAuthorizedRoles(self::PERMISSION_ENROLL_USER) 
            as $authorizedRole
        ) {
            $user = $this->actingAsRole($authorizedRole); 

            $this->performEnrollmentRequest($this->activitySchedule);

            $response = $this->performEnrollmentRequest($conflictingActivitySchedule);
            $response->assertStatus(302);
            $response->assertSessionHas(
                'error', 
                "⚠️ Ya estabas inscrito en otra sala para la misma fecha/hora: " . 
                Carbon::parse($this->activitySchedule->start_datetime)
                    ->translatedFormat('l/d, \a \l\a\s G:i') . '.'
                );

            $this->assertDatabaseMissing('activity_schedule_user', [
                'user_id' => $user->id,
                'activity_schedule_id' => $conflictingActivitySchedule->id,
            ]);
        }
    }

}