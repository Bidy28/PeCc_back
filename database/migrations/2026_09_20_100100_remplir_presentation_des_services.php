<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Valeurs rapatriées du content.ts du front. La clé de rapprochement est
     * le nom, comparé sans tenir compte de la casse ni des espaces : la base
     * contient « Pompe à chaleur » là où le front écrit « Pompe à Chaleur ».
     */
    private const PRESENTATIONS = [
        'plomberie' => [
            'nom' => 'Plomberie',
            'description' => "Votre plombier pour l'eau et les sanitaires : recherche de fuites, débouchage, installation sanitaire et chauffe-eau. Salles de bain et évacuations pris en charge de A à Z.",
            'icone' => 'droplets',
            'couleur' => '#2b6fff',
            'ordre' => 1,
        ],
        'chauffage' => [
            'nom' => 'Chauffage',
            'description' => "Votre chauffagiste pour chaudières gaz et bois, radiateurs et circuits de chauffage. Entretien, rénovation et dépannage pour un confort thermique optimal en hiver.",
            'icone' => 'flame',
            'couleur' => '#ff6b35',
            'ordre' => 2,
        ],
        'climatisation' => [
            'nom' => 'Climatisation',
            'description' => "Votre climaticien pour la pose et la maintenance de climatiseurs split, bi-split et multi-split. Confort d'été, performance énergétique et silence garantis.",
            'icone' => 'snowflake',
            'couleur' => '#22d3ee',
            'ordre' => 3,
        ],
        'pac' => [
            'nom' => 'Pompe à Chaleur',
            'description' => "Installateur de pompe à chaleur (PAC) air/air et air/eau : chauffage et eau chaude économes. Remplacement de chaudière, ballons thermodynamiques et entretien annuel.",
            'icone' => 'thermo',
            'couleur' => '#34d399',
            'ordre' => 4,
        ],
        'ventilation' => [
            'nom' => 'Ventilation',
            'description' => "Installateur ventilation / VMC simple ou double flux, entretien et dépannage. Qualité d'air intérieur, lutte contre l'humidité et économies sur le chauffage.",
            'icone' => 'wind',
            'couleur' => '#c264f0',
            'ordre' => 5,
        ],
    ];

    public function up(): void
    {
        $services = DB::table('services')->select('id', 'nom')->get();
        $slugsPris = [];

        foreach ($services as $service) {
            $slug = null;
            $valeurs = [];

            foreach (self::PRESENTATIONS as $cle => $presentation) {
                if (Str::lower(trim($service->nom)) === Str::lower($presentation['nom'])) {
                    $slug = $cle;
                    $valeurs = [
                        'description' => $presentation['description'],
                        'icone' => $presentation['icone'],
                        'couleur' => $presentation['couleur'],
                        'ordre' => $presentation['ordre'],
                    ];
                    break;
                }
            }

            // Métier absent du content.ts : slug dérivé du nom, habillage par
            // défaut. Il reste modifiable depuis l'admin par la suite.
            $slug ??= Str::slug($service->nom) ?: 'metier-'.$service->id;

            // Un slug doit rester unique même si deux métiers portent le même
            // nom : on suffixe avec l'id plutôt que d'échouer sur l'index.
            if (in_array($slug, $slugsPris, true)) {
                $slug .= '-'.$service->id;
            }
            $slugsPris[] = $slug;

            DB::table('services')
                ->where('id', $service->id)
                ->update($valeurs + ['slug' => $slug]);
        }

        // Toutes les lignes ont un slug : la contrainte peut être posée.
        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->string('slug')->nullable()->change();
        });
    }
};
