<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table pivot book_genre : Catégorisation thématique des livres
     * Un livre peut appartenir à plusieurs genres (ex: Fantasy + Aventure)
     */
    public function up(): void
    {
        Schema::create('book_genre', function (Blueprint $table) {
            $table->foreignId('book_id')
                  ->constrained('books')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('FK vers books');
                  
            $table->foreignId('genre_id')
                  ->constrained('genres')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('FK vers genres');
                  
            $table->timestamps();
            
            // Clé primaire composite
            $table->primary(['book_id', 'genre_id']);
        });

        // Index composite inversé pour recherche par genre -> livres
        Schema::table('book_genre', function (Blueprint $table) {
            $table->index(['genre_id', 'book_id'], 'book_genre_genre_book_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_genre');
    }
};