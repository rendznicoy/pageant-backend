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
            'event_date' => fake()->dateTimeBetween('now', '+6 months'),
            'status' => fake()->randomElement(['inactive', 'active', 'completed']),
            'created_by' => User::factory(),
        ];
    }
}
