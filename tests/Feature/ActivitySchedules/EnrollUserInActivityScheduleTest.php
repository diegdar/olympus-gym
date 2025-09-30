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
use Database\Seeders\ActivitySeeder;
use Database\Seeders\RoleSeeder;

class EnrollUserInActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, ActivitySeeder::class]);
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

    private function createAnActivitySchedule(?string $startDatetime = null, int $maxEnrollment = 10): ActivitySchedule
    {
      $activity = Activity::factory()->create();
      $room = Room::factory()->create();
        return ActivitySchedule::factory()->create([
            'activity_id' => $activity->id,
            'room_id' => $room->id,
            'max_enrollment' => $maxEnrollment,
            'start_datetime' => $startDatetime?? Carbon::now(),
        ]);
    }

    public function test_authorized_user_can_enroll_in_an_activity_schedule(): void
    {
        $activitySchedule = $this->createAnActivitySchedule();

        foreach ($this->getAuthorizedRoles('activity.schedules.enroll') as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole);

            $response = $this->performEnrollmentRequest($activitySchedule);

            $response->assertStatus(302);
            $response->assertRedirect(route('activity.schedules.index'));
            $response->assertSessionHas('success');

            $this->assertDatabaseHas('activity_schedule_user', [
                'user_id' => $user->id,
                'activity_schedule_id' => $activitySchedule->id,
            ]);
            $activitySchedule->refresh();
            $this->assertEquals(1, $activitySchedule->users()->count());
        }
    }

    public function test_unauthorized_user_cannot_enroll_in_an_activity_schedule(): void
    {
        $activitySchedule = $this->createAnActivitySchedule();

        foreach ($this->getUnauthorizedRoles('activity.schedules.enroll') as $unauthorizedRole) {
            $this->actingAsRole($unauthorizedRole);

            $response = $this->performEnrollmentRequest($activitySchedule);

            $response->assertStatus(403);
            $this->assertDatabaseMissing('activity_schedule_user', [
                'activity_schedule_id' => $activitySchedule->id,
            ]);
        }
    }

    public function test_authorized_user_cannot_enroll_in_an_activity_schedule_twice(): void
    {
        $activitySchedule = $this->createAnActivitySchedule();

        foreach ($this->getAuthorizedRoles('activity.schedules.enroll') as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole); 

            $firstEnrollmentResponse = $this->performEnrollmentRequest($activitySchedule);
            $firstEnrollmentResponse->assertStatus(302);
            $firstEnrollmentResponse->assertSessionHas('success');

            $this->assertDatabaseHas('activity_schedule_user', [
                'user_id' => $user->id,
                'activity_schedule_id' => $activitySchedule->id,
            ]);
            $activitySchedule->refresh();
            $this->assertEquals(1, $activitySchedule->users()->count());

            $secondEnrollmentResponse = $this->performEnrollmentRequest($activitySchedule);
            $secondEnrollmentResponse->assertStatus(302);
            $secondEnrollmentResponse->assertSessionHas(
                'error', 
                $this->getAlreadyEnrolledErrorMessage($activitySchedule)
                );

            $this->assertDatabaseCount('activity_schedule_user', 1);
            $activitySchedule->refresh();
            $this->assertEquals(1, $activitySchedule->users()->count());
        }
    }

    public function test_authorized_user_cannot_enroll_if_no_slots_available(): void
    {
        $activitySchedule = $this->createAnActivitySchedule(maxEnrollment: 1);

        foreach ($this->getAuthorizedRoles('activity.schedules.enroll') as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole); 

            // ocupar la plaza con otro usuario para simular lleno
            $other = User::factory()->create()->assignRole('member');
            $activitySchedule->users()->attach($other->id);

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
            $this->assertEquals(1, $activitySchedule->users()->count());
        }
    }

    public function test_authorized_user_cannot_enroll_in_another_activity_schedule_at_the_same_time(): void
    {
        $activitySchedule = $this->createAnActivitySchedule(startDatetime: '2023-01-01 10:00');
        $conflictingActivitySchedule = $this->createAnActivitySchedule(startDatetime: '2023-01-01 10:00');

        foreach (
            $this->getAuthorizedRoles('activity.schedules.enroll')
            as $authorizedRole
        ) {
            $user = $this->actingAsRole($authorizedRole); 

            $this->performEnrollmentRequest($activitySchedule);

            $response = $this->performEnrollmentRequest($conflictingActivitySchedule);
            $response->assertStatus(302);
            $response->assertSessionHas(
                'error', 
                "⚠️ Ya estabas inscrito en otra sala para la misma fecha/hora: " . 
                Carbon::parse($activitySchedule->start_datetime)
                    ->translatedFormat('l/d, \a \l\a\s G:i') . '.'
                );

            $this->assertDatabaseMissing('activity_schedule_user', [
                'user_id' => $user->id,
                'activity_schedule_id' => $conflictingActivitySchedule->id,
            ]);
        }
    }

}