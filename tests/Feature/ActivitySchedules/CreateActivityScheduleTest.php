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
use App\Enums\OperationHours;
use App\Models\Activity;
use App\Models\Room;

class CreateActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);            
    }
    
    private function getCreateActivityScheduleFormAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route('activity.schedules.create'));
    }

    public function test_authorized_user_can_view_create_activity_schedule_form()
    {
        foreach (
                $this->getAuthorizedRoles('activity.schedules.create')
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

    public function test_unauthorized_user_cannot_view_create_activity_schedule_form()
    {
        foreach (
            $this->getUnauthorizedRoles('activity.schedules.create') as $unauthorizedRole
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
            ->from(route('activity.schedules.create'))
            ->post(route('activity.schedules.store', $newData));
    }    

    public function test_authorized_user_can_create_an_activity_schedule()
    {
        foreach (
            $this->getAuthorizedRoles('activity.schedules.store') as $authorizedRole
        ) {
            $activityScheduleData = ActivitySchedule::factory()->raw();
            $response = $this->CreateActivityScheduleAs($authorizedRole, $activityScheduleData);

            $response->assertRedirect(route('activity.schedules.index'))
                     ->assertSessionHas('success');

            $this->assertDatabaseHas('activity_schedules', $activityScheduleData);
        }
    }

    public function test_unauthorized_user_cannot_create_an_activity_schedule()
    {
        foreach (
            $this->getUnauthorizedRoles('activity.schedules.store') as $unauthorizedRole
        ) {
            $activityScheduleData = ActivitySchedule::factory()->raw();
            $response = $this->CreateActivityScheduleAs($unauthorizedRole, $activityScheduleData);

            $response->assertStatus(403)
                     ->assertSessionMissing('success');

            $this->assertDatabaseMissing('activity_schedules', $activityScheduleData);
        }
    }
         
    #[DataProvider('invalidActivityScheduleDataProvider')]
    public function test_validation_errors_for_invalid_activity_schedule_data(array $invalidData, string $field, string $error): void
    {
        $activity = Activity::factory()->create(['id' => 1]);
        $room = Room::factory()->create(['id' => 1]);

        if($error === 'room is not available') {
            ActivitySchedule::factory()->create([
                'start_datetime' => '2028-01-01 10:00:00',
                'end_datetime' => '2028-01-01 11:00:00',
                'room_id' => $room->id,
                'activity_id' => $activity->id,
            ]);
        }

        foreach (
            $this->getAuthorizedRoles('activity.schedules.store') as $authorizedRole
        ) {
            $response = $this->CreateActivityScheduleAs($authorizedRole, $invalidData);

            $response->assertRedirect(route('activity.schedules.create'))
                     ->assertSessionHasErrors($field);

            $this->assertDatabaseMissing('activity_schedules', $invalidData);                           
        }
    }

    public static function invalidActivityScheduleDataProvider(): array
    {      
        $now = now();
        $nextHour = $now->copy()->addHour();
        $pastDay = $now->copy()->subDay();

        return [
            // activity_id
            'empty activity_id' => [
                ['activity_id' => '', 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30],
                'activity_id',
                'empty activity_id'
            ],
            'activity_id does not exist' => [
                ['activity_id' => 9999, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30],
                'activity_id',
                'activity_id does not exist'
            ],
            // start_datetime
            'empty start_datetime' => [
                ['activity_id' => 1, 'start_datetime' => '', 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30],
                'start_datetime',
                'empty start_datetime'
            ],
            'start_datetime in the past' => [
                ['activity_id' => 1, 'start_datetime' => $pastDay, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30],
                'start_datetime',
                'start_datetime in the past'
            ],
            'start_datetime before operation hours' => [
                [   'activity_id' => 1, 
                    'start_datetime' => now()
                        ->setHour(OperationHours::START_HOUR->value - 1),
                    'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30
                ],
                'start_datetime',
                'start_datetime before operation hours'
            ],
            'start_datetime after operation hours' => [
                [   'activity_id' => 1, 'start_datetime' => now()
                        ->setHour(OperationHours::END_HOUR->value + 1),
                    'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 30
                ],
                'start_datetime',
                'start_datetime after operation hours'
            ],
            // room_id
            'empty room_id' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => '', 'end_datetime' => $nextHour, 'max_enrollment' => 30],
                'room_id',
                'empty room_id'
            ],
            'room_id does not exist' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 9999, 'end_datetime' => $nextHour, 'max_enrollment' => 30],
                'room_id',
                'room_id does not exist'
            ],
            'room is not available' => [
                [   'activity_id' => 1, 
                    'start_datetime' =>     
                        '2028-01-01 10:00:00',  
                    'room_id' => 1,
                    'end_datetime' => $nextHour,
                    'max_enrollment' => 30, 
                ],
                'room_id',
                'room is not available'
            ],
            // max_enrollment
            'empty max_enrollment' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => ''],
                'max_enrollment',
                'empty max_enrollment'
            ],
            'max_enrollment less than 10' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 5],
                'max_enrollment',
                'max_enrollment less than 10'
            ],
            'max_enrollment greater than 50' => [
                ['activity_id' => 1, 'start_datetime' => $now, 'room_id' => 1, 'end_datetime' => $nextHour, 'max_enrollment' => 60],
                'max_enrollment',
                'max_enrollment greater than 50'
            ],
        ];
    }

}
