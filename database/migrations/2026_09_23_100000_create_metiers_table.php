<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Détail du savoir-faire d'un service : les blocs « titre + description »
     * affichés dans la pop-up « En savoir plus » et sur la page métier.
     *
     * Un service en a plusieurs ; jusqu'ici ces blocs étaient figés dans le
     * content.ts du front (constante PRESTATIONS).
     */
    public function up(): void
    {
        Schema::create('metiers', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();

            // Même convention que les autres tables : Laravel déduit la
            // relation du nom `service_id`. Le détail disparaît avec son
            // service.
            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metiers');
    }
};
