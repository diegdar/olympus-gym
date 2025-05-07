<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     $numberOfSchedules = 10;

    //     for ($i = 0; $i < $numberOfSchedules; $i++) {
    //         $schedule = $this->getAnUniqueSchedule();
    //         Schedule::create($schedule->getAttributes());
    //     }

    // }

    // /**
    //  * Generate a unique schedule by ensuring the combination of day_of_week, 
    //  * start_time, and room_id does not already exist in the database.
    //  *
    //  * @return Model A unique Schedule model instance.
    //  */
    // private function getAnUniqueSchedule(): Model
    // {
    //     do {
    //         $ScheduleFactory = Schedule::factory()->make();

    //         $existingSchedule = Schedule::query() 
    //             ->where('day_of_week', $ScheduleFactory->day_of_week)
    //             ->where('start_time', $ScheduleFactory->start_time)
    //             ->where('room_id', $ScheduleFactory->room_id)
    //             ->exists();

    //     } while ($existingSchedule);

    //     return $ScheduleFactory;
    // }

    public function run(): void
    {
        $daysOfWeek = [
            'Lunes',
            'Martes',
            'Miércoles',
            'Jueves',
            'Viernes',
            'Sábado',
        ];

        $classesPerDay = 6;

        foreach ($daysOfWeek as $day) {
            // Generar al menos 6 horarios únicos para cada día
            $generatedSchedules = [];
            while (count($generatedSchedules) < $classesPerDay) {
                $schedule = $this->getAnUniqueScheduleForDay($day, $generatedSchedules);
                $generatedSchedules[] = $schedule->getAttributes();
                Schedule::create($schedule->getAttributes());
            }
        }
    }

    /**
     * Generate a unique schedule for a specific day by ensuring the combination of
     * start_time and room_id does not already exist for that day in the database
     * and in the currently generated schedules for that day.
     *
     * @param  string  $dayOfWeek The day of the week for the schedule.
     * @param  array  $existingSchedulesForDay Already generated schedules for the current day.
     * @return Model A unique Schedule model instance.
     */
    private function getAnUniqueScheduleForDay(string $dayOfWeek, array $existingSchedulesForDay): Model
    {
        do {
            $scheduleFactory = Schedule::factory()->make(['day_of_week' => $dayOfWeek]);

            $isDuplicateInDatabase = Schedule::query()
                ->where('day_of_week', $dayOfWeek)
                ->where('start_time', $scheduleFactory->start_time)
                ->where('room_id', $scheduleFactory->room_id)
                ->exists();

            $isDuplicateInCurrent = collect($existingSchedulesForDay)->contains(function ($schedule) use ($scheduleFactory) {
                return $schedule['day_of_week'] === $scheduleFactory->day_of_week &&
                       $schedule['start_time'] === $scheduleFactory->start_time &&
                       $schedule['room_id'] === $scheduleFactory->room_id;
            });

        } while ($isDuplicateInDatabase || $isDuplicateInCurrent);

        return $scheduleFactory;
    }
    
}
