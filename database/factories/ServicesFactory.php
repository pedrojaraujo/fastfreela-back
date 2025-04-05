<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Services>
 */
class ServicesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'contractor_id' => User::where('user_type', 'contractor')->inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'message' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['pendente', 'aceito', 'rejeitado']),
        ];
    }
}
