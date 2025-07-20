<?php
declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class GetUserReservationsService
{
    public function __invoke()
    {
        $reservations = $this->getUserReservations();

        return $this->formatDateTimes($reservations);
    }

    /**
     * Fetch all reservations for the current authenticated user.
     *
     * @return Collection
     */
    private function getUserReservations(): Collection
    {
        return Auth::user()->activySchedules()
            ->where('start_datetime', '>', Carbon::now())
            ->with('activity', 'room')
            ->get();
    }

    /**
     * Format the start and end datetime of reservations.
     *
     * @param Collection $reservations
     * @return Collection
     */
    private function formatDateTimes(Collection $reservations): Collection
    {
        foreach ($reservations as $reservation) {
            $reservation->start_datetime = Carbon::parse($reservation->start_datetime)->translatedFormat('l d/F, H:i');
            $reservation->end_datetime = Carbon::parse($reservation->end_datetime)->translatedFormat('l d/F, H:i');
        }

        return $reservations;
    }

}
