<?php
declare(strict_types=1);

namespace Tests\Feature\Activities;

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;

class CreateActivityTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected const PERMISSION_CREATE_ACTIVITY = 'activities.create';
    protected const PERMISSION_STORE_ACTIVITY = 'activities.store';

    protected const ROUTE_ACTIVITYS_INDEX = 'activities.index';

    protected const ROUTE_CREATE_ACTIVITY_VIEW = 'activities.create';
    protected const ROUTE_STORE_ACTIVITY = 'activities.store';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);            
    }
    
    private function getCreateActivityFormAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_CREATE_ACTIVITY_VIEW));
    }

    public function test_authorized_user_can_view_create_activity_form()
    {
        foreach (
                $this->getAuthorizedRoles                (self::PERMISSION_CREATE_ACTIVITY)       
                as $authorizedRole
            ) 
        {            
            $response = $this->getCreateActivityFormAs($authorizedRole);

            $response->assertStatus(200)
                     ->assertSee('Crear actividad')
                     ->assertSee('Nombre')
                     ->assertSee('Descripción')
                     ->assertSee('Duración');
        }
    }   

    public function test_unauthorized_user_cannot_view_create_activity_form()
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION_CREATE_ACTIVITY) as $unauthorizedRole
        ) {
            $response = $this->getCreateActivityFormAs($unauthorizedRole);

            $response->assertStatus(403)
                     ->assertDontSee('Crear actividad')
                     ->assertDontSee('Nombre')
                     ->assertDontSee('Descripción')
                     ->assertDontSee('Duración');
        }        
    }   

    private function CreateActivityAs(string $roleName, array $newData): TestResponse
    {
        return $this->actingAsRole($roleName)
            ->from(route(self::ROUTE_CREATE_ACTIVITY_VIEW))
            ->post(route(self::ROUTE_STORE_ACTIVITY, $newData));
    }    

    public function test_authorized_user_can_create_an_activity()
    {
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION_STORE_ACTIVITY) as $authorizedRole
        ) {
            $activity = Activity::factory()->raw();
            $response = $this->CreateActivityAs($authorizedRole, $activity);

            $response->assertRedirect(route(self::ROUTE_ACTIVITYS_INDEX))
                     ->assertSessionHas('msg');

            foreach ($activity as $key => $value) {
                $this->assertDatabaseHas('activities', [
                    $key => $value,
                ]);
            }
        }
    }

    public function test_unauthorized_user_cannot_create_a_activity()
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION_STORE_ACTIVITY) as $unauthorizedRole
        ) {
            $activity = Activity::factory()->raw();
            $response = $this->CreateActivityAs($unauthorizedRole, $activity);

            $response->assertStatus(403)
                     ->assertSessionMissing('msg');

            foreach ($activity as $key => $value) {
                $this->assertDatabaseMissing('activities', [
                    $key => $value,
                ]);
            }
        }
    }
         
    #[DataProvider('invalidActivityDataProvider')]
    public function test_validation_errors_for_invalid_activity_data(array $invalidData, array $expectedErrors): void
    {
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION_STORE_ACTIVITY) as $authorizedRole
        ) {
            $response = $this->CreateActivityAs($authorizedRole, $invalidData);

            $response->assertRedirect(route(self::ROUTE_CREATE_ACTIVITY_VIEW))
                     ->assertSessionHasErrors($expectedErrors);

            foreach($invalidData as $key => $value) {
                $this->assertDatabaseMissing('activities', [
                    $key => $value,
                ]);
            }                            
        }
    }

    public static function invalidActivityDataProvider(): array
    {      
        return [
            // name
            'empty name' => [
                'invalidData' => ['name' => '', 'description' => 'A activity without a name'],
                'expectedErrors' => ['name'],
            ],
            'name too short' => [
                'invalidData' => ['name' => 'A', 'description' => 'A activity with a short name'],
                'expectedErrors' => ['name'],
            ],
            'name too long' => [
                'invalidData' => ['name' => str_repeat('A', 51), 'description' => 'A activity with a very long name'],
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
