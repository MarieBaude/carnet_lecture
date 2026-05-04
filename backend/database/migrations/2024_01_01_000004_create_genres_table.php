<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table genres : Catégories littéraires pour classifier les livres
     * - name : nom du genre (100 chars suffisant)
     * - slug : version URL-friendly (indexée pour recherches rapides)
     */
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)
                  ->comment('Nom du genre littéraire');
            $table->string('slug')->unique()
                  ->comment('Slug URL unique pour le genre');
            $table->timestamps();
        });

        // Index pour recherche rapide par slug (utilisé dans les URLs)
        Schema::table('genres', function (Blueprint $table) {
            $table->index('slug', 'genres_slug_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};