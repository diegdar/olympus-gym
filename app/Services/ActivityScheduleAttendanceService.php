<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\ActivitySchedule;

/**
 * ActivityScheduleAttendanceService
 * - Lista inscritos con flag de asistencia
 * - Exporta CSV (compatible Excel es-ES)
 * - Actualiza asistencia en el pivot
 */
class ActivityScheduleAttendanceService
{
    /**
     * @return \Illuminate\Support\Collection<int, array{id:int,name:string,email:string,attended:bool}>
     */
    public function getEnrolledUsers(ActivitySchedule $schedule)
    {
        return $schedule->users()
            ->select(['users.id','users.name','users.email','activity_schedule_user.attended'])
            ->orderBy('users.name')
            ->get()
            ->map(fn($u)=>[
                'id' => (int)$u->id,
                'name' => (string)$u->name,
                'email' => (string)$u->email,
                'attended' => (bool)$u->attended,
            ]);
    }

    public function exportCsv(ActivitySchedule $schedule)
    {
        $rows = $this->getEnrolledUsers($schedule);
        $filename = 'usuarios_inscritos_schedule_'.$schedule->id.'_'.date('Ymd_His').'.csv';
        $handle = fopen('php://temp', 'w+');
        // BOM UTF-8
        fwrite($handle, "\xEF\xBB\xBF");
        $delimiter = ';';
        fputcsv($handle, ['ID','Nombre','Email','attended'], $delimiter);
        foreach ($rows as $row) {
            fputcsv($handle, [
                $row['id'],
                $row['name'],
                $row['email'],
                $row['attended'] ? 'true' : 'false',
            ], $delimiter);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]);
    }

    /**
     * @param array<int, array{id:int,attended:bool}> $records
     */
    public function updateAttendance(ActivitySchedule $schedule, array $records): void
    {
        $map = collect($records)->keyBy('id');
        $schedule->users()->whereIn('users.id', $map->keys())->get()->each(function ($user) use ($schedule, $map) {
            $schedule->users()->updateExistingPivot($user->id, [
                'attended' => (bool) $map[$user->id]['attended'],
            ]);
        });
    }
}
