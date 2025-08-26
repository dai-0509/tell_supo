<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CallLog>
 */
class CallLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $results = ['success', 'no_answer', 'busy', 'appointment', 'not_interested', 'callback'];
        $result = fake()->randomElement($results);
        
        return [
            'customer_id' => \App\Models\Customer::factory(),
            'user_id' => \App\Models\User::factory(),
            'called_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'result' => $result,
            'next_call_date' => $result === 'callback' 
                ? fake()->dateTimeBetween('now', '+7 days') 
                : null,
            'notes' => fake()->optional(0.8)->paragraph(),
        ];
    }

    /**
     * アポ獲得結果
     */
    public function appointment()
    {
        return $this->state(function (array $attributes) {
            return [
                'result' => 'appointment',
                'next_call_date' => fake()->dateTimeBetween('+1 day', '+14 days'),
                'notes' => 'アポイントメント獲得: ' . fake()->sentence(),
            ];
        });
    }

    /**
     * コールバック希望
     */
    public function callback()
    {
        return $this->state(function (array $attributes) {
            return [
                'result' => 'callback',
                'next_call_date' => fake()->dateTimeBetween('+1 day', '+7 days'),
                'notes' => 'コールバック希望: ' . fake()->sentence(),
            ];
        });
    }

    /**
     * 今日の架電ログ
     */
    public function today()
    {
        return $this->state(function (array $attributes) {
            return [
                'called_at' => fake()->dateTimeBetween('today', 'now'),
            ];
        });
    }

    /**
     * 今週の架電ログ
     */
    public function thisWeek()
    {
        return $this->state(function (array $attributes) {
            return [
                'called_at' => fake()->dateTimeBetween('monday this week', 'now'),
            ];
        });
    }
}
