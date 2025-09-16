<?php

namespace App\Livewire\Dashboard;

use App\Models\ActivitySchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class RecommendedActivities extends Component
{
    public array $items = [];

    public function mount(): void { $this->refreshList(); }

    #[On('activity-enrolled')]
    #[On('activity-unenrolled')]
    public function refreshList(): void { $this->items = $this->buildItems(); }

    private function buildItems(): array
    {
        $user = Auth::user();
        $now = Carbon::now();
        $end = $now->copy()->addHours(48);

        return ActivitySchedule::query()
            ->whereBetween('start_datetime', [$now, $end])
            ->with(['activity:id,name', 'room:id,name'])
            ->get()
            ->filter(fn(ActivitySchedule $sch) => ! $sch->users()->where('users.id', $user->id)->exists()
                && $sch->users()->count() < $sch->max_enrollment)
            ->sortBy('start_datetime')
            ->take(5)
            ->map(fn(ActivitySchedule $sch) => [
                'id' => $sch->id,
                'activity_name' => $sch->activity->name,
                'room_name' => $sch->room->name,
                'start_datetime' => $sch->start_datetime,
                'free_slots' => max(0, $sch->max_enrollment - $sch->users()->count()),
            ])
            ->values()
            ->all();
    }

    public function enroll(int $scheduleId): void
    {
        $user = Auth::user();
        $schedule = ActivitySchedule::withCount('users')->findOrFail($scheduleId);
        if ($schedule->users_count >= $schedule->max_enrollment) {
            $this->dispatch('notify', type: 'error', message: 'No quedan plazas disponibles.');
            return;
        }
        $schedule->users()->syncWithoutDetaching([$user->id => ['attended' => false]]);
        $this->dispatch('activity-enrolled', id: $scheduleId);        
        $this->refreshList();// Also refresh our local list
        $this->dispatch('dashboard-upcoming-refresh');
    }

    public function render()
    {
        return view('livewire.dashboard.recommended-activities');
    }
}
