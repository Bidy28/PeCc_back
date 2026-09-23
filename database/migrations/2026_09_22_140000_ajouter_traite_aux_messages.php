<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permet à l'admin de distinguer les demandes déjà traitées des nouvelles.
     * Les messages existants sont considérés comme non traités.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('traite')->default(false)->after('message');

            // La liste est filtrée sur cette colonne à chaque affichage.
            $table->index('traite');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['traite']);
            $table->dropColumn('traite');
        });
    }
};
