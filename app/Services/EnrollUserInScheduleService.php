<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActivitySchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollUserInScheduleService
{
    /**
     * Attempts to enroll the user in the specified activity schedule.
     * This method performs several validation checks before attempting enrollment.
     *
     * @param ActivitySchedule $activitySchedule The activity schedule to enroll the user in.
     * @return array An associative array containing the 'status' ('success' or 'error') and a 'message'.
     */
    public function __invoke(ActivitySchedule $activitySchedule): array
    {
        $userId       = Auth::id();
        $date         = $this->
                         formatDate($activitySchedule->start_datetime);
        $activityScheduleName = $activitySchedule->activity->name;

        if (
            $error = $this->validate($activitySchedule, $userId, $date, $activityScheduleName)
        ) {
            return $error;
        }

        return $this->performEnrollment($activitySchedule, $userId, $activityScheduleName, $date);
    }

    /**
     * Performs several validation checks before attempting to enroll the user in the activity schedule.
     * Checks include:
     * - If the current enrollment count has reached the maximum enrollment count.
     * - If the user is already enrolled in the activity schedule.
     * - If the user is already enrolled in another activity schedule at the same date and time.
     * If any of these checks fail, an associative array is returned with 'status' => 'error' and a 'message' with an error message.
     * If all checks pass, null is returned.
     *
     * @param ActivitySchedule $activitySchedule The activity schedule to check.
     * @param int $userId The ID of the user to check.
     * @param string $date The date of the activity schedule.
     * @param string $activityScheduleName The name of the activity schedule.
     * @return array|null An associative array with 'status' and 'message' if an error occurred, or null if all checks passed.
     */
    private function validate(ActivitySchedule $activitySchedule, int $userId, string $date, string $activityScheduleName): ?array
    {
        $checks = [
            [$activitySchedule->current_enrollment >= $activitySchedule->max_enrollment,
             "⚠️ No hay cupos disponibles para la actividad {$activityScheduleName} el día {$date}."],
            [$activitySchedule->users->contains($userId),
             "⚠️ Ya estabas inscrito en la actividad {$activityScheduleName} para el {$date}."],
            [$this->hasConflictInOtherRoom($activitySchedule, $userId),
             "⚠️ Ya estabas inscrito en otra sala para la misma fecha/hora: {$date}."],
        ];

        foreach ($checks as [$fail, $msg]) {
            if ($fail) {
                return ['status' => 'error', 'message' => $msg];
            }
        }

        return null;
    }

    /**
     * Enrolls the user in the activity schedule, increments the current enrollment count,
     * and returns a success message with the activity name and the date.
     *
     * @param ActivitySchedule $activitySchedule The activity schedule to enroll the user in.
     * @param int $userId The ID of the user to enroll.
     * @param string $activityScheduleName The name of the activity schedule.
     * @param string $date The date of the activity schedule.
     * @return array An associative array with 'status' => 'success' and a 'message' with the success message.
     */
    private function performEnrollment(ActivitySchedule $activitySchedule, int $userId, string $activityScheduleName, string $date): array
    {
        $activitySchedule->users()->attach($userId);
        $activitySchedule->increment('current_enrollment');

        return [
            'status'  => 'success',
            'message' => "Te has inscrito correctamente en la actividad {$activityScheduleName} para el día {$date}.",
        ];
    }

    /**
     * Formats a given datetime string into a human-readable format.
     *
     * The format includes the day of the week, day, and time in 24-hour format,
     * with words "a las" in between the date and time.
     *
     * @param string $datetime The datetime string to be formatted.
     * @return string The formatted date string.
     */

    private function formatDate(string $datetime): string
    {
        return Carbon::parse($datetime)
                     ->translatedFormat('l/d, \a \l\a\s G:i');
    }

    /**
     * Checks if the specified user is enrolled in any other activity schedule
     * that occurs at the exact same start datetime but in a different room.
     *
     * @param ActivitySchedule $activitySchedule The current activity schedule being considered.
     * @param int $userId The ID of the user to check.
     * @return bool True if the user is enrolled in another schedule at the same time in a different room, false otherwise.
     */
    private function hasConflictInOtherRoom(ActivitySchedule $activitySchedule, int $userId): bool
    {
        return DB::table('activity_schedule_user')
            ->where('user_id', $userId)
            ->whereIn('activity_schedule_id', function ($q) use ($activitySchedule) {
                $q->select('id')
                  ->from('activity_schedules')
                  ->where('start_datetime', $activitySchedule->start_datetime)
                  ->where('room_id', '!=', $activitySchedule->room_id);
            })->exists();
    }
}
