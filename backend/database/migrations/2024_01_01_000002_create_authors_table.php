<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table authors : Stocke les informations sur les auteurs
     * - name : nom complet (200 chars pour noms composés longs)
     * - biography : texte libre pour biographie détaillée
     * - birth_date : date de naissance (nullable car pas toujours connue)
     */
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)
                  ->comment('Nom complet de l\'auteur (prénom + nom)');
            $table->text('biography')->nullable()
                  ->comment('Biographie détaillée de l\'auteur');
            $table->date('birth_date')->nullable()
                  ->comment('Date de naissance (peut être inconnue)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};