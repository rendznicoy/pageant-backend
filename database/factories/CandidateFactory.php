<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'candidate_number' => fake()->unique()->randomNumber(3),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'sex' => fake()->randomElement(['male', 'female']),
            'team' => fake()->word(),
            'photo' => null,
        ];
    }
}
