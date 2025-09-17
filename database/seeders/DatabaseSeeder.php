<?php
declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; 

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // In testing, keep seeding minimal to speed up and reduce memory usage
        if (app()->environment('testing')) {
            $this->call(RoleSeeder::class);
            $this->call(SubscriptionSeeder::class);
            $this->call(ActivitySeeder::class);
            $this->call(RoomSeeder::class);
            $this->call(UserSeeder::class);
            // Skip SubscriptionUserSeeder, ActivitySchedulesSeeder and UserAttendanceSeeder in tests
            return;
        }

        // Default (non-testing) seeding
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SubscriptionSeeder::class);
        $this->call(SubscriptionUserSeeder::class);
        $this->call(ActivitySeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(ActivitySchedulesSeeder::class);
        $this->call(UserAttendanceSeeder::class);
    }

}
