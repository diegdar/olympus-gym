<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivitySchedules>
 */
class ActivitySchedulesFactory extends Factory
{

    /**
     * Hours between which activities can start.
     */
    private const START_HOUR = 7;
    private const END_HOUR = 21;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $room = Room::factory()->create();
        $activity = Activity::factory()->create();
        $startHour = rand(self::START_HOUR, self::END_HOUR - 1);
        $startMinute = fake()->randomElement([0, 30]);
        $startTime = now()->setHour($startHour)->setMinute($startMinute)->setSecond(0);
        $endTime = $startTime->addHours(fake()->randomElement([30, 45, 60]));
        $maxEnrollment = fake()->numberBetween(30, 50);
        return [
            'activity_id' => $activity->id,
            'start_datetime' => $startTime,
            'room_id' => $room->id,
            'end_datetime' => $endTime,
            'max_enrollment' => $maxEnrollment,
            'current_enrollment' => 0,
        ];
    }
}
