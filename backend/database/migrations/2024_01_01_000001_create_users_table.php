<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table users : Gère l'authentification et les profils utilisateurs
     * - email unique pour identification
     * - role enum pour différencier utilisateurs et administrateurs
     * - bio et avatar pour personnalisation du profil
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('bio')->nullable()->comment('Biographie du profil utilisateur');
            $table->string('avatar_url')->nullable()->comment('URL de l\'avatar');
            $table->enum('role', ['user', 'admin'])->default('user')
                  ->comment('Rôle utilisateur : user (lecteur), admin (gestionnaire)');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};