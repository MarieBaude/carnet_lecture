<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Les Ombres du Passé',
            'Le Dernier Rivage',
            'La Quête Éternelle',
            'Au-delà des Étoiles',
            'Le Secret des Anciens',
            'Les Ailes de la Nuit',
            'Le Chant du Loup',
            'La Porte des Mondes',
            "L'Épée de Vérité",
            "Les Brumes d'Automne",
            'Le Cercle de Pierre',
            'La Mer Infinie',
        ];

        return [
            'title' => fake()->randomElement($titles) . ' ' . fake()->randomElement(['I', 'II', 'III', '']),
            'isbn' => fake()->unique()->isbn13(),
            'cover_variant' => fake()->numberBetween(1, 10),
            'publisher' => fake()->company(),
            'published_date' => fake()->dateTimeBetween('-50 years', 'now')->format('Y-m-d'),
            'page_count' => fake()->numberBetween(80, 1200),
            'language' => 'fr',
            'summary' => fake()->paragraphs(4, true),
        ];
    }
}