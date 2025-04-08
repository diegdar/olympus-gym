<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fees = ['monthly' => 1, 'quarterly' => 3, 'yearly' => 12];        
        $fee = fake()->randomElement(array_keys($fees));
        $duration = $fees[$fee];

        return [
            'fee' => $fee,
            'price' => fake()->numberBetween(10, 100),
            'duration' => $duration,      
            'description' => fake()->sentence(),
        ];
    }
}
