<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use App\Models\ActivitySchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;

class CreateActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    // Permissions
    protected const PERMISSION_CREATE_ACTIVITY_SCHEDULE = 'activity.schedules.create';
    protected const PERMISSION_STORE_ACTIVITY_SCHEDULE = 'activity.schedules.store';
    // Routes
    protected const ROUTE_ACTIVITY_SCHEDULES_INDEX = 'activity.schedules.index';

    protected const ROUTE_CREATE_ACTIVITY_SCHEDULE_VIEW = 'activity.schedules.create';
    protected const ROUTE_STORE_ACTIVITY_SCHEDULE = 'activity.schedules.store';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);            
    }
    
    private function getCreateActivityScheduleFormAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_CREATE_ACTIVITY_SCHEDULE_VIEW));
    }

    public function test_authorized_user_can_view_create_activity_form()
    {
        foreach (
                $this->getAuthorizedRoles                (self::PERMISSION_CREATE_ACTIVITY_SCHEDULE)       
                as $authorizedRole
            ) 
        {            
            $response = $this->getCreateActivityScheduleFormAs($authorizedRole);

            $response->assertStatus(200)
                     ->assertSee('Crear horario para una actividad')
                     ->assertSee('Actividad')
                     ->assertSee('Fecha/hora')
                     ->assertSee('Sala')
                     ->assertSee('Total plazas');
        }
    }   

    public function test_unauthorized_user_cannot_view_create_activity_form()
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION_CREATE_ACTIVITY_SCHEDULE) as $unauthorizedRole
        ) {
            $response = $this->getCreateActivityScheduleFormAs($unauthorizedRole);

            $response->assertStatus(403)
                     ->assertDontSee('Crear horario para una actividad')
                     ->assertDontSee('Actividad')
                     ->assertDontSee('Fecha/hora')
                     ->assertDontSee('Sala')
                     ->assertDontSee('Total plazas');
        }        
    }   

    private function CreateActivityScheduleAs(string $roleName, array $newData): TestResponse
    {
        return $this->actingAsRole($roleName)
            ->from(route(self::ROUTE_CREATE_ACTIVITY_SCHEDULE_VIEW))
            ->post(route(self::ROUTE_STORE_ACTIVITY_SCHEDULE, $newData));
    }    

    public function test_authorized_user_can_create_an_activity()
    {
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION_STORE_ACTIVITY_SCHEDULE) as $authorizedRole
        ) {
            $activityScheduleData = ActivitySchedule::factory()->raw();
            $response = $this->CreateActivityScheduleAs($authorizedRole, $activityScheduleData);

            $response->assertRedirect(route(self::ROUTE_ACTIVITY_SCHEDULES_INDEX))
                     ->assertSessionHas('msg');

            foreach ($activityScheduleData as $key => $value) {
                $this->assertDatabaseHas('activity_schedules', [
                    $key => $value,
                ]);
            }
        }
    }

    public function test_unauthorized_user_cannot_create_a_activity()
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION_STORE_ACTIVITY_SCHEDULE) as $unauthorizedRole
        ) {
            $activityScheduleData = ActivitySchedule::factory()->raw();
            $response = $this->CreateActivityScheduleAs($unauthorizedRole, $activityScheduleData);

            $response->assertStatus(403)
                     ->assertSessionMissing('msg');

            foreach ($activityScheduleData as $key => $value) {
                $this->assertDatabaseMissing('activity_schedules', [
                    $key => $value,
                ]);
            }
        }
    }
         
    #[DataProvider('invalidActivityDataProvider')]
    public function test_validation_errors_for_invalid_activity_data(array $invalidData, array $expectedErrors): void
    {
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION_STORE_ACTIVITY_SCHEDULE) as $authorizedRole
        ) {
            $response = $this->CreateActivityScheduleAs($authorizedRole, $invalidData);

            $response->assertRedirect(route(self::ROUTE_CREATE_ACTIVITY_SCHEDULE_VIEW))
                     ->assertSessionHasErrors($expectedErrors);

            foreach($invalidData as $key => $value) {
                $this->assertDatabaseMissing('activity_schedules', [
                    $key => $value,
                ]);
            }                            
        }
    }

    public static function invalidActivityDataProvider(): array
    {      
        $now = now();
        $nextHour = $now->copy()->addHour();
        $pastDay = $now->copy()->subDay();
        $prevHour = $now->copy()->subHour();

        return [
            // activity_id
            'empty activity_id' => [
                ['activity_id' => '', 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30, 'current_enrollment' => 0],
                ['activity_id']
            ],
            'activity_id does not exist' => [
                ['activity_id' => 9999, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30, 'current_enrollment' => 0],
                ['activity_id']
            ],
            // start_datetime
            'empty start_datetime' => [
                ['activity_id' => 1, 'start_datetime' => '', 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30, 'current_enrollment' => 0],
                ['start_datetime']
            ],
            'start_datetime in the past' => [
                ['activity_id' => 1, 'start_datetime' => $pastDay, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30, 'current_enrollment' => 0],
                ['start_datetime']
            ],
            // room_id
            'empty room_id' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => '', 'end_datetime' => $nextHour, 'max_enrollment' => 30, 'current_enrollment' => 0],
                ['room_id']
            ],
            'room_id does not exist' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 9999, 'end_datetime' => $nextHour, 'max_enrollment' => 30, 'current_enrollment' => 0],
                ['room_id']
            ],
            // end_datetime
            'empty end_datetime' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => '', 'max_enrollment' => 30, 'current_enrollment' => 0],
                ['end_datetime']
            ],
            'end_datetime before start_datetime' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $prevHour, 'max_enrollment' => 30, 'current_enrollment' => 0],
                ['end_datetime']
            ],
            // max_enrollment
            'empty max_enrollment' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => '', 'current_enrollment' => 0],
                ['max_enrollment']
            ],
            'max_enrollment less than 10' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 5, 'current_enrollment' => 0],
                ['max_enrollment']
            ],
            'max_enrollment greater than 50' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 60, 'current_enrollment' => 0],
                ['max_enrollment']
            ],
            // current_enrollment
            'empty current_enrollment' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30, 'current_enrollment' => ''],
                ['current_enrollment']
            ],
            'current_enrollment less than 0' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30, 'current_enrollment' => -5],
                ['current_enrollment']
            ],
            'current_enrollment greater than max_enrollment' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30, 'current_enrollment' => 40],
                ['current_enrollment']
            ],
        ];
    }    

}
