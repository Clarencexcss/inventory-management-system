<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MeatCut>
 */
class MeatCutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'animal_type' => fake()->randomElement(['beef', 'pork', 'chicken', 'lamb', 'goat']),
            'cut_type' => fake()->word(),
            'default_price_per_kg' => fake()->randomNumber(3),
            'minimum_stock_level' => fake()->randomElement([5, 10, 15, 20, 25]),
        ];
    }
}