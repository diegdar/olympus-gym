<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivitySchedules;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ActivitySchedulesSeeder extends Seeder
{

    /**
     * The days of the week to schedule activities.
     *
     * @var array<string>
     */
    protected array $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

    /**
     * The first hour of the day to schedule activities.
     *
     * @var int
     */
    protected int $firstHour = 7;

    /**
     * The last hour of the day to schedule activities.
     *
     * @var int
     */
    protected int $lastHour = 21;

    /**
     * The minimum number of activities to schedule per day.
     *
     * @var int
     */
    protected $minActivitiesPerDay = 5;

    /**
     * The maximum number of activities to schedule per day.
     *
     * @var int
     */
    protected $maxActivitiesPerDay = 8;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = Activity::all();
        $rooms = Room::all();

        foreach ($this->daysOfWeek as $day) {
            $this->createDailySchedules($activities, $rooms, $day);
        }
    }

    /**
     * Creates a set of unique activity schedules for a given day.
     *
     * @param Collection<int, Activity> $activities The collection of available activities.
     * @param Collection<int, Room> $rooms The collection of available rooms.
     * @param string $dayOfWeek The day of the week to create schedules for.
     * @return void
     */
    protected function createDailySchedules(Collection $activities, Collection $rooms, string $dayOfWeek): void
    {
        $numberOfActivities = rand($this->minActivitiesPerDay, $this->maxActivitiesPerDay);
        $generatedSchedules = collect();

        for ($i = 0; $i < $numberOfActivities; $i++) {
            $activity = $activities->random();
            $room = $rooms->random();
            $startTime = $this->generateUniqueStartTime($dayOfWeek, $room, $generatedSchedules);
            $endTime = $this->generateEndTime($startTime);
            $maxEnrollment = rand(10, 30);
            $currentEnrollment = rand(0, $maxEnrollment);

            ActivitySchedules::create([
                'activity_id' => $activity->id,
                'day_of_week' => $dayOfWeek,
                'room_id' => $room->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'max_enrollment' => $maxEnrollment,
                'current_enrollment' => $currentEnrollment,
            ]);

            $generatedSchedules->push([
                'start_time' => $startTime,
                'room_id' => $room->id,
                'day_of_week' => $dayOfWeek,
            ]);
        }
    }

/**
     * Generates a unique start time for a given day and room, ensuring no overlaps using a do...while loop.
     *
     * @param string $dayOfWeek The day of the week.
     * @param Room $room The room where the activity will take place.
     * @param Collection $existingSchedules The collection of already generated schedules for the current day.
     * @return string The unique start time in 'H:i:s' format.
     */
    protected function generateUniqueStartTime(string $dayOfWeek, Room $room, Collection $existingSchedules): string
    {
        $startTime = null;
        $isDuplicate = true;

        do {
            $startHour = rand($this->firstHour, $this->lastHour);
            $startMinute = fake()->randomElement([0, 30]);
            $startTime = Carbon::createFromTime($startHour, $startMinute)->format('H:i:s');

            $isDuplicate = $existingSchedules->contains(function ($schedule) use ($startTime, $room, $dayOfWeek) {
                return $schedule['start_time'] === $startTime &&
                       $schedule['room_id'] === $room->id &&
                       $schedule['day_of_week'] === $dayOfWeek;
            });

        } while ($isDuplicate);

        return $startTime;
    }

    /**
     * Generates an end time that is at least 30 minutes after the start time.
     *
     * @param string $startTime The start time in 'H:i:s' format.
     * @return string The end time in 'H:i:s' format.
     */
    protected function generateEndTime(string $startTime): string
    {
        $start = Carbon::parse($startTime);
        $durationMinutes = fake()->randomElement([30, 45, 60]); // Activities last between 30 and 120 minutes
        // $durationMinutes = rand(30, 120); // Activities last between 30 and 120 minutes
        return $start->addMinutes($durationMinutes)->format('H:i:s');
    }
}
