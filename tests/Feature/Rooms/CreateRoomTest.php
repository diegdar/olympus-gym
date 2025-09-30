<?php
declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;

class CreateRoomTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);            
    }
    
    private function getCreateRoomFormAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route('rooms.create'));
    }

    public function test_authorized_user_can_view_create_room_form()
    {
        foreach (
                $this->getAuthorizedRoles                ('rooms.create')       
                as $authorizedRole
            ) 
        {            
            $response = $this->getCreateRoomFormAs($authorizedRole);

            $response->assertStatus(200)
                     ->assertSee('Crear sala')
                     ->assertSee('Nombre')
                     ->assertSee('Descripción');
        }
    }   

    public function test_unauthorized_user_cannot_view_create_room_form()
    {
        foreach (
            $this->getUnauthorizedRoles('rooms.create') as $unauthorizedRole
        ) {
            $response = $this->getCreateRoomFormAs($unauthorizedRole);

            $response->assertStatus(403)
                     ->assertDontSee('Crear sala')
                     ->assertDontSee('Nombre')
                     ->assertDontSee('Descripción');
        }        
    }   

    private function CreateRoomAs(string $roleName, array $newData): TestResponse
    {
        return $this->actingAsRole($roleName)
            ->from(route('rooms.create'))
            ->post(route('rooms.store', $newData));
    }    

    public function test_authorized_user_can_create_a_room()
    {
        foreach (
            $this->getAuthorizedRoles('rooms.store') as $authorizedRole
        ) {
            $room = Room::factory()->raw();
            $response = $this->CreateRoomAs($authorizedRole, $room);

            $response->assertRedirect(route('rooms.index'))
                     ->assertSessionHas('msg');

            foreach ($room as $key => $value) {
                $this->assertDatabaseHas('rooms', [
                    $key => $value,
                ]);
            }
        }
    }

    public function test_unauthorized_user_cannot_create_a_room()
    {
        foreach (
            $this->getUnauthorizedRoles('rooms.store') as $unauthorizedRole
        ) {
            $room = Room::factory()->raw();
            $response = $this->CreateRoomAs($unauthorizedRole, $room);

            $response->assertStatus(403)
                     ->assertSessionMissing('msg');

            foreach ($room as $key => $value) {
                $this->assertDatabaseMissing('rooms', [
                    $key => $value,
                ]);
            }
        }
    }
         
    #[DataProvider('invalidRoomDataProvider')]
    public function test_validation_errors_for_invalid_room_data(array $invalidData, array $expectedErrors): void
    {
        foreach (
            $this->getAuthorizedRoles('rooms.store') as $authorizedRole
        ) {
            $response = $this->CreateRoomAs($authorizedRole, $invalidData);

            $response->assertRedirect(route('rooms.create'))
                     ->assertSessionHasErrors($expectedErrors);

            foreach($invalidData as $key => $value) {
                $this->assertDatabaseMissing('rooms', [
                    $key => $value,
                ]);
            }                            
        }
    }

    public static function invalidRoomDataProvider(): array
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
            // description
            'description too short' => [
                'invalidData' => ['name' => 'short', 'description' => 'Short'],
                'expectedErrors' => ['description'],
            ],
            'description too long' => [
                'invalidData' => ['name' => 'Test Room', 'description' => str_repeat('A', 2001)],
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
