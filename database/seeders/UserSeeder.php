<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => env('SUPER_ADMIN_NAME'),
            'email' => env('SUPER_ADMIN_EMAIL'),
            'password' => Hash::make(env('SUPER_ADMIN_PASSWORD'))
        ])->assignRole('super-admin'); 

        User::factory()->create([
            'name' => env('ADMIN_NAME'),
            'email' => env('ADMIN_EMAIL'),
            'password' => Hash::make(env('ADMIN_PASSWORD'))
        ])->assignRole('admin');        

        User::factory()->create([
            'name' => 'member',
            'email' => 'member@member.com',
            'password' => Hash::make('PassNix$123')
        ])->assignRole('member'); 
        
        User::factory(15)->create()
            ->each(function ($user) {
                $user->assignRole('member');
        });
    }
}
