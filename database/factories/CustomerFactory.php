<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_name' => $this->faker->company(),
            'contact_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('0#########'),
            'industry' => $this->faker->randomElement(['IT', '製造業', '小売業', '金融業', '医療・福祉', '教育', '建設・不動産', '運輸・物流', '飲食・宿泊', '士業・コンサル', 'その他']),
            'temperature_rating' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'E', 'F']),
            'area' => $this->faker->randomElement(['東京', '大阪', '名古屋', '福岡', '札幌', '仙台', '広島']),
            'status' => $this->faker->randomElement(['受けブロ', '会話のみ', '見込みあり', '競合サービス利用中', '過去取引あり', '取引中', '架電禁止']),
            'priority' => $this->faker->numberBetween(1, 5),
            'memo' => $this->faker->optional()->realText(200),
        ];
    }

    /**
     * Indicate that the customer has high temperature rating.
     */
    public function highTemperature(): static
    {
        return $this->state(fn (array $attributes) => [
            'temperature_rating' => 'A',
            'priority' => 1,
        ]);
    }

    /**
     * Indicate that the customer has low temperature rating.
     */
    public function lowTemperature(): static
    {
        return $this->state(fn (array $attributes) => [
            'temperature_rating' => 'F',
            'priority' => 5,
        ]);
    }
}
