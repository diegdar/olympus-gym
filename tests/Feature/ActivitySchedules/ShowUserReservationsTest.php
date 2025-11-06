<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use App\Models\ActivitySchedule;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Testing\TestResponse;

class ShowUserReservationsTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function getUserReservationsAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route('user.reservations'));
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
        $this->post(route('activity.schedules.enroll', $activitySchedule))
            ->assertStatus(302)
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activity_schedule_user', [
            'user_id' => $user->id,
            'activity_schedule_id' => $activitySchedule->id,
        ]);

    $activitySchedule->refresh();
    $this->assertEquals(1, $activitySchedule->users()->count());
    }

    public function test_authorized_user_can_see_their_reservations(): void
    {
        foreach (
            $this->getAuthorizedRoles('user.reservations')
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
            $this->getUnauthorizedRoles('user.reservations')
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

        foreach ($this->getAuthorizedRoles('activity.schedules.unenroll') as $role) {
            $user = $this->createUserAndSignIn($role);
            $this->actingAs($user);

            $this->performEnrollmentRequest($activitySchedule, $user);

            $this->from(route('user.reservations'))
                ->delete(route('activity.schedules.unenroll', $activitySchedule))
                ->assertStatus(302)
                ->assertRedirect(route('user.reservations'))
                ->assertSessionHas('success');

            $this->assertDatabaseMissing('activity_schedule_user', [
                'user_id' => $user->id,
                'activity_schedule_id' => $activitySchedule->id,
            ]);

            $activitySchedule->refresh();
            $this->assertEquals(0, $activitySchedule->users()->count());
        }
    }

    public function test_unauthorized_user_cannot_unenroll_from_reservation(): void
    {
        $activitySchedule = ActivitySchedule::factory()->create();
        $authorizedUser = $this->createUserAndSignIn(
            $this->getAuthorizedRoles('activity.schedules.enroll')[0]
        );
        $this->performEnrollmentRequest($activitySchedule, $authorizedUser);

        foreach ($this->getUnauthorizedRoles('activity.schedules.unenroll') as $role) {
            $this->createUserAndSignIn($role);

            $this->from(route('user.reservations'))
                ->delete(route('activity.schedules.unenroll', $activitySchedule))
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

        foreach ($this->getAuthorizedRoles('user.reservations') as $role) {
            $user = $this->createUserAndSignIn($role);

            $this->performEnrollmentRequest($activitySchedule, $user);

            $this->from(route('user.reservations'))
                ->get(route('activity.schedules.show', $activitySchedule))
                ->assertStatus(200)
                ->assertSeeText($activitySchedule->activity->name)
                ->assertSeeText($activitySchedule->room->name)
                ->assertSeeText(Carbon::parse($activitySchedule->start_datetime)->format('G:i'))
                ->assertSeeText(Carbon::parse($activitySchedule->start_datetime)->translatedFormat('l, d F'))
                ->assertSeeText($activitySchedule->activity->duration)
                ->assertSeeText($activitySchedule->max_enrollment)
                ->assertSeeText((string) $activitySchedule->users()->count());
        }
    }
}
