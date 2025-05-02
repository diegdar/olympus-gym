<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hour = fake()->numberBetween(7, 21);
        $minute = fake()->randomElement([0, 30]);
        $startTime = sprintf('%02d:%02d', $hour, $minute);
        $dayOfWeek = fake()->randomElement(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']);
        $room = Room::all()->random();

        return [ 
            'day_of_week' => $dayOfWeek,
            'start_time' => $startTime,
            'room_id' => $room->id,
        ];
    }

}
