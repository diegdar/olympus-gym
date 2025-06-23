<?php
declare(strict_types=1);

namespace Tests\Feature\Activities;

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;

class EditActivityTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected const PERMISSION = 'activities.edit';
    protected const ROUTE_EDIT_VIEW = 'activities.edit';
    protected const ROUTE_UPDATE = 'activities.update';
    protected const ROUTE_INDEX = 'activities.index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function getEditActivityFormAs(string $roleName, Activity $activity): TestResponse
    {
        return $this->actingAsRole($roleName)
                    ->get(route(self::ROUTE_EDIT_VIEW, $activity));
    }    

    public function test_authorized_user_can_view_edit_activity_form()
    {        
        foreach (
                $this->getAuthorizedRoles(self::PERMISSION)       
                as $authorizedRole
            ) 
        {            
            $activityData = Activity::factory()->raw();
            $activityToEdit = Activity::create($activityData);
            $response = $this->getEditActivityFormAs($authorizedRole, $activityToEdit);

            $response->assertStatus(200)
                     ->assertSee('Editar actividad');
                     
            foreach($activityData as $key => $value) {
                $response->assertSee($key)
                         ->assertSee($value);
            }
        }
    }

    public function test_unauthorized_user_cannot_view_edit_activity_form()
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION) as $unauthorizedRole
        ) {            
            $activityToEdit = Activity::factory()->create();
            $response = $this->getEditActivityFormAs($unauthorizedRole, $activityToEdit);

            $response->assertStatus(403);
        }
    }

    private function submitActivityToUpdate(string $roleName, Activity $activity, array $Newdata): TestResponse
    {
        return $this->actingAsRole($roleName)
            ->from(route(self::ROUTE_EDIT_VIEW, $activity))
            ->put(route(self::ROUTE_UPDATE, $activity), $Newdata);
    }  
    
    public function test_can_update_activity_with_valid_data()
    {
        
        foreach (
                $this->getAuthorizedRoles(self::PERMISSION)       
                as $authorizedRole
            ) 
        {             
            $activityToEdit = Activity::factory()->create();
            $newActivityData = Activity::factory()->raw();
            $response = $this->submitActivityToUpdate($authorizedRole, $activityToEdit, $newActivityData);

            $response->assertRedirect(route(self::ROUTE_INDEX))
                     ->assertStatus(302)
                     ->assertSessionHas('msg');

            foreach ($newActivityData as $key => $value) {
                $this->assertDatabaseHas('activities', [
                    'id' => $activityToEdit->id,
                    $key => $value,
                ]);
            }
        }
    }

    #[DataProvider('invalidActivityDataProvider')]
    public function test_validation_errors_are_shown_if_data_is_invalid(array $invalidData, array $expectedErrors): void
    {
        foreach (
                $this->getAuthorizedRoles(self::PERMISSION)       
                as $authorizedRole
            ) 
        {             
            $activityToEdit = Activity::factory()->create();
            $response = $this->submitActivityToUpdate($authorizedRole, $activityToEdit, $invalidData);

            $response->assertRedirect(route(self::ROUTE_EDIT_VIEW, $activityToEdit))
                     ->assertSessionHasErrors($expectedErrors);
        }
    }

    public static function invalidActivityDataProvider(): array
    {
        return [
            // name
            'empty name' => [
                'invalidData' => ['name' => '', 'description' => 'A room without a name'],
                'expectedErrors' => ['name'],
            ],
            'name too short' => [
                'invalidData' => ['name' => 'A', 'description' => 'A room with a short name'],
                'expectedErrors' => ['name'],
            ],
            'name too long' => [
                'invalidData' => ['name' => str_repeat('A', 51), 'description' => 'A room with a very long name'],
                'expectedErrors' => ['name'],
            ],
            // duration
            'duration not an integer' => [
                'invalidData' => ['name' => 'Test Activity', 'description' => 'A valid description', 'duration' => 'not an integer'],
                'expectedErrors' => ['duration'],
            ],
            'duration less than 15' => [
                'invalidData' => ['name' => 'Test Activity', 'description' => 'A valid description', 'duration' => 10],
                'expectedErrors' => ['duration'],
            ],
            // description
            'description too short' => [
                'invalidData' => ['name' => 'short', 'description' => 'Short'],
                'expectedErrors' => ['description'],
            ],
            'description too long' => [
                'invalidData' => ['name' => 'Test Activity', 'description' => str_repeat('A', 2001)],
                'expectedErrors' => ['description'],
            ],
            // all fields
            'all fields invalid' => [
                'invalidData' => ['name' => '', 'description' => str_repeat('A', 2001)],
                'expectedErrors' => ['name', 'description'],
            ],
        ];
    }
    
}
