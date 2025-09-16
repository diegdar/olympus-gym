<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\ActivitySchedule;
use App\Models\Activity;
use Carbon\Carbon;

class UpdateActivityScheduleService
{
    public function __invoke(Request $request, ActivitySchedule $activitySchedule): void
    {
        $activity = Activity::findOrFail((int) $request->activity_id);
        $startDatetime = $this->getStartDatetime((string) $request->start_datetime);
        $endDatetime = $request->filled('end_datetime')
            ? Carbon::parse((string) $request->end_datetime)
            : $this->calculateEndDatetime($startDatetime, (int) $activity->duration);

        $data = [
            'activity_id' => $activity->id,
            'start_datetime' => $startDatetime,
            'end_datetime' => $endDatetime,
            'room_id' => (int) $request->room_id,
            'max_enrollment' => (int) $request->max_enrollment,
        ];

        $activitySchedule->update($data);
    }

    private function getStartDatetime(string $datetime): Carbon
    {
        return Carbon::parse($datetime);
    }

    private function calculateEndDatetime(Carbon $startDatetime, int $duration): Carbon
    {
        return $startDatetime->copy()->addMinutes($duration);
    }
}
