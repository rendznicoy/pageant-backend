<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
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
            'category_name' => fake()->word(),
            'category_weight' => fake()->randomFloat(2, 10, 30),
            'max_score' => 10,
        ];
    }

    public function weighted(): static
    {
        return $this->state([
            'category_weight' => fake()->randomFloat(2, 30, 50),
        ]);
    }

    public function named(string $name): static
    {
        return $this->state(['category_name' => $name]);
    }
}
