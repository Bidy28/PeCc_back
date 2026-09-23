<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chantiers présentés dans la galerie du site.
     */
    public function up(): void
    {
        Schema::create('realisations', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('ville')->nullable();

            // Même convention que la table `prestations` : Laravel déduit la
            // relation du nom `service_id`, sans avoir à la préciser.
            // La réalisation est supprimée si son métier l'est.
            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            // Chemin relatif sur le disque "uploads" (ex : "realisations/x7Kd2.webp").
            // Jamais une URL complète : le domaine peut changer, pas le fichier.
            $table->string('image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisations');
    }
};
