<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\ActivitySchedule;
use App\Contracts\Export\Exporter;
use App\Services\Export\CsvExporter;
use App\Services\Export\JsonExporter;

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
        // Mantener compatibilidad con tests existentes (CSV por defecto)
        return $this->export($schedule, 'csv');
    }

    public function export(ActivitySchedule $schedule, string $format = 'csv')
    {
        $rows = $this->getEnrolledUsers($schedule);

        $exporter = $this->makeExporter($format);
        $payload = $exporter->export($rows);
        $filename = $exporter->filename('usuarios_inscritos_schedule_'.$schedule->id.'_'.date('Ymd_His'));

        return response($payload, 200, [
            'Content-Type' => $exporter->contentType(),
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]);
    }

    private function makeExporter(string $format): Exporter
    {
        return match (strtolower($format)) {
            'json' => new JsonExporter(),
            default => new CsvExporter(),
        };
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
