<?php
declare(strict_types=1);

namespace Tests\Feature\Activities;

use Tests\Traits\TestHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Testing\TestResponse;
use Database\Seeders\RoleSeeder;
use App\Models\Activity;

class ShowActivityTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);                          
    }

    private function showActivityAs(string $roleName, int $activityId): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route('activities.show',  $activityId));
    }

    public function test_authorized_user_can_see_a_specific_activity()
    {
        foreach (
                $this->getAuthorizedRoles('activities.show')
                as $authorizedRole
            ) 
        {
            $activitytoShow = Activity::factory()->create();
            $response = $this->showActivityAs($authorizedRole, $activitytoShow->id);
            $response->assertStatus(200)
                     ->assertSee($activitytoShow->name)
                     ->assertSee($activitytoShow->description)
                     ->assertSee($activitytoShow->duration);
        }
    }

    public function test_unauthorized_user_cannot_see_a_specific_activity()
    {
        foreach (
                $this->getUnauthorizedRoles('activities.show')
                as $unauthorizedRole
            ) 
        {
            $activitytoShow = Activity::factory()->create();
            $response = $this->showActivityAs($unauthorizedRole, $activitytoShow->id);

            $response->assertStatus(403)
                     ->assertDontSee($activitytoShow->name)
                     ->assertDontSee($activitytoShow->description)
                     ->assertDontSee($activitytoShow->duration . ' minutos');
        }
    }
}
