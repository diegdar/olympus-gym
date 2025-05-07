<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class ActivityScheduleListService
{
    // public function __invoke()
    // {
    //     return $this->createScheduleMatrix();      
    // }

    public function getActivityScheduleRecords(): Collection
    {
        return DB::table('activity_schedule as asch')
        ->join('schedules as sch', 'asch.schedule_id', '=', 'sch.id')
        ->join('activities as act', 'asch.activity_id', '=', 'act.id')
        ->join('rooms as r', 'sch.room_id', '=', 'r.id')
        ->select(
            'asch.id as pivot_id',
            'act.id as activity_id',
            'act.name as activity_name',
            'act.duration',
            'sch.id as schedule_id',
            'sch.day_of_week',
            'sch.start_time',
            'asch.end_time',
            'asch.max_enrollment',
            'asch.current_enrollment',
            'r.id as room_id',            
            'r.name as room_name'            
        )->get();
    }

    public function createScheduleMatrix(array $daysOfWeek, array $timeSlots): array
    {
        $entries = $this->getActivityScheduleRecords();
        // $daysOfWeek = $entries->pluck('day_of_week')->unique()->toArray();
        // $timeSlots  = $entries->pluck('start_time')->unique()->sort()->toArray();        
        $scheduleMatrix = [];

        foreach ($daysOfWeek as $day) {
            foreach ($timeSlots as $time) {
                $scheduleMatrix[$day][$time] = [];
            }
        }

        foreach ($entries as $entry) {
            $scheduleMatrix[$entry->day_of_week][$entry->start_time][] = [
                'pivot_id' => $entry->pivot_id,
                'activity_id' => $entry->activity_id,
                'activity_name' => $entry->activity_name,
                'duration' => $entry->duration,
                'schedule_id' => $entry->schedule_id,
                'end_time' => $entry->end_time,
                'max_enrollment' => $entry->max_enrollment,
                'current_enrollment' => $entry->current_enrollment,
                'room_id' => $entry->room_id,
                'room_name' => $entry->room_name,
                'start_time' => $entry->start_time,
                'day_of_week' => $entry->day_of_week,
            ];
        }

        return $scheduleMatrix;
    }

}
