<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShowActivityScheduleService
{        
    /**
     * Retrieve a specific activity schedule with related activity and room details.
     *
     * @param int $activityScheduleId The ID of the activity schedule to retrieve.
     * @return \stdClass|null An object containing the activity schedule details or null if not found.
     */
    public function __invoke(int $activityScheduleId): ?\stdClass
    {
        $activitySchedule = DB::table('activity_schedules as asch')
            ->join('activities as act', 'asch.activity_id', '=', 'act.id')
            ->join('rooms as r', 'asch.room_id', '=', 'r.id')
            ->where('asch.id', $activityScheduleId)
            ->select(
                'asch.id as activity_schedule_id',
                'asch.start_datetime as start_datetime_raw', 
                'asch.end_datetime as end_datetime_raw',
                'act.id as activity_id',
                'act.name as activity_name',
                'act.duration',
                'asch.max_enrollment',
                'asch.current_enrollment',
                'r.id as room_id',
                'r.name as room_name'
            )
            ->first();

        if ($activitySchedule) {
            $activitySchedule->start_time_formatted = Carbon::parse($activitySchedule->start_datetime_raw)->format('G:i');
            $activitySchedule->end_time_formatted = Carbon::parse($activitySchedule->end_datetime_raw)->format('G:i');            
            $activitySchedule->day_date_formatted = Carbon::parse($activitySchedule->start_datetime_raw)->translatedFormat('l, d F');
            
            $activitySchedule->available_slots = 
                        $activitySchedule->max_enrollment - $activitySchedule->current_enrollment;
        }

        return $activitySchedule;
    }
}