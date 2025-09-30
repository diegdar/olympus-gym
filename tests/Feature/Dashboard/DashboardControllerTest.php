<?php
declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\Activity;
use App\Models\ActivitySchedule;
use App\Models\Room;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_dashboard_view_renders_for_member(): void
    {
        $user = $this->verifiedMember();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertStatus(200)
            ->assertSee('Tus próximas clases');
    }

    public function test_weekly_attendance_endpoint_returns_shape(): void
    {
        $user = $this->verifiedMember();
        $this->seedUserPastAttendances($user); // ensure some data exists

        $json = $this->actingAs($user)
            ->getJson(route('member.stats.weekly-attendance'))
            ->assertOk()
            ->json();

        $this->assertIsArray($json['labels']);
        $this->assertIsArray($json['values']);
        $this->assertCount(8, $json['labels']);
        $this->assertCount(8, $json['values']);
        $this->assertMatchesRegularExpression('/^\d{1,2}\/\d{1,2}$/', $json['labels'][0]);
    }

    public function test_activity_distribution_endpoint_returns_labels_and_values(): void
    {
        $user = $this->verifiedMember();
        $this->seedUserPastAttendances($user, 3);

        $json = $this->actingAs($user)
            ->getJson(route('member.stats.activity-distribution'))
            ->assertOk()
            ->json();

        $this->assertIsArray($json['labels']);
        $this->assertIsArray($json['values']);
        $this->assertNotEmpty($json['labels']);
        $this->assertNotEmpty($json['values']);
        $this->assertSameSize($json['labels'], $json['values']);
    }

    // ----------------- helpers -----------------

    private function verifiedMember(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole('member');

        // attach a subscription so the dashboard can show subscription info
        $subscription = Subscription::factory()->create(['fee' => 'monthly', 'duration' => 1]);
        $user->subscriptions()->attach($subscription->id, [
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
            'payment_date' => now()->subDays(5),
            'status' => 'active',
        ]);

        return $user;
    }

    private function seedUserPastAttendances(User $user, int $weeks = 2): void
    {
        // create a few attended schedules in the last N weeks
        $activities = Activity::factory()->count(2)->create();
        $room = Room::factory()->create();

        for ($w = 1; $w <= $weeks; $w++) {
            $weekStart = Carbon::now()->startOfWeek()->subWeeks($w);
            $start = $weekStart->copy()->addDays(1)->setTime(10, 0);
            $end = $start->copy()->addMinutes((int) $activities->first()->duration);

            $sch = ActivitySchedule::create([
                'activity_id' => $activities->random()->id,
                'start_datetime' => $start->toDateTimeString(),
                'room_id' => $room->id,
                'end_datetime' => $end->toDateTimeString(),
                'max_enrollment' => 20,
            ]);

            $sch->users()->attach($user->id, ['attended' => true]);
        }
    }
}
