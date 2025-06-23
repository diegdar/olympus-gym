<?php
declare(strict_types=1);

namespace Tests\Feature\Activities;

use App\Models\ActivitySchedule;
use Carbon\Carbon;
use Tests\Traits\TestHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Testing\TestResponse;

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

}
