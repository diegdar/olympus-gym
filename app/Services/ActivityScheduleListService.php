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
        return DB::table('activity_schedules as asch')
        ->join('activities as act', 'asch.activity_id', '=', 'act.id')
        ->join('rooms as r', 'asch.room_id', '=', 'r.id')
        ->select(
            'asch.id as pivot_id',
            'asch.day_of_week',
            'act.id as activity_id',
            'act.name as activity_name',
            'act.duration',
            'asch.start_time',
            'asch.end_time',
            'asch.max_enrollment',
            'asch.current_enrollment',
            'r.id as room_id',            
            'r.name as room_name'            
        )->get();
    }

    public function createScheduleMatrix(Collection $records, array $daysOfWeek, array $timeSlots): array
    {
        // $records = $this->getActivityScheduleRecords();
        // $daysOfWeek = $records->pluck('day_of_week')->unique()->toArray();
        // $timeSlots  = $records->pluck('start_time')->unique()->sort()->toArray();        
        $recordsMatrix = [];

        foreach ($daysOfWeek as $day) {
            foreach ($timeSlots as $time) {
                $recordsMatrix[$day][$time] = [];
            }
        }

        foreach ($records as $record) {
            $recordsMatrix[$record->day_of_week][$record->start_time][] = [
                'pivot_id' => $record->pivot_id,
                'activity_id' => $record->activity_id,
                'room_id' => $record->room_id,
                'day_of_week' => $record->day_of_week,
                'start_time' => $record->start_time,
                'end_time' => $record->end_time,
                'activity_name' => $record->activity_name,
                'room_name' => $record->room_name,
                'duration' => $record->duration,
                'max_enrollment' => $record->max_enrollment,
                'current_enrollment' => $record->current_enrollment,
            ];
        }

        return $recordsMatrix;
    }

}
