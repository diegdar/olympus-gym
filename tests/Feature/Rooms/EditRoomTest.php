<?php
declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;

class EditRoomTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected const PERMISSION = 'rooms.edit';
    protected const ROUTE_EDIT_ROOM_VIEW = 'rooms.edit';
    protected const ROUTE_UPDATE_ROOM = 'rooms.update';
    protected const ROUTE_INDEX = 'rooms.index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function getEditRoomFormAs(string $roleName, Room $room): TestResponse
    {
        return $this->actingAsRole($roleName)
                    ->get(route(self::ROUTE_EDIT_ROOM_VIEW, $room));
    }    

    public function test_authorized_user_can_view_edit_room_form()
    {        
        foreach (
                $this->getAuthorizedRoles                (self::PERMISSION)       
                as $authorizedRole
            ) 
        {            
            $roomData = Room::factory()->raw();
            $roomToEdit = Room::create($roomData);
            $response = $this->getEditRoomFormAs($authorizedRole, $roomToEdit);

            $response->assertStatus(200)
                     ->assertSee('Editar sala');
                     
            foreach($roomData as $key => $value) {
                $response->assertSee($key)
                         ->assertSee($value);
            }
        }
    }

    public function test_unauthorized_user_cannot_view_edit_room_form()
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION) as $unauthorizedRole
        ) {            
            $roomToEdit = Room::factory()->create();
            $response = $this->getEditRoomFormAs($unauthorizedRole, $roomToEdit);

            $response->assertStatus(403);
        }
    }

    private function submitRoomToUpdate(string $roleName, Room $room, array $Newdata): TestResponse
    {
        return $this->actingAsRole($roleName)
            ->from(route(self::ROUTE_EDIT_ROOM_VIEW, $room))
            ->put(route(self::ROUTE_UPDATE_ROOM, $room), $Newdata);
    }  
    
    public function test_can_update_room_with_valid_data()
    {
        
        foreach (
                $this->getAuthorizedRoles                (self::PERMISSION)       
                as $authorizedRole
            ) 
        {             
            $roomToEdit = Room::factory()->create();
            $newRoomData = Room::factory()->raw();
            $response = $this->submitRoomToUpdate($authorizedRole, $roomToEdit, $newRoomData);

            $response->assertRedirect(route(self::ROUTE_INDEX))
                     ->assertStatus(302)
                     ->assertSessionHas('msg');

            foreach ($newRoomData as $key => $value) {
                $this->assertDatabaseHas('rooms', [
                    'id' => $roomToEdit->id,
                    $key => $value,
                ]);
            }
        }
    }

    #[DataProvider('invalidRoomDataProvider')]
    public function test_validation_errors_are_shown_if_data_is_invalid(array $invalidData, array $expectedErrors): void
    {
        foreach (
                $this->getAuthorizedRoles                (self::PERMISSION)       
                as $authorizedRole
            ) 
        {             
            $roomToEdit = Room::factory()->create();
            $response = $this->submitRoomToUpdate($authorizedRole, $roomToEdit, $invalidData);

            $response->assertRedirect(route(self::ROUTE_EDIT_ROOM_VIEW, $roomToEdit))
                     ->assertSessionHasErrors($expectedErrors);
        }

        foreach($invalidData as $key => $value) {
            $this->assertDatabaseMissing('rooms', [
                'id' => $roomToEdit->id,
                $key => $value,
            ]);
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
