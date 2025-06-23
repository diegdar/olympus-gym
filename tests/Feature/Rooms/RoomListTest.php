<?php
declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class RoomListTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    // permissions
    protected const PERMISSION_LIST_ROOMS = 'rooms.index';
    protected const PERMISSION_CREATE_ROOM = 'rooms.create';
        protected const PERMISSION_SHOW_ROOM = 'rooms.show';

    protected const PERMISSION_EDIT_ROOM = 'rooms.edit';
    protected const PERMISSION_DESTROY_ROOM = 'rooms.destroy';
    // routes
    protected const ROUTE_ROOMS_INDEX = 'rooms.index';
    protected const ROUTE_CREATE_ROOM_VIEW = 'rooms.create';
    protected const ROUTE_SHOW_ROOM = 'rooms.show';
    protected const ROUTE_EDIT_ROOM_VIEW = 'rooms.edit';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function getRoomsListAs(?string $roleName = null)
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_ROOMS_INDEX));
    }

    public function test_authorized_user_can_see_rooms(): void
    {
        $rooms = Room::all();
        foreach (
                $this->getAuthorizedRoles                (self::PERMISSION_LIST_ROOMS)       
                as $authorizedRole
            ) 
        { 
            $response = $this->getRoomsListAs($authorizedRole);
            $response->assertStatus(200)
                     ->assertSee('Lista de salas')
                     ->assertSeeInOrder(['Id', 'Sala']);

            foreach ($rooms as $room) {
                $response->assertSeeText($room->name);
            }
        }
    }

    public function test_unauthorized_user_cannot_see_rooms_list(): void
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION_LIST_ROOMS) as $unauthorizedRole
        ) {
            $response = $this->getRoomsListAs($unauthorizedRole);
            $response->assertStatus(403)
                     ->assertDontSee('Lista de salas')
                     ->assertDontSee('Id')
                     ->assertDontSee('Sala');
        }
    }  

    public function test_create_button_is_visible_depends_on_permission(): void
    {
        $this->assertButtonVisible(
            self::PERMISSION_CREATE_ROOM,
            'Crear sala'
        );

        $this->assertButtonNotVisibleOr403(
            self::PERMISSION_CREATE_ROOM,
            'Crear sala'
        );
    }

    public function test_edit_button_is_visible_depends_on_permission(): void
    {
        $room = Room::factory()->create();

        $this->assertButtonVisible(
            self::PERMISSION_EDIT_ROOM,
            'Editar'
        );

        $this->assertButtonNotVisibleOr403(
            self::PERMISSION_EDIT_ROOM,
            'Editar'
        );
    }

    public function test_destroy_button_is_visible_depends_on_permission(): void
    {
        $room = Room::factory()->create();

        $this->assertButtonVisible(
            self::PERMISSION_DESTROY_ROOM,
            'Borrar'
        );

        $this->assertButtonNotVisibleOr403(
            self::PERMISSION_DESTROY_ROOM,
            'Borrar'            
        );
    }

    private function assertButtonVisible(string $permission, string $text): void
    {
        foreach ($this->getAuthorizedRoles($permission) as $authorizedRole) {
            $response = $this->getRoomsListAs($authorizedRole);
            $response->assertStatus(200)
                     ->assertSeeText($text);
        }
    }

    private function assertButtonNotVisibleOr403(string $permission, string $text): void
    {
        foreach ($this->getUnauthorizedRoles($permission) as $unauthorizedRole) {
            $response = $this->getRoomsListAs($unauthorizedRole);

            if ($response->status() === 200) {
                $response->assertDontSeeText($text);
            } else {
                $response->assertStatus(403);
            }
        }
    }

}