<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivitySchedule;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Enums\OperationHours;

class ActivitySchedulesSeeder extends Seeder
{
    /**
     * Activities per day range.
     */
    private const MIN_PER_DAY = 5;
    private const MAX_PER_DAY = 8;

    /**
     * Weekdays to seed (next 7 days).
     */
    private const DAYS_COUNT = 365;

    /**
     * Run the database seeds for activity schedules.
     *
     * This function truncates the existing activity schedules and seeds new schedules
     * for the next 7 days. It retrieves all activities and rooms, and for each day,
     * generates a schedule by calling the seedDay method.
     */
    public function run(): void
    {
        $activities = Activity::all();
        $rooms = Room::all();

        foreach (range(0, self::DAYS_COUNT - 1) as $offset) {
            $date = Carbon::today()->addDays($offset);
            $this->seedDay($date, $activities, $rooms);
        }
    }

    /**
     * Seeds a day with activities.
     *
     * @param Carbon $date the date to seed
     * @param Collection $activities the activities to pick from
     * @param Collection $rooms the rooms to pick from
     */
    private function seedDay(Carbon $date, Collection $activities, Collection $rooms): void
    {
        $slotsNeeded = rand(self::MIN_PER_DAY, self::MAX_PER_DAY);
        $existing = collect();

        while ($existing->count() < $slotsNeeded) {
            $activity = $activities->random();
            $room = $rooms->random();
            [$start, $end] = $this->pickTimeSlot($date, $activity->duration, $room, $existing);
            $maxEnrollment = rand(10, 30);
            $currentEnrollment = rand(0, $maxEnrollment);
            if (! $start) {
                break;
            }

            ActivitySchedule::create([
                'activity_id'       => $activity->id,
                'start_datetime'    => $start->toDateTimeString(),
                'room_id'           => $room->id,
                'end_datetime'      => $end->toDateTimeString(),
                'max_enrollment'    => $maxEnrollment,
                'current_enrollment'=> $currentEnrollment,
            ]);

            $existing->push(compact('room', 'start', 'end'));
        }
    }

    /**
     * Attempt to find a time slot for an activity of a given duration.
     *
     * It will return an array containing a start and end datetime.
     * If no available slot is found after $maxAttempts, it will return [null, null].
     *
     * @param Carbon $date
     * @param int $duration
     * @param Room $room
     * @param Collection $existing
     * @return array
     */
    private function pickTimeSlot(Carbon $date, int $duration, Room $room, Collection $existing): array
    {
        $attempts = 0;
        $maxAttempts = 50;

        do {
            $startHour = rand(OperationHours::START_HOUR->value, OperationHours::END_HOUR->value - 1);
            $startMinute = fake()->randomElement([0, 30]);

            $start = $date->copy()->setTime($startHour, $startMinute);
            $end = $start->copy()->addMinutes($duration);

            $conflict = $existing->contains(fn($slot) =>
                $slot['room']->id === $room->id 
                && ($start->lt($slot['end']) 
                && $end->gt($slot['start']))
            );

            $attempts++;
            if (! $conflict) {
                return [$start, $end];
            }
        } while ($attempts < $maxAttempts);

        return [null, null];
    }
}
