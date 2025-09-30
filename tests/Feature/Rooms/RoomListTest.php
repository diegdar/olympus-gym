<?php
declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;
use Database\Seeders\RoleSeeder;
use Database\Seeders\RoomSeeder;

class RoomListTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, RoomSeeder::class]);
    }

    private function getRoomsListAs(?string $roleName = null)
    {
        return $this->actingAsRole($roleName)->get(route('rooms.index'));
    }

    public function test_authorized_user_can_see_rooms(): void
    {
        $rooms = Room::all();
        foreach (
                $this->getAuthorizedRoles                ('rooms.index')       
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
            $this->getUnauthorizedRoles('rooms.index') as $unauthorizedRole
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
            'rooms.create',
            'Crear sala'
        );

        $this->assertButtonNotVisibleOr403(
            'rooms.create',
            'Crear sala'
        );
    }

    public function test_edit_button_is_visible_depends_on_permission(): void
    {
        $room = Room::factory()->create();

        $this->assertButtonVisible(
            'rooms.edit',
            'Editar'
        );

        $this->assertButtonNotVisibleOr403(
            'rooms.edit',
            'Editar'
        );
    }

    public function test_destroy_button_is_visible_depends_on_permission(): void
    {
        $room = Room::factory()->create();

        $this->assertButtonVisible(
            'rooms.destroy',
            'Borrar'
        );

        $this->assertButtonNotVisibleOr403(
            'rooms.destroy',
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