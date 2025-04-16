<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_name' => fake()->catchPhrase(),
            'event_code' => strtoupper(fake()->unique()->lexify('EVT???')),
            'start_date' => fake()->dateTimeBetween('now', '+6 months'),
            'end_date' => fake()->dateTimeBetween('+6 months', '+1 year'),
            'status' => fake()->randomElement(['inactive', 'active', 'completed']),
            'created_by' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }
}
