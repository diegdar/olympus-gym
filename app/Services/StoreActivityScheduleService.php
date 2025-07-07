<?php
declare(strict_types=1);

namespace App\Services;
use Illuminate\Http\Request;
use App\Models\ActivitySchedule;
use App\Models\Activity;
use Carbon\Carbon;

class StoreActivityScheduleService
{
    public function __invoke(Request $request)
    {  
        $activity = Activity::findOrFail((int) $request->activity_id);
        $startDatetime = $this
            ->getStartDatetime($request->start_datetime);
        $endDatetime = $this
            ->calculateEndDatetime($startDatetime, $activity->duration);
        
        $data = [
            'activity_id' => $activity->id,
            'start_datetime' => $startDatetime,
            'end_datetime' => $endDatetime,
            'room_id' => $request->room_id,
            'max_enrollment' => $request->max_enrollment,
        ];

        ActivitySchedule::create($data);
    }

    private function getStartDatetime(string $datetime): Carbon
    {
        return Carbon::parse($datetime);
    }    

    private function calculateEndDatetime(Carbon $startDatetime, int $duration): Carbon
    {
        return $startDatetime->copy()
            ->addMinutes($duration);
    }
}
