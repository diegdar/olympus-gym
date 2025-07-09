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

class EditActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected const PERMISSION = 'activity.schedules.edit';
    protected const ROUTE_EDIT_VIEW = 'activity.schedules.edit';
    protected const ROUTE_UPDATE = 'activity.schedules.update';
    protected const ROUTE_INDEX = 'activity.schedules.index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }
    private function getEditActivityFormAs(string $roleName, ActivitySchedule $activitySchedule): TestResponse
    {
        return $this->actingAsRole($roleName)
                    ->get(route(self::ROUTE_EDIT_VIEW, $activitySchedule));
    }


    public function test_authorized_user_can_view_edit_activity_schedule_form()
    {        
        foreach (
                $this->getAuthorizedRoles(self::PERMISSION)       
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
            $this->getUnauthorizedRoles(self::PERMISSION) as $unauthorizedRole
        ) {            
            $activityToEdit = ActivitySchedule::factory()->create();
            $response = $this->getEditActivityFormAs($unauthorizedRole, $activityToEdit);            

            $response->assertStatus(403);
        }
    }

    private function submitActivityScheduleToUpdate(string $roleName, ActivitySchedule $activitySchedule, array $Newdata): TestResponse
    {
        return $this->actingAsRole($roleName)
                    ->from(route(self::ROUTE_EDIT_VIEW, $activitySchedule))
                    ->put(route(self::ROUTE_UPDATE, $activitySchedule), $Newdata);
    }

    public function test_can_update_activity_schedule_with_valid_data()
    {
        foreach (
                $this->getAuthorizedRoles(self::PERMISSION)       
                as $authorizedRole
            ) 
        {            
            $activityScheduleToEdit = ActivitySchedule::factory()->create();
            $newActivityScheduleData = ActivitySchedule::factory()->raw();
            $response = $this->submitActivityScheduleToUpdate($authorizedRole, $activityScheduleToEdit, 
                $newActivityScheduleData);

            $response->assertRedirect(route(self::ROUTE_INDEX))
                     ->assertStatus(302)
                     ->assertSessionHas('success');

            foreach ($newActivityScheduleData as $attribute => $value) {
                $this->assertDatabaseHas('activity_schedules', [
                    'id' => $activityScheduleToEdit->id,
                    $attribute => $value,
                ]);
            }                     
        }
    }

    #[DataProvider('invalidActivityScheduleDataProvider')]
    public function test_validation_errors_are_shown_if_data_is_invalid(array $invalidData, array $expectedErrors): void
    {
        foreach (
                $this->getAuthorizedRoles(self::PERMISSION)       
                as $authorizedRole
            ) 
        {             
            $activityToEdit = ActivitySchedule::factory()->create();
            $response = $this->submitActivityScheduleToUpdate($authorizedRole, $activityToEdit, $invalidData);

            $response->assertRedirect(route(self::ROUTE_EDIT_VIEW, $activityToEdit))
                     ->assertSessionHasErrors($expectedErrors);
        }

    }

    public static function invalidActivityScheduleDataProvider(): array
    {
        return [
            // activity_id
            'empty activity_id' =>[
                'invalidData' => [
                    'activity_id' => '',
                    'start_datetime' => now(),
                    'room_id' => 1,
                    'end_datetime' => now()->addHour(),
                    'max_enrollment' => 30,
                    'current_enrollment' => 0,
                ],
                'expectedErrors' => ['activity_id']                
            ],
            'invalid activity_id' =>[
                'invalidData' => [
                    'activity_id' => 0,
                    'start_datetime' => now(),
                    'room_id' => 1,
                    'end_datetime' => now()->addHour(),
                    'max_enrollment' => 30,
                    'current_enrollment' => 0,
                ],
                'expectedErrors' => ['activity_id']                
            ],
            // start_datetime
            'empty start_datetime' =>[
                'invalidData' => [
                    'activity_id' => 1,
                    'start_datetime' => '',
                    'room_id' => 1,
                    'end_datetime' => now()->addHour(),
                    'max_enrollment' => 30,
                    'current_enrollment' => 0,
                ],
                'expectedErrors' => ['start_datetime']                
            ],
            'start_datetime before today' =>[
                'invalidData' => [
                    'activity_id' => 1,
                    'start_datetime' => now()->subDay(),
                    'room_id' => 1,
                    'end_datetime' => now()->addHour(),
                    'max_enrollment' => 30,
                    'current_enrollment' => 0,
                ],
                'expectedErrors' => ['start_datetime']                
            ],
            // room_id
            'empty room_id' =>[
                'invalidData' => [
                    'activity_id' => 1,
                    'start_datetime' => now(),
                    'room_id' => '',
                    'end_datetime' => now()->addHour(),
                    'max_enrollment' => 30,
                    'current_enrollment' => 0,
                ],
                'expectedErrors' => ['room_id']                
            ],
            'invalid room_id' =>[
                'invalidData' => [
                    'activity_id' => 1,
                    'start_datetime' => now(),
                    'room_id' => 0,
                    'end_datetime' => now()->addHour(),
                    'max_enrollment' => 30,
                    'current_enrollment' => 0,
                ],
                'expectedErrors' => ['room_id']                
            ],
            // max_enrollment
            'empty max_enrollment' =>[
                'invalidData' => [
                    'activity_id' => 1,
                    'start_datetime' => now(),
                    'room_id' => 1,
                    'end_datetime' => now()->addHour(),
                    'max_enrollment' => '',
                    'current_enrollment' => 0,
                ],
                'expectedErrors' => ['max_enrollment']                
            ],
            'invalid max_enrollment' =>[
                'invalidData' => [
                    'activity_id' => 1,
                    'start_datetime' => now(),
                    'room_id' => 1,
                    'end_datetime' => now()->addHour(),
                    'max_enrollment' => -1,
                    'current_enrollment' => 0,
                ],
                'expectedErrors' => ['max_enrollment']                
            ],
        ];
    }    
}
