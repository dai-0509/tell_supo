<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KpiTarget>
 */
class KpiTargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $targetType = fake()->randomElement(['weekly', 'monthly']);
        
        return [
            'user_id' => \App\Models\User::factory(),
            'target_type' => $targetType,
            'target_date' => $targetType === 'weekly' 
                ? \Carbon\Carbon::parse(fake()->dateTimeBetween('-4 weeks', '+2 weeks'))->startOfWeek()->format('Y-m-d')
                : \Carbon\Carbon::parse(fake()->dateTimeBetween('-3 months', '+1 month'))->startOfMonth()->format('Y-m-d'),
            'call_target' => $targetType === 'weekly' 
                ? fake()->numberBetween(50, 150)
                : fake()->numberBetween(200, 600),
            'appointment_target' => $targetType === 'weekly'
                ? fake()->numberBetween(5, 20)
                : fake()->numberBetween(20, 80),
        ];
    }

    /**
     * 週次目標
     */
    public function weekly()
    {
        return $this->state(function (array $attributes) {
            return [
                'target_type' => 'weekly',
                'target_date' => now()->startOfWeek()->format('Y-m-d'),
                'call_target' => fake()->numberBetween(50, 150),
                'appointment_target' => fake()->numberBetween(5, 20),
            ];
        });
    }

    /**
     * 月次目標
     */
    public function monthly()
    {
        return $this->state(function (array $attributes) {
            return [
                'target_type' => 'monthly',
                'target_date' => now()->startOfMonth()->format('Y-m-d'),
                'call_target' => fake()->numberBetween(200, 600),
                'appointment_target' => fake()->numberBetween(20, 80),
            ];
        });
    }
}
