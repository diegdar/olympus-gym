<?php
declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;

class DestroyRoomTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected const PERMISSION = 'rooms.destroy';
    // Routes
    protected const ROUTE_ROOM_INDEX = 'rooms.index';
    protected const ROUTE_DESTROY_ROOM = 'rooms.destroy';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function destroyRoomAs(string $roleName, int $roomId): TestResponse
    {
        return $this->actingAsRole($roleName)->delete(route(self::ROUTE_DESTROY_ROOM, $roomId));
    }

    public function test_can_destroy_room_as_authorized_user()
    {        
        foreach (
                $this->getAuthorizedRoles                (self::PERMISSION)       
                as $authorizedRole
            ) 
        { 
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
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION) as $unauthorizedRole
        ) {
            $response = $this->destroyRoomAs($unauthorizedRole, $roomToDestroy->id);
            $response->assertStatus( 403);

            $this->assertDatabaseHas('rooms', ['id' => $roomToDestroy->id]);
        }
    }    

}
