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

    protected array $authorizedRoles;

    protected array $unauthorizedRoles;

    protected const PERMISSION_NAME = 'rooms.index';
    protected const ROUTE = 'rooms.index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->authorizedRoles = $this->getAuthorizedRoles(self::PERMISSION_NAME);

        $this->unauthorizedRoles = $this->getUnauthorizedRoles(self::PERMISSION_NAME);              
    }

    private function getRoomsListAs(?string $roleName = null)
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE));
    }

    public function test_authorized_user_can_see_rooms()
    {
        $rooms = Room::all();
        foreach ($this->authorizedRoles as $authorizedRole) {
            $response = $this->getRoomsListAs($authorizedRole);

            $response->assertStatus(200)
                        ->assertSee('nombre')
                        ->assertSee('descripción');
            foreach ($rooms as $room) {
                $response->assertSee($room->name);
            }
        }
    }

    public function test_unauthorized_user_gets_403_when_trying_to_see_rooms()
    {
        foreach ($this->unauthorizedRoles as $unauthorizedRole) {
            $response = $this->getRoomsListAs($unauthorizedRole);

            $response->assertStatus(403)
                        ->assertDontSee('nombre')
                        ->assertDontSee('descripción');
        }
    }


}
