<?php

namespace Database\Factories;

use App\Models\CallLog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CallLog>
 */
class CallLogFactory extends Factory
{
    protected $model = CallLog::class;

    public function definition(): array
    {
        $started_at = fake()->dateTimeBetween('-1 month', 'now');
        $ended_at = fake()->optional(0.8)->dateTimeBetween($started_at, 'now');

        return [
            'user_id' => User::factory(),
            'customer_id' => Customer::factory(),
            'started_at' => $started_at,
            'ended_at' => $ended_at,
            'result' => fake()->randomElement(['通話成功', '受けブロ', '会話のみ', '見込みあり']),
            'notes' => fake()->optional(0.7)->realText(200),
        ];
    }

    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => '通話成功',
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => '受けブロ',
            'ended_at' => null,
        ]);
    }
}
