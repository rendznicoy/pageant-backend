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

    public function male(): static
    {
        return $this->state([
            'sex' => 'male',
            'team' => 'Gladiators',
        ]);
    }

    public function female(): static
    {
        return $this->state([
            'sex' => 'female',
            'team' => 'Gladiators',
        ]);
    }

    public function number(int $num): static
    {
        return $this->state(['candidate_number' => $num]);
    }
}
