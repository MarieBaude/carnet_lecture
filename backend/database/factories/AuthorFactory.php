<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstNames = ['Victor', 'Albert', 'J.K.', 'George', 'Jane', 'J.R.R.', 'Isaac', 'Margaret', 'Terry', 'Ursula', 'Philip', 'Arthur', 'Agatha', 'Stephen', 'Neil'];
        $lastNames = ['Hugo', 'Camus', 'Rowling', 'Orwell', 'Austen', 'Tolkien', 'Asimov', 'Atwood', 'Pratchett', 'Le Guin', 'Dick', 'Clarke', 'Christie', 'King', 'Gaiman'];

        return [
            'name' => fake()->randomElement($firstNames) . ' ' . fake()->randomElement($lastNames),
            'biography' => fake()->paragraphs(3, true),
            'birth_date' => fake()->dateTimeBetween('-200 years', '-30 years')->format('Y-m-d'),
        ];
    }
}