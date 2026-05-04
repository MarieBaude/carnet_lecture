<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['wishlist', 'owned', 'reading', 'read', 'dropped']);
            $table->tinyInteger('rating')->nullable();
            $table->integer('current_page')->nullable();
            $table->date('started_at')->nullable();
            $table->date('finished_at')->nullable();
            $table->text('user_comment')->nullable();
            $table->string('shelf', 100)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'book_id']);
            $table->index('status');
            $table->index('shelf');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_books');
    }
};