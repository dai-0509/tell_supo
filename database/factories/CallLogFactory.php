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
        $startedAt = fake()->dateTimeBetween('-1 month', 'now');
        $endedAt = fake()->optional(0.8)->dateTimeBetween($startedAt, 'now');

        return [
            'user_id' => User::factory(),
            'customer_id' => Customer::factory(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'result' => fake()->randomElement(['connected', 'no_answer', 'busy', 'failed', 'voicemail']),
            'notes' => fake()->optional(0.7)->realText(200),
        ];
    }

    public function connected(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'connected',
        ]);
    }

    public function noAnswer(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'no_answer',
            'ended_at' => null,
        ]);
    }
}
