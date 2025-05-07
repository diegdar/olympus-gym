<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Ensures:
     * 1. Each activity is assigned between 4 and 6 unique schedules.
     * 2. Each day has at least 6 distinct activities.
     * 3. No activity is scheduled twice in the same room, day, and time.
     * 4. The pivot table fields end_time, max_enrollment, and current_enrollment are all set.
     */
    public function run(): void
    {
        $activities       = Activity::all();
        $allSchedules     = Schedule::all();
        $usedCombinations = collect();

        // 1) Assign 4–6 schedules at random to each activity
        foreach ($activities as $activity) {
            $this->assignRandomSchedulesToActivity($activity, $allSchedules, $usedCombinations);
        }

        // 2) For each day that actually exists in schedules, ensure at least 6 activities
        $daysInUse = $allSchedules
            ->pluck('day_of_week')
            ->unique();

        foreach ($daysInUse as $day) {
            $this->ensureMinimumActivitiesForDay($day, $activities, $allSchedules, $usedCombinations);
        }
    }

    /**
     * Assigns between 4 and 6 schedules at random to a single activity,
     * ensuring no duplicate room/day/time combinations.
     *
     * @param Activity   $activity
     * @param Collection $allSchedules       All available schedules
     * @param Collection $usedCombinations   Tracks already-used activity+schedule keys
     */
    private function assignRandomSchedulesToActivity(Activity $activity, Collection $allSchedules, Collection &$usedCombinations): void
    {
        $targetCount  = rand(4, 6);
        $assigned     = 0;

        while ($assigned < $targetCount) {
            $candidate = $allSchedules->random();

            $key = $this->makeCombinationKey($activity->id, $candidate);
            if (! $usedCombinations->contains($key)) {
                $activity->schedules()->attach($candidate->id, [
                    'end_time'           => $this->calculateEndTime($candidate->start_time, $activity->duration),
                    'max_enrollment'     => rand(10, 30),
                    'current_enrollment' => 0,
                ]);

                $usedCombinations->push($key);
                $assigned++;
            }
        }
    }

    /**
     * Ensures that, for the given day_of_week, at least 6 distinct activities have a schedule.
     *
     * @param string     $dayOfWeek
     * @param Collection $activities
     * @param Collection $allSchedules
     * @param Collection $usedCombinations
     */
    private function ensureMinimumActivitiesForDay(string $dayOfWeek, Collection $activities, Collection $allSchedules, Collection &$usedCombinations): void
    {
        $schedulesForDay = $allSchedules->where('day_of_week', $dayOfWeek);

        // Count how many distinct activities already scheduled this day
        $count = DB::table('activity_schedule')
            ->join('schedules', 'activity_schedule.schedule_id', '=', 'schedules.id')
            ->where('schedules.day_of_week', $dayOfWeek)
            ->distinct('activity_schedule.activity_id')
            ->count('activity_schedule.activity_id');

        // If fewer than 6, fill up
        while ($count < 6) {
            if ($schedulesForDay->isEmpty()) {
                // No schedules exist for this day—nothing we can attach
                break;
            }

            $candidateSchedule = $schedulesForDay->random();
            $activity          = $activities->random();
            $key               = $this->makeCombinationKey($activity->id, $candidateSchedule);

            if (! $usedCombinations->contains($key)) {
                $activity->schedules()->attach($candidateSchedule->id, [
                    'end_time'           => $this->calculateEndTime($candidateSchedule->start_time, $activity->duration),
                    'max_enrollment'     => rand(10, 30),
                    'current_enrollment' => 0,
                ]);

                $usedCombinations->push($key);
                $count++;
            }
        }
    }

    /**
     * Calculate end_time by adding duration (minutes) to start_time.
     *
     * @param string $startTime in 'H:i:s'
     * @param int    $duration  in minutes
     * @return string           End time, formatted 'H:i:s'
     */
    private function calculateEndTime(string $startTime, int $duration): string
    {
        return Carbon::createFromFormat('H:i:s', $startTime)
                     ->addMinutes($duration)
                     ->format('H:i:s');
    }

    /**
     * Build a unique key for an activity+schedule combination,
     * based on activity ID, room, day_of_week, and start_time.
     *
     * @param int      $activityId
     * @param Schedule $schedule
     * @return string
     */
    private function makeCombinationKey(int $activityId, Schedule $schedule): string
    {
        return implode('-', [
            $activityId,
            $schedule->room_id,
            $schedule->day_of_week,
            $schedule->start_time,
        ]);
    }
}
