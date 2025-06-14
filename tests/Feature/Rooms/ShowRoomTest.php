<?php
declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;
use Illuminate\Testing\TestResponse;

class ShowRoomTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected const PERMISSION = 'rooms.show';
    protected const ROUTE = 'rooms.show';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }
    
    private function showRoomAs(string $roleName, int $roomId): TestResponse
    {
        return $this->actingAsRole($roleName)
                    ->get(route(self::ROUTE, $roomId)
                );
    }
    public function test_authorized_user_can_see_a_specific_room()
    {
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION) 
            as $authorizedRole
        ) {
            $roomToShow = Room::factory()->create();
            $response = $this->showRoomAs($authorizedRole, $roomToShow->id);

            $response->assertStatus(200)
                     ->assertSee($roomToShow->name)
                     ->assertSee($roomToShow->description);
        }
    }

    public function test_unauthorized_user_cannot_see_a_specific_room()
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION) 
            as $unauthorizedRole
        ) {
            $roomToShow = Room::factory()->create();
            $response = $this->showRoomAs($unauthorizedRole, $roomToShow->id);

            $response->assertStatus(403)
                     ->assertDontSee($roomToShow->name)
                     ->assertDontSee($roomToShow->description);
        }
    }
}
