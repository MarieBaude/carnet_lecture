<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
 */
class GenreFactory extends Factory
{
    private array $genres = [
        'Fantasy',
        'Science-Fiction',
        'Romance',
        'Policier',
        'Thriller',
        'Horreur',
        'Historique',
        'Biographie',
        'Philosophie',
        'Poésie',
        'Théâtre',
        'Jeunesse',
        'Young Adult',
        'Aventure',
        'Dystopie',
        'Contemporain',
        'Classique',
        'Manga',
        'BD',
        'Essai',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement($this->genres),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
        ];
    }
}