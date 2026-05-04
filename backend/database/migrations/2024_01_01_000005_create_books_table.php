<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table books : Table centrale du projet, stocke toutes les informations des livres
     * 
     * Choix techniques :
     * - ISBN sur 13 caractères (norme ISBN-13 depuis 2007)
     * - cover_variant (1-10) pour varier les dégradés CSS côté front
     * - language sur 2 caractères (code ISO 639-1)
     * - softDeletes pour archivage sans perte de données
     * - Index GIN pour recherche full-text en français sur titre et résumé
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500)
                  ->comment('Titre complet du livre (500 chars pour titres longs)');
            $table->string('isbn', 13)->nullable()->unique()
                  ->comment('ISBN-13 du livre (13 caractères)');
            $table->unsignedTinyInteger('cover_variant')
                  ->comment('Variante de couverture (1-10) pour dégradé CSS');
            $table->string('publisher', 200)->nullable()
                  ->comment('Maison d\'édition');
            $table->date('published_date')->nullable()
                  ->comment('Date de publication');
            $table->unsignedInteger('page_count')->nullable()
                  ->comment('Nombre de pages');
            $table->string('language', 2)->default('fr')
                  ->comment('Code langue ISO 639-1 (fr, en, es, etc.)');
            $table->text('summary')->nullable()
                  ->comment('Résumé du livre');
            $table->timestamps();
            $table->softDeletes()
                  ->comment('Suppression douce pour archivage');
        });

        // Index composites et full-text PostgreSQL
        Schema::table('books', function (Blueprint $table) {
            // Index B-tree standard pour le titre (recherches exactes)
            $table->index('title', 'books_title_idx');
            
            // Index sur la date de publication (tri chronologique fréquent)
            $table->index('published_date', 'books_published_date_idx');
            
            // Index sur la langue (filtrage par langue)
            $table->index('language', 'books_language_idx');
        });

        /**
         * Index GIN pour full-text search PostgreSQL en français
         * 
         * Utilise to_tsvector avec configuration 'french' pour :
         * - Supprimer les mots vides (le, la, les, etc.)
         * - Gérer la stemming (chercher -> cherch)
         * - Gérer les accents (théâtre -> theatre)
         * 
         * Les indexes GIN (Generalized Inverted Index) sont optimisés pour
         * les recherches full-text dans PostgreSQL
         */
        DB::statement("
            CREATE INDEX books_title_fulltext_idx 
            ON books 
            USING GIN (to_tsvector('french', title))
        ");
        
        DB::statement("
            CREATE INDEX books_summary_fulltext_idx 
            ON books 
            USING GIN (to_tsvector('french', summary))
        ");

        // Index combiné pour recherche full-text sur titre ET résumé
        DB::statement("
            CREATE INDEX books_fulltext_combined_idx 
            ON books 
            USING GIN (to_tsvector('french', coalesce(title, '') || ' ' || coalesce(summary, '')))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};