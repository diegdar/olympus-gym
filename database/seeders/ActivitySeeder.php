<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Activity::create([
            'name' => 'Yoga',
            'description' => 'Clase de yoga para principiantes',
            'duration' => 60,
        ]);
        Activity::create([
            'name' => 'Pilates',
            'description' => 'Clase de pilates para tonificar el cuerpo',
            'duration' => 45,
        ]);
        Activity::create([
            'name' => 'Zumba',
            'description' => 'Clase de zumba para quemar calorías',
            'duration' => 50,
        ]);
        Activity::create([
            'name' => 'Entrenamiento funcional',
            'description' => 'Entrenamiento funcional para mejorar la fuerza y resistencia',
            'duration' => 60,
        ]);
        Activity::create([
            'name' => 'Entrenamiento de fuerza',
            'description' => 'Entrenamiento de fuerza para aumentar la masa muscular',
            'duration' => 60,
        ]);
    }
}
