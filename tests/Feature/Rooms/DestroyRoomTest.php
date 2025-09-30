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

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function destroyRoomAs(string $roleName, int $roomId): TestResponse
    {
        return $this->actingAsRole($roleName)->delete(route('rooms.destroy', $roomId));
    }

    public function test_can_destroy_room_as_authorized_user()
    {        
        foreach (
                $this->getAuthorizedRoles                ('rooms.destroy')       
                as $authorizedRole
            ) 
        { 
            $roomToDestroy = Room::factory()->create();
            $response = $this->destroyRoomAs($authorizedRole, $roomToDestroy->id);
            $response->assertStatus(302)
                     ->assertRedirect(route('rooms.index'));

            $this->assertDatabaseMissing('rooms', ['id' => $roomToDestroy->id]);
        }
    }

    public function test_cannot_destroy_a_room_as_unauthorized_user()
    {        
        $roomToDestroy = Room::factory()->create();
        foreach (
            $this->getUnauthorizedRoles('rooms.destroy') as $unauthorizedRole
        ) {
            $response = $this->destroyRoomAs($unauthorizedRole, $roomToDestroy->id);
            $response->assertStatus( 403);

            $this->assertDatabaseHas('rooms', ['id' => $roomToDestroy->id]);
        }
    }    

}
