<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
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
            'document'      => fake()->randomNumber(),
            'email'         => fake()->unique()->safeEmail(),
            'cellphone'     => fake()->randomNumber()
        
            // $table->id();
            // $table->unsignedBigInteger('provider_id');
            // $table->string('cep', 9); 
            // $table->string('street', 70); 
            // $table->string('number', 10); 
            // $table->string('city', 60); 
            // $table->string('state', 60); 
            // $table->string('neighborhood', 60); 
            // $table->timestamps();
        ];
    }
}
