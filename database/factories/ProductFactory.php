<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'          => fake()->unique()->name(),
            'quantity'      => fake()->numberBetween(5,40),
            'minimum'       => fake()->numberBetween(5,20),
            'maximum'       => fake()->numberBetween(40,60),
            'category_id'   => fake()->numberBetween(1,3),
            'provider_id'   => fake()->numberBetween(1,3),
            'unity_id'      => fake()->numberBetween(1,3),
            // $table->string('name', 50);
            // $table->float('quantity', 8, 3);
            
            // $table->integer('minimum');
            // $table->integer('maximum');

            // $table->unsignedBigInteger('category_id');
            // $table->unsignedBigInteger('provider_id')->nullable();
            // $table->unsignedBigInteger('unity_id')->nullable();

            // $table->boolean('disabled')->default(false);
        ];
    }
}
