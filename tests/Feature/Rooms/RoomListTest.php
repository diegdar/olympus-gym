<?php
declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\RoleTestHelper;

class RoomListTest extends TestCase
{
    use RefreshDatabase, RoleTestHelper;

    protected array $authRolesForRoomsList;
    protected array $authRolesForCreateRoom;
    protected array $unauthRolesForRoomsList;
    protected array $unauthRolesForCreateRoom;

    protected const PERMISSION_ROOMS_LIST = 'rooms.index';
    protected const PERMISSION_CREATE_ROOM = 'rooms.create';
    protected const ROUTE_ROOMS_INDEX = 'rooms.index';
    protected const ROUTE_CREATE_ROOM_VIEW = 'rooms.create';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->authRolesForRoomsList = $this->getAuthorizedRoles(self::PERMISSION_ROOMS_LIST);
        $this->unauthRolesForRoomsList = $this->getUnauthorizedRoles(self::PERMISSION_ROOMS_LIST);

        $this->authRolesForCreateRoom = $this->getAuthorizedRoles(self::PERMISSION_CREATE_ROOM);
        $this->unauthRolesForCreateRoom = $this->getUnauthorizedRoles(self::PERMISSION_CREATE_ROOM);
    }

    private function getRoomsListAs(?string $roleName = null)
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_ROOMS_INDEX));
    }

    private function assertRoomTableHeadersVisible($response): void
    {
        $response->assertSeeText('Id')
                 ->assertSeeText('Sala')
                 ->assertSeeText('Descripción');
    }

    private function assertRoomTableHeadersNotVisible($response): void
    {
        $response->assertDontSeeText('Id')
                 ->assertDontSeeText('Sala')
                 ->assertDontSeeText('Descripción');
    }

    public function test_authorized_user_can_see_rooms(): void
    {
        $rooms = Room::all();

        foreach ($this->authRolesForRoomsList as $role) {
            $response = $this->getRoomsListAs($role);
            $response->assertStatus(200);
            $this->assertRoomTableHeadersVisible($response);

            foreach ($rooms as $room) {
                $response->assertSeeText($room->name);
            }
        }
    }

    public function test_unauthorized_user_gets_403_when_trying_to_see_rooms(): void
    {
        foreach ($this->unauthRolesForRoomsList as $role) {
            $response = $this->getRoomsListAs($role);
            $response->assertStatus(403);
            $this->assertRoomTableHeadersNotVisible($response);
        }
    }

    public function test_authorized_user_can_see_create_room_button(): void
    {
        foreach ($this->authRolesForCreateRoom as $role) {
            $response = $this->getRoomsListAs($role);
            $response->assertStatus(200);
            $response->assertSeeText('Crear sala');
            $response->assertSee(route(self::ROUTE_CREATE_ROOM_VIEW), false);
        }
    }

    public function test_unauthorized_user_does_not_see_create_room_button(): void
    {
        foreach ($this->unauthRolesForCreateRoom as $role) {
            $response = $this->getRoomsListAs($role);

            if ($response->status() === 200) {
                $response->assertDontSeeText('Crear sala');
                $response->assertDontSee(route(self::ROUTE_CREATE_ROOM_VIEW), false);
            } else {
                $response->assertStatus(403);
            }
        }
    }
}
