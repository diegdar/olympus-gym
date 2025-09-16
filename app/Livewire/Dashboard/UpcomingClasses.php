<?php

namespace App\Livewire\Dashboard;

use App\Models\ActivitySchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class UpcomingClasses extends Component
{
    public array $rows = [];

    public function mount(): void { $this->refresh(); }

    #[On('dashboard-upcoming-refresh')]
    #[On('activity-enrolled')]
    #[On('activity-unenrolled')]
    public function refresh(): void
    {
        $user = Auth::user();
        $now = Carbon::now();
        $end = $now->copy()->addDays(7);

        $schedules = ActivitySchedule::query()
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->whereBetween('start_datetime', [$now, $end])
            ->with(['activity:id,name', 'room:id,name'])
            ->withCount('users')
            ->orderBy('start_datetime')
            ->get();

        $this->rows = $schedules->map(fn($sch) => [
            'id' => $sch->id,
            'date' => Carbon::parse($sch->start_datetime)->translatedFormat('d/m H:i'),
            'activity' => $sch->activity->name,
            'room' => $sch->room->name,
            'enrolled' => $sch->users_count . '/' . $sch->max_enrollment,
            'can_unenroll' => true,
            'start_at' => $sch->start_datetime,
        ])->sortBy('start_at')->values()->all();
    }

    public function unenroll(int $scheduleId): void
    {
        $user = Auth::user();
        $schedule = ActivitySchedule::findOrFail($scheduleId);
        $schedule->users()->detach($user->id);
        $this->dispatch('activity-unenrolled', id: $scheduleId);
        $this->refresh();
        $this->dispatch('recommended-refresh');
    }

    public function render()
    {
        return view('livewire.dashboard.upcoming-classes');
    }
}
