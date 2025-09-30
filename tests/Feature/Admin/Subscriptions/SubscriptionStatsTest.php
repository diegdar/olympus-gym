<?php
declare(strict_types=1);

namespace Tests\Feature\Admin\Subscriptions;

use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class SubscriptionStatsTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        Subscription::create(['fee' => 'monthly','description' => 'm','price' => 10,'duration' => 1]);
        Subscription::create(['fee' => 'quarterly','description' => 'q','price' => 25,'duration' => 3]);
        Subscription::create(['fee' => 'yearly','description' => 'y','price' => 80,'duration' => 12]);
    }

    private function seedScenario(): void
    {
        $monthly = Subscription::where('fee','monthly')->first();
        $quarterly = Subscription::where('fee','quarterly')->first();

        $m1 = User::factory()->create()->assignRole('member');
        $m2 = User::factory()->create()->assignRole('member');
        $m3 = User::factory()->create()->assignRole('member');

        foreach ([[$m1,$monthly,1], [$m2,$monthly,1], [$m3,$quarterly,3]] as [$user,$sub,$months]) {
            $start = now();
            $user->subscriptions()->attach($sub->id, [
                'start_date' => $start,
                'end_date' => $start->copy()->addMonths($months),
                'payment_date' => $start,
                'status' => 'active',
            ]);
        }
    }

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.subscriptions.stats'))->assertRedirect('/login');
    }

    public function test_user_without_permission_cannot_access_index(): void
    {
        $memberUser = User::factory()->create()->assignRole('member');
        $this->actingAs($memberUser)
                ->get(route('admin.subscriptions.stats'))
                ->assertForbidden();
    }

    public function test_authorized_user_can_view_index_page(): void
    {
        $adminUser = User::factory()->create()->assignRole('admin');
        $this->actingAs($adminUser)
                ->get(route('admin.subscriptions.stats'))
                ->assertOk()
                ->assertSee('Estadísticas');
    }

    public function test_percentages_endpoint_structure(): void
    {
        $this->seedScenario();
        $adminUser = User::factory()->create()->assignRole('admin');
        $this->actingAs($adminUser);
        $this->getJson(route('admin.subscriptions.percentages'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['fee','users','percentage','fee_translated']
                ],
                'total_active_users',
                'average_age'
            ]);
    }

    public function test_percentages_values_are_calculated_correctly(): void
    {
        $this->seedScenario();
        $adminUser = User::factory()->create()->assignRole('admin');
        $this->actingAs($adminUser);

    $response = $this->getJson(route('admin.subscriptions.percentages'));
        $data = collect($response->json('data'));

        $monthly = $data->firstWhere('fee','monthly');
        $quarterly = $data->firstWhere('fee','quarterly');

        $this->assertEquals(3, $response->json('total_active_users'));
        $this->assertEquals(2, $monthly['users']);
        $this->assertEquals(1, $quarterly['users']);
        $this->assertEquals(round((2/3)*100,2), $monthly['percentage']);
    $this->assertEquals(round((1/3)*100,2), $quarterly['percentage']);
    $this->assertTrue($response->json('average_age') === null || is_numeric($response->json('average_age')));
    }

    public function test_monthly_net_json_export_returns_expected_structure(): void
    {
        $this->seedScenario();
        $adminUser = User::factory()->create()->assignRole('admin');
        $this->actingAs($adminUser);

        $year = now()->year;
        $response = $this->get(route('admin.subscriptions.monthly-net.export.json', ['year' => $year]));
        $response->assertOk();
        $this->assertStringContainsString((string)$year, $response->headers->get('content-disposition'));
        $response->assertJsonStructure([
                'year',
                'data' => [
                    ['month','month_name','signups','cancellations','net']
                ]
            ]);
    }

    public function test_monthly_net_excel_export_downloads_file(): void
    {
        $this->seedScenario();
        $adminUser = User::factory()->create()->assignRole('admin');
        $this->actingAs($adminUser);

        $year = now()->year;
        $response = $this->get(route('admin.subscriptions.monthly-net.export.excel', ['year' => $year]));
        $response->assertOk();
    $this->assertTrue(str_contains($response->headers->get('content-disposition'), '.csv'));
    }
}
