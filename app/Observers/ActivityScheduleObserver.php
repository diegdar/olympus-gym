<?php
declare(strict_types=1);

namespace App\Observers;

use App\Models\ActivitySchedule;
use Illuminate\Support\Facades\Cache;

class ActivityScheduleObserver
{
    public function created(ActivitySchedule $schedule): void
    {
        $this->bumpVersion();
    }

    public function updated(ActivitySchedule $schedule): void
    {
        $this->bumpVersion();
    }

    public function deleted(ActivitySchedule $schedule): void
    {
        $this->bumpVersion();
    }

    public function restored(ActivitySchedule $schedule): void
    {
        $this->bumpVersion();
    }

    public function forceDeleted(ActivitySchedule $schedule): void
    {
        $this->bumpVersion();
    }

    private function bumpVersion(): void
    {
        $key = 'activity_schedules:list:version';
        // increment or init to 1 if missing
        $current = (int) Cache::get($key, 1);
        Cache::forever($key, $current + 1);
    }
}
