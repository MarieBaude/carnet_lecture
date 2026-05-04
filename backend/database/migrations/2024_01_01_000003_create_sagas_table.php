<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table sagas : Regroupe les livres appartenant à une même série
     * - name : titre de la saga (300 chars pour titres longs)
     * - description : résumé global de la saga
     */
    public function up(): void
    {
        Schema::create('sagas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 300)
                  ->comment('Nom complet de la saga/série');
            $table->text('description')->nullable()
                  ->comment('Description globale de la saga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sagas');
    }
};