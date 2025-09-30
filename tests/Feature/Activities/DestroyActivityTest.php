<?php
declare(strict_types=1);

namespace Tests\Feature\Activities;


use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;

class DestroyActivityTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function destroyActivityAs(string $roleName, int $activityId): TestResponse
    {
        return $this->actingAsRole($roleName)->delete(route('activities.destroy', $activityId));
    }

    public function test_can_destroy_activity_as_authorized_user()
    {        
        foreach (
                $this->getAuthorizedRoles('activities.destroy')
                as $authorizedRole
            ) 
        { 
            $activityToDestroy = Activity::factory()->create();
            $response = $this->destroyActivityAs($authorizedRole, $activityToDestroy->id);
            $response->assertStatus(302)
                      ->assertRedirect(route('activities.index'));

            $this->assertDatabaseMissing('activities', ['id' => $activityToDestroy->id]);
        }
    }

    public function test_cannot_destroy_a_activity_as_unauthorized_user()
    {        
        $activityToDestroy = Activity::factory()->create();
        foreach (
            $this->getUnauthorizedRoles('activities.destroy') as $unauthorizedRole
        ) {
            $response = $this->destroyActivityAs($unauthorizedRole, $activityToDestroy->id);
            $response->assertStatus( 403);

            $this->assertDatabaseHas('activities', ['id' => $activityToDestroy->id]);
        }
    }    

}
