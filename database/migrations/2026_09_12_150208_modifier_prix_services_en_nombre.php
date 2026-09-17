<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Transforme le prix en nombre décimal avec deux chiffres après la virgule.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('prix', 10, 2)->change();
        });
    }

    /**
     * Restaure le prix en texte.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('prix')->change();
        });
    }
};
