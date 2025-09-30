<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use App\Models\ActivitySchedule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Enums\OperationHours;
use App\Models\Activity;
use App\Models\Room;

class EditActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }
    
    private function getEditActivityFormAs(string $roleName, ActivitySchedule $activitySchedule): TestResponse
    {
        return $this->actingAsRole($roleName)
                     ->get(route('activity.schedules.edit', $activitySchedule));
    }


    public function test_authorized_user_can_view_edit_activity_schedule_form()
    {        
        foreach (
                $this->getAuthorizedRoles('activity.schedules.edit')
                as $authorizedRole
            ) 
        {            
            $activitySchedule = ActivitySchedule::factory()->raw();
            $activityToEdit = ActivitySchedule::create($activitySchedule);
            $response = $this->getEditActivityFormAs($authorizedRole, $activityToEdit);

            $response->assertStatus(200)
                     ->assertSee('Editar horario de la actividad')
                     ->assertSee('Actividad')
                     ->assertSee('Fecha/hora')
                     ->assertSee('Sala')
                     ->assertSee('Total plazas')
                     ->assertSee($activityToEdit->activity->name)
                     ->assertSee($activityToEdit->room->name)
                     ->assertSee(Carbon::parse(
                                        $activityToEdit->start_datetime
                                        )->format('Y-m-d H:i'))
                     ->assertSee($activityToEdit->max_enrollment);      
        }
    }

    public function test_unauthorized_user_cannot_view_edit_activity_schedule_form()
    {
        foreach (
            $this->getUnauthorizedRoles('activity.schedules.edit') as $unauthorizedRole
        ) {            
            $activityToEdit = ActivitySchedule::factory()->create();
            $response = $this->getEditActivityFormAs($unauthorizedRole, $activityToEdit);            

            $response->assertStatus(403);
        }
    }

    private function submitActivityScheduleToUpdate(string $roleName, ActivitySchedule $activitySchedule, array $Newdata): TestResponse
    {
        return $this->actingAsRole($roleName)
                     ->from(route('activity.schedules.edit', $activitySchedule))
                     ->put(route('activity.schedules.update', $activitySchedule), $Newdata);
    }

    public function test_authorized_user_can_update_activity_schedule_with_valid_data()
    {
        foreach (
                $this->getAuthorizedRoles('activity.schedules.edit')
                as $authorizedRole
            ) 
        {            
            $activityScheduleToEdit = ActivitySchedule::factory()->create();
            $activity = Activity::factory()->create();
            $room = Room::factory()->create();

            $newActivityScheduleData = [
                'activity_id' => $activity->id,
                'room_id' => $room->id,
                'start_datetime' => '2028-01-01 14:30:00',
                'end_datetime' => '2028-01-01 15:00:00',
                'max_enrollment' => 40,
            ];

            $response = $this->submitActivityScheduleToUpdate($authorizedRole, $activityScheduleToEdit, $newActivityScheduleData);

            $response->assertRedirect(route('activity.schedules.index'))
                     ->assertStatus(302)
                     ->assertSessionHas('success');

            $this->assertDatabaseHas('activity_schedules', [
                'id' => $activityScheduleToEdit->id,
                'activity_id' => $newActivityScheduleData['activity_id'],
                'room_id' => $newActivityScheduleData['room_id'],
                'start_datetime' => $newActivityScheduleData['start_datetime'],
                'end_datetime' => $newActivityScheduleData['end_datetime'],
                'max_enrollment' => $newActivityScheduleData['max_enrollment'],
            ]);
        }
    }

    #[DataProvider('invalidActivityScheduleDataProvider')]
    public function test_validation_errors_are_shown_if_data_is_invalid(array $invalidData, string $field, string $error): void
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
                $this->getAuthorizedRoles('activity.schedules.edit')
                as $authorizedRole
            ) 
        {             
            $activityToEdit = ActivitySchedule::factory()->create();
            $response = $this->submitActivityScheduleToUpdate($authorizedRole, $activityToEdit, $invalidData);

            $response->assertRedirect(route('activity.schedules.edit', $activityToEdit))
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
