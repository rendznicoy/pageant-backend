<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Judge;
use App\Models\Candidate;
use App\Models\Category;
use App\Models\Event;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Score>
 */
class ScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => null,
            'judge_id' => null,
            'candidate_id' => null,
            'category_id' => null,
            'score' => fake()->numberBetween(1, 10),
        ];
    }
}
