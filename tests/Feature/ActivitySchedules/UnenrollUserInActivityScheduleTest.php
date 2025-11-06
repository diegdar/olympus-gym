<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use Database\Seeders\RoleSeeder;
use Tests\TestCase;
use App\Models\ActivitySchedule;
use App\Models\User;

class UnenrollUserInActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function performEnrollmentRequest(ActivitySchedule $activitySchedule, User $user): void
    {
        $this->post(route('activity.schedules.enroll', $activitySchedule))
            ->assertStatus(302)
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activity_schedule_user', [
            'user_id' => $user->id,
            'activity_schedule_id' => $activitySchedule->id,
        ]);

    $activitySchedule->refresh();
    $this->assertEquals(1, $activitySchedule->users()->count());
    }

    private function performUnenrollmentRequest(ActivitySchedule $activitySchedule)
    {
        return $this->delete(route('activity.schedules.unenroll', $activitySchedule));
    }

    public function test_authorized_user_can_unenroll_in_an_activity_schedule(): void
    {
        $activitySchedule = ActivitySchedule::factory()->create();

        foreach (
            $this->getAuthorizedRoles('activity.schedules.unenroll')
            as $authorizedRole
        ) {
            $user = $this->createUserAndSignIn($authorizedRole);

            $this->performEnrollmentRequest($activitySchedule, $user);

             $this->performUnenrollmentRequest($activitySchedule)
                  ->assertStatus(302)
                  ->assertRedirect(route('activity.schedules.index'))
                  ->assertSessionHas('success');

            $this->assertDatabaseMissing('activity_schedule_user', [
                'user_id' => $user->id,
                'activity_schedule_id' => $activitySchedule->id,
            ]);

            $activitySchedule->refresh();
            $this->assertEquals(0, $activitySchedule->users()->count());
        }
    }

    public function test_unauthorized_user_cannot_unenroll_in_an_activity_schedule(): void
    {
        $activitySchedule = ActivitySchedule::factory()->create();
        $enrolledUser = $this->createUserAndSignIn(
                $this->getAuthorizedRoles('activity.schedules.enroll')[0]);
        $this->performEnrollmentRequest($activitySchedule, $enrolledUser);

        foreach (
            $this->getUnauthorizedRoles('activity.schedules.unenroll')
            as $unauthorizedRole
        ) {
            $this->createUserAndSignIn($unauthorizedRole);

            $this->performUnenrollmentRequest($activitySchedule);
            $this->assertDatabaseHas('activity_schedule_user', [
                'activity_schedule_id' => $activitySchedule->id,
                'user_id' => $enrolledUser->id,
            ]);

            $activitySchedule->refresh();
            $this->assertEquals(1, $activitySchedule->users()->count());
        }
    }

}
