<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use App\Models\ActivitySchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Testing\TestResponse;

class ShowUserReservationsTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    // permission
    protected const PERMISSION_SHOW_RESERVATIONS = 'user.reservations';
    protected const PERMISSION_ENROLL_USER = 'activity.schedules.enroll';
    protected const PERMISSION_UNENROLL_USER = 'activity.schedules.unenroll';

    // routes
    protected const ROUTE_SHOW_RESERVATIONS = 'user.reservations';
    protected const ROUTE_SHOW_ACTIVITY_SCHEDULE = 'activity.schedules.show';
    protected const ROUTE_ENROLL_USER = 'activity.schedules.enroll';
    protected const ROUTE_UNENROLL_USER = 'activity.schedules.unenroll';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function getUserReservationsAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_SHOW_RESERVATIONS));
    }

    private function formatDateTime($dateTime): string
    {
        return Carbon::parse($dateTime)->translatedFormat('l d/F, H:i');
    }

    private function getReservationTexts(): array
    {
        return ['Mis Reservas', 'Fecha/Hora', 'Actividad', 'Sala', 'Acciones'];
    }

    private function assertReservationDetailsAreVisible(TestResponse $response): void
    {
        foreach ($this->getReservationTexts() as $text) {
            $response->assertSee($text);
        }

        foreach (Auth::user()->activySchedules as $reservation) {
            $response->assertSee($reservation->activity->name)
                     ->assertSee($reservation->room->name)
                     ->assertSee($this->formatDateTime($reservation->start_datetime));
        }
    }

    private function assertReservationDetailsAreNotVisible(TestResponse $response): void
    {
        foreach ($this->getReservationTexts() as $text) {
            $response->assertDontSee($text);
        }

        foreach (Auth::user()->activySchedules as $reservation) {
            $response->assertDontSee($reservation->activity->name)
                     ->assertDontSee($reservation->room->name)
                     ->assertDontSee($this->formatDateTime($reservation->start_datetime));
        }
    }

    private function performEnrollmentRequest(ActivitySchedule $activitySchedule, User $user): void
    {
        $this->post(route(self::ROUTE_ENROLL_USER, $activitySchedule))
            ->assertStatus(302)
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activity_schedule_user', [
            'user_id' => $user->id,
            'activity_schedule_id' => $activitySchedule->id,
        ]);

        $activitySchedule->refresh();
        $this->assertEquals(1, $activitySchedule->current_enrollment);
    }

    public function test_authorized_user_can_see_their_reservations(): void
    {
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION_SHOW_RESERVATIONS)
            as $role
        ) {
            $this->assertReservationDetailsAreVisible(
                $this->getUserReservationsAs($role)
            );
        }
    }

    public function test_unauthorized_user_can_not_see_reservations(): void
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION_SHOW_RESERVATIONS)
            as $role
        ) {
            $response = $this->getUserReservationsAs($role);
            $response->assertStatus(403);
            $this->assertReservationDetailsAreNotVisible($response);
        }
    }

    public function test_authorized_user_can_unenroll_from_reservation(): void
    {
        $activitySchedule = ActivitySchedule::factory()->create();

        foreach ($this->getAuthorizedRoles(self::PERMISSION_UNENROLL_USER) as $role) {
            $user = $this->createUserAndAssignRole($role);

            $this->performEnrollmentRequest($activitySchedule, $user);

            $this->from(route(self::ROUTE_SHOW_RESERVATIONS))
                ->delete(route(self::ROUTE_UNENROLL_USER, $activitySchedule))
                ->assertStatus(302)
                ->assertRedirect(route(self::ROUTE_SHOW_RESERVATIONS))
                ->assertSessionHas('success');

            $this->assertDatabaseMissing('activity_schedule_user', [
                'user_id' => $user->id,
                'activity_schedule_id' => $activitySchedule->id,
            ]);

            $activitySchedule->refresh();
            $this->assertEquals(0, $activitySchedule->current_enrollment);
        }
    }

    public function test_unauthorized_user_cannot_unenroll_from_reservation(): void
    {
        $activitySchedule = ActivitySchedule::factory()->create();
        $authorizedUser = $this->createUserAndAssignRole(
            $this->getAuthorizedRoles(self::PERMISSION_ENROLL_USER)[0]
        );
        $this->performEnrollmentRequest($activitySchedule, $authorizedUser);

        foreach ($this->getUnauthorizedRoles(self::PERMISSION_UNENROLL_USER) as $role) {
            $this->createUserAndAssignRole($role);

            $this->from(route(self::ROUTE_SHOW_RESERVATIONS))
                ->delete(route(self::ROUTE_UNENROLL_USER, $activitySchedule))
                ->assertStatus(403);

            $this->assertDatabaseHas('activity_schedule_user', [
                'user_id' => $authorizedUser->id,
                'activity_schedule_id' => $activitySchedule->id,
            ]);
        }
    }

    public function test_authorized_user_can_see_activity_schedule_details_from_reservations(): void
    {
        $activitySchedule = ActivitySchedule::factory()->create();

        foreach ($this->getAuthorizedRoles(self::PERMISSION_SHOW_RESERVATIONS) as $role) {
            $user = $this->createUserAndAssignRole($role);

            $this->performEnrollmentRequest($activitySchedule, $user);

            $this->from(route(self::ROUTE_SHOW_RESERVATIONS))
                ->get(route(self::ROUTE_SHOW_ACTIVITY_SCHEDULE, $activitySchedule))
                ->assertStatus(200)
                ->assertSeeText($activitySchedule->activity->name)
                ->assertSeeText($activitySchedule->room->name)
                ->assertSeeText(Carbon::parse($activitySchedule->start_datetime)->format('G:i'))
                ->assertSeeText(Carbon::parse($activitySchedule->start_datetime)->translatedFormat('l, d F'))
                ->assertSeeText($activitySchedule->activity->duration)
                ->assertSeeText($activitySchedule->max_enrollment)
                ->assertSeeText($activitySchedule->current_enrollment);
        }
    }
}
