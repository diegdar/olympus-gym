<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Carbon\Carbon;
use App\Models\Activity;
use App\Models\ActivitySchedule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Request;

class RoomAvailableRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $roomId = (int) $value;

        $startDatetime = $this->getStartDatetime();
        $activity = $this->getActivity();

        if (!$activity) {
            return;
        }

        $endDatetime = $this->calculateEndDatetime($startDatetime, $activity->duration);

        if ($this->isRoomOccupied($roomId, $startDatetime, $endDatetime)) {
            $fail('La sala seleccionada no está disponible en la fecha y horario especificado.');
        }
    }

    private function getStartDatetime(): Carbon
    {
        return Carbon::parse(Request::input('start_datetime'));
    }

    private function getActivity(): ?Activity
    {
        $activityId = (int) Request::input('activity_id');
        return Activity::find($activityId);
    }

    private function calculateEndDatetime(Carbon $startDatetime, int $durationMinutes): Carbon
    {
        return $startDatetime->copy()->addMinutes($durationMinutes);
    }

    private function isRoomOccupied(int $roomId, Carbon $startDatetime, Carbon $endDatetime): bool
    {
        return ActivitySchedule::where('room_id', $roomId)
            ->where(function ($query) use ($startDatetime, $endDatetime) {
                $query->where('start_datetime', '<', $endDatetime)
                      ->where('end_datetime', '>', $startDatetime);
            })
            ->exists();
    }
}
