<?php
declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RoleTestHelper;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;
use Illuminate\Testing\TestResponse;


class ShowRoomTest extends TestCase
{
    use RefreshDatabase, RoleTestHelper;

    protected array $authorizedRoles;

    protected array $unauthorizedRoles;

    protected const PERMISSION_NAME = 'rooms.show';
    protected const ROUTE_EDIT_ROOM_VIEW = 'rooms.show';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    
    private function getShowRoomAs(string $roleName, Room $room): TestResponse
    {
        return $this->actingAsRole($roleName)
                    ->get(route(self::ROUTE_EDIT_ROOM_VIEW, $room));
    }
    public function test_authorized_user_can_view_show_room()
    {
        foreach ($this->getAuthorizedRoles(self::PERMISSION_NAME) as $authorizedRole) {
            $roomData = Room::factory()->raw();
            $roomToShow = Room::create($roomData);
            $response = $this->getShowRoomAs($authorizedRole, $roomToShow);

            $response->assertStatus(200)
                     ->assertSee($roomToShow->name)
                     ->assertSee($roomToShow->description);
        }
    }

    public function test_unauthorized_user_cannot_view_show_room()
    {
        foreach ($this->getUnauthorizedRoles(self::PERMISSION_NAME) as $unauthorizedRole) {
            $roomToShow = Room::factory()->create();
            $response = $this->getShowRoomAs($unauthorizedRole, $roomToShow);

            $response->assertStatus(403)
                     ->assertDontSee($roomToShow->name)
                     ->assertDontSee($roomToShow->description);
        }
    }
}
