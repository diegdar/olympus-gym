<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ActivitySchedule;
use App\Models\User;
use App\Services\ActivityScheduleAttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityScheduleAttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_enrolled_users_returns_expected_shape(): void
    {
        $schedule = ActivitySchedule::factory()->create();
        $user = User::factory()->create();
        $schedule->users()->attach($user->id, ['attended' => true]);

        $service = new ActivityScheduleAttendanceService();
        $rows = $service->getEnrolledUsers($schedule);

        $this->assertCount(1, $rows);
        $first = $rows->first();
        $this->assertSame($user->id, $first['id']);
        $this->assertTrue($first['attended']);
    }

    public function test_update_attendance_updates_pivot_flags(): void
    {
        $schedule = ActivitySchedule::factory()->create();
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();
        $schedule->users()->attach($u1->id, ['attended' => false]);
        $schedule->users()->attach($u2->id, ['attended' => false]);

        $service = new ActivityScheduleAttendanceService();
        $service->updateAttendance($schedule, [
            ['id' => $u1->id, 'attended' => true],
            ['id' => $u2->id, 'attended' => false],
        ]);

        $this->assertDatabaseHas('activity_schedule_user', [
            'activity_schedule_id' => $schedule->id,
            'user_id' => $u1->id,
            'attended' => 1,
        ]);
        $this->assertDatabaseHas('activity_schedule_user', [
            'activity_schedule_id' => $schedule->id,
            'user_id' => $u2->id,
            'attended' => 0,
        ]);
    }
}
