<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Saga>
 */
class SagaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sagas = [
            "Les Chroniques d'Émeraude",
            'Le Cycle des Origines',
            'La Trilogie des Abysses',
            'Les Archives Stellaires',
            'Les Royaumes Cachés',
            'La Lignée des Rois',
        ];

        return [
            'name' => fake()->randomElement($sagas),
            'description' => fake()->paragraphs(2, true),
        ];
    }
}