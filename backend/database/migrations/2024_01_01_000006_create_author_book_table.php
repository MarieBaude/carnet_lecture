<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table pivot author_book : Relation Many-to-Many entre auteurs et livres
     * 
     * Règles de suppression :
     * - ON DELETE RESTRICT sur author_id : empêche de supprimer un auteur
     *   si des livres lui sont associés (protection des données)
     * - ON DELETE CASCADE sur book_id : un livre supprimé retire ses associations
     */
    public function up(): void
    {
        Schema::create('author_book', function (Blueprint $table) {
            $table->foreignId('author_id')
                  ->constrained('authors')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->comment('FK vers authors (restrict pour protéger les données)');
                  
            $table->foreignId('book_id')
                  ->constrained('books')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('FK vers books (cascade pour nettoyer les relations)');
                  
            $table->timestamps();
            
            // Clé primaire composite
            $table->primary(['author_id', 'book_id']);
        });

        // Index composite inversé pour recherche par livre -> auteurs
        Schema::table('author_book', function (Blueprint $table) {
            $table->index(['book_id', 'author_id'], 'author_book_book_author_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('author_book');
    }
};