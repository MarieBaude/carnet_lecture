<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin de test
        \App\Models\User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@carnet.fr',
        ]);

        // 2. 10 utilisateurs lambda
        \App\Models\User::factory(10)->create();

        // 3. Tous les genres (20 max)
        \App\Models\Genre::factory(20)->create();

        // 4. 50 auteurs
        $authors = \App\Models\Author::factory(50)->create();

        // 5. 5 sagas
        $sagas = \App\Models\Saga::factory(5)->create();

        // 6. 200 livres avec relations
        \App\Models\Book::factory(200)->create()->each(function ($book) use ($authors, $sagas) {
            // Attacher 1-3 auteurs
            $book->authors()->attach(
                $authors->random(rand(1, 3))->pluck('id')->toArray()
            );

            // Attacher 1-4 genres
            $genres = \App\Models\Genre::inRandomOrder()->take(rand(1, 4))->pluck('id')->toArray();
            $book->genres()->attach($genres);

            // 30% de chance d'appartenir à une saga
            if (rand(1, 100) <= 30) {
                $book->sagas()->attach(
                    $sagas->random()->id,
                    ['tome_number' => rand(1, 10) + (rand(0, 1) ? 0.5 : 0)]
                );
            }
        });
    }
}