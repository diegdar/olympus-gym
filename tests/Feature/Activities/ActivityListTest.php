<?php
declare(strict_types=1);

namespace Tests\Feature\Activities;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;

class ActivityListTest extends TestCase
{
    use RefreshDatabase;

    protected array $authorizedRoles = [
        'super-admin',
        'admin',
        'member'
    ];
    
    protected Collection $activities;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();         
        $this->activities = Activity::all();
    }

    private function actingAsRole(string $roleName): self
    {
        $user = User::factory()->create()->assignRole($roleName);
        return $this->actingAs($user);
    }

    private function getActivitiesIndexAs(?string $roleName = null)
    {
        $user = User::factory()->create()->assignRole($roleName);
        return $this->actingAs($user)->get(route('activities.index'));
    }

    public function test_authorized_user_can_see_activity_list()
    {
        foreach ($this->authorizedRoles as $roleName) {
            $response = $this->getActivitiesIndexAs($roleName);

            $response->assertStatus(200)
                        ->assertSee('Horario Actividades')
                        ->assertSee('Hora');
            foreach ($this->activities as $activity) {
                $response->assertSee($activity->name);
            }
        }
    }

    public function test_unauthorized_user_gets_403()
    {
            $response = $this->getActivitiesIndexAs();
            $response->assertStatus(403);
    }
}
