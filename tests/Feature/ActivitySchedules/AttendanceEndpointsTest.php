<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use App\Models\ActivitySchedule;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class AttendanceEndpointsTest extends TestCase
{
    use RefreshDatabase, TestHelper;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function makeScheduleWithUsers(int $count = 1, array $pivot = []): array
    {
        $schedule = ActivitySchedule::factory()->create();
        $users = User::factory()->count($count)->create();
        foreach ($users as $i => $u) {
            $schedule->users()->attach($u->id, array_merge(['attended' => (bool)($pivot[$i]['attended'] ?? false)], []));
        }
        return [$schedule, $users];
    }

    public function test_enrolled_users_json_returns_attended_field(): void
    {
    [$schedule, $users] = $this->makeScheduleWithUsers(1, [['attended' => true]]);
    /** @var \App\Models\User $actor */
    $actor = User::factory()->create()->assignRole('admin');
        $this->actingAs($actor);

        $resp = $this->getJson(route('activity.schedules.enrolled-users', $schedule));
        $resp->assertOk()
            ->assertJsonStructure(['data' => [['id','name','email','attended']]])
            ->assertJsonPath('data.0.attended', true);
    }

    public function test_export_csv_includes_attended_and_semicolon_delimiter_and_bom(): void
    {
    [$schedule, $users] = $this->makeScheduleWithUsers(1, [['attended' => false]]);
    /** @var \App\Models\User $actor */
    $actor = User::factory()->create()->assignRole('admin');
        $this->actingAs($actor);

        $resp = $this->get(route('activity.schedules.enrolled-users', [$schedule, 'format' => 'csv']));
        $resp->assertOk();
        $resp->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    $content = $resp->getContent();
        // BOM
        $this->assertSame("\xEF\xBB\xBF", substr($content, 0, 3));
        // Encabezado y fila con ';' y columna attended
        $this->assertStringContainsString("ID;Nombre;Email;attended", $content);
        $this->assertStringContainsString(";false", $content);
    }

    public function test_update_attendance_updates_pivot(): void
    {
    [$schedule, $users] = $this->makeScheduleWithUsers(2, [['attended' => false], ['attended' => false]]);
    /** @var \App\Models\User $actor */
    $actor = User::factory()->create()->assignRole('admin');
        $this->actingAs($actor);

        $payload = [
            'records' => [
                ['id' => $users[0]->id, 'attended' => true],
                ['id' => $users[1]->id, 'attended' => false],
            ],
        ];
        $resp = $this->putJson(route('activity.schedules.attendance', $schedule), $payload);
        $resp->assertOk()->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('activity_schedule_user', [
            'activity_schedule_id' => $schedule->id,
            'user_id' => $users[0]->id,
            'attended' => 1,
        ]);
        $this->assertDatabaseHas('activity_schedule_user', [
            'activity_schedule_id' => $schedule->id,
            'user_id' => $users[1]->id,
            'attended' => 0,
        ]);
    }
}
