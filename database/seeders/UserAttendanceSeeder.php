<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\OperationHours;
use App\Models\Activity;
use App\Models\ActivitySchedule;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserAttendanceSeeder extends Seeder
{
    /**
     * Seed attended classes for the configured member over the last 4 full weeks.
     * - Finds the user by env('MEMBER_EMAIL'). If not found, it returns early.
     * - For each of the last 4 full weeks (Mon-Sun), it ensures 2-3 schedules exist in that week
     *   and attaches the user with attended=true, picking non-conflicting random slots.
     */
    public function run(): void
    {
        if (app()->environment('testing')) {
            $this->command?->warn('Skipping UserAttendanceSeeder in testing environment.');
            return;
        }
        $email = (string) env('MEMBER_EMAIL');
        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->command?->warn("User not found for MEMBER_EMAIL='{$email}', skipping UserAttendanceSeeder.");
            return;
        }

        // Work over the last 4 full weeks (excluding current partial week)
        $startOfThisWeek = Carbon::now()->startOfWeek();
        for ($w = 1; $w <= 4; $w++) {
            $weekStart = $startOfThisWeek->copy()->subWeeks($w);
            $weekEnd = $weekStart->copy()->endOfWeek();
            $this->seedAttendanceForWeek($user, $weekStart, $weekEnd);
        }
    }

    private function seedAttendanceForWeek(User $user, Carbon $weekStart, Carbon $weekEnd): void
    {
        $targetCount = random_int(2, 3);
        $existing = ActivitySchedule::query()
            ->whereBetween('start_datetime', [$weekStart, $weekEnd])
            ->pluck('id');

        // Attach up to $targetCount existing schedules first
        $attached = $this->attachSome($user, $existing->all(), $targetCount);
        if ($attached >= $targetCount) {
            return;
        }

        // Create additional schedules within the week if not enough
        $toCreate = $targetCount - $attached;
        $this->createAndAttachSchedules($user, $weekStart, $weekEnd, $toCreate);
    }

    private function attachSome(User $user, array $scheduleIds, int $limit): int
    {
        if (empty($scheduleIds) || $limit <= 0) {
            return 0;
        }

        shuffle($scheduleIds);
        $count = 0;
        foreach ($scheduleIds as $scheduleId) {
            if ($count >= $limit) {
                break;
            }
            $already = DB::table('activity_schedule_user')
                ->where('user_id', $user->id)
                ->where('activity_schedule_id', $scheduleId)
                ->exists();
            if ($already) {
                continue;
            }
            DB::table('activity_schedule_user')->insert([
                'activity_schedule_id' => $scheduleId,
                'user_id' => $user->id,
                'attended' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $count++;
        }
        return $count;
    }

    private function createAndAttachSchedules(User $user, Carbon $weekStart, Carbon $weekEnd, int $count): void
    {
        $activities = Activity::all();
        $rooms = Room::all();
        if ($activities->isEmpty() || $rooms->isEmpty()) {
            return; // nothing to create
        }

        $created = 0;
        $existing = collect();
        while ($created < $count) {
            $activity = $activities->random();
            $room = $rooms->random();
            [$start, $end] = $this->pickTimeSlotWithinWeek($weekStart, $weekEnd, $activity->duration, $room, $existing);
            if (! $start) {
                break;
            }
            $maxEnrollment = random_int(10, 30);
            $schedule = ActivitySchedule::create([
                'activity_id' => $activity->id,
                'start_datetime' => $start->toDateTimeString(),
                'room_id' => $room->id,
                'end_datetime' => $end->toDateTimeString(),
                'max_enrollment' => $maxEnrollment,
            ]);

            DB::table('activity_schedule_user')->insert([
                'activity_schedule_id' => $schedule->id,
                'user_id' => $user->id,
                'attended' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $existing->push(compact('room', 'start', 'end'));
            $created++;
        }
    }

    private function pickTimeSlotWithinWeek(Carbon $weekStart, Carbon $weekEnd, int $duration, Room $room, $existing): array
    {
        $attempts = 0;
        $maxAttempts = 50;

        do {
            $dayOffset = random_int(0, 6);
            $baseDay = $weekStart->copy()->addDays($dayOffset);
            $startHour = random_int(OperationHours::START_HOUR->value, OperationHours::END_HOUR->value - 1);
            $startMinute = [0, 30][array_rand([0, 30])];

            $start = $baseDay->copy()->setTime($startHour, $startMinute);
            if ($start->lt($weekStart) || $start->gt($weekEnd)) {
                $attempts++;
                continue;
            }
            $end = $start->copy()->addMinutes($duration);

            $conflict = $existing->contains(fn($slot) =>
                $slot['room']->id === $room->id && ($start->lt($slot['end']) && $end->gt($slot['start']))
            );

            $attempts++;
            if (! $conflict) {
                return [$start, $end];
            }
        } while ($attempts < $maxAttempts);

        return [null, null];
    }
}
