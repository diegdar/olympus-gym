<?php
declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RoleTestHelper;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class DestroyRoomTest extends TestCase
{
    use RefreshDatabase, RoleTestHelper;

    protected array $authRolesForDestroyRoom;    
    protected array $unauthRolesForDestroyRoom;
    protected const PERMISSION_DESTROY_ROOM = 'rooms.destroy';
    // Routes
    protected const ROUTE_ROOM_INDEX = 'rooms.index';
    protected const ROUTE_DESTROY_ROOM = 'rooms.destroy';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->authRolesForDestroyRoom = $this->getAuthorizedRoles(self::PERMISSION_DESTROY_ROOM);
        $this->unauthRolesForDestroyRoom = $this->getUnauthorizedRoles(self::PERMISSION_DESTROY_ROOM);
    }

    private function destroyRoomAs(string $roleName, int $roomId): TestResponse
    {
        return $this->actingAsRole($roleName)->delete(route(self::ROUTE_DESTROY_ROOM, $roomId));
    }

    public function test_can_destroy_room_as_authorized_user()
    {        
        foreach ($this->authRolesForDestroyRoom as $authorizedRole) {
            $roomToDestroy = Room::factory()->create();
            $response = $this->destroyRoomAs($authorizedRole, $roomToDestroy->id);
            $response->assertStatus(302)
                     ->assertRedirect(route(self::ROUTE_ROOM_INDEX));

            $this->assertDatabaseMissing('rooms', ['id' => $roomToDestroy->id]);
        }
    }

    public function test_cannot_destroy_a_room_as_unauthorized_user()
    {        
        $roomToDestroy = Room::factory()->create();
        foreach ($this->unauthRolesForDestroyRoom as $unauthorizedRole) {
            $response = $this->destroyRoomAs($unauthorizedRole, $roomToDestroy->id);
            $response->assertStatus( 403);

            $this->assertDatabaseHas('rooms', ['id' => $roomToDestroy->id]);
        }
    }    

}
