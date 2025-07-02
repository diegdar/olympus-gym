<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use Tests\TestCase;
use App\Models\ActivitySchedule;
use App\Models\Activity;
use App\Models\Room;
use App\Models\User;

class UnenrollUserInActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    // Permissions and Routes
    protected const PERMISSION_LIST = 'activity.schedules.index';
    protected const PERMISSION_UNENROLL_USER = 'activity.schedules.unenroll';
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

    private function performUnenrollmentRequest(ActivitySchedule $activitySchedule)
    {
        return $this->post(route('activity.schedules.unenroll', $activitySchedule));
    }

    

}
