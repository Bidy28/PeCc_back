<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Articles du blog. Même structure que `realisations`, avec en plus une
     * date de publication saisie par l'admin : elle sert à l'affichage et au
     * tri, et ne dépend donc pas de created_at (un article peut être ajouté
     * après coup avec sa vraie date).
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->date('date');

            // Même convention que `prestations` et `realisations` : Laravel
            // déduit la relation du nom `service_id`.
            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            // Chemin relatif sur le disque "uploads" (ex : "articles/x7Kd2.webp").
            // Jamais une URL complète : le domaine peut changer, pas le fichier.
            $table->string('image')->nullable();

            $table->timestamps();

            // Le tri se fait toujours sur la date décroissante : un index
            // évite un tri complet de la table à chaque appel.
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
