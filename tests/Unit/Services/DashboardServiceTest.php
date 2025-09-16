<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Activity;
use App\Models\ActivitySchedule;
use App\Models\Room;
use App\Models\Subscription;
use App\Models\User;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_build_view_data_days_left_is_ceiled_for_future(): void
    {
        $user = $this->memberWithSubscription(endInDays: 2.2);
        $svc = new DashboardService();

        $data = $svc->buildViewData($user);
        $this->assertGreaterThanOrEqual(3, $data['daysLeft']); // 2.2 days -> ceil = 3
    }

    public function test_weekly_attendance_stats_labels_and_values(): void
    {
        $user = $this->memberWithSubscription();
        $this->makeAttendedInPastWeek($user);

        $svc = new DashboardService();
        $res = $svc->weeklyAttendanceStats($user);

        $this->assertCount(8, $res['labels']);
        $this->assertCount(8, $res['values']);
        $this->assertMatchesRegularExpression('/^\d{1,2}\/\d{1,2}$/', $res['labels'][0]);
    }

    public function test_activity_distribution_counts_by_activity(): void
    {
        $user = $this->memberWithSubscription();
        [$yoga, $boxing] = Activity::factory()->count(2)->create();
        $room = Room::factory()->create();

        // 2 yoga attendances, 1 boxing
        $this->createAttendance($user, $yoga, $room, 3);
        $this->createAttendance($user, $yoga, $room, 5);
        $this->createAttendance($user, $boxing, $room, 7);

        $svc = new DashboardService();
        $res = $svc->activityDistribution($user);

        $this->assertContains($yoga->name, $res['labels']);
        $this->assertContains($boxing->name, $res['labels']);
        $this->assertSameSize($res['labels'], $res['values']);
    }

    // -------------- helpers --------------

    private function memberWithSubscription(float $endInDays = 30.0): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('member');
        $subscription = Subscription::factory()->create(['fee' => 'monthly', 'duration' => 1]);
        $user->subscriptions()->attach($subscription->id, [
            'start_date' => now()->subDays(5),
            'end_date' => now()->copy()->addDays((int) ceil($endInDays)),
            'payment_date' => now()->subDays(5),
            'status' => 'active',
        ]);
        return $user;
    }

    private function makeAttendedInPastWeek(User $user): void
    {
        $activity = Activity::factory()->create();
        $room = Room::factory()->create();
        $start = Carbon::now()->startOfWeek()->addDays(2)->setTime(10, 0);
        $end = $start->copy()->addMinutes((int) $activity->duration);

        $sch = ActivitySchedule::create([
            'activity_id' => $activity->id,
            'start_datetime' => $start->toDateTimeString(),
            'room_id' => $room->id,
            'end_datetime' => $end->toDateTimeString(),
            'max_enrollment' => 20,
        ]);
        $sch->users()->attach($user->id, ['attended' => true]);
    }

    private function createAttendance(User $user, $activity, $room, int $daysAgo): void
    {
        $start = Carbon::now()->subDays($daysAgo)->setTime(12, 0);
        $end = $start->copy()->addMinutes((int) $activity->duration);
        $sch = ActivitySchedule::create([
            'activity_id' => $activity->id,
            'start_datetime' => $start->toDateTimeString(),
            'room_id' => $room->id,
            'end_datetime' => $end->toDateTimeString(),
            'max_enrollment' => 20,
        ]);
        $sch->users()->attach($user->id, ['attended' => true]);
    }
}
