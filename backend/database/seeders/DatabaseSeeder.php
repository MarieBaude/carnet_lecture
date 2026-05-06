<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin de test
        \App\Models\User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@carnet.fr',
        ]);

        // 2. 10 utilisateurs
        \App\Models\User::factory(10)->create();

        // 3. 20 genres
        \App\Models\Genre::factory(20)->create();

        // 4. 50 auteurs
        $authors = \App\Models\Author::factory(50)->create();

        // 5. 5 sagas
        $sagas = \App\Models\Saga::factory(5)->create();

        // 6. 200 livres
        \App\Models\Book::factory(200)->create()->each(function ($book) use ($authors, $sagas) {
            $book->authors()->attach(
                $authors->random(rand(1, 3))->pluck('id')->toArray()
            );

            $genres = \App\Models\Genre::inRandomOrder()->take(rand(1, 4))->pluck('id')->toArray();
            $book->genres()->attach($genres);

            if (rand(1, 100) <= 30) {
                $book->sagas()->attach(
                    $sagas->random()->id,
                    ['tome_number' => rand(1, 10) + (rand(0, 1) ? 0.5 : 0)]
                );
            }
        });
    }
}