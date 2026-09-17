<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table des services.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prix');
            $table->json('elements');
            $table->timestamps();
        });
    }

    /**
     * Supprime la table des services.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
