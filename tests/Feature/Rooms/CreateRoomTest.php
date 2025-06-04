<?php
declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RoleTestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CreateRoomTest extends TestCase
{
    use RefreshDatabase, RoleTestHelper;

    protected array $authorizedRoles;

    protected array $unauthorizedRoles;

    protected const PERMISSION_NAME = 'rooms.create';

    protected const ROUTE_ROOMS_INDEX = 'rooms.index';

    protected const ROUTE_CREATE_ROOM_VIEW = 'rooms.create';
    protected const ROUTE_STORE_ROOM = 'rooms.store';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->authorizedRoles = $this->getAuthorizedRoles(self::PERMISSION_NAME);

        $this->unauthorizedRoles = $this->getUnauthorizedRoles(self::PERMISSION_NAME);              
    }
    
    private function getCreateRoomFormAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_CREATE_ROOM_VIEW));
    }

    public function test_authorized_user_can_view_create_room_form()
    {
        foreach ($this->authorizedRoles as $authorizedRole) {
            $response = $this->getCreateRoomFormAs($authorizedRole);

            $response->assertStatus(200)
                     ->assertSee('Crear sala')
                     ->assertSee('Nombre')
                     ->assertSee('Descripción');
        }
    }   

    public function test_unauthorized_user_cannot_view_create_room_form()
    {
        foreach ($this->unauthorizedRoles as $unauthorizedRole) {
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
            ->from(route(self::ROUTE_CREATE_ROOM_VIEW))
            ->post(route(self::ROUTE_STORE_ROOM, $newData));
    }    

    public function test_authorized_user_can_create_a_room()
    {
        foreach ($this->authorizedRoles as $authorizedRole) {
            $room = Room::factory()->raw();
            $response = $this->CreateRoomAs($authorizedRole, $room);

            $response->assertRedirect(route(self::ROUTE_ROOMS_INDEX))
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
        foreach ($this->unauthorizedRoles as $unauthorizedRole) {
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
    public function test_validation_errors_for_invalid_room_data(array $formData, array $expectedErrors): void
    {
        foreach ($this->authorizedRoles as $authorizedRole) {
            $response = $this->CreateRoomAs($authorizedRole, $formData);

            $response->assertRedirect(route(self::ROUTE_CREATE_ROOM_VIEW))
                     ->assertSessionHasErrors($expectedErrors);

            $this->assertDatabaseMissing('rooms', [
                'name' => $formData['name'] ?? '',
                'description' => $formData['description'] ?? '',
            ]);                     
        }
    }

    public static function invalidRoomDataProvider(): array
    {
        return [
            // name
            'empty name' => [
                'formData' => ['name' => '', 'description' => 'A room without a name'],
                'expectedErrors' => ['name'],
            ],
            'name too short' => [
                'formData' => ['name' => 'A', 'description' => 'A room with a short name'],
                'expectedErrors' => ['name'],
            ],
            'name too long' => [
                'formData' => ['name' => str_repeat('A', 51), 'description' => 'A room with a very long name'],
                'expectedErrors' => ['name'],
            ],
            // description
            'description too short' => [
                'formData' => ['name' => 'short', 'description' => 'Short'],
                'expectedErrors' => ['description'],
            ],
            'description too long' => [
                'formData' => ['name' => 'Test Room', 'description' => str_repeat('A', 1001)],
                'expectedErrors' => ['description'],
            ],
            // all fields
            'all fields invalid' => [
                'formData' => ['name' => '', 'description' => str_repeat('A', 1001)],
                'expectedErrors' => ['name', 'description'],
            ],
        ];
    }

}
