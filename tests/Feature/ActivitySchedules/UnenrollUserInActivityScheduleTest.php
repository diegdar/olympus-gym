<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use Tests\TestCase;
use App\Models\ActivitySchedule;
use App\Models\User;

class UnenrollUserInActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    // Permissions
    protected const PERMISSION_UNENROLL_USER = 'activity.schedules.unenroll';
    protected const PERMISSION_ENROLL_USER = 'activity.schedules.enroll';
    // Routes
    protected const ROUTE_INDEX = 'activity.schedules.index';
    protected const ROUTE_ENROLL = 'activity.schedules.enroll';
    protected const ROUTE_UNENROLL = 'activity.schedules.unenroll';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function performEnrollmentRequest(ActivitySchedule $activitySchedule, User $user): void
    {
        $this->post(route(self::ROUTE_ENROLL, $activitySchedule))
            ->assertStatus(302)
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activity_schedule_user', [
            'user_id' => $user->id,
            'activity_schedule_id' => $activitySchedule->id,
        ]);

        $activitySchedule->refresh();
        $this->assertEquals(1, $activitySchedule->current_enrollment);  
    }    

    private function performUnenrollmentRequest(ActivitySchedule $activitySchedule)
    {
        return $this->delete(route(self::ROUTE_UNENROLL, $activitySchedule));
    }

    public function test_authorized_user_can_unenroll_in_an_activity_schedule(): void
    {
        $activitySchedule = ActivitySchedule::factory()->create();
        
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION_UNENROLL_USER)
            as $authorizedRole
        ) {
            $user = $this->createUserAndAssignRole($authorizedRole);

            $this->performEnrollmentRequest($activitySchedule, $user);           

             $this->performUnenrollmentRequest($activitySchedule)
                  ->assertStatus(302)
                  ->assertRedirect(route(self::ROUTE_INDEX))
                  ->assertSessionHas('success');

            $this->assertDatabaseMissing('activity_schedule_user', [
                'user_id' => $user->id,
                'activity_schedule_id' => $activitySchedule->id,
            ]);

            $activitySchedule->refresh();
            $this->assertEquals(0, $activitySchedule->current_enrollment);
        }
    }

    public function test_unauthorized_user_cannot_unenroll_in_an_activity_schedule(): void
    {
        $activitySchedule = ActivitySchedule::factory()->create();
        $enrolledUser = $this->createUserAndAssignRole(
                $this->getAuthorizedRoles(self::PERMISSION_ENROLL_USER)[0]);
        $this->performEnrollmentRequest($activitySchedule, $enrolledUser);

        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION_UNENROLL_USER)
            as $unauthorizedRole
        ) {
            $this->createUserAndAssignRole($unauthorizedRole);

            $this->performUnenrollmentRequest($activitySchedule);
            $this->assertDatabaseHas('activity_schedule_user', [
                'activity_schedule_id' => $activitySchedule->id,
                'user_id' => $enrolledUser->id,
            ]);
            
            $activitySchedule->refresh();
            $this->assertEquals(1, $activitySchedule->current_enrollment);
        }
    }

}
