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
    protected const ROUTE_STORE_ROOM = 'rooms.store';
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
        $authorizedRoles = $this->getAuthorizedRoles(self::PERMISSION_LIST_ROOMS);
        foreach ($authorizedRoles as $role) {
            $response = $this->getRoomsListAs($role);
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
        $unauthorizedRoles = $this->getUnauthorizedRoles(self::PERMISSION_LIST_ROOMS);
        foreach ($unauthorizedRoles as $role) {
            $response = $this->getRoomsListAs($role);
            $response->assertStatus(403)
                     ->assertDontSee('Lista de salas')
                     ->assertDontSee('Id')
                     ->assertDontSee('Sala');
        }
    }  

    public function test_create_button_is_visible_depends_on_permission(): void
    {
        $authorizedRoles = $this->getAuthorizedRoles(self::PERMISSION_CREATE_ROOM);
        foreach ($authorizedRoles as $role) {
            $response = $this->getRoomsListAs($role);
            $response->assertStatus(200);
            $response->assertSeeText('Crear sala');
            $response->assertSee(route(self::ROUTE_CREATE_ROOM_VIEW), false);
        }

        $unauthorizedRoles = $this->getUnauthorizedRoles(self::PERMISSION_CREATE_ROOM);
        foreach ($unauthorizedRoles as $role) {
            $response = $this->getRoomsListAs($role);

            if ($response->status() === 200) {
                $response->assertDontSeeText('Crear sala');
                $response->assertDontSee(route(self::ROUTE_CREATE_ROOM_VIEW), false);
            } else {
                $response->assertStatus(403);
            }
        }
    }

    public function test_show_button_is_visible_depends_on_permission(): void
    {
        $room = Room::factory()->create();

        $authorizedRoles = $this->getAuthorizedRoles(self::PERMISSION_SHOW_ROOM);
        foreach ($authorizedRoles as $role) {
            $response = $this->getRoomsListAs($role);
            $response->assertStatus(200);
            $response->assertSeeText('Ver');
            $response->assertSee(route(self::ROUTE_SHOW_ROOM, $room->id), false);
        }

        $unauthorizedRoles = $this->getUnauthorizedRoles(self::PERMISSION_SHOW_ROOM);
        foreach ($unauthorizedRoles as $role) {
            $response = $this->getRoomsListAs($role);

            if ($response->status() === 200) {
                $response->assertDontSeeText('Ver');
                $response->assertDontSee(route(self::ROUTE_SHOW_ROOM, $room->id), false);
            } else {
                $response->assertStatus(403);
            }
        }
    }

    public function test_edit_button_is_visible_depends_on_permission(): void
    {
        $room = Room::factory()->create();

        $authorizedRoles = $this->getAuthorizedRoles(self::PERMISSION_EDIT_ROOM);
        foreach ($authorizedRoles as $role) {
            $response = $this->getRoomsListAs($role);
            $response->assertStatus(200);
            $response->assertSeeText('Editar');
            $response->assertSee(route(self::ROUTE_EDIT_ROOM_VIEW, $room->id), false);
        }

        $unauthorizedRoles = $this->getUnauthorizedRoles(self::PERMISSION_DESTROY_ROOM);
        foreach ($unauthorizedRoles as $role) {
            $response = $this->getRoomsListAs($role);

            if ($response->status() === 200) {
                $response->assertDontSeeText('Editar');
                $response->assertDontSee(route(self::ROUTE_EDIT_ROOM_VIEW, $room->id), false);
            } else {
                $response->assertStatus(403);
            }
        }
    }

    public function test_destroy_button_is_visible_depends_on_permission(): void
    {
        $room = Room::factory()->create();
        $authorizedRoles = $this->getAuthorizedRoles(self::PERMISSION_DESTROY_ROOM);
        foreach ($authorizedRoles as $role) {
            $response = $this->getRoomsListAs($role);
            $response->assertStatus(200);
            $response->assertSeeText('Borrar');
            $response->assertSee(route(self::ROUTE_EDIT_ROOM_VIEW, $room->id), false);
        }

        $unauthorizedRoles = $this->getUnauthorizedRoles(self::PERMISSION_DESTROY_ROOM);
        foreach ($unauthorizedRoles as $role) {
            $response = $this->getRoomsListAs($role);

            if ($response->status() === 200) {
                $response->assertDontSeeText('Borrar');
                $response->assertDontSee(route(self::ROUTE_EDIT_ROOM_VIEW, $room->id), false);
            } else {
                $response->assertStatus(403);
            }
        }
    }

}