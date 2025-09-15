<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movement>
 */
class MovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type'          => implode(fake()->randomElements(['E', 'S'], true)),
            'quantity'      => fake()->randomDigitNot(0),
            'description'   => fake()->sentence('3'),
            'product_id'    => fake()->numberBetween(1,30),
            'user_id'       => 1,
        ];
    }
}
