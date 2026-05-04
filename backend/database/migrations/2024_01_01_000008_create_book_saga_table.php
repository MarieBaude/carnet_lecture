<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table pivot book_saga : Gère l'appartenance aux sagas
     * 
     * Particularité : tome_number en decimal(3,1) pour gérer :
     * - Tomes principaux : 1, 2, 3
     * - Tomes intermédiaires : 1.5, 2.5
     * - Préquelles : 0.5
     */
    public function up(): void
    {
        Schema::create('book_saga', function (Blueprint $table) {
            $table->foreignId('book_id')
                  ->constrained('books')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('FK vers books');
                  
            $table->foreignId('saga_id')
                  ->constrained('sagas')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('FK vers sagas');
                  
            $table->decimal('tome_number', 3, 1)->nullable()
                  ->comment('Numéro de tome (1, 2, 2.5, etc.), null si hors-série');
                  
            $table->timestamps();
            
            // Clé primaire composite
            $table->primary(['book_id', 'saga_id']);
        });

        // Index pour tri par tome dans une saga
        Schema::table('book_saga', function (Blueprint $table) {
            $table->index(['saga_id', 'tome_number'], 'book_saga_saga_tome_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_saga');
    }
};