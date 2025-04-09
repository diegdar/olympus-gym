<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subscription::create([
            'fee' => 'monthly',
            'description' => 'Suscripción mensual',
            'price' => 50,
            'duration' => 1
        ]);

        Subscription::create([
            'fee' => 'quarterly',
            'description' => 'Suscripción trimestral',
            'price' => 135,
            'duration' => 3
        ]);

        Subscription::create([
            'fee' => 'yearly',
            'description' => 'Suscripción anual',
            'price' => 480,
            'duration' => 12
        ]);
    }
}
