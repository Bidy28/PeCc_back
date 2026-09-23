<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute à chaque métier ce qui n'existait jusqu'ici que dans le
     * content.ts du front : identifiant d'URL, description, habillage visuel,
     * visibilité et ordre d'affichage.
     *
     * Le slug est créé nullable ici : les lignes existantes n'en ont pas
     * encore. La migration suivante les remplit puis pose la contrainte
     * d'unicité — l'ordre inverse échouerait sur des valeurs NULL dupliquées.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('nom');
            $table->text('description')->nullable()->after('slug');
            $table->string('icone', 40)->default('zap')->after('description');
            $table->string('couleur', 7)->default('#2b6fff')->after('icone');
            $table->boolean('actif')->default(true)->after('couleur');
            $table->unsignedSmallInteger('ordre')->default(0)->after('actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'description',
                'icone',
                'couleur',
                'actif',
                'ordre',
            ]);
        });
    }
};
