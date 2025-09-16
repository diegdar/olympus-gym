<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\OperationHours;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivitySchedule>
 */
class ActivityScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $room = Room::factory()->create();
        $activity = Activity::factory()->create();

        $startHour = rand( 
            OperationHours::START_HOUR->value,
         OperationHours::END_HOUR->value - 1
        );
        $startMinute = fake()->randomElement([0, 30]);
        $startTime = now()->setHour($startHour)->setMinute($startMinute)->setSecond(0);
        $formatedStartTime = $startTime->format('Y-m-d H:i:s');
        $endTime = $startTime->copy()->addMinutes((int) $activity->duration);
        $formatedEndTime = $endTime->format('Y-m-d H:i:s');
        $maxEnrollment = fake()->numberBetween(30, 50);
        
        return [
            'activity_id' => $activity->id,
            'start_datetime' => $formatedStartTime,
            'room_id' => $room->id,
            'end_datetime' => $formatedEndTime,
            'max_enrollment' => $maxEnrollment,
        ];
    }
}
