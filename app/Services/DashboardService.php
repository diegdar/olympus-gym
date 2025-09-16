<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\ActivitySchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
	/**
	 * Build member dashboard data array.
	 */
	public function getMemberData(User $user): array
	{
		return [
			'currentSubscription'   => $this->currentSubscription($user),
			'upcomingSchedules'     => $this->upcomingSchedules($user),
			'attendedThisWeek'      => $this->attendedThisWeek($user),
			'weeklyGoal'            => 3,
			'recommendedActivities' => $this->recommendedActivities($user),
		];
	}

	protected function currentSubscription(User $user)
	{
		return $user->subscriptions()
			->orderByDesc('subscription_user.end_date')
			->first();
	}

	protected function upcomingSchedules(User $user): Collection
	{
		$now = Carbon::now();
		$end = $now->copy()->addDays(7);

		return ActivitySchedule::query()
			->whereHas('users', fn($q) => $q->where('users.id', $user->id))
			->whereBetween('start_datetime', [$now, $end])
			->with(['activity:id,name', 'room:id,name'])
			->withCount('users')
			->orderBy('start_datetime')
			->get();
	}

	protected function attendedThisWeek(User $user): int
	{
		$startOfWeek = Carbon::now()->startOfWeek();
		return DB::table('activity_schedule_user')
			->join('activity_schedules', 'activity_schedules.id', '=', 'activity_schedule_user.activity_schedule_id')
			->where('activity_schedule_user.user_id', $user->id)
			->where('activity_schedule_user.attended', true)
			->whereBetween('activity_schedules.start_datetime', [$startOfWeek, Carbon::now()])
			->count();
	}


	protected function recommendedActivities(User $user): array
	{
		// Simple heuristic: future activities next 48h where user not enrolled and free slots left
		$now = Carbon::now();
		$end = $now->copy()->addHours(48);
		return ActivitySchedule::query()
			->whereBetween('start_datetime', [$now, $end])
			->with(['activity:id,name', 'room:id,name'])
			->get()
			->filter(function(ActivitySchedule $sch) use ($user) {
				$enrolled = $sch->users()->where('users.id', $user->id)->exists();
				$hasFree = $sch->users()->count() < $sch->max_enrollment;
				return !$enrolled && $hasFree;
			})
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

	public function weeklyAttendanceStats(User $user): array
	{
		$weeks = 8;
		$data = [];
		$labels = [];
		for ($i = $weeks - 1; $i >= 0; $i--) {
			$start = Carbon::now()->startOfWeek()->subWeeks($i);
			$end = $start->copy()->endOfWeek();
			$count = DB::table('activity_schedule_user')
				->join('activity_schedules', 'activity_schedules.id', '=', 'activity_schedule_user.activity_schedule_id')
				->where('activity_schedule_user.user_id', $user->id)
				->where('activity_schedule_user.attended', true)
				->whereBetween('activity_schedules.start_datetime', [$start, $end])
				->count();
			// Etiqueta como fecha (inicio de semana): 14/9, 21/9
			$labels[] = $start->format('j/n');
			$data[] = $count;
		}
		return ['labels' => $labels, 'values' => $data];
	}

	public function activityDistribution(User $user): array
	{
		$rows = DB::table('activity_schedule_user')
			->join('activity_schedules', 'activity_schedules.id', '=', 'activity_schedule_user.activity_schedule_id')
			->join('activities', 'activities.id', '=', 'activity_schedules.activity_id')
			->select('activities.name', DB::raw('count(*) as total'))
			->where('activity_schedule_user.user_id', $user->id)
			->where('activity_schedule_user.attended', true)
			->groupBy('activities.name')
			->orderByDesc('total')
			->limit(10)
			->get();
		return [
			'labels' => $rows->pluck('name'),
			'values' => $rows->pluck('total'),
		];
	}

	/**
	 * Build flattened view data to avoid inline computations in Blade.
	 */
	public function buildViewData(User $user): array
	{
		$memberData = $this->getMemberData($user);

		$currentSubscription = $memberData['currentSubscription'] ?? null;
		$endDate = $currentSubscription?->pivot?->end_date ?? null;
		$endCarbon = $endDate ? Carbon::parse($endDate) : null;
		$daysLeft = null;
		if ($endCarbon) {
			$hours = now()->diffInHours($endCarbon, false);
			if ($hours >= 0) {
				$daysLeft = (int) ceil($hours / 24);
			} else {
				$daysLeft = (int) floor($hours / 24); // negativo o expirado
			}
		}

		$attended = $memberData['attendedThisWeek'] ?? 0;
		$goal = $memberData['weeklyGoal'] ?? 3;
		$pct = $goal > 0 ? min(100, (int) round(($attended / $goal) * 100)) : 0;

		return [
			'currentSubscription' => $currentSubscription,
			'endCarbon' => $endCarbon,
			'daysLeft' => $daysLeft,
			'attended' => $attended,
			'goal' => $goal,
			'pct' => $pct,
			'recommended' => $memberData['recommendedActivities'] ?? [],
			'upcoming' => $memberData['upcomingSchedules'] ?? collect(),
		];
	}
}

