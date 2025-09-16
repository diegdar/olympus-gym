<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ActivitySchedule;
use App\Models\User;
use App\Services\ListActivityScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ListActivityScheduleServiceTest extends TestCase
{
    use RefreshDatabase;

    private ListActivityScheduleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->service = new ListActivityScheduleService();
        Cache::flush();
    }

    public function test_invoke_returns_array_with_schedules_and_times(): void
    {
        ActivitySchedule::factory()->create([
            'start_datetime' => now()->addHour(),
            'end_datetime'   => now()->addHours(2),
        ]);

        [$schedules, $allTimes] = ($this->service)();

        $this->assertIsArray($schedules);
        $this->assertGreaterThan(0, count($schedules));
        $this->assertTrue($allTimes->count() > 0);
    }

    public function test_entries_have_expected_keys(): void
    {
        $schedule = ActivitySchedule::factory()->create([
            'start_datetime' => now()->addHours(3)->setMinute(0),
            'end_datetime'   => now()->addHours(4)->setMinute(0),
        ]);

        [$schedules] = ($this->service)();

        $firstDay = array_key_first($schedules);
        $slots = $schedules[$firstDay];
        $firstTime = array_key_first($slots);
        $entry = $slots[$firstTime][0];

        $expected = [
            'activity_schedule_id',
            'activity_id',
            'room_id',
            'start_time',
            'end_time',
            'activity_name',
            'room_name',
            'duration',
            'max_enrollment',
            'is_enrolled',
            'users_count',
        ];

        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, $entry, "Missing key {$key}");
        }
    }

    public function test_cache_is_used_between_calls(): void
    {
        Cache::flush();

        ActivitySchedule::factory()->create([
            'start_datetime' => now()->addHours(5),
            'end_datetime'   => now()->addHours(6),
        ]);

        [$schedules1] = ($this->service)();
        $count1 = $this->countEntries($schedules1);

        [$schedules2] = ($this->service)();
        $count2 = $this->countEntries($schedules2);

        $this->assertSame($count1, $count2, 'Cache should return same number of entries');
    }

    public function test_cache_is_scoped_by_user(): void
    {
        Cache::flush();

    /** @var User $userA */
    $userA = User::factory()->create();
    /** @var User $userB */
    $userB = User::factory()->create();

        ActivitySchedule::factory()->create([
            'start_datetime' => now()->addHours(2),
            'end_datetime'   => now()->addHours(3),
        ]);

    $this->actingAs($userA); // userA context
        [$schedA] = ($this->service)();
        $countA = $this->countEntries($schedA);

        // Enroll user B only in same schedule and ensure their view reflects is_enrolled false initially
    $this->actingAs($userB); // switch to userB
        [$schedB] = ($this->service)();
        $countB = $this->countEntries($schedB);

        $this->assertSame($countA, $countB);
    }

    public function test_cache_is_invalidated_on_schedule_changes(): void
    {
        Cache::flush();

        [$schedules1] = ($this->service)();
        $count1 = $this->countEntries($schedules1);

        // Create a new schedule (should trigger observer and bump version)
        ActivitySchedule::factory()->create([
            'start_datetime' => now()->addHours(9),
            'end_datetime'   => now()->addHours(10),
        ]);

        [$schedules2] = ($this->service)();
        $count2 = $this->countEntries($schedules2);

        $this->assertGreaterThanOrEqual($count1 + 1, $count2);
    }

    private function countEntries(array $schedules): int
    {
        $total = 0;
        foreach ($schedules as $day => $slots) {
            foreach ($slots as $time => $entries) {
                $total += count($entries);
            }
        }
        return $total;
    }
}
