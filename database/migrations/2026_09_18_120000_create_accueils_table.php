<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accueils', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('sous_titre')->nullable();
            // Chemin relatif sur le disque "uploads" (ex : "accueil/x7Kd2.webp").
            // Jamais une URL complète : le domaine peut changer, pas le fichier.
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // Une seule ligne, créée dès la migration : l'admin la modifie,
        // il ne la crée jamais. Évite d'avoir à gérer le cas "pas encore de contenu".
        DB::table('accueils')->insert([
            'titre' => 'Plomberie, chauffage et climatisation',
            'sous_titre' => "Interventions rapides et devis gratuit dans toute la région.",
            'image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accueils');
    }
};
