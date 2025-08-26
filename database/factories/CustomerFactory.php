<?php

namespace Database\Factories;

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
        $industries = [
            'IT・ソフトウェア', '製造業', '建設業', '不動産', '金融・保険',
            '小売業', '卸売業', '運輸・物流', '教育', '医療・福祉',
            '飲食業', 'サービス業', 'コンサルティング'
        ];

        $statuses = ['見込み', '商談中', '受注', '失注', '保留'];
        $priorities = ['high', 'medium', 'low'];

        return [
            'user_id' => \App\Models\User::factory(),
            'company_name' => fake()->company(),
            'contact_name' => fake()->name(),
            'email' => fake()->optional(0.8)->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'industry' => fake()->randomElement($industries),
            'status' => fake()->randomElement($statuses),
            'priority' => fake()->randomElement($priorities),
            'memo' => fake()->optional(0.7)->paragraph(),
        ];
    }
}
